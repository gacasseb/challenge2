<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PersonalDetailController extends Controller
{
    protected $personDetailService;

    public function __construct(PersonDetailService $personDetailService)
    {
        $this->personDetailService = $personDetailService;
    }

    public function getPersonalDetails($userEmail)
    {
        $personalDetail = PersonalDetail::where('email', $userEmail)->first();

        if ($personalDetail && $this->isFresh($personalDetail->updated_at)) {
            return response()->json($personalDetail);
        }

        $data = $this->personDetailService->fetchPersonalDetails($userEmail);

        if ($data) {
            $personalDetail = PersonalDetail::updateOrCreate(
                ['email' => $userEmail],
                $data
            );

            return response()->json($personalDetail);
        }

        return response()->json(['message' => 'Failed to fetch personal details'], 500);
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
