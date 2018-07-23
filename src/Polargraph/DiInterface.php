<?php

namespace Polargraph;

use ArrayAccess as ArrayAccessInterface;

interface DiInterface extends ArrayAccessInterface
{
    public function set(string $name, callable $definition);

    public function unset(string $name);

    public function get(string $name);

    public function has(string $name);
}
