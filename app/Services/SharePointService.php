<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SharePointService
{
    protected $tenantId;
    protected $clientId;
    protected $clientSecret;
    protected $siteId;

    public function __construct()
    {
        $this->tenantId     = config('services.graph.tenant_id');
        $this->clientId     = config('services.graph.client_id');
        $this->clientSecret = config('services.graph.client_secret');
        $this->siteId       = config('services.graph.site_id');
    }

    // Get Microsoft Graph access token
    protected function getAccessToken()
    {
        $url = "https://login.microsoftonline.com/{$this->tenantId}/oauth2/v2.0/token";

        $response = Http::asForm()->post($url, [
            'grant_type'    => 'client_credentials',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope'         => 'https://graph.microsoft.com/.default',
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        return null;
    }

    // Upload file and return its public webUrl
 public function uploadFile($file, $fileName)
{
    $accessToken = $this->getAccessToken();
    if (!$accessToken) {
        return null;
    }

    $uploadUrl = "https://graph.microsoft.com/v1.0/sites/{$this->siteId}/drive/root:/Announcements/{$fileName}:/content";

    $response = Http::withToken($accessToken)
        ->withBody(file_get_contents($file->getRealPath()), $file->getMimeType())
        ->put($uploadUrl);

    if ($response->successful()) {
        // ✅ webUrl is already returned in the upload response
        return $response->json()['webUrl'] ?? null;
    }

    return null;
}
}
