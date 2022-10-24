<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment\Vendas;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class UnitObserver
{
    public function updated($unit)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $unit->loadMissing(
            'sale_step'
        );

        $data = $unit->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->vendas()->realEstateDevelopmentUnits()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($unit)
    {
        //$this->updated($unit);
    }
}
