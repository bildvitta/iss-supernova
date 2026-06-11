<?php

namespace Bildvitta\IssSupernova\Observers\Vendas;

use Bildvitta\IssSupernova\Exceptions\Vendas\UnitException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class UnitObserver
{
    /**
     * @throws UnitException
     */
    public function updated($unit)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $unit->refresh();

        $unit->loadMissing(
            'sale_step',
            'product',
            'block_user',
            'block_reason',
        );
        if ($unit->product) {
            $unit->product->loadMissing(
                'hub_company'
            );
        }

        $data = $unit->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['product']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->vendas()->realEstateDevelopmentUnits()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[UnitObserver][updated] '.$exception->getMessage(), $data);
            throw new UnitException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    public function deleted($unit)
    {
        // $this->updated($unit);
    }
}
