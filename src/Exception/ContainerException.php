<?php

declare(strict_types=1);

namespace Norvica\Container\Exception;

use LogicException;
use Psr\Container\ContainerExceptionInterface;

final class ContainerException extends LogicException implements ContainerExceptionInterface
{
}
