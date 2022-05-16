<?php

namespace Bildvitta\IssSupernova\Observers;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SaleObserver
{
    public function created($sale)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $sale->loadMissing(
            'customer',
            'bonds',
            'product',
            'insurance',
            'seller',
            'unit',
            'manager',
            'supervisor',
            'blueprint',
            'proposal_model',
            'periodicities',
            'facts',
            'real_estate_agency'
        );
        $data = $sale->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->sales()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($sale)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $sale->loadMissing(
            'customer',
            'bonds',
            'product',
            'insurance',
            'seller',
            'unit',
            'manager',
            'supervisor',
            'blueprint',
            'proposal_model',
            'periodicities',
            'facts',
            'real_estate_agency'
        );
        $data = $sale->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->sales()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($sale)
    {
        //
    }
}