<?php

namespace Polargraph\Di;

use Polargraph\Di;
use Polargraph\DiInterface;
use Exception;

trait InjectableTrait
{
    protected $container;

    public function setDi(DiInterface $di): DiInterface
    {
        $this->container = $di;
        return $this->container;
    }

    public function getDi(): DiInterface
    {
        if(empty($this->container)) {
            $di = Di::instance();
            $this->setDi($di);
        }
        return $this->container;
    }

    public function __get(string $property)
    {
        $di = $this->container;
        if(empty($di)) {
            $di = Di::instance();
            $this->setDi($di);
        }
        if($property === 'di') {
            return $di;
        }
        if($di->has($property)) {
            $service = $di->get($property);
            $this->$property = $service;
            return $service;
        }
        throw new Exception('Access to undefined property ' . $property);
    }
}