<?php

namespace Bildvitta\IssSupernova\Observers;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class CompanyObserver
{
    public function created($company)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $company->loadMissing(
            'domains',
            'main_company'
        );
        if ($company->main_company) {
            $company->main_company->loadMissing(
                'domains'
            );
        }
        $data = $company->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->companies()->create($data);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }

        return $response;
    }

    public function updated($company)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $company->loadMissing(
            'domains',
            'main_company'
        );
        if ($company->main_company) {
            $company->main_company->loadMissing(
                'domains'
            );
        }
        $data = $company->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->companies()->update($data);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }

        return $response;
    }

    public function deleted($company)
    {
        $this->updated($company);
    }
}
