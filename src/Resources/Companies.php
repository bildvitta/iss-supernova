<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;

class Companies
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function create($data)
    {
        return $this->issSupernova->request->post(
            '/companies',
            $data
        )->throw()->object();
    }

    public function update($data)
    {
        return $this->issSupernova->request->put(
            '/companies',
            $data
        )->throw()->object();
    }
}
