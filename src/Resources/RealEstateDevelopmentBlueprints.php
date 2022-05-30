<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;

class RealEstateDevelopmentBlueprints
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function create($data)
    {
        return $this->issSupernova->request->post(
            '/real-estate-developments/blueprints',
            $data
        )->throw()->object();
    }

    public function update($data)
    {
        return $this->issSupernova->request->put(
            '/real-estate-developments/blueprints',
            $data
        )->throw()->object();
    }
}
