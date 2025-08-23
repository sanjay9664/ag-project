<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PDF;
use Illuminate\Support\Facades\Mail;
use MongoDB\Client;
use Illuminate\Support\Facades\Cache;
use App\Models\Site;
use App\Models\Admin;
use App\Http\Controllers\Backend\SiteController;

class SendDailySiteReport extends Command
{
    protected $signature = 'report:send-daily';
    protected $description = 'Generate DGMS site report and send to client via email';

    public function handle()
    {
        ini_set('max_execution_time', 300);
        $start = microtime(true);

        $admin = Admin::where('email', 'user@agpower.com')->first();
        $userEmail = $admin->email;

        $siteData = $admin->hasRole('superadmin')
            ? Site::select(['id','site_name','slug','email','data','device_id','clusterID'])->get()
            : Site::where('email',$userEmail)->select(['id','site_name','slug','email','data','device_id','clusterID'])->get();

        $mdValues = $this->extractMdFields(
            $siteData->pluck('data')->map(fn($data) => json_decode($data, true))->toArray()
        );

        $eventData = [];
        if (!empty($mdValues)) {
            $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

            if (!empty($uniqueMdValues)) {
                $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
                $mongoClient = new \MongoDB\Client($mongoUri);
                $collection = $mongoClient->isa_qa->device_events;

                $dateThreshold = new \MongoDB\BSON\UTCDateTime((new \DateTime('-48 hours'))->getTimestamp() * 1000);
                $cacheKey = 'event_data_' . sha1(implode(',', $uniqueMdValues));

                $eventData = Cache::remember($cacheKey, 300, function () use ($collection, $uniqueMdValues, $dateThreshold) {
                   $cursor = $collection->find(
                        [
                            'module_id' => ['$in' => array_values($uniqueMdValues)],
                            'createdAt' => ['$gte' => $dateThreshold],
                        ],
                        [
                            'sort' => ['createdAt' => -1],
                            'limit' => 1000,
                            'noCursorTimeout' => true,
                        ]
                    );

                    $moduleLatest = [];
                    foreach ($cursor as $event) {
                        $moduleId = $event['module_id'];
                        if (!isset($moduleLatest[$moduleId])) {
                            $moduleLatest[$moduleId] = $event;
                        }
                    }
                    return array_values($moduleLatest);
                });
            }
        }
        // dd($eventData);
        
        usort($eventData, fn($a, $b) => ($b['created_at_timestamp'] ?? 0) <=> ($a['created_at_timestamp'] ?? 0));

        $latestCreatedAt = !empty($eventData)
            ? $eventData[0]['createdAt']->toDateTime()
                ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
                ->format('d-m-Y H:i:s')
            : 'N/A';

        foreach ($eventData as &$event) {
            $event['createdAt'] = $event['createdAt']->toDateTime()
                ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
                ->format('d-m-Y H:i:s');
            $event['latestCreatedAt'] = $latestCreatedAt;
        }

        $eventMap = collect($eventData)->mapWithKeys(function ($event) {
            $key = strtolower(trim($event['device_id'] ?? ''));
            return [$key => $event];
        });

        foreach ($siteData as $site) {
            $deviceId = strtolower(trim($site->device_id ?? ''));
            $matchingEvent = $eventMap[$deviceId] ?? null;

            $updatedAt = 'N/A';

            if (
                $matchingEvent &&
                isset($matchingEvent['updatedAt']) &&
                $matchingEvent['updatedAt'] instanceof \MongoDB\BSON\UTCDateTime
            ) {
                $updatedAt = $matchingEvent['updatedAt']
                    ->toDateTime()
                    ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
                    ->format('d-m-Y H:i:s');
            }

            $site->updatedAt = $updatedAt;
        }    

        // 🔹 Fetch DG & Controller Statuses
        $statuses = $this->getStatusesForSites($siteData->pluck('id')->toArray());

        foreach ($siteData as $site) {
            $site->dg_status = $statuses[$site->id]['dg_status'] ?? 'OFFLINE';
            $site->controller_status = $statuses[$site->id]['controller_status'] ?? 'OFFLINE';
        }

        $sitejsonData = json_decode($siteData->first()->data ?? '{}', true);

        $pdf = \PDF::loadView('pdf.site_report', compact('siteData','sitejsonData','eventData','latestCreatedAt'))
                ->setPaper('a4', 'landscape');

        $fileName = 'DGMS_Site_Overview_' . now()->format('Y-m-d') . '.pdf';
    
        $pdfPath = public_path("daily-pdf/{$fileName}");
            if (!file_exists(public_path('daily-pdf'))) {
                mkdir(public_path('daily-pdf'), 0777, true);
            }
            $pdf->save($pdfPath);

        \Mail::send([], [], function ($message) use ($pdfPath, $fileName) {
            $message->to('agpower.solution20@gmail.com')
                    ->subject('DGMS Site Overview Report - ' . now()->format('d M Y'))
                    ->attach($pdfPath, ['as' => $fileName, 'mime' => 'application/pdf']);
        });

        $this->info('Report sent successfully!');
    }

    private function getStatusesForSites($siteIds)
    {
        $sites = Site::whereIn('id', $siteIds)->select('id', 'device_id', 'clusterID')->get();

        $httpClient = new \GuzzleHttp\Client();
        $dgRequests = [];
        $controllerRequests = [];
        $dgResults = [];
        $controllerResults = [];

        foreach ($sites as $site) {
            if ($site->device_id) {
                $dgRequests[$site->id] = new \GuzzleHttp\Psr7\Request(
                    'GET',
                    "http://app.sochiot.com/api/config-engine/device/status/uuid/{$site->device_id}"
                );
            }
            if ($site->clusterID) {
                $controllerRequests[$site->id] = new \GuzzleHttp\Psr7\Request(
                    'GET',
                    "http://app.sochiot.com/api/config-engine/gateway/status/uuid/{$site->clusterID}"
                );
            }
        }

        $dgPool = new \GuzzleHttp\Pool($httpClient, $dgRequests, [
            'concurrency' => 10,
            'fulfilled' => function ($response, $siteId) use (&$dgResults) {
                $dgResults[$siteId] = strtoupper(trim($response->getBody()->getContents()));
            },
            'rejected' => function () {}
        ]);

        $ctrlPool = new \GuzzleHttp\Pool($httpClient, $controllerRequests, [
            'concurrency' => 10,
            'fulfilled' => function ($response, $siteId) use (&$controllerResults) {
                $controllerResults[$siteId] = strtoupper(trim($response->getBody()->getContents()));
            },
            'rejected' => function () {}
        ]);

        $dgPool->promise()->wait();
        $ctrlPool->promise()->wait();

        $statuses = [];
        foreach ($siteIds as $siteId) {
            $statuses[$siteId] = [
                'dg_status' => $dgResults[$siteId] ?? 'OFFLINE',
                'controller_status' => $controllerResults[$siteId] ?? 'OFFLINE',
            ];
        }

        return $statuses;
    }

    private function extractMdFields($data)
    {
        $mdFields = [];

        array_walk_recursive($data, function ($value, $key) use (&$mdFields) {
            if ($key === 'md' && !is_null($value)) {
                $mdFields[] = $value;
            }
        });

        return $mdFields;
    }

    // public function handle()
    // {
    //     $request = \Illuminate\Http\Request::create('/admin/sites', 'GET');

    //     $controller = new \App\Http\Controllers\Backend\SiteController();

    //     $response = $controller->AdminSites($request);

    //     if ($response instanceof \Illuminate\View\View) {
    //         $siteData = $response->getData();
    //     } elseif ($response instanceof \Illuminate\Http\JsonResponse) {
    //         $siteData = $response->getData(true);
    //     } elseif ($response instanceof \Illuminate\Http\RedirectResponse) {
    //         // Redirected (probably due to login/session issue)
    //         $siteData = ['error' => 'Redirected to login. Please check authentication.'];
    //     } else {
    //         $siteData = [];
    //     }


    //     $pdf = \PDF::loadView('pdf.site_report', compact('siteData'))->setPaper('a4', 'landscape');
    //     $fileName = 'DGMS_Site_Overview_' . now()->format('Y-m-d') . '.pdf';
    //     $pdfPath = public_path("daily-pdf/{$fileName}");
    //     if (!file_exists(public_path('daily-pdf'))) {
    //         mkdir(public_path('daily-pdf'), 0777, true);
    //     }
    //     $pdf->save($pdfPath);

    //     // Send Email
    //     \Mail::send([], [], function ($message) use ($pdfPath, $fileName) {
    //         $message->to('Amarsinghnikumbh@gmail.com')
    //                 ->subject('DGMS Site Overview Report - ' . now()->format('d M Y'))
    //                 ->attach($pdfPath, [
    //                     'as' => $fileName,
    //                     'mime' => 'application/pdf',
    //                 ]);
    //     });

    //     $this->info('Report sent successfully!');
    // }
}
