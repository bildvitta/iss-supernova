<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;

class Juridico
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function documents()
    {
        return new \Bildvitta\IssSupernova\Resources\Juridico\Documents($this->issSupernova);
    }

    public function signerDocuments()
    {
        return new \Bildvitta\IssSupernova\Resources\Juridico\SignerDocuments($this->issSupernova);
    }

    public function historics()
    {
        return new \Bildvitta\IssSupernova\Resources\Juridico\Historics($this->issSupernova);
    }
}
