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

        $blueprint->refresh();
        $blueprint->loadMissing(
            'real_estate_development',
            'real_estate_developments_characteristics'
        );
        if ($blueprint->real_estate_developments_characteristics) {
            foreach ($blueprint->real_estate_developments_characteristics as $realEstateDevelopmentAccessory) {
                $realEstateDevelopmentAccessory->loadMissing(
                    'accessory'
                );
                if ($realEstateDevelopmentAccessory->accessory) {
                    $realEstateDevelopmentAccessory->accessory->loadMissing('accessory_categorization');
                }
            }
        }

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

        $blueprint->refresh();
        $blueprint->loadMissing(
            'real_estate_development',
            'real_estate_developments_characteristics'
        );
        if ($blueprint->real_estate_developments_characteristics) {
            foreach ($blueprint->real_estate_developments_characteristics as $realEstateDevelopmentAccessory) {
                $realEstateDevelopmentAccessory->loadMissing(
                    'accessory'
                );
                if ($realEstateDevelopmentAccessory->accessory) {
                    $realEstateDevelopmentAccessory->accessory->loadMissing('accessory_categorization');
                }
            }
        }

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
        $this->updated($blueprint);
    }
}
