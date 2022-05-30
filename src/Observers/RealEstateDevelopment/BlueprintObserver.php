<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class BlueprintObserver
{
    public function created($blueprint)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $blueprint->loadMissing('real_estate_development');

        $data = $blueprint->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentBlueprints()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($blueprint)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $blueprint->loadMissing('real_estate_development');

        $data = $blueprint->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentBlueprints()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($blueprint)
    {
        //
    }
}
