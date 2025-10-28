<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\Exceptions\RealEstateDevelopment\ParameterException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ParameterObserver
{
    /**
     * @throws ParameterException
     */
    public function created($parameter)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $parameter->loadMissing('realEstateDevelopment');
        if ($parameter->realEstateDevelopment) {
            $parameter->realEstateDevelopment->loadMissing(
                'hub_company'
            );
        }

        $data = $parameter->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentParameters()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error("[ParameterObserver][created]" . $exception->getMessage(), $data);
            throw new ParameterException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws ParameterException
     */
    public function updated($parameter)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $parameter->refresh();

        $parameter->loadMissing('realEstateDevelopment');
        if ($parameter->realEstateDevelopment) {
            $parameter->realEstateDevelopment->loadMissing(
                'hub_company'
            );
        }

        $data = $parameter->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentParameters()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error("[ParameterObserver][updated]" . $exception->getMessage(), $data);
            throw new ParameterException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws ParameterException
     */
    public function deleted($parameter)
    {
        $this->updated($parameter);
    }
}
