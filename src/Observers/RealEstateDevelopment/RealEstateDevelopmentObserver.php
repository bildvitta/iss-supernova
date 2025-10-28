<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\Exceptions\RealEstateDevelopment\RealEstateDevelopmentException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class RealEstateDevelopmentObserver
{
    /**
     * @throws RealEstateDevelopmentException
     */
    public function created($realEstateDevelopment)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $realEstateDevelopment->loadMissing(
            'hub_company',
            'real_estate_development_type',
            'hub_company_real_estate_agency',
        );
        $data = $realEstateDevelopment->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->realEstateDevelopments()->create($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[RealEstateDevelopmentObserver][created]'.$exception->getMessage(), $data);
            throw new RealEstateDevelopmentException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws RealEstateDevelopmentException
     */
    public function updated($realEstateDevelopment)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $realEstateDevelopment->refresh();

        $realEstateDevelopment->loadMissing(
            'hub_company',
            'real_estate_development_type',
            'hub_company_real_estate_agency',
        );
        $data = $realEstateDevelopment->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->realEstateDevelopments()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[RealEstateDevelopmentObserver][updated]'.$exception->getMessage(), $data);
            throw new RealEstateDevelopmentException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws RealEstateDevelopmentException
     */
    public function deleted($realEstateDevelopment)
    {
        $this->updated($realEstateDevelopment);
    }
}
