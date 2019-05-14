<?php

namespace Dumper;

use Dumper\ObjectType;
use \ReflectionClass;

class ObjectHandler extends ObjectType
{
    public $class;


    public function __construct($object)
    {
        $this->initialize($object);
    }

    public function initialize($object)
    {
        if (is_object($object)) {
            $name = get_class($object);
            $this->hasParent($name) ? $this->setObject($name) : $this->class[] = $name;
        }
    }

    public function setObject($name)
    {
        $class = new ReflectionClass($name);

        while ($parent = $class->getParentClass()) {
            //If we have one parent, set both class and parent, otherwise set all parents.
            if (empty($parent->getParentClass())) {
                $this->setClassParent($class, $parent);
            } else {
                $this->setClass($class);
            }
            $this->setParent($class, $parent);

            $this->setInterface($class, $parent);

            $this->setProperty($class, 'is_public | is_static');
            //keep going while this class has one parent.
            $class = $parent;
        }
    }

    private function setClassParent($class, $parent)
    {
        $this->class[] = $class->getName();
        $this->class[] = $parent->getName();
    }

    private function setClass($class)
    {
        $this->class[] = $class->getName();
    }

    private function setParent($class, $parent)
    {
        $this->parent[$class->getName()] = $parent->getName();
    }

    private function setInterface($class, $parent)
    {
        $interfaces = $class->getInterfaceNames();
        if (! empty($interfaces)) {
            foreach ($interfaces as $value) {
                $this->interface[$parent->getName()] = $value;
            }
        }
    }

    private function setProperty($class, $mode)
    {
        $split = explode('|', $mode);
        $count = count($split);
        $constant = '';
        if ($count > 1) {
            foreach ($split as $index => $flag) {
                if ($count == $index + 1) {
                    $constant.= 'ReflectionProperty::'.strtoupper($flag);
                } else {
                    $constant.= 'ReflectionProperty::'.strtoupper($flag).'|';
                }
            }
        } else {
            $constant = 'ReflectionProperty::'.strtoupper($mode);
        }
        $properties = $class->getProperties(\ReflectionProperty::IS_PUBLIC);
        if (! empty($properties)) {
            foreach ($properties as $key => $property) {
                if ($count > 1) {
                    $name = substr(trim($split[$key]), 3);
                    $this->property['public'][rtrim($name)] = $property->getName();
                } else {
                    $name = substr(trim($mode), 3);
                    $this->property['public'][$name] = $property->getName();
                }
            }
        }
    }
}
