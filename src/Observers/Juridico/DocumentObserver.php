<?php

namespace Bildvitta\IssSupernova\Observers\Juridico;

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

        if (!in_array($document->creator_user?->company?->uuid, Config::get('iss-supernova.companies'))) {
            return;
        }

        if ($document->provider_external_id == null) {
            return;
        }

        $document->loadMissing(
            'document_type',
        );

        $data = $document->toArray();

        $data['code_safe'] = $document->signature_parameter->signature_parameter_providers->where('slug', 'code_safe')->first()->value ?? null; //required
        $data['code_folder'] = $document->signature_parameter->signature_parameter_providers->where('slug', 'code_folder')->first()->value ?? null; //required
        $data['creator_user'] = $document->creator_user->hub_uuid ?? null;
        $data['email'] = $document->signature_parameter->signature_parameter_signatory_types()->whereHas('signatory_type', function($query) {
            $query->where('name', 'Crédito');
        })->first()->email ?? null;
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->juridico()->documents()->create($data);
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

        $document->refresh();

        if (!in_array($document->creator_user?->company?->uuid, Config::get('iss-supernova.companies'))) {
            return;
        }

        if ($document->provider_external_id == null) {
            return;
        }

        $document->loadMissing(
            'document_type',
        );

        $data = $document->toArray();

        $data['code_safe'] = $document->signature_parameter->signature_parameter_providers->where('slug', 'code_safe')->first()->value ?? null; //required
        $data['code_folder'] = $document->signature_parameter->signature_parameter_providers->where('slug', 'code_folder')->first()->value ?? null; //required
        $data['creator_user'] = $document->creator_user->hub_uuid ?? null;
        $data['email'] = $document->signature_parameter->signature_parameter_signatory_types()->whereHas('signatory_type', function($query) {
            $query->where('name', 'Crédito');
        })->first()->email ?? null;
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->juridico()->documents()->update($data);
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
