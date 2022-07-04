<?php

namespace Framework;

abstract class Entity
{
    public function __set($name, $value)
    {
        $name = str_replace('_', '', ucwords($name, '_'));
        $setter = sprintf('set%s', $name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
    }
}
