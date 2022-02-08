<?php

namespace Bildvitta\IssSupernova;

use Bildvitta\IssSupernova\Contracts\IssSupernovaFactory;
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
            if (Cache::has($clientId)) {
                $accessToken = Cache::get($clientId);
            } else {
                $accessToken = $this->getToken();
                Cache::add($clientId, $accessToken, now()->addSeconds(31536000));
            }
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
            ->withHeaders($this->getHeaders());
    }

    public function getHeaders(): array
    {
        return array_merge(
            self::DEFAULT_HEADERS,
            [
                'Almobi-Host' => Config::get('app.slug', ''),
            ]
        );
    }

    //TODO endpoints para os objetos aqui
    /*
    public function objects(): Objects
    {
        return new Objects($this);
    }
    */
}
