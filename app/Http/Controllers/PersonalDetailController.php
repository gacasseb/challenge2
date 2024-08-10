<?php

namespace App\Http\Controllers;

use App\Services\PersonDetailService;
use App\Models\PersonalDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PersonalDetailController extends Controller
{
    protected $personDetailService;

    public function __construct(PersonDetailService $personDetailService)
    {
        $this->personDetailService = $personDetailService;
    }

    public function getPersonalDetails(User $user)
    {
        $personalDetail = PersonalDetail::where('user_id', $user->id)->first();

        if ($personalDetail && $this->isFresh($personalDetail->updated_at)) {
            return response()->json($personalDetail);
        }

        $data = $this->personDetailService->fetchPersonalDetails($user->email);

        if ($data) {
            $personalDetail = PersonalDetail::updateOrCreate(
                ['user_id' => $user->id],
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
