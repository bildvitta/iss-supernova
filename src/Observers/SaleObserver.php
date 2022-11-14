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
            'hub_company_real_estate_agency',
            'justified_user',
            'accessories'
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
                'bonds',
                'bonds_from',
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

        if ($sale->accessories) {
            foreach ($sale->accessories as $saleAccessory) {
                $saleAccessory->touch();
            }
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
            'hub_company_real_estate_agency',
            'justified_user',
            'accessories'
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
                'bonds',
                'bonds_from',
            );
        }
        $data = $sale->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->sales()->update($data);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }

        if ($sale->accessories) {
            foreach ($sale->accessories as $saleAccessory) {
                $saleAccessory->touch();
            }
        }

        if ($sale->periodicities) {
            foreach ($sale->periodicities as $periodicity) {
                $periodicity->touch();
            }
        }

        return $response;
    }

    public function deleted($sale)
    {
        $this->updated($sale);
    }
}
