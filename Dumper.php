<?php

namespace Dumper;

use Dumper\FormatHandler;
use Dumper\StyleHandler;

class Dumper
{
    public $depth = 0;
    public $usedObject = [];
    public $html = '';
    public $indent = '';

    public function __construct()
    {
        $this->formatHandler = new FormatHandler();
    }

    public function toHtml($object)
    {
        if (is_array($object) || is_object($object)) {
            if (is_object($object)) {
                $this->setUsedObject($object);
                $this->bindObjectToHandler($object);
            }

            if ($this->depth === 0) {
                $this->setFirstLine(gettype($object), $object);
            }
            $this->depth++;

            $indent = str_repeat($this->formatHandler->indent, $this->depth);
            $result = [];
            $index = 0;
            foreach ((array)$object as $key => $value) {
                $formatKey = $this->getPropertyStyle($key);
                $content = $formatKey ?? $key;

                if (! is_array($value) && !is_object($value)) {
                    $format = $this->formatHandler->formatInputType($value);
                    $this->formatValue($content, $value, $format, $indent);

                    $lastIndex = $index + 1;
                    if ($lastIndex === count($object)) {
                        $this->displayEndBrackets($object);
                    }
                } else {
                    if ($this->skipRecursion($value)) {
                        $style = $this->formatHandler->getKeyValueStyle($content, $value, 'recursive');
                        $this->html.= $indent.$style.'<br />';
                        continue;
                    }

                    if (is_object($value)) {
                        $style = $this->formatHandler->getKeyValueStyle($content, $value, 'object');
                        $this->html.= $indent.$style.'<br />';
                    } else {
                        $style = $this->formatHandler->getKeyValueStyle($content, $value, 'array');
                        $this->html.= $indent.$style.'<br />';
                    }
                }
                $result[$key] = $this->toHtml($value);
                $index++;
            }
            return $result;
        }
        return $object;
    }

    private function setFirstLine($type, $data)
    {
        if ($type === 'array') {
            $this->setArrayFirstLine($data);
        } elseif ($type === 'object') {
            $this->setObjectFirstLine($data);
        }
    }

    private function setObjectFirstLine($object)
    {
        $class = $this->formatHandler->object->class[0] ?? null;
        $parent = $this->formatHandler->object->parent[$class] ?? '0';
        $interface = $this->formatHandler->object->interface[$parent] ?? '0';

        $keyWords = $this->formatHandler->format->get('keyWords');

        $extend = $keyWords['parent'].' => ';
        $extend .= $parent;

        $implement = $keyWords['interface'].' => ';
        $implement .= $interface;

        if (isset($class)) {
            $suffix = '@'.$class.'['.$extend.', '.$implement.']';
            $style = $this->formatHandler->getObjectStyle('class', 'object', $suffix);
            $this->html.= '<br />'.'&emsp;'.$style.'<br />';
        } elseif ('' !== ($format = $this->formatHandler->formatObjectType($object))) {
            $this->html.= '<br />'.'&emsp;'.$format.'<br />';
        }
    }

    private function setArrayFirstLine($array)
    {
        $style = $this->formatHandler->getArrayStyle($array);
        $this->html.= '<br />'.$this->formatHandler->indent.$style.'<br />';
    }

    private function setUsedObject($object)
    {
        $this->usedObject[] = get_class($object);
    }

    private function bindObjectToHandler($object)
    {
        if (! isset($this->formatHanlder->object)) {
            $this->formatHandler->setObject($object);
        }

        return;
    }

    private function formatValue($key, $value, $format, $indent)
    {
        if (is_array($format)) {
            list($right, $left) = $format;
            if (($type = gettype($value)) === 'string') {
                $length = $format['length'];
                $name = $type."($length) ".$right.$value.$left;
                $style = $this->formatHandler->getKeyValueStyle($key, $name);
                $this->html.= $indent.$style.'<br />';
            } elseif (($type = gettype($value)) === 'double' || 'boolean' || 'integer') {
                $length = $format['length'];
                $name = $type.'('.$length.')';
                $style = $this->formatHandler->getKeyValueStyle($key, $name);
                $this->html.= $indent.$style.'<br />';
            }
        } else {
            $name = gettype($value);
            $style = $this->formatHandler->getKeyValueStyle($key, $name);
            $this->html.= $indent.$style.'<br />';
        }
    }

    private function getPropertyStyle($key)
    {
        if (! empty($this->formatHandler->object)
            && intval($this->depth) === 1
            && ($property = $this->formatHandler->formatPropertyName($key)) !== false
        ) {
            $formatKey = $property;
        }

        return $formatKey ?? null;
    }

    private function skipRecursion($value)
    {
        if (is_object($value) && in_array(get_class($value), $this->usedObject, true)) {
            return true;
        }
        return false;
    }

    private function displayEndBrackets($object)
    {
        isset($this->formatHandler->object) ? $i = $this->depth - 1 : $i = $this->depth;
        for ($i; $i > 0; $i--) {
            $indentBracket = str_repeat($this->formatHandler->indent, $i);
            $style = $this->formatHandler->getBraceStyle('}');
            $this->html.= $indentBracket.$style.'<br />';
        }
        //stop parent : end depth.
        $this->depth = 1;
    }
}
