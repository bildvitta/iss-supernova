<?php

namespace Bildvitta\IssSupernova\Observers\Juridico;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class HistoricObserver
{
    public function created($historic)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        if (!in_array($historic->document->creator_user?->company?->uuid, Config::get('iss-supernova.companies'))) {
            return;
        }

        $data = $historic->toArray();

        $data['sale'] = $historic->document->external_id ?? null;
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->juridico()->historics()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($historic)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $historic->refresh();

        if (!in_array($historic->document->creator_user?->company?->uuid, Config::get('iss-supernova.companies'))) {
            return;
        }

        $data = $historic->toArray();

        $data['sale'] = $historic->document->external_id ?? null;
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->juridico()->historics()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($historic)
    {
        $this->updated($historic);
    }
}
