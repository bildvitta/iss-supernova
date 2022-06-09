<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class AccessoryObserver
{
    public function created($accessory)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $accessory->loadMissing(
            'real_estate_development',
            'accessory',
            'typologies'
        );
        if ($accessory->real_estate_development) {
            $accessory->real_estate_development->loadMissing(
                'blueprints'
            );
        }
        if ($accessory->typologies) {
            foreach ($accessory->typologies as $acessoryTypology) {
                $acessoryTypology->loadMissing(
                    'blueprints'
                );
            }
        }

        $data = $accessory->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentAccessories()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($accessory)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $accessory->loadMissing(
            'real_estate_development',
            'accessory',
            'typologies'
        );
        if ($accessory->real_estate_development) {
            $accessory->real_estate_development->loadMissing(
                'blueprints'
            );
        }
        if ($accessory->typologies) {
            foreach ($accessory->typologies as $acessoryTypology) {
                $acessoryTypology->loadMissing(
                    'blueprints'
                );
            }
        }

        $data = $accessory->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentAccessories()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($accessory)
    {
        $this->updated($accessory);
    }
}
