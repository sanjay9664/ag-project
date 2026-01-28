<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request as GuzzleRequest;

class SiteStatusController extends Controller
{
    public function statuses()
    {
        $sites = Site::select('id', 'device_id', 'clusterID')->get();

        $client = new Client([
            'timeout' => 5,
        ]);

        $dgRequests = [];
        $ctrlRequests = [];

        foreach ($sites as $site) {
            if ($site->device_id) {
                $dgRequests[$site->id] =
                    new GuzzleRequest('GET',
                        "http://app.sochiot.com/api/config-engine/device/status/uuid/{$site->device_id}");
            }

            if ($site->clusterID) {
                $ctrlRequests[$site->id] =
                    new GuzzleRequest('GET',
                        "http://app.sochiot.com/api/config-engine/gateway/status/uuid/{$site->clusterID}");
            }
        }

        $dgResults = [];
        $ctrlResults = [];

        $dgPool = new Pool($client, $dgRequests, [
            'concurrency' => 15,
            'fulfilled' => function ($response, $siteId) use (&$dgResults) {
                $dgResults[$siteId] = strtoupper(trim($response->getBody()->getContents()));
            },
            'rejected' => function () {}
        ]);

        $ctrlPool = new Pool($client, $ctrlRequests, [
            'concurrency' => 15,
            'fulfilled' => function ($response, $siteId) use (&$ctrlResults) {
                $ctrlResults[$siteId] = strtoupper(trim($response->getBody()->getContents()));
            },
            'rejected' => function () {}
        ]);

        $dgPool->promise()->wait();
        $ctrlPool->promise()->wait();

        $statuses = [];

        foreach ($sites as $site) {
            $statuses[$site->id] = [
                'dg_status' => $dgResults[$site->id] ?? 'OFFLINE',
                'controller_status' => $ctrlResults[$site->id] ?? 'OFFLINE',
            ];
        }

        return response()->json($statuses);
    }
}
