<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TypologyObserver
{
    public function created($typology)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $typology->loadMissing('realEstateDevelopment');
        $typology->realEstateDevelopment->last_parameter = $typology->realEstateDevelopment->last_parameter();
        $typology->realEstateDevelopment->hub_company = $typology->realEstateDevelopment->hub_company;
        $data = $typology->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentTypologies()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($typology)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $typology->loadMissing('realEstateDevelopment');
        $typology->realEstateDevelopment->last_parameter = $typology->realEstateDevelopment->last_parameter();
        $typology->realEstateDevelopment->hub_company = $typology->realEstateDevelopment->hub_company;
        $data = $typology->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentTypologies()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($typology)
    {
        //
    }
}
