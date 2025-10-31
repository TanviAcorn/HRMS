<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\CronController;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    	$schedule->call(function () {
    		$controller = new \App\Http\Controllers\CronController();
    		$controller->sendBirthdayReminder();
    	})->dailyAt('12:30');
    	
    	$schedule->call(function () {
    		$controller = new \App\Http\Controllers\CronController();
    		$controller->sendAnniversaryReminder();
    	})->dailyAt('12:35');
    	
    	$schedule->call(function () {
    		$controller = new \App\Http\Controllers\CronController();
    		$controller->startEmployeeSuspension();
    	})->dailyAt('00:05');
    	
    	$schedule->call(function () {
    		$controller = new \App\Http\Controllers\CronController();
    		$controller->endEmployeeSuspension();
    	})->dailyAt('00:10');
    	
    	
    	$schedule->call(function () {
    		$controller = new \App\Http\Controllers\CronController();
    		$controller->upcomingEndNoticePeriod();
    	})->dailyAt('09:00');
    	
    	$schedule->call(function () {
    		$controller = new \App\Http\Controllers\CronController();
    		$controller->upcomingEndProbationPeriod();
    	})->dailyAt('13:00');
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->sendHoldSalaryReleaseReminderMail();
    	// })->dailyAt('09:15');
    	
    	
    	$schedule->call(function () {
    		$controller = new \App\Http\Controllers\CronController();
    		$controller->updateEmployeeRelivedStatus();
    	})->dailyAt('00:03');
    	
    	$schedule->call(function () {
    		$controller = new \App\Http\Controllers\CronController();
    		$controller->updateEmployeeNoticePeriodStatus();
    	})->dailyAt('00:20');
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->addMonthlyPaidLeaveBalance();
    	// })->monthlyOn('16' , '00:20');
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->addEmployeeDailyAttendance();
    	// })->dailyAt('00:05');
    	
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->fetchEmployeeDailyAttendance();
    	// })->everyTenMinutes();
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->retiveTimeAttendanceEvents();
    	// })->everyFiveMinutes();
    	
    	
    	
    	
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->updateSalaryIntoMaster();
    	// })->dailyAt('00:30');
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->sendHoldSalaryReleaseReminderMail();
    	// })->dailyAt('08:15');
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->sendPendingLeaveReminder();
    	// })->dailyAt('09:00');
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->sendMissingLeaveReminder();
    	// })->dailyAt('09:15');
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->retriveNotification();
    	// })->everyFiveMinutes();
    	
    	// $schedule->call(function () {
    	// 	$controller = new \App\Http\Controllers\CronController();
    	// 	$controller->manageCarryForwardLeave();
    	// })->yearlyOn(12, 16, '00:30');
    	
    	
    	//manageAttendanceSummary
    	
    	
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
