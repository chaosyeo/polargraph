<?php

namespace Polargraph;

use Polargraph\DiInterface;
use Polargraph\Di\ServiceInterface;
use Polargraph\Di\Service;
use Exception;

class Di implements DiInterface
{
    protected $services = array();

    protected static $instance = null;

    public function __construct()
    {
        $instance = self::$instance;
        if(empty($instance)) {
            self::$instance = $this;
        }
    }

    public static function instance()
    {
        $instance = self::$instance;
        if(empty($instance)) {
            throw new Exception("A di object is required to access the services");
        }
        return $instance;
    }

    public function set(string $name, callable $definition): ServiceInterface
    {
        $service = new Service($name, $definition);
        $this->services[$name] = $service;
        return $service;
    }

    public function get(string $name, $parameters = null)
    {
        if($this->has($name)) {
            $service = $this->services[$name];
            $instance = $service->resolve($parameters, $this);
            return $instance;
        }
        throw new Exception('Service ' . $name . ' was not found in di container.');
    }

    public function unset(string $name): bool
    {
        unset($this->services[$name]);
        return true;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->services);
    }

    public function offsetSet($name, $definition): ServiceInterface
    {
        return $this->set($name, $definition);
    }

    public function offsetGet($name): ServiceInterface
    {
        return $this->get($name);
    }

    public function offsetUnset($name): bool
    {
        return $this->unset($name);
    }

    public function offsetExists($name): bool
    {
        return $this->has($name);
    }
}
