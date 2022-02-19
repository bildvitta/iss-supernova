<?php

namespace Bildvitta\IssSupernova\Observers\RealEstateDevelopment;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Log;

class RealEstateDevelopmentObserver
{
    public function created($realEstateDeveloptment)
    {
        $issSupernova = new IssSupernova();

        $data = $realEstateDeveloptment->toArray();
        $data['sync_to'] = 'sys';

        try {
            $response = $issSupernova->realEstateDevelopments()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($customer)
    {
        //
    }

    public function deleted($customer)
    {
        //
    }
}
