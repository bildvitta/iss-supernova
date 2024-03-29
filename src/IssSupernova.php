<?php

namespace Bildvitta\IssSupernova;

use Bildvitta\IssSupernova\Resources\Companies;
use Bildvitta\IssSupernova\Resources\CustomerBankAccounts;
use Bildvitta\IssSupernova\Resources\CustomerCreditCards;
use Bildvitta\IssSupernova\Resources\CustomerDependents;
use Bildvitta\IssSupernova\Resources\CustomerDocuments;
use Bildvitta\IssSupernova\Resources\CustomerFgtsAccounts;
use Bildvitta\IssSupernova\Resources\CustomerFinancialCommitments;
use Bildvitta\IssSupernova\Resources\CustomerFormalIncomes;
use Bildvitta\IssSupernova\Resources\CustomerHeritageCars;
use Bildvitta\IssSupernova\Resources\CustomerHeritagePropertys;
use Bildvitta\IssSupernova\Resources\CustomerInformalIncomes;
use Bildvitta\IssSupernova\Resources\CustomerInvestments;
use Bildvitta\IssSupernova\Resources\CustomerMonthlyFamilyExpenses;
use Bildvitta\IssSupernova\Resources\CustomerPersonalReferences;
use Bildvitta\IssSupernova\Resources\Customers;
use Bildvitta\IssSupernova\Resources\RealEstateAgencies;
use Bildvitta\IssSupernova\Resources\RealEstateDevelopmentAccessories;
use Bildvitta\IssSupernova\Resources\RealEstateDevelopmentBlueprints;
use Bildvitta\IssSupernova\Resources\RealEstateDevelopmentParameters;
use Bildvitta\IssSupernova\Resources\RealEstateDevelopments;
use Bildvitta\IssSupernova\Contracts\IssSupernovaFactory;
use Bildvitta\IssSupernova\Resources\Juridico;
use Bildvitta\IssSupernova\Resources\Sicaq;
use Bildvitta\IssSupernova\Resources\RealEstateDevelopmentTypologies;
use Bildvitta\IssSupernova\Resources\RealEstateDevelopmentUnits;
use Bildvitta\IssSupernova\Resources\SaleAccessories;
use Bildvitta\IssSupernova\Resources\SalePeriodicities;
use Bildvitta\IssSupernova\Resources\Sales;
use Bildvitta\IssSupernova\Resources\SYS;
use Bildvitta\IssSupernova\Resources\Users;
use Bildvitta\IssSupernova\Resources\Vendas;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class IssSupernova extends HttpClient implements IssSupernovaFactory
{
    public PendingRequest $request;

    private ?string $token;

    public function __construct(?string $token = '')
    {
        parent::__construct();

        $programmatic = true;
        if ($token != '') {
            $programmatic = false;
        }

        $this->setToken($token, $programmatic);
    }

    public function setToken(string $token, bool $programmatic = false): IssSupernova
    {
        $this->token = $token;

        if ($programmatic) {
            $clientId = Config::get('hub.programatic_access.client_id');
            /*
            if (Cache::has($clientId)) {
                $accessToken = Cache::get($clientId);
            } else {
                $accessToken = $this->getToken();
                Cache::add($clientId, $accessToken, now()->addSeconds(31536000));
            }
            */
            $accessToken = $this->getToken();
            $this->token = $accessToken;
        }

        $this->prepareRequest();

        return $this;
    }

    private function getToken()
    {
        $hubUrl = Config::get('hub.base_uri') . Config::get('hub.oauth.token_uri');
        $clientId = Config::get('hub.programatic_access.client_id');
        $secretId = Config::get('hub.programatic_access.client_secret');
        $response = Http::asForm()->post($hubUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $secretId,
            'scope' => '*',
        ]);

        return $response->json('access_token');
    }

    private function prepareRequest(): PendingRequest
    {
        return $this->request = Http::withToken($this->token)
            ->baseUrl(Config::get('iss-supernova.base_uri').Config::get('iss-supernova.prefix'))
            ->withOptions(self::DEFAULT_OPTIONS)
            ->withHeaders(self::DEFAULT_HEADERS);
    }

    //

    public function get($url, $query = [])
    {
        return $this->request->get($url, $query);
    }

    public function post($url, $data = [])
    {
        return $this->request->post($url, $data);
    }

    public function put($url, $data = [])
    {
        return $this->request->put($url, $data);
    }

    public function patch($url, $data = [])
    {
        return $this->request->patch($url, $data);
    }

    public function delete($url, $data = [])
    {
        return $this->request->delete($url, $data);
    }

    //

    public function realEstateDevelopments()
    {
        return new RealEstateDevelopments($this);
    }

    public function realEstateDevelopmentParameters()
    {
        return new RealEstateDevelopmentParameters($this);
    }

    public function realEstateDevelopmentUnits()
    {
        return new RealEstateDevelopmentUnits($this);
    }

    public function realEstateDevelopmentTypologies()
    {
        return new RealEstateDevelopmentTypologies($this);
    }

    public function realEstateDevelopmentBlueprints()
    {
        return new RealEstateDevelopmentBlueprints($this);
    }

    public function realEstateDevelopmentAccessories()
    {
        return new RealEstateDevelopmentAccessories($this);
    }

    public function realEstateAgencies()
    {
        return new RealEstateAgencies($this);
    }

    public function customers()
    {
        return new Customers($this);
    }

    public function customerBankAccounts()
    {
        return new CustomerBankAccounts($this);
    }

    public function customerCreditCards()
    {
        return new CustomerCreditCards($this);
    }

    public function customerDependents()
    {
        return new CustomerDependents($this);
    }

    public function customerDocuments()
    {
        return new CustomerDocuments($this);
    }

    public function customerFgtsAccounts()
    {
        return new CustomerFgtsAccounts($this);
    }

    public function customerFinancialCommitments()
    {
        return new CustomerFinancialCommitments($this);
    }

    public function customerFormalIncomes()
    {
        return new CustomerFormalIncomes($this);
    }

    public function customerHeritageCars()
    {
        return new CustomerHeritageCars($this);
    }

    public function customerHeritagePropertys()
    {
        return new CustomerHeritagePropertys($this);
    }

    public function customerInformalIncomes()
    {
        return new CustomerInformalIncomes($this);
    }

    public function customerInvestments()
    {
        return new CustomerInvestments($this);
    }

    public function customerMonthlyFamilyExpenses()
    {
        return new CustomerMonthlyFamilyExpenses($this);
    }

    public function customerPersonalReferences()
    {
        return new CustomerPersonalReferences($this);
    }

    public function users()
    {
        return new Users($this);
    }

    public function companies()
    {
        return new Companies($this);
    }

    public function sales()
    {
        return new Sales($this);
    }

    public function salePeriodicities()
    {
        return new SalePeriodicities($this);
    }

    public function saleAccessories()
    {
        return new SaleAccessories($this);
    }

    public function vendas()
    {
        return new Vendas($this);
    }

    public function sys()
    {
        return new SYS($this);
    }

    public function sicaq()
    {
        return new Sicaq($this);
    }

    public function juridico()
    {
        return new Juridico($this);
    }
}
