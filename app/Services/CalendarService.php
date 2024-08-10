<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class CalendarService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.calendar_api.url'); // Assume this URL is set in your config
    }

    /**
     * Fetch calendar events for a given user.
     *
     * @param  \App\Models\User  $user
     * @return array|null
     */
    public function fetchCalendarEvents(User $user)
    {
        $integration = $user->integrations()->where('name', 'calendar')->first();

        if (!$integration) {
            // No integration found for the user
            return null;
        }

        $token = $integration->pivot->api_key;

        try {
            $response = Http::withToken($token)->get("{$this->apiUrl}/events");

            if ($response->successful()) {
                $events = $response->json()['data'];
                $currentDate = date('Y-m-d'); // Get the current date in YYYY-MM-DD format

                $todayEvents = array_filter($events, function($event) use ($currentDate) {
                    $eventDate = date('Y-m-d', strtotime($event['start']));
                    return $eventDate === $currentDate;
                });
                return $todayEvents;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }
    }
}
