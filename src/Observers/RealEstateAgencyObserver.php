<?php

namespace Bildvitta\IssSupernova\Observers;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RealEstateAgencyObserver
{
    public function created($realEstateAgency)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $data = $realEstateAgency->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateAgencies()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($realEstateAgency)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $data = $realEstateAgency->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateAgencies()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($realEstateAgency)
    {
        $this->updated($realEstateAgency);
    }
}
