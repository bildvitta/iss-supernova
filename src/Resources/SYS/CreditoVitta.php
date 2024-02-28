<?php

namespace Bildvitta\IssSupernova\Resources\SYS;

use Bildvitta\IssSupernova\IssSupernova;

class CreditoVitta
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function list($query)
    {
        return $this->issSupernova->request->get(
            '/sys/credito-vitta',
            $query
        )->throw()->object();
    }
}
