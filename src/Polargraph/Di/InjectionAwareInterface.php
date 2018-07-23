<?php

namespace Polargraph\Di;

use Polargraph\DiInterface;

interface InjectionAwareInterface
{
    public function setDi(DiInterface $di): DiInterface;
    public function getDi(): DiInterface;
}