<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class UnitObserver
{
    public function created($unit)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $unit->loadMissing(
            'realEstateDevelopment',
            'typology',
            'real_estate_developments_blueprints',
            'mirror_group',
            'mirror_subgroup'
        );
        if ($unit->typology) {
            $unit->typology->loadMissing(
                'blueprints'
            );
        }
        if ($unit->realEstateDevelopment) {
            $unit->realEstateDevelopment->loadMissing(
                'hub_company'
            );
        }
        $data = $unit->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentUnits()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($unit)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $unit->loadMissing(
            'realEstateDevelopment',
            'typology',
            'real_estate_developments_blueprints',
            'mirror_group',
            'mirror_subgroup'
        );
        if ($unit->typology) {
            $unit->typology->loadMissing(
                'blueprints'
            );
        }
        if ($unit->realEstateDevelopment) {
            $unit->realEstateDevelopment->loadMissing(
                'hub_company'
            );
        }
        $data = $unit->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentUnits()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($unit)
    {
        $this->updated($unit);
    }
}
