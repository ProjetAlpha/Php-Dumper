<?php

namespace Dumper;

class Format
{
    protected $propertyPrefix = [
        'public' => '+',
        'private' => '-',
        'protected' => '#'
    ];

    protected $type = [
        'string' => ['"', '"'],
        'integer' => ['(', ')'],
        'boolean' => ['(', ')'],
        'double' => ['(', ')'],
        'NULL' => ''
    ];

    protected $keyWords = [
        'object' => 'Object',
        'parent' => 'extends',
        'interface' => 'implements'
    ];

    protected $objectTypeList = [
        'standard' => 'Object(stdClass)',
        'class' => 'Object',
        'closure' => 'Object(Closure)',
        'anonymous' => 'Object(class@anonymous)',
        'generator' => 'Object(Generator)'
    ];


    public function __construct()
    {
    }

    public function get($object)
    {
        if (property_exists($this, $object)) {
            return $this->{$object};
        }
    }

    public function getPropertyPrefix($name)
    {
        if ($this->has($name, $this->propertyPrefix)) {
            return $this->propertyPrefix[$name];
        }

        throw new InvalidArgumentException(sprintf('%s dont have any prefix'), $name);
    }

    public function has($name, $object)
    {
        return array_key_exists($name, $object);
    }
}
