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
            'customers',
            'product',
            'insurance',
            'seller',
            'unit',
            'manager',
            'supervisor',
            'blueprint',
            'proposal_model',
            'buying_options',
            'periodicities',
            'facts',
            'real_estate_agency',
            'justified_user'
        );
        if ($sale->product) {
            $sale->product->loadMissing(
                'hub_company'
            );
        }
        if ($sale->unit) {
            $sale->unit->loadMissing(
                'typology',
                'sale_step',
            );
        }
        if ($sale->customer) {
            $sale->customer->loadMissing(
                'related_customer',
                'related_customers'
            );
        }
        $data = $sale->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->sales()->create($data);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }

        if ($sale->periodicities) {
            foreach ($sale->periodicities as $periodicity) {
                $periodicity->touch();
            }
        }

        return $response;
    }

    public function updated($sale)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $sale->loadMissing(
            'customer',
            'customers',
            'product',
            'insurance',
            'seller',
            'unit',
            'manager',
            'supervisor',
            'blueprint',
            'proposal_model',
            'buying_options',
            'periodicities',
            'facts',
            'real_estate_agency',
            'justified_user'
        );
        if ($sale->product) {
            $sale->product->loadMissing(
                'hub_company'
            );
        }
        if ($sale->unit) {
            $sale->unit->loadMissing(
                'typology',
                'sale_step',
            );
        }
        if ($sale->customer) {
            $sale->customer->loadMissing(
                'related_customer',
                'related_customers'
            );
        }
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
        $this->updated($sale);
    }
}
