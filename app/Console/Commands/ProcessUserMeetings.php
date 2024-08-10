<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Services\MeetingService;
use App\Services\PersonDetailService;
use App\Notifications\MeetingNotification;
use App\Models\User;

class ProcessUserMeetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-user-meetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch calendar and personal details for each user and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(CalendarService $calendarService, MeetingService $meetingService)
    {
        $users = User::all();

        foreach ($users as $user) {
            $calendarEvents = $calendarService->fetchCalendarEvents($user);

            $meetingService->fetchMeetingInformation($user, $calendarEvents);

            // Create the notification data
            // $notificationData = [
            //     'user' => $user,
            //     'personalDetails' => $personalDetails,
            //     'meetings' => $calendarEvents,
            // ];

            // Send notification
            // Notification::send($user, new MeetingNotification($notificationData));
        }
    }
}
