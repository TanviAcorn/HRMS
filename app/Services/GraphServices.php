<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class GraphServices
{
    /**
     * Get an access token via OAuth2 client credentials.
     */
    protected function getAccessToken(): ?string
    {
        $tenantId = env('GRAPH_TENANT_ID');
        $clientId = env('GRAPH_CLIENT_ID');
        $clientSecret = env('GRAPH_CLIENT_SECRET');

        if (!$tenantId || !$clientId || !$clientSecret) {
            Log::error('Graph Auth Failed: Missing tenant/client credentials.');
            return null;
        }

        try {
            $client = new Client(['timeout' => 20]);
            $resp = $client->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
                'form_params' => [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://graph.microsoft.com/.default',
                ],
            ]);
            $data = json_decode((string) $resp->getBody(), true);
            return $data['access_token'] ?? null;
        } catch (\Throwable $e) {
            Log::error('Graph Auth Failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload file to SharePoint folder using Microsoft Graph REST (simple upload <= 4MB)
     */
    public function uploadAnnouncementFile($siteId, $folder, $file)
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            // Prefer unique filename to avoid conflicts
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . preg_replace('/\s+/', '_', $originalName);

            $realPath = $file->getRealPath();
            if (!is_readable($realPath)) {
                Log::error('SharePoint Upload Failed: Uploaded file path not readable');
                return null;
            }

            $size = filesize($realPath) ?: 0;
            if ($size > 4 * 1024 * 1024) {
                Log::error('SharePoint Upload Failed: File exceeds 4MB; chunked upload not implemented');
                return null;
            }

            $fileContent = file_get_contents($realPath);

            $client = new Client(['base_uri' => 'https://graph.microsoft.com/v1.0/', 'timeout' => 60]);

            // PUT /sites/{site-id}/drive/root:/Folder/File:/content
            $path = sprintf('sites/%s/drive/root:/%s/%s:/content', $siteId, rawurlencode($folder), rawurlencode($fileName));

            $resp = $client->request('PUT', $path, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/octet-stream',
                ],
                'body' => $fileContent,
            ]);

            $data = json_decode((string) $resp->getBody(), true);
            
            if (isset($data['webUrl'])) {
                return $data['webUrl'];
            }

            // Fallback to constructed URL
            $tenantDomain = 'acornsolution.sharepoint.com';
            $sitePath = '/sites/HRprocesses';
            $encodedFolder = str_replace(' ', '%20', $folder);
            $encodedFileName = rawurlencode($fileName);
            return "https://{$tenantDomain}{$sitePath}/Shared%20Documents/{$encodedFolder}/{$encodedFileName}";

        } catch (\Throwable $e) {
            Log::error('SharePoint Upload Failed: ' . $e->getMessage());
            return null;
        }
    }
}
