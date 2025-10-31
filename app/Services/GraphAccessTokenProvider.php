<?php

namespace App\Services;

use Microsoft\Kiota\Abstractions\Authentication\AccessTokenProviderInterface;
use Microsoft\Kiota\Abstractions\Authentication\AllowedHostsValidator;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Client;

class GraphAccessTokenProvider implements AccessTokenProviderInterface
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $tenantId;
    protected array $scopes;

    public function __construct(string $tenantId, string $clientId, string $clientSecret, array $scopes = ['https://graph.microsoft.com/.default'])
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tenantId = $tenantId;
        $this->scopes = $scopes;
    }

    public function getAuthorizationToken(RequestInterface $request): string
    {
        $client = new Client();

        $response = $client->post("https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token", [
            'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => implode(' ', $this->scopes),
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        return $data['access_token'] ?? '';
    }

    public function getAllowedHostsValidator(): AllowedHostsValidator
    {
        return new AllowedHostsValidator(["graph.microsoft.com"]);
    }
}
