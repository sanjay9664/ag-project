<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use App\Models\MongodbFrontend;
use Carbon\Carbon;

class SiteStatusController extends Controller
{
    public function statuses()
    {
        // Fetch sites
        $sites = Site::select('id', 'device_id', 'clusterID')->get();

        // ✅ TOTAL SITE COUNT (ONLY ADDITION)
        $totalSites = $sites->count();

        // STEP 1: Get latest mongo data for all device_ids in ONE query
        $deviceIds = $sites->pluck('device_id')->filter()->toArray();

        $latestMongo = MongodbFrontend::whereIn('data->device_id', $deviceIds)
            ->get()
            ->map(function ($row) {
                $data = $row->data;

                return [
                    'device_id'    => $data['device_id'] ?? null,
                    'generated_at' => $data['generated_at'] ?? 0,
                ];
            })
            ->sortByDesc('generated_at')
            ->unique('device_id')
            ->keyBy('device_id');

        // STEP 2: Hit SochIoT status API in parallel
        $client = new Client(['timeout' => 5]);

        $dgRequests = [];
        $ctrlRequests = [];

        foreach ($sites as $site) {
            if ($site->device_id) {
                $dgRequests[$site->id] = new GuzzleRequest(
                    'GET',
                    "http://app.sochiot.com/api/config-engine/device/status/uuid/{$site->device_id}"
                );
            }

            if ($site->clusterID) {
                $ctrlRequests[$site->id] = new GuzzleRequest(
                    'GET',
                    "http://app.sochiot.com/api/config-engine/gateway/status/uuid/{$site->clusterID}"
                );
            }
        }

        $dgResults = [];
        $ctrlResults = [];

        (new Pool($client, $dgRequests, [
            'concurrency' => 15,
            'fulfilled' => function ($response, $siteId) use (&$dgResults) {
                $dgResults[$siteId] = strtoupper(
                    json_decode($response->getBody(), true) ?? 'OFFLINE'
                );
            },
        ]))->promise()->wait();

        (new Pool($client, $ctrlRequests, [
            'concurrency' => 15,
            'fulfilled' => function ($response, $siteId) use (&$ctrlResults) {
                $ctrlResults[$siteId] = strtoupper(
                    json_decode($response->getBody(), true) ?? 'OFFLINE'
                );
            },
        ]))->promise()->wait();

        // STEP 3: Build final response
        $statuses = [];

        foreach ($sites as $site) {

            $updatedTime = 'N/A';

            if ($site->device_id && isset($latestMongo[$site->device_id])) {
                $millis = $latestMongo[$site->device_id]['generated_at'];

                $updatedTime = Carbon::createFromTimestampMs($millis)
                    ->setTimezone('Asia/Kolkata')
                    ->format('d M Y, h:i:s A');
            }

            $statuses[$site->id] = [
                'dg_status'          => $dgResults[$site->id] ?? 'OFFLINE',
                'controller_status'  => $ctrlResults[$site->id] ?? 'OFFLINE',
                'updated_time'       => $updatedTime,
            ];
        }

        // ✅ FINAL RESPONSE (total_sites + data)
        return response()->json([
            'total_sites' => $totalSites,
            'data'        => $statuses,
        ]);
    }
}
