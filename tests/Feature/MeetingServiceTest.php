<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Meeting;
use App\Models\PersonalDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Mockery;
use Tests\TestCase;
use App\Services\MeetingService;
use App\Services\PersonDetailService;
use Illuminate\Support\Facades\Http;

class MeetingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $personalDetailServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->personalDetailServiceMock = Mockery::mock(\App\Services\PersonDetailService::class);
        $this->app->instance(\App\Services\PersonDetailService::class, $this->personalDetailServiceMock);
    }

    protected function apiUrl()
    {
        return config('services.calendar_api.url');
    }

    public function test_fetches_meeting_information_correctly()
    {
        $user = User::factory()->create();
        $secondUser = User::factory()->create([
            'name' => 'Colleague',
            'email' => 'seconduser@usergems.com'
        ]);
        $pastMeeting = Meeting::factory()->create([
            'event_id' => 2,
            'title' => 'Meeting Title',
            'start' => '2024-08-05T10:00:00',
            'end' => '2024-08-05T11:00:00',
        ]);

        $person = PersonalDetail::factory()->create([
            'id' => 1,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'avatar' => 'avatar_url',
            'linkedin_url' => 'linkedin_url',
            'email' => 'person@example.com',
            'title' => 'Title',
        ]);

        $pastMeeting->persons()->attach($person->id);

        $personDetailServiceMock = $this->createMock(PersonDetailService::class);
        $personDetailServiceMock->expects($this->once())
            ->method('getPersonDetails')
            ->with('person@example.com')
            ->willReturn($person);

        Http::fake([
            "{$this->apiUrl()}/personal-details/person@example.com" => Http::response([
                'firstName' => 'John',
                'lastName' => 'Doe',
                'avatar' => 'avatar_url',
                'linkedin_url' => 'linkedin_url',
                'title' => 'Title',
            ], 200),
        ]);

        $event = [
            'id' => 1,
            'title' => 'Meeting Title',
            'start' => '2024-08-10T10:00:00',
            'end' => '2024-08-10T11:00:00',
            'accepted' => ['seconduser@usergems.com'],
            'rejected' => ['person@example.com'],
        ];

        $meetingService = new MeetingService($personDetailServiceMock);

        $result = $meetingService->fetchMeetingInformation($user, [$event]);
        
        $this->assertCount(1, $result);
        $this->assertEquals($event['title'], $result[0]['title']);
        $this->assertEquals($event['start'], $result[0]['start']);
        $this->assertEquals($event['end'], $result[0]['end']);
        // Check if the person had past meeting with colleagues
        $this->assertEquals($result[0]['persons'][0]['common_meetings'][0]->name, $secondUser->name);
        $this->assertEquals($result[0]['persons'][0]['common_meetings'][0]->total_meetings, 1);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}

