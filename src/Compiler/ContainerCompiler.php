<?php

declare(strict_types=1);

namespace Norvica\Container\Compiler;

use Closure;
use Norvica\Container\Definition\Definitions;
use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Obj;
use Norvica\Container\Definition\Ref;
use Norvica\Container\Definition\Run;
use Norvica\Container\Definition\Val;
use Norvica\Container\Exception\ContainerException;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure as Closure_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\NodeVisitor;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

final class ContainerCompiler
{
    private readonly Definitions $definitions;
    private readonly Parser $parser;
    private array $ast;
    private array $map;
    private array $postponed;

    public function __construct(
        Definitions $definitions,
    ) {
        $ids = array_keys($definitions->all());
        $hashes = array_map('md5', $ids);
        $this->map = array_combine($ids, $hashes);
        $this->definitions = $definitions;
        $this->postponed = [];
        $this->parser = (new ParserFactory())->createForNewestSupportedVersion();
    }

    public function compile(): string
    {
        $this->ast = $this->parser->parse(file_get_contents(__DIR__ . '/template.php'));
        $body = &$this->ast[1]->expr->class->stmts;
        $map = &$body[0]->props[0]->default->items;

        foreach ($this->map as $id => $hash) {
            $definition = $this->definitions->get($id);
            $body[] = new ClassMethod(
                name: "_{$hash}",
                subNodes: [
                    'flags' => ReflectionMethod::IS_PRIVATE,
                    'params' => [],
                    'stmts' => [
                        new Return_(
                            expr: $this->definition($definition, $id),
                        ),
                    ],
                ],
            );
        }

        while ($this->postponed) {
            $id = array_shift($this->postponed);
            $body[] = new ClassMethod(
                name: "_{$this->map[$id]}",
                subNodes: [
                    'flags' => ReflectionMethod::IS_PRIVATE,
                    'params' => [],
                    'stmts' => [
                        new Return_(
                            expr: $this->definition(new Obj($id), $id),
                        ),
                    ],
                ],
            );
        }

        foreach ($this->map as $id => $hash) {
            $map[] = new ArrayItem(
                value: new String_(value: $hash),
                key: new String_(value: $id),
            );
        }

        return (new Standard())->prettyPrintFile($this->ast);
    }

    private function definition(
        Val|Env|Run|Ref|Obj|array|string|int|float|bool|null $definition,
        string|null $id = null,
    ): Expr {
        if ($definition === null || is_scalar($definition)) {
            return $this->scalar($definition);
        }

        if (is_array($definition)) {
            $items = [];
            foreach ($definition as $key => $item) {
                $items[] = new ArrayItem(
                    value: $this->definition($item),
                    key: is_int($key) ? new Int_(value: $key): new String_(value: $key),
                );
            }

            return new Array_(items: $items);
        }

        if ($definition instanceof Val) {
            return $this->val($definition);
        }

        if ($definition instanceof Env) {
            return $this->env($definition);
        }

        if ($definition instanceof Ref) {
            return $this->ref($definition);
        }

        if ($definition instanceof Obj) {
            return $this->obj($definition, $id);
        }

        if ($definition instanceof Run) {
            return $this->run($definition, $id);
        }

        throw new ContainerException(); // TODO: message
    }

    private function val(Val $definition): Expr
    {
        return $this->scalar($definition->value);
    }

    private function env(Env $definition): Expr
    {
        return new FuncCall(
            name: new FullyQualified(
                name: 'Norvica\Container\_env',
            ),
            args: [
                new Arg(
                    value: new String_(value: $definition->name),
                    name: new Identifier(name: 'name'),
                ),
                new Arg(
                    value: $this->scalar($definition->default),
                    name: new Identifier(name: 'default'),
                ),
                new Arg(
                    value: new String_(value: $definition->type()),
                    name: new Identifier(name: 'type'),
                ),
            ],
        );
    }

    private function ref(Ref $definition): Expr
    {
        return new MethodCall(
            var: new Variable(name: 'this'),
            name: new Identifier(name: 'get'),
            args: [
                new Node\Arg(
                    value: new String_(
                        value: $definition->id,
                    ),
                )
            ],
        );
    }

    private function scalar(string|int|float|bool|null $value): Expr
    {
        return match (true) {
            is_string($value) => new String_(value: $value),
            is_int($value) => new Int_(value: $value),
            is_float($value) => new Node\Scalar\Float_(value: $value),
            is_bool($value) => new Expr\ConstFetch(
                name: new FullyQualified(
                    name: var_export($value, true),
                )
            ),
            $value === null => new Expr\ConstFetch(
                name: new FullyQualified(
                    name: 'null',
                )
            ),
        };
    }

    private function obj(Obj $definition, string|null $id = null): Expr
    {
        if (is_string($definition->instantiator)
            && class_exists($definition->instantiator)) {
            // Foo::class
            // 'Some\function'
            // 'Foo::bar'
            $rc = new ReflectionClass($definition->instantiator);
            $instantiation = new Expr\New_(
                class: new FullyQualified(name: $definition->instantiator),
                args: $this->args(
                    $definition->arguments,
                    $rc->hasMethod('__construct')
                        ? $rc->getMethod('__construct')
                        : null,
                ),
            );
        } else {
            $instantiation = $this->run(new Run($definition->instantiator, ...$definition->arguments), $id);
        }

        if (count($definition->calls) < 1) {
            return $instantiation;
        }

        $stmts = [
            new Expression(
                expr: new Assign(
                    var: new Variable(
                        name: 'instance',
                    ),
                    expr: $instantiation,
                )
            )
        ];

        foreach ($definition->calls as $call) {
            $stmts[] = new Expression(
                expr: new MethodCall(
                    var: new Variable(
                        name: 'instance',
                    ),
                    name: new Identifier(
                        name: $call->method,
                    ),
                    args: $this->args($call->arguments), // TODO: reflection based on factory return type
                )
            );
        }

        $stmts[] = new Return_(expr: new Variable(name: 'instance'));

        return new FuncCall(
            name: new Closure_(
                subNodes: [
                    'stmts' => $stmts,
                ],
            )
        );
    }

    private function run(Run $definition, string|null $id = null): Expr
    {
        if (is_array($definition->instantiator)) {
            // [ref('a'), 'foo']
            if (is_object($definition->instantiator[0])) {
                if ($definition->instantiator[0] instanceof Ref) {
                    return new FuncCall(
                        name: new FuncCall(
                            name: new Array_(
                                items: [
                                    new ArrayItem(
                                        value: $this->ref($definition->instantiator[0]),
                                    ),
                                    new ArrayItem(
                                        value: new String_(value: $definition->instantiator[1]),
                                    ),
                                ],
                            ),
                            args: [new VariadicPlaceholder()],
                        ),
                        args: $this->args($definition->arguments),
                    );
                }

                // [new Foo(), 'bar']
                // [Foo::create(), 'bar']
                // [$foo, 'bar']
                throw new ContainerException(); // TODO: message
            }

            // [Foo::class, 'bar']
            $rm = new ReflectionMethod($definition->instantiator[0], $definition->instantiator[1]);

            return new FuncCall(
                name: new FuncCall(
                    name: new Array_(
                        items: [
                            new ArrayItem(
                                value: new ClassConstFetch(
                                    class: new FullyQualified(
                                        name: $definition->instantiator[0],
                                    ),
                                    name: 'class',
                                ),
                            ),
                            new ArrayItem(
                                value: new String_(value: $definition->instantiator[1]),
                            ),
                        ],
                    ),
                    args: [new VariadicPlaceholder()],
                ),
                args: $this->args($definition->arguments, $rm),
            );
        }

        if ($definition->instantiator instanceof Closure) {
            $rf = new ReflectionFunction($definition->instantiator);

            if ($rf->isAnonymous()) {
                // fn() => new \stdClass()
                // function() => {return new \stdClass();}
                // TODO: allow only static closures
                // TODO: don't allow 'use'
                $function = $this->anonymous($rf, $id);

                return new FuncCall(
                    name: $function,
                    args: $this->args($definition->arguments, $rf),
                );
            }

            if (null !== $rc = $rf->getClosureCalledClass()) {
                // Foo::bar(...)
                if (!$rf->isStatic()) {
                    // (new Foo())->bar(...)
                    throw new ContainerException(); // TODO: message
                }

                return new FuncCall(
                    name: new StaticCall(
                        class: new FullyQualified(
                            name: $rc->getName(),
                        ),
                        name: new Identifier(
                            name: $rf->getName(),
                        ),
                        args: [new VariadicPlaceholder()],
                    ),
                    args: $this->args($definition->arguments, $rf),
                );
            }

            return new FuncCall(
                name: new FullyQualified(
                    name: $rf->getName(),
                ),
                args: $this->args($definition->arguments, $rf),
            );
        }

        throw new ContainerException(); // TODO: message
    }

    /**
     * @return Arg[]
     */
    private function args(
        array $arguments,
        ReflectionMethod|ReflectionFunction|null $reflection = null,
    ): array {
        $args = [];
        foreach ($arguments as $i => $argument) {
            $processed = $this->definition($argument);
            $args[$i] = new Arg(
                value: $processed,
                name: is_string($i)
                    ? new Identifier(name: $i)
                    : null,
            );
        }

        if ($reflection) {
            foreach ($reflection->getParameters() as $i => $rp) {
                if ($rp->isVariadic()) {
                    break;
                }

                $name = $rp->getName();
                if (isset($args[$i]) || isset($args[$name])) {
                    continue;
                }

                if ($rp->isDefaultValueAvailable()) {
                    continue;
                }

                $args[$rp->getName()] = new Arg(
                    value: $this->autowire($rp),
                    name: new Identifier(name: $name),
                );
            }
        }

        return $args;
    }

    private function anonymous(ReflectionFunction $rf, string $id): Closure_|ArrowFunction
    {
        $filename = $rf->getFileName();
        $start = $rf->getStartLine();
        $end = $rf->getEndLine();

        // FIXME: optimize
        $ast = $this->parser->parse(file_get_contents($filename));

        // resolve names
        $resolver = new \PhpParser\NodeVisitor\NameResolver();
        $traverser = new \PhpParser\NodeTraverser();
        $traverser->addVisitor($resolver);
        $ast = $traverser->traverse($ast);

        $function = null;
        NodeTraversal::traverse($ast, static function (Node $node) use (&$function, $start, $end) {
            if (!in_array($node->getType(), ['Expr_ArrowFunction', 'Expr_Closure'])) {
                // continue
                return null;
            }

            /** @var Closure_|ArrowFunction $node */
            if ($node->getStartLine() === $start && $node->getEndLine() === $end) {
                $function = $node;
                foreach ($function->params as $param) {
                    $param->attrGroups = [];
                }

                return NodeVisitor::DONT_TRAVERSE_CHILDREN;
            }

            return null;
        });

        return $function;
    }

    private function autowire(ReflectionParameter $rp): Expr
    {
        if (null !== $ref = ($rp->getAttributes(Ref::class)[0] ?? null)) {
            return $this->definition($ref->newInstance());
        }

        if (null !== $env = ($rp->getAttributes(Env::class)[0] ?? null)) {
            return $this->definition($env->newInstance());
        }

        $rt = $rp->getType();

        $reference = "'\${$rp->getName()}' in '{$rp->getDeclaringClass()?->getName()}::{$rp->getDeclaringFunction()->getName()}()'";

        if ($rt === null) {
            throw new ContainerException("Cannot autowire parameter {$reference} without type being defined.");
        }

        if (!$rt instanceof ReflectionNamedType) {
            throw new ContainerException("Cannot autowire parameter {$reference} based on union or intersection type.");
        }

        if ($rt->isBuiltin()) {
            throw new ContainerException("Cannot autowire parameter {$reference} based on built-in type '{$rt->getName()}'.");
        }

        $id = $rt->getName();
        if (isset($this->map[$id])) {
            return $this->definition(new Ref($id));
        }

        $this->map[$id] = md5($id);
        $this->postponed[] = $id;

        return $this->definition(new Ref($id));
    }
}
