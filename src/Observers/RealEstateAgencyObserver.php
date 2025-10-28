<?php

namespace Bildvitta\IssSupernova\Observers;

use Bildvitta\IssSupernova\Exceptions\RealEstateAgencyException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RealEstateAgencyObserver
{
    /**
     * @throws RealEstateAgencyException
     */
    public function created($realEstateAgency)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $data = $realEstateAgency->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->realEstateAgencies()->create($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[RealEstateAgencyObserver][created] '.$exception->getMessage(), $data);
            throw new RealEstateAgencyException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws RealEstateAgencyException
     */
    public function updated($realEstateAgency)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $realEstateAgency->refresh();

        $data = $realEstateAgency->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->realEstateAgencies()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[RealEstateAgencyObserver][updated] '.$exception->getMessage(), $data);
            throw new RealEstateAgencyException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws RealEstateAgencyException
     */
    public function deleted($realEstateAgency)
    {
        $this->updated($realEstateAgency);
    }
}
