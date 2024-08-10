<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Integration;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class CalendarServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function apiUrl()
    {
        return config('services.calendar_api.url');
    }

    /**
     * Fetch calendar events from calendar service
     */
    public function test_fetch_calendar_events(): void
    {
        $user = User::factory()->create();
        $integration = Integration::factory()->create([
            'name' => 'calendar',
        ]);
        $user->integrations()->attach($integration->id, ['api_key' => 'test-api-key']);

        Http::fake([
            "{$this->apiUrl()}/events" => Http::response([
                'data' => [
                    [
                        'id' => 1,
                        'title' => 'Event 1',
                        'start' => '2024-08-10T10:00:00',
                        'end' => '2024-08-10T11:00:00',
                        'accepted' => ['seconduser@usergems.com'],
                        'rejected' => ['person@example.com']
                    ],
                    [
                        'id' => 2,
                        'title' => 'Event 2',
                        'start' => '2024-08-10T10:00:00',
                        'end' => '2024-08-10T11:00:00',
                        'accepted' => ['seconduser@usergems.com'],
                        'rejected' => ['person@example.com'],
                    ],
                    // should not be included
                    [
                        'id' => 3,
                        'title' => 'Event 2',
                        'start' => '2024-08-09T10:00:00',
                        'end' => '2024-08-09T11:00:00',
                        'accepted' => ['seconduser@usergems.com'],
                        'rejected' => ['person@example.com'],
                    ],
                ]
            ], 200),
        ]);

        $calendarService = app(\App\Services\CalendarService::class);
        $events = $calendarService->fetchCalendarEvents($user);

        $this->assertNotNull($events);
        $this->assertCount(2, $events);
        $this->assertEquals('Event 1', $events[0]['title']);
        $this->assertEquals('Event 2', $events[1]['title']);
    }
}
