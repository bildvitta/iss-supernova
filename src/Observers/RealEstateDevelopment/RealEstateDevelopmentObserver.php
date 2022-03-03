<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class RealEstateDevelopmentObserver
{
    public function created($realEstateDeveloptment)
    {
        if (App::runningUnitTests()) {
            return;
        }
        
        $data = $realEstateDeveloptment->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopments()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($realEstateDeveloptment)
    {
        if (App::runningUnitTests()) {
            return;
        }
        
        $data = $realEstateDeveloptment->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->realEstateDevelopments()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($realEstateDeveloptment)
    {
        //
    }
}
