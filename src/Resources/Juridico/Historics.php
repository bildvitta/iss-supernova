<?php

namespace Bildvitta\IssSupernova\Resources\Juridico;

use Bildvitta\IssSupernova\IssSupernova;

class Historics
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function create($data)
    {
        return $this->issSupernova->request->post(
            '/juridico/historics',
            $data
        )->throw()->object();
    }

    public function update($data)
    {
        return $this->issSupernova->request->put(
            '/juridico/historics',
            $data
        )->throw()->object();
    }
}
