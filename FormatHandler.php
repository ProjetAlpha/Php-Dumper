<?php

namespace Dumper;

use Dumper\Format;
use Dumper\ObjectHandler;
use Dumper\StyleHandler;

class FormatHandler
{
    const SPACE = ' ';
    const BRACE = '{';
    const BRACKET_STYLE = '<span style="color:white">';
    const END_SPAN = "</span>";
    const BRACKET_START = '[';
    const BRACKET_END = ']';
    const SLASH = '/';

    public function __construct()
    {
        $this->setStyle();

        $this->format = new Format();
        $this->objectTypeList = $this->format->get('objectTypeList');
    }



    private function setStyle()
    {
        $this->style = new StyleHandler();
        $this->style->getTag() ? $this->tag = 'span' : $this->tag = 'pre';
        $this->font = $this->getFont();
        $this->indent = $this->style->getIndent();
        $this->defaultTypeColor = $this->style->getBasicColor('type');
        $this->arrowStyle = $this->style->getBasicColor('arrow');
        $this->bracketStyle = $this->style->getBasicColor('bracket');
        $this->bracketColor = $this->style->getBasicColor('type');
        if (!empty($backgroundColor = $this->style->getBasicColor('background'))) {
            $styleBlock = sprintf('
              <style type="text/css">
                body {
                  background-color:%s!important;
                }
              </style>
            ', $backgroundColor);
            echo $styleBlock;
        }
    }

    private function getFont()
    {
        $font = $this->style->getFont();

        $setting = implode(' ', $font);
        $wordPos = str_word_count($setting, 2);
        //TODO, marche avec des chiffres mais pas avec des lettres.
        $firstWordPos = key($wordPos);
        $pos = $firstWordPos + strlen($wordPos[$firstWordPos]);

        $setting[$pos] = '/';
        return $setting;
    }

    public function setObject($object)
    {
        $this->object = new ObjectHandler($object);
    }

    public function formatInputType($value)
    {
        $type = gettype($value);
        $format = $this->format->get('type');
        if (array_key_exists($type, $format)) {
            if ($type === 'string') {
                $format[$type]['length'] = strlen($value);
            } elseif ($type === 'double' || 'boolean' || 'integer') {
                if ($type === 'boolean') {
                    $value ? $value = 'true' : $value = 'false';
                }
                $format[$type]['length'] = $value;
            }
            $result = $format[$type];
        }

        return $result ?? '';
    }

    public function formatPropertyName($name)
    {
        foreach ($this->object->class as $class) {
            if ($this->object->isPrivate($name, $class)) {
                $format = $this->getPrivateName($name, $class);
                return $format;
            } elseif ($this->object->isProtected($name)) {
                $format = $this->getProtectedName($name);
                return $format;
            } else {
                foreach ($this->object->property['public'] as $props) {
                    if (strpos($name, $props) === 0) {
                        $format = $this->getPublicName($name);
                        return $format;
                    }
                }
            }
        }
        return false;
    }

    public function formatObjectType($object)
    {
        foreach ($this->objectTypeList as $key => $value) {
            $method = 'is'.ucfirst($key);
            if (method_exists($this->objectHandler, $method)) {
                if ($this->objectHandler->{$method}($object) !== false) {
                    $typeStyle = $this->getObjectStyle($key, $value);
                    return $typeStyle;
                }
            }
        }

        return '';
    }

    public function getObjectStyle($name, $type, $option = null)
    {
        if ($type === 'object') {
            $suffix = $option ?? '';
            $objectFormat = $type.'('.$name.$suffix.')'.self::SPACE.self::BRACE;
        } else {
            $objectFormat = $type.self::SPACE.self::BRACE;
        }
        $style = $this->getStyle($this->defaultTypeColor, $this->font, $objectFormat, $this->tag);

        return $style;
    }

    public function getArrayStyle($array)
    {
        $arrayFormat = 'array('.count($array).') {';
        $style = $this->getStyle($this->defaultTypeColor, $this->font, $arrayFormat, $this->tag);
        return $style;
    }

    public function getBraceStyle($brace)
    {
        $style = $this->getStyle($this->defaultTypeColor, $this->font, $brace, $this->tag);
        return $style;
    }

    private function getBracketStyle($bracket)
    {
        $style = $this->getStyle($this->bracketStyle, $this->font, $bracket, $this->tag);
        return $style;
    }

    private function getArrowStyle($arrow)
    {
        $style = $this->getStyle($this->arrowStyle, $this->font, $arrow, $this->tag);
        return $style;
    }

    public function getKeyValueStyle($key, $value, $option = null)
    {
        $keyStyle = $this->getKeyStyle($key);
        if ($option === 'array') {
            $valueStyle = $this->getArrayStyle($value);
        } elseif ($option === 'object') {
            $valueStyle = $this->formatObjectType($value);
        } elseif ($option === 'recursive') {
            $valueStyle = $this->getValueStyle('**RECURSIVE**');
        } else {
            $valueStyle = $this->getValueStyle($value);
        }

        return $keyStyle.self::SPACE.$this->getArrowStyle('=>').self::SPACE.$valueStyle;
    }

    public function getKeyStyle($key)
    {
        $style = $this->getStyle($this->style->getBasicColor('key'), $this->font, $key, $this->tag);
        // <span style="color:white">
        return $this->getBracketStyle('[').$style.$this->getBracketStyle(']');
        //return self::BRACKET_START.$style.self::BRACKET_END;
    }

    public function getValueStyle($value)
    {
        $style = $this->getStyle($this->style->getBasicColor('value'), $this->font, $value, $this->tag);
        return $style;
    }

    public function getPrivateName($name, $class)
    {
        $prefix = $this->format->getPropertyPrefix('private');
        $color = $this->style->getColorProperty('private');

        $style = $this->getStyle($color, $this->font, $prefix.'private', $this->tag);
        $style .= self::SPACE.ltrim(strtr($name, [$class => '']));

        return $style;
    }

    public function getProtectedName($name)
    {
        $prefix = $this->format->getPropertyPrefix('protected');
        $color = $this->style->getColorProperty('protected');

        $style = $this->getStyle($color, $this->font, $prefix.'protected', $this->tag);
        $style .= self::SPACE.ltrim(substr($name, 2));

        return $style;
    }

    public function getPublicName($name)
    {
        $prefix = $this->format->getPropertyPrefix('public');
        $color = $this->style->getColorProperty('public');

        $style = $this->getStyle($color, $this->font, $prefix.'public', $this->tag);
        $style .= self::SPACE.$name;

        return $style;
    }

    public function getStyle($color, $font, $name, $tag = null)
    {
        if (null === $tag) {
        }

        list($startTag, $endTag) = $this->setTag($tag, $color, $font);
        return $startTag.$name.$endTag;
    }

    private function setTag($name, $color = null, $font = null)
    {
        $styleColor = $color ?? 'initial';
        $styleFont = $font ?? 'initial';

        if ($name === 'span') {
            $formatTag = array('0' => '<span style="color:'.$styleColor.';font:'.$styleFont.';">', '1' => '</span>');
        } else {
            $formatTag = array('0' => '<pre style="color:'.$styleColor.';font:'.$styleFont.';">', '1' => '</pre>');
        }

        return $formatTag;
    }
}
