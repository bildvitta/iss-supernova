<?php

namespace Bildvitta\IssSupernova\Resources;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Http\Client\Response;

class Cicaq
{
    /**
     * @var IssSupernova
     */
    private IssSupernova $issSupernova;

    /**
     * @param IssSupernova $issSupernova
     */
    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    /**
     * @param string $realEstateDevelopmentUuid
     * @param string $typologyUuid
     * @param string $document
     * @return Response
     */
    public function periodicities(string $realEstateDevelopmentUuid, string $typologyUuid, string $document): Response
    {
        $data = [
            'real_estate_development' => $realEstateDevelopmentUuid,
            'typology' => $typologyUuid,
            'document' => $document,
        ];

        return $this->issSupernova->request->get(
            '/cicaq/periodicities',
            $data
        );
    }
}
