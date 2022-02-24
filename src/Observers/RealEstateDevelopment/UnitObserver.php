<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Log;

class UnitObserver
{
    public function created($parameter)
    {
        $parameter->loadMissing(
            'realEstateDevelopment',
            'typology',
            'real_estate_developments_accessories',
            'real_estate_developments_blueprints',
            'mirror_group',
            'mirror_subgroup'
        );
        $data = $parameter->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentUnits()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($parameter)
    {
        $parameter->loadMissing(
            'realEstateDevelopment',
            'typology',
            'real_estate_developments_accessories',
            'real_estate_developments_blueprints',
            'mirror_group',
            'mirror_subgroup'
        );
        $data = $parameter->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentUnits()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($parameter)
    {
        //
    }
}
