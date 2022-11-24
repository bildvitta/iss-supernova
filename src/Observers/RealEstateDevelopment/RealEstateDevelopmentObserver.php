<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RealEstateDevelopmentObserver
{
    public function created($realEstateDeveloptment)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $realEstateDeveloptment->loadMissing(
            'hub_company',
            'real_estate_development_type',
            'hub_company_real_estate_agency',
        );
        $data = $realEstateDeveloptment->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopments()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($realEstateDeveloptment)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $realEstateDeveloptment->loadMissing(
            'hub_company',
            'real_estate_development_type',
            'hub_company_real_estate_agency',
        );
        $data = $realEstateDeveloptment->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopments()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($realEstateDeveloptment)
    {
        $this->updated($realEstateDeveloptment);
    }
}
