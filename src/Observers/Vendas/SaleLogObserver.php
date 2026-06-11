<?php

namespace Bildvitta\IssSupernova\Observers\Vendas;

use Bildvitta\IssSupernova\Exceptions\Vendas\SaleLogException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SaleLogObserver
{
    /**
     * @throws SaleLogException
     */
    public function created($saleLog)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $saleLog->loadMissing(
            'sale',
            'user',
            'hub_company',
        );
        if ($saleLog->sale) {
            $saleLog->sale->loadMissing(
                'unit',
                'real_estate_development',
            );

            if ($saleLog->sale->real_estate_development) {
                $saleLog->sale->real_estate_development->loadMissing(
                    'hub_company'
                );
            }
        }

        $data = $saleLog->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['sale']['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->post('/sale-logs', $data);
        } catch (\Throwable $exception) {
            Log::error('[SaleLogObserver][created] '.$exception->getMessage(), $data);
            throw new SaleLogException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws SaleLogException
     */
    public function updated($saleLog)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $saleLog->refresh();

        $saleLog->loadMissing(
            'sale',
            'user',
            'hub_company',
        );
        if ($saleLog->sale) {
            $saleLog->sale->loadMissing(
                'unit',
                'real_estate_development',
            );

            if ($saleLog->sale->real_estate_development) {
                $saleLog->sale->real_estate_development->loadMissing(
                    'hub_company'
                );
            }
        }

        $data = $saleLog->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['sale']['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->put('/sale-logs', $data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[SaleLogObserver][updated] '.$exception->getMessage(), $data);
            throw new SaleLogException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws SaleLogException
     */
    public function deleted($saleLog)
    {
        $this->updated($saleLog);
    }
}
