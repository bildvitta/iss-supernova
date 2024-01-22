<?php

namespace Bildvitta\IssSupernova\Resources\Juridico;

use Bildvitta\IssSupernova\IssSupernova;

class SignerDocuments
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function create($data)
    {
        return $this->issSupernova->request->post(
            '/juridico/signer-documents',
            $data
        )->throw()->object();
    }

    public function update($data)
    {
        return $this->issSupernova->request->put(
            '/juridico/signer-documents',
            $data
        )->throw()->object();
    }
}
