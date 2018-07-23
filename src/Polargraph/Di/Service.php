<?php

namespace Polargraph\Di;

use Polargraph\Di\ServiceInterface;
use Polargraph\DiInterface;
use Exception;
use Closure;

class Service implements ServiceInterface
{
    protected $name;
    protected $definition;
    protected $instance;

    public final function __construct(string $name, Closure $definition)
    {
        $this->name = $name;
        $this->definition = $definition;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDefinition(Closure $definition): void
    {
        $this->definition = $definition;
    }

    public function getDefinition(string $name): Closure
    {
        return $this->definition;
    }

    public function resolve(array $parameters = null, DiInterface $di = null)
    {
        $definition = $this->definition;
        if(is_object($definition) && is_a($definition, 'Closure')) {
            $definition = $definition->bindTo($di);
            if(count($parameters) > 0) {
                $instance = call_user_func_array($definition, $parameters);
            } else {
                $instance = call_user_func($definition);
            }
            $this->instance = $instance;
            return $this->instance;
        }
        throw new Exception('Service can not resolve the instance.');
    }
}