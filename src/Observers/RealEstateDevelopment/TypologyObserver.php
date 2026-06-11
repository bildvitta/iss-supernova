<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\Exceptions\RealEstateDevelopment\TypologyException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TypologyObserver
{
    /**
     * @throws TypologyException
     */
    public function created($typology)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $typology->loadMissing('realEstateDevelopment');
        if ($typology->realEstateDevelopment) {
            $typology->realEstateDevelopment->last_parameter = $typology->realEstateDevelopment->last_parameter();
            $typology->realEstateDevelopment->loadMissing(
                'hub_company'
            );
        }
        $data = $typology->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentTypologies()->create($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[TypologyObserver][created]'.$exception->getMessage(), $data);
            throw new TypologyException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws TypologyException
     */
    public function updated($typology)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $typology->refresh();

        $typology->loadMissing('realEstateDevelopment');
        if ($typology->realEstateDevelopment) {
            $typology->realEstateDevelopment->last_parameter = $typology->realEstateDevelopment->last_parameter();
            $typology->realEstateDevelopment->loadMissing(
                'hub_company'
            );
        }
        $data = $typology->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentTypologies()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[TypologyObserver][updated]'.$exception->getMessage(), $data);
            throw new TypologyException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws TypologyException
     */
    public function deleted($typology)
    {
        $this->updated($typology);
    }
}
