<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class TypologyObserver
{
    public function created($parameter)
    {
        if (App::runningUnitTests()) {
            return;
        }

        $parameter->loadMissing('realEstateDevelopment');
        $data = $parameter->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentTypologies()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($parameter)
    {
        if (App::runningUnitTests()) {
            return;
        }
        
        $parameter->loadMissing('realEstateDevelopment');
        $data = $parameter->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopmentTypologies()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($parameter)
    {
        //
    }
}
