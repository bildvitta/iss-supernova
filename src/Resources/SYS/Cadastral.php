<?php

namespace Bildvitta\IssSupernova\Resources\SYS;

use Bildvitta\IssSupernova\IssSupernova;

class Cadastral
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function create($data)
    {
        return $this->issSupernova->request->post(
            '/sys/cadastral',
            $data
        )->throw()->object();
    }
}
