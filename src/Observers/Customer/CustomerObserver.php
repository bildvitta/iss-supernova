<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class CustomerObserver
{
    public function created($customer)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $customer->loadMissing(
            'channel',
            'subchannel',
            'civil_status',
            'income_bracket',
            'nationality',
            'street_type',
            'property_type',
            'property_occupation',
            'proof_of_residence_type',
            'billing_street_type',
            'degree_level',
            'occupation',
            'occupation_type',
            'user',
            'funnel',
            'country',
            'user',
            'supervisor',
            'manager',
            'real_estate_agency',
            'educational_course',
            'bonds',
            'bonds_from',
        );
        if ($customer->user) {
            $customer->user->loadMissing(
                'company'
            );
        }

        $this->triggerSubObjects($customer);

        $data = $customer->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customers()->create($data);
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

        $customer->refresh();

        $customer->loadMissing(
            'channel',
            'subchannel',
            'civil_status',
            'income_bracket',
            'nationality',
            'street_type',
            'property_type',
            'property_occupation',
            'proof_of_residence_type',
            'billing_street_type',
            'degree_level',
            'occupation',
            'occupation_type',
            'user',
            'funnel',
            'country',
            'user',
            'supervisor',
            'manager',
            'real_estate_agency',
            'educational_course',
            'bonds',
            'bonds_from',
        );
        if ($customer->user) {
            $customer->user->loadMissing(
                'company'
            );
        }

        $this->triggerSubObjects($customer);

        $data = $customer->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customers()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($customer)
    {
        $this->updated($customer);
    }

    private function triggerSubObjects($customer) {
        if ($customer->is_incomplete_registration) {
            return;
        }

        $relashionships = [
            'bank_accounts',
            'credit_cards',
            'dependents',
            'fgts_accounts',
            'financial_commitments',
            'formal_incomes',
            'heritage_cars',
            'heritage_properties',
            'cars',
            'properties',
            'informal_incomes',
            'investments',
            'monthly_family_expenses',
            'personal_references',
            'documents',
        ];

        foreach ($relashionships as $relashionship) {
            if ($customer->$relashionship) {
                foreach ($customer->$relashionship as $relashionshipObject) {
                    event('eloquent.updated: ' . get_class($relashionshipObject), $relashionshipObject);
                }
            }
        }
    }
}
