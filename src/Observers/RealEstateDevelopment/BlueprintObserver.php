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
            'real_estate_developments_characteristics',
            'real_estate_developments_typologies'
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
        if ($blueprint->real_estate_developments_typologies) {
            foreach ($blueprint->real_estate_developments_typologies as $typology) {
                $typology->loadMissing(
                    'units'
                );
            }
        }
        if ($blueprint->real_estate_development) {
            $blueprint->real_estate_development->loadMissing(
                'hub_company'
            );
        }

        $data = $blueprint->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

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
            'real_estate_developments_characteristics',
            'real_estate_developments_typologies',
            'real_estate_developments_blueprint_images',
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
        if ($blueprint->real_estate_developments_typologies) {
            foreach ($blueprint->real_estate_developments_typologies as $typology) {
                $typology->loadMissing(
                    'units'
                );
            }
        }
        if ($blueprint->real_estate_development) {
            $blueprint->real_estate_development->loadMissing(
                'hub_company'
            );
        }

        $data = $blueprint->toArray();

        foreach($data['real_estate_developments_blueprint_images'] as $index => $blueprintImage) {
            if (!empty($blueprintImage['image'])) {
                $data['real_estate_developments_blueprint_images'][$index]['image'] = explode('?', $blueprintImage['image'])[0];
            }
        }

        $data['sync_to'] = 'sys';

        if (!in_array($data['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

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
