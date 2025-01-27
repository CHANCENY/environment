<?php

namespace Simp\Environment\Definition;

class DefinitionException extends \Exception
{

    /**
     * @param string $string
     */
    public function __construct(string $string)
    {
        parent::__construct($string);
    }
}