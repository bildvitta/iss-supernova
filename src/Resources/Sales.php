<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Http\Client\RequestException;

class Sales
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    /**
     * @throws RequestException
     */
    public function create($data)
    {
        return $this->issSupernova->request->post(
            '/sales',
            $data
        )->throw()->object();
    }

    /**
     * @throws RequestException
     */
    public function update($data)
    {
        return $this->issSupernova->request->put(
            '/sales',
            $data
        )->throw()->object();
    }
}
