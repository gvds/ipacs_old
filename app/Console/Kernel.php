<?php

namespace App\Console;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // $schedule->call(\App\Label::addEventsToLabelQueue())->hourly();
        $schedule->call(function () {
            $thresholdDate = Carbon::parse('next friday');
            \App\event_subject::whereIn('eventstatus_id', [0, 1, 2])
                ->where('labelStatus', '0')
                ->join('events', 'event_id', 'events.id')
                ->join('arms', 'arm_id', 'arms.id')
                ->where('project_id', session('currentProject'))
                ->whereNotNull('eventDate')
                ->where('minDate', "<=", $thresholdDate)
                ->where('active', true)
                ->update(['labelStatus' => 1]);
        })->hourly();

        // $schedule->call(\App\event_sample::nexusStatusUpdate())->hourly();
        $schedule->call(function(){
                try {
                    $containers = Http::withToken(config('services.nexus.token'))
                        ->acceptJson()
                        ->timeout(5)
                        ->withOptions([
                            'verify' => public_path('nexus.pem')
                            // 'verify' => false
                        ])
                        ->post(config('services.nexus.url') . 'containerStatus', []);
                    if ($containers->clientError()) {
                        throw new Exception('Error updating Biorepository sample statuses - Could not get sample storage status data from Nexus: ' . $containers['message'], 1);
                    }
                    if ($containers->serverError()) {
                        throw new Exception('Error updating Biorepository sample statuses - Nexus server error: ' . $containers['message'], 1);
                    }

                    $container_arr = (array) json_decode($containers->body());

                    foreach ($container_arr as $storageName => $samples) {
                        $projects = \App\project::with([
                            'sampletypes' => function ($query) {
                                $query->whereIn('sampletypes.storageDestination', ['BiOS', 'Nexus']);
                            },
                            'sampletypes.event_samples' => function ($query) {
                                $query->whereIn('samplestatus_id', [2, 3, 9]);
                            }
                        ], 'sampletypes.event_samples')
                            ->where('storageProjectName', $storageName)
                            ->get();

                        $inStorage = array_keys((array) $samples, 'inStorage');
                        $loggedOut = array_keys((array) $samples, 'loggedOut');

                        $update_inStorage_IDs = [];
                        $update_loggedOut_IDs = [];

                        foreach ($projects as $key => $project) {
                            foreach ($project->sampletypes as $key => $sampletype) {
                                $sample_ids = array_filter($sampletype->event_samples->toArray(), fn ($sample) => ($sample['samplestatus_id'] !== 3) and (in_array($sample['barcode'], $inStorage)));
                                $update_inStorage_IDs = array_merge($update_inStorage_IDs, array_map(fn ($sample) => $sample['id'], $sample_ids));

                                $sample_ids = array_filter($sampletype->event_samples->toArray(), fn ($sample) => ($sample['samplestatus_id'] !== 9) and (in_array($sample['barcode'], $loggedOut)));
                                $update_loggedOut_IDs = array_merge($update_loggedOut_IDs, array_map(fn ($sample) => $sample['id'], $sample_ids));
                            }
                        }

                        $affected = DB::table('event_sample')
                            ->whereIn('id', $update_inStorage_IDs)
                            ->update(['samplestatus_id' => 3]);
                        if (count($update_inStorage_IDs) != $affected) {
                            throw new Exception('Updating in-storage Biorepository sample statuses failed', 1);
                        }

                        $affected = DB::table('event_sample')
                            ->whereIn('id', $update_loggedOut_IDs)
                            ->update(['samplestatus_id' => 9]);
                        if (count($update_loggedOut_IDs) != $affected) {
                            throw new Exception('Updating logged-out Biorepository sample statuses failed', 1);
                        }
                    }
                } catch (\Throwable $th) {
                    \App\User::where('username', env('system_admin'))->first()->notify($th->getMessage());
                }
            })->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
