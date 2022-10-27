<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;
use Bildvitta\IssSupernova\Resources\SYS\Cadastral;

class SYS
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function cadastral()
    {
        return new Cadastral($this->issSupernova);
    }
}
