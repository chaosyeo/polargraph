<?php

namespace Polargraph\Di;

use Polargraph\DiInterface;
use Closure;

interface ServiceInterface
{

    public function getName(): string;

    public function setDefinition(Closure $definition): void;

    public function getDefinition(string $name): Closure;

    public function resolve(array $parameters = array(), DiInterface $di = null);

}
