<?php

//TODO : générer une config cache, si la date de modification de cache.php < date de modification de config.php, require Config.php. //TODO : donner la posibilité d'ajouter via une fonction ou method une configuration personnalisée. Prendre le fichier de config et ajouter les propriétés css personnaliées. Si déja ajouter, ne pas recharger la modification => fichier générer avec css peronnalisé. Régénere le fichier que si nouvel instance. Meme instance = même fichier généré. Nouvel instance avec call function pour css personnalisé => overwrite le fichier généré existant ou créer le fichier.
//Ex. ['custom']['type']['white-space'] = 'preline';
//function('type', ['white-space' => 'preline']);
//function('value', ['white-space' => 'preline']);
//function('key', ['white-space' => 'preline']);
//function('public', ['white-space' => 'preline']);

namespace Dumper;

class StyleHandler
{
    const DUMPER_PATH = '/Dumper'; //vendor/src/Dumper

    public function __construct()
    {
        /*if (! file_exists($this->getCache()) || filemtime($this->getCache()) < filemtime($this->getConfigFile())) {
            $this->config = $this->setCache();
        }

        if(! isset($this->config)) {
            $this->config = require ($this->getCache());
        }*/
        $this->config = $this->getConfigFile();

        $this->initialize();
    }

    private function initialize()
    {
        $this->color = $this->config['color'];
        $this->font = $this->config['font'];
        $this->tag = $this->config['tag'];
        $this->indent = $this->config['indent'];
    }

    private function setCache()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        if (! is_writable($root.self::DUMPER_PATH)) {
            throw new RuntimeException('Could not write to folder');
        }

        $config = require($root.self::DUMPER_PATH);
        file_put_contents($root.self::DUMPER_PATH.'/dumper_cache.php', $config);
        return $config;
    }

    private function getCache()
    {
        $root = $_SERVER['DOCUMENT_ROOT'];
        return  $root.self::DUMPER_PATH.'/dumper_cache.php';
    }

    private function getConfigFile()
    {
        return require_once('Config.php');
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function getIndent()
    {
        return $this->indent;
    }

    public function getFontByName($name)
    {
        if ($this->has($name, $this->font)) {
            return $this->font[$name];
        }

        throw new InvalidArgumentException(sprintf('Undefined font setting for %s'), $name);
    }

    public function getFont()
    {
        return $this->font;
    }

    public function getColorProperty($name)
    {
        if ($this->has($name, $this->color['property'])) {
            return $this->color['property'][$name];
        }

        throw new InvalidArgumentException(sprintf('Undefined property color for %s'), $name);
    }

    public function getBasicColor($name)
    {
        if ($this->has($name, $this->color)) {
            return $this->color[$name];
        }

        throw new InvalidArgumentException(sprintf('Undefined color setting for %s'), $name);
    }

    private function has($name, $object)
    {
        return array_key_exists($name, $object);
    }
}
