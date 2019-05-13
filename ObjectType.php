<?php

namespace Dumper;

use StdClass;
use Closure;
use ReflectionClass;
use Generator;

class ObjectType
{
    public function isClass($object)
    {
        $name = get_class($object);
        return $object instanceof $name;
    }

    public function isStandard($object)
    {
        return $object instanceof stdClass;
    }

    public function isClosure($object)
    {
        return $object instanceof Closure;
    }

    public function isAnonymous($object)
    {
        $name = get_class($object);
        return (new ReflectionClass($name))->isAnonymous();
    }

    public function isGenerator($object)
    {
        return $object instanceof Generator;
    }

    public function isPrivate($name, $class)
    {
        return strpos($name, $class) > 0;
    }

    public function isProtected($name)
    {
        return strpos($name, '*') > 0;
    }

    public function hasParent($name)
    {
        $parent = (new ReflectionClass($name))->getParentClass();
        return count($parent) !== 0;
    }
}
