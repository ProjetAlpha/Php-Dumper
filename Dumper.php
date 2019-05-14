<?php

namespace Dumper;

use Dumper\FormatHandler;
use Dumper\StyleHandler;

class Dumper
{
    public $depth = 0;
    public $usedObject = [];
    private $html = '';
    public $indent = '';

    public function __construct()
    {
        $this->formatHandler = new FormatHandler();
    }

    public function basicInput($input)
    {
        $indent = str_repeat($this->formatHandler->indent, 1);
        $format = $this->formatHandler->formatInputType($input);
        $this->formatValue(null, $input, $format, $indent);
        $this->displayResult();
    }

    private function displayResult()
    {
        $clearBodyAndDisplay = sprintf("<script type='text/javascript'>
        window.onload=function(){
        document.body.innerHTML = '%s';
      }
      </script>", $this->html);
        echo $clearBodyAndDisplay;
    }

    public function load($object, $clearDump = false)
    {
        if (!empty($this->html)) {
            if ($clearDump === true) {
                $this->html = '';
            }
            $this->depth = 0;
            $this->usedObject = [];
            $this->indent = '';
        }
        if (!(is_array($object) || is_object($object))) {
            return ($this->basicInput($object));
        }
        $this->toHtml($object);
        $this->displayResult();
    }

    public function toHtml($object)
    {
        $depth = 0;
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

                if (!is_array($value) && !is_object($value)) {
                    $format = $this->formatHandler->formatInputType($value);
                    $this->formatValue($content, $value, $format, $indent);
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
            $this->depth-=1;
            $indentBracket = str_repeat($this->formatHandler->indent, $this->depth);
            $style = $this->formatHandler->getBraceStyle('}');
            $this->html.= $indentBracket.$style.'<br />';
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
            $this->html.= '<br />'.$style.'<br />';
        } elseif ('' !== ($format = $this->formatHandler->formatObjectType($object))) {
            $this->html.= '<br />'.$format.'<br />';
        }
    }

    private function setArrayFirstLine($array)
    {
        $style = $this->formatHandler->getArrayStyle($array);
        $this->html.= '<br />'.$style.'<br />';
    }

    private function setUsedObject($object)
    {
        $this->usedObject[] = get_class($object);
    }

    private function bindObjectToHandler($object)
    {
        $this->formatHandler->setObject($object);
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
        if (is_object($value) && in_array(get_class($value), $this->usedObject, true) && get_class($value) !== "stdClass") {
            return true;
        }
        return false;
    }
}
