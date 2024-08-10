<?php

namespace App\Services;

use App\Models\User;
use App\Models\Meeting;
use App\Services\PersonDetailService;
use Illuminate\Support\Facades\DB;

class MeetingService
{
    public function __construct(PersonDetailService $personDetailService) {
        $this->personDetailService = $personDetailService;
    }

    protected function getMeetingDetails($meeting, User $user, $persons) {
        $personInfo = [];
        foreach($persons as $person) {
            $meetingCounts = DB::table('meeting_personal_detail')
                ->join('meeting_user', 'meeting_personal_detail.meeting_id', '=', 'meeting_user.meeting_id')
                ->join('users', 'meeting_user.user_id', '=', 'users.id')
                ->where('meeting_personal_detail.personal_detail_id', $person->id)
                ->select('users.id', 'users.name', DB::raw('count(*) as total_meetings'))
                ->groupBy('meeting_user.user_id')
                ->get();

            $personInfo[] = [
                'name' => "$person->first_name $person->last_name",
                'avatar' => $person->avatar,
                'linkedin_url' => $person->linkedin_url,
                'title' => $person->title,
                'common_meetings' => $meetingCounts,
            ];
        }

        $details = [
            'title' => $meeting->title,
            'start' => $meeting->start,
            'end' => $meeting->end,
            'users' => $meeting->users,
            'persons' => $personInfo,
        ];

        return $details;
    }

    protected function retrieveMeetingFromEvent($event, User $user) {
        $meeting = Meeting::updateOrCreate(
            ['event_id' => $event['id']],
            [
                'title' => $event['title'],
                'start' => $event['start'],
                'end' => $event['end'],
            ]
        );

        $attendees = array_merge($event['accepted'], $event['rejected']);

        $userEmails = [];
        $personEmails = [];
        foreach ($attendees as $email) {
            if (strpos($email, '@usergems.com') !== false) {
                $userEmails[] = $email;
            } else {
                $personEmails[] = $email;
            }
        }
        
        $userIds = User::whereIn('email', $userEmails)->pluck('id');
        $persons = [];

        foreach ($personEmails as $email) {
            $person = $this->personDetailService->getPersonDetails($email);
            $persons[$person->id] = $person;
        }

        $meeting->users()->syncWithoutDetaching($userIds);
        $meeting->persons()->syncWithoutDetaching(array_keys($persons));

        return $this->getMeetingDetails($meeting, $user, $persons);
    }

    public function fetchMeetingInformation(User $user, $calendarEvents) {
        return array_map(function ($event) use($user) {
            return $this->retrieveMeetingFromEvent($event, $user);
        }, $calendarEvents);
    }
}
