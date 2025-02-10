<?php

namespace Bildvitta\IssSupernova\Resources\SYS;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Http\Client\Response;

class CessaoDireito
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function gerar(array $payload): Response
    {
        return $this->issSupernova->request->post(
            '/sys/cessao-direito/gerar',
            $payload
        );
    }
}
