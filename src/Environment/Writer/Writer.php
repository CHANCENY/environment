<?php

namespace Simp\Environment\Writer;

use Random\RandomException;
use Simp\Environment\Definition\Definition;
use Simp\Environment\Definition\DefinitionException;
use Simp\Environment\Definition\Helper;
use Symfony\Component\Yaml\Yaml;

class Writer extends Definition
{
    use Helper;
    private array|null $writer_entry;
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

    /**
     * @throws RandomException
     */
    public function createStorage(string $key): string
    {
        $in_entry = $this->writer_entry[$key] ?? [];
        if(empty($in_entry)) {
            $this->writer_entry[$key] = [
                'save_steps' => [
                    'serialized' => null,
                    'encode' => null,
                    'base64' => null,
                    'hash' => false
                ],
                'parse_Steps' => [
                    'dehash' => false,
                    'base64decode' => null,
                    'decode' => null,
                    'unserialized' => null,
                ]
            ];
            file_put_contents($this->getStore().DIRECTORY_SEPARATOR.'register.yml',
            Yaml::dump($this->writer_entry));
        }
        return $key;
    }

    /**
     * @throws DefinitionException
     */
    public function save(string $storage_key, $data): bool
    {
        $data_type = gettype($data);
        $entry = $this->writer_entry[$storage_key] ?? [];
        if(empty($entry)) {
            throw new DefinitionException("Writer storage entry not found");
        }

        if($data_type === 'array' || $data_type === 'object') {

            if($this->getIsHash()){

                $entry['save_steps']['serialized'] = true;
                $entry['save_steps']['encode'] = true;
                $entry['save_steps']['base64'] = true;
                $entry['save_steps']['hash'] = true;

                $entry['parse_Steps']['dehash'] = true;
                $entry['parse_Steps']['base64decode'] = true;
                $entry['parse_Steps']['decode'] = true;
                $entry['parse_Steps']['unserialized'] = true;
            }
        }


        $processed_data = $data;
        foreach ($entry['save_steps'] as $key => $value) {
            if ($value === true) {
                $processed_data = $this->$key($processed_data);
            }
        }

        $this->writer_entry[$storage_key] = $entry;
        file_put_contents($this->getStore().DIRECTORY_SEPARATOR.'register.yml',Yaml::dump($this->writer_entry));

        if($this->getSeparationMode()) {
            $file_path = $this->getStore().DIRECTORY_SEPARATOR.$storage_key.'.yml';
            return !empty(file_put_contents($file_path,Yaml::dump($processed_data)));
        }
        $collections = Yaml::parseFile($this->getStore().DIRECTORY_SEPARATOR.'collections.yml');
        if(empty($collections)) {
            $collections = [
                $storage_key => $processed_data
            ];
        }
        else {
            $collections[$storage_key] = $processed_data;
        }
        return !empty(file_put_contents($this->getStore().DIRECTORY_SEPARATOR.'collections.yml',
        Yaml::dump($collections)));
    }

    /**
     * @throws RandomException
     * @throws DefinitionException
     */
    public static function create(string $key, $data = null): Writer|bool
    {
        $writer = new Writer();
        $hash_key = $writer->createStorage($key);
        if($data !== null) {
            return $writer->save($hash_key, $data);
        }
        return $writer;
    }

}