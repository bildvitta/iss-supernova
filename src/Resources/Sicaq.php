<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Http\Client\Response;

class Sicaq
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function periodicities(string $realEstateDevelopmentUuid, string $typologyUuid, string $document): Response
    {
        $data = [
            'real_estate_development' => $realEstateDevelopmentUuid,
            'typology'                => $typologyUuid,
            'document'                => $document,
        ];

        return $this->issSupernova->get(
            '/sicaq/periodicities',
            $data
        );
    }
}
