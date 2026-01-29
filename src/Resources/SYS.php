<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;
use Bildvitta\IssSupernova\Resources\SYS\Cadastral;
use Bildvitta\IssSupernova\Resources\SYS\CessaoDireito;
use Bildvitta\IssSupernova\Resources\SYS\Tipologias;
use stdClass;

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

    public function statusUnidade(string $unitUuid): stdClass
    {
        return $this->issSupernova
            ->get('/sys/status-unidade', ['unit_uuid' => $unitUuid])
            ->throw()
            ->object();
    }

    public function tipologias(): Tipologias
    {
        return new Tipologias($this->issSupernova);
    }

    public function cessaoDireito(): CessaoDireito
    {
        return new CessaoDireito($this->issSupernova);
    }
}
