<?php

namespace Simp\Environment\Parser;

use Simp\Environment\Definition\Definition;
use Simp\Environment\Definition\Helper;
use Symfony\Component\Yaml\Yaml;

class Parser extends Definition
{
    use Helper;
    private mixed $writer_entry;

    public function __construct(?string $storage_path = null)
    {
        parent::__construct($storage_path);
        $store_path = $this->getStore();
        if(!is_dir($store_path)) {
            @mkdir($store_path, 0755);
            @mkdir($store_path.DIRECTORY_SEPARATOR.'backup', 0755);
            @touch($store_path.DIRECTORY_SEPARATOR.'register.yml');
            @touch($store_path.DIRECTORY_SEPARATOR.'collections.yml');
        }
        $this->writer_entry = Yaml::parseFile($store_path.DIRECTORY_SEPARATOR.'register.yml');
    }

    public function parse(string $key)
    {
        $entry = $this->writer_entry[$key] ?? null;
        if(empty($entry)) {
            return null;
        }
        $unprocessed_data = null;
        if(!$this->getSeparationMode()) {
            $content = Yaml::parseFile($this->getStore().DIRECTORY_SEPARATOR.'collections.yml');
            $unprocessed_data = $content[$key] ?? null;
        }
        else {
            $file = $this->getStore().DIRECTORY_SEPARATOR.$key.'.yml';
            if(file_exists($file)) {
                $content = Yaml::parseFile($file);
                $unprocessed_data = $content[$key] ?? null;
            }
        }
        if(empty($unprocessed_data)) {
            return null;
        }

        $processed_data = $unprocessed_data;
        foreach ($entry['parse_Steps'] as $key=>$value) {
            if($value === true) {
                $processed_data = $this->$key($processed_data);
            }
        }
        return $processed_data;
    }

    public static function get(string $key): mixed
    {
        return (new static())->parse($key);
    }
}