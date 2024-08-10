<?php

namespace App\Services;

use App\Models\PersonalDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PersonDetailService
{
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.person_detail_api.url');
        $this->apiKey = config('services.person_detail_api.key');
    }

    public function getPersonalDetails($personEmail)
    {
        var_dump("alooooooooou!!!!!");
        $personalDetail = PersonalDetail::where('email', $personEmail)->first();

        if ($personalDetail && $this->isFresh($personalDetail->updated_at)) {
            return $personalDetail;
        }

        $data = $this->fetchPersonalDetails($personEmail);

        if (!empty($data)) {
            $personalDetail = PersonalDetail::updateOrCreate(
                ['email' => $personEmail],
                $data
            );

            return $personalDetail;
        }
    }

    /**
     * Fetch personal details from the API.
     *
     * @param  int  $personEmail
     * @return array|null
     */
    public function fetchPersonalDetails($personEmail)
    {
        try {
            $response = Http::get("{$this->apiUrl}/personal-details/{$personEmail}");

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

    /**
     * Check if the data is fresh (within 30 days).
     *
     * @param  Carbon  $updatedAt
     * @return bool
     */
    protected function isFresh($updatedAt)
    {
        return Carbon::parse($updatedAt)->greaterThanOrEqualTo(now()->subDays(30));
    }
}
