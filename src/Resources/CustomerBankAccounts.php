<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;

class CustomerBankAccounts
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function create($data)
    {
        return $this->issSupernova->post(
            '/customers/bank-accounts',
            $data
        )->throw()->object();
    }

    public function update($data)
    {
        return $this->issSupernova->put(
            '/customers/bank-accounts',
            $data
        )->throw()->object();
    }
}
