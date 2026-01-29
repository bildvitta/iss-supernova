<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;

class CustomerDependents
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function create($data)
    {
        return $this->issSupernova->post(
            '/customers/dependents',
            $data
        )->throw()->object();
    }

    public function update($data)
    {
        return $this->issSupernova->put(
            '/customers/dependents',
            $data
        )->throw()->object();
    }
}
