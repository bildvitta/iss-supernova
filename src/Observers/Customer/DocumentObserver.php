<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DocumentObserver
{
    public function created($document)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $document->loadMissing(
            'customer',
            'document_type',
        );
        if ($document->customer) {
            $document->customer->loadMissing(
                'bonds',
                'bonds_from',
            );
        }
        $data = $document->toArray();
        $data['sync_to'] = 'sys';

        //Passo o campo file novamente pois Document::getFileAttribute() gera uma url temporária de 5 minutos do S3
        $data['file'] = $document->getAttributes()['file'];

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerDocuments()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($document)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $document->loadMissing(
            'customer',
            'document_type',
        );
        if ($document->customer) {
            $document->customer->loadMissing(
                'bonds',
                'bonds_from',
            );
        }
        $data = $document->toArray();
        $data['sync_to'] = 'sys';

        //Passo o campo file novamente pois Document::getFileAttribute() gera uma url temporária de 5 minutos do S3
        $data['file'] = $document->getAttributes()['file'];

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerDocuments()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($document)
    {
        $this->updated($document);
    }
}
