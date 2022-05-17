<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DocumentObserver
{
    public function created($customer)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $customer->loadMissing(
            'customer',
            'document_type',
        );
        $data = $customer->toArray();
        $data['sync_to'] = 'sys';

        //Passo o campo file novamente pois Document::getFileAttribute() gera uma url temporária de 5 minutos do S3
        $data['file'] = $customer->getAttributes()['file'];

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerDocuments()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($customer)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $customer->loadMissing(
            'customer',
            'document_type',
        );
        $data = $customer->toArray();
        $data['sync_to'] = 'sys';

        //Passo o campo file novamente pois Document::getFileAttribute() gera uma url temporária de 5 minutos do S3
        $data['file'] = $customer->getAttributes()['file'];

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerDocuments()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($customer)
    {
        //
    }
}
