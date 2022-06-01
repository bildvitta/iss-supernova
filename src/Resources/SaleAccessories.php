<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;

class SaleAccessories
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function create($data)
    {
        return $this->issSupernova->request->post(
            '/sales/accessories',
            $data
        )->throw()->object();
    }

    public function update($data)
    {
        return $this->issSupernova->request->put(
            '/sales/accessories',
            $data
        )->throw()->object();
    }
}
