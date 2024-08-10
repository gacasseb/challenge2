<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PersonDetailService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.person_detail_api.url');
        $this->apiKey = config('services.person_detail_api.key');
    }

    /**
     * Fetch personal details from the API.
     *
     * @param  int  $userEmail
     * @return array|null
     */
    public function fetchPersonalDetails($userEmail)
    {
        try {
            $response = Http::get("{$this->apiUrl}/personal-details/{$userEmail}");

            if ($response->successful()) {
                return $response->json();
            } else {
                // @TODO: Handle non-successful response
                return null;
            }
        } catch (\Exception $e) {
            // @TODO: Handle exceptions
            return null;
        }
    }
}
