<?php

namespace Simp\Environment;

use Random\RandomException;
use Simp\Environment\Definition\DefinitionException;
use Simp\Environment\Parser\Parser;
use Simp\Environment\Writer\Writer;

class Environment
{
    /**
     * Get Writer.
     * @return Writer
     */
    public static function getEditable(): Writer
    {
        return new Writer();
    }

    /**
     * Get Parser.
     * @return Parser
     */
    public static function getConfig(): Parser
    {
        return new Parser();
    }

    /**
     * @throws RandomException
     * @throws DefinitionException
     */
    public static function create(string $key, $value): bool
    {
        $writer = self::getEditable();
        $key_hash = $writer->createStorage($key);
        return $writer->save($key_hash, $value);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public static function load(string $key): mixed
    {
        $reader = self::getConfig();
        return $reader->parse($key);
    }
}