<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\Exceptions\Customer\DocumentException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DocumentObserver
{
    /**
     * @throws DocumentException
     */
    public function created($document)
    {
        if (! Config::get('iss-supernova.base_uri')) {
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
                'user',
            );

            if ($document->customer->user) {
                $document->customer->user->loadMissing(
                    'company'
                );
            }
        }
        $data = $document->toArray();
        $data['sync_to'] = 'sys';

        // Passo o campo file novamente pois Document::getFileAttribute() gera uma url temporária de 5 minutos do S3
        $data['file'] = $document->getAttributes()['file'];

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->customerDocuments()->create($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[DocumentObserver][created] '.$exception->getMessage(), $data);
            throw new DocumentException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws DocumentException
     */
    public function updated($document)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $document->refresh();

        $document->loadMissing(
            'customer',
            'document_type',
        );
        if ($document->customer) {
            $document->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($document->customer->user) {
                $document->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $document->toArray();
        $data['sync_to'] = 'sys';

        // Passo o campo file novamente pois Document::getFileAttribute() gera uma url temporária de 5 minutos do S3
        $data['file'] = $document->getAttributes()['file'];

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->customerDocuments()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[DocumentObserver][updated] '.$exception->getMessage(), $data);
            throw new DocumentException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws DocumentException
     */
    public function deleted($document)
    {
        $this->updated($document);
    }
}
