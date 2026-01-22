<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Carbon\Carbon;

class SiteOverviewController extends Controller
{
    public function sendapi()
    {
        $sites = Site::select(
            'id',
            'site_name',
            'device_id',
            'clusterID',
            'data',
            'updated_at'
        )->get();

        $now = Carbon::now('Asia/Kolkata');
        $response = [];

        foreach ($sites as $site) {

            // Last updated time (IST)
            $updatedAt = $site->updated_at
                ? Carbon::parse($site->updated_at)->timezone('Asia/Kolkata')
                : null;

            // ONLINE / OFFLINE logic
            $isOnline = $updatedAt
                ? $updatedAt->diffInHours($now) < 24
                : false;

            $response[] = [
                'id'            => $site->id,
                'name'          => $site->site_name,
                'device_id'     => $site->device_id,
                'cluster_id'    => $site->clusterID,

                // ✅ THIS IS WHAT YOU WANT
                'status'        => $isOnline ? 'ONLINE' : 'OFFLINE',

                // Optional (UI / debug ke liye)
                'last_updated'  => $updatedAt
                    ? $updatedAt->format('Y-m-d H:i:s')
                    : 'N/A',
            ];
        }

        return response()->json([
            'summary' => [
                'total' => count($response),
                'online' => collect($response)->where('status', 'ONLINE')->count(),
                'offline' => collect($response)->where('status', 'OFFLINE')->count(),
            ],
            'data' => $response
        ], 200);
    }
}
