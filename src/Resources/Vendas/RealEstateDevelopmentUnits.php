<?php

namespace Bildvitta\IssSupernova\Resources\Vendas;

use Bildvitta\IssSupernova\IssSupernova;

class RealEstateDevelopmentUnits
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function update($data)
    {
        return $this->issSupernova->request->put(
            '/sales/units',
            $data
        )->throw()->object();
    }
}
