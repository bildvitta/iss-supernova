<?php

namespace Bildvitta\IssSupernova\Observers\Juridico;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SignerDocumentObserver
{
    public function created($signerDocument)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        if (!in_array($signerDocument->document->creator_user?->company?->uuid, Config::get('iss-supernova.companies'))) {
            return;
        }

        $data = $signerDocument->toArray();

        $data['document'] = $signerDocument->document->uuid ?? null;
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->juridico()->signerDocuments()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($signerDocument)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $signerDocument->refresh();

        if (!in_array($signerDocument->document->creator_user?->company?->uuid, Config::get('iss-supernova.companies'))) {
            return;
        }

        $data = $signerDocument->toArray();

        $data['document'] = $signerDocument->document->uuid ?? null;
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->juridico()->signerDocuments()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($signerDocument)
    {
        $this->updated($signerDocument);
    }
}
