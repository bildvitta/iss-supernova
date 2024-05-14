<?php

namespace Bildvitta\IssSupernova\Resources\SYS;

use Bildvitta\IssSupernova\IssSupernova;
use stdClass;

class Tipologias
{
    private IssSupernova $issSupernova;

    public function __construct(IssSupernova $issSupernova)
    {
        $this->issSupernova = $issSupernova;
    }

    public function options(
        string $realEstateDevelopmentUuid, 
        string $search = '', 
        array $ids = [], 
        int $limit = 12, 
        int $offset = 0
    ): stdClass {
        $query = [
            'real_estate_development_uuid' => $realEstateDevelopmentUuid,
            'search' => $search,
            'ids' => $ids,
            'limit' => $limit,
            'offset' => $offset
        ];

        return $this->issSupernova
            ->request
            ->get('/sys/repasse/tipologias', $query)
            ->throw()
            ->object();
    }

    public function show(int $sysTypologyId): stdClass 
    {
        return $this->issSupernova
            ->request
            ->get('/sys/repasse/tipologias/' . $sysTypologyId)
            ->throw()
            ->object();
    }
}
