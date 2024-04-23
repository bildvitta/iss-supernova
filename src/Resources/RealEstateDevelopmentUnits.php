<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;
use stdClass;

class RealEstateDevelopmentUnits
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function create($data)
    {
        return $this->issSupernova->request->post(
            '/real-estate-developments/units',
            $data
        )->throw()->object();
    }

    public function update($data)
    {
        return $this->issSupernova->request->put(
            '/real-estate-developments/units',
            $data
        )->throw()->object();
    }

    public function sysStatus(string $unitUuid): stdClass
    {
        return $this->issSupernova->request->get(
            sprintf('/real-estate-developments/units/%s/sys/status', $unitUuid)
        )->throw()->object();
    }
}
