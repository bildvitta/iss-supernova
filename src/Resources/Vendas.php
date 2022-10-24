<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;

class Vendas
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function realEstateDevelopmentUnits()
    {
        return new \Bildvitta\IssSupernova\Resources\Vendas\RealEstateDevelopmentUnits($this->issSupernova);
    }
}
