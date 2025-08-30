<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Site;
use Illuminate\Support\Facades\DB;
use MongoDB\Client as MongoClient;

class SyncFrontendData extends Command
{
    protected $signature = 'sync:frontend-data';
    protected $description = 'Sync latest device_events data into mongodb_frontend_table';

    public function handle()
    {
        $this->info("Starting MongoDB → mongodb_frontend sync...");

        $start = microtime(true);

        $sites = Site::select(['id', 'site_name', 'slug', 'email', 'data', 'device_id', 'clusterID'])->get();

        $mdValues = $this->extractMdFields(
            $sites->pluck('data')->map(fn($data) => json_decode($data, true))->toArray()
        );

        if ($sites->isEmpty()) {
            $this->warn("No sites found.");
            return;
        }

        $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
        $mongoClient = new \MongoDB\Client($mongoUri);
        $collection = $mongoClient->isa_qa->device_events;

        $dateThreshold = new \MongoDB\BSON\UTCDateTime((new \DateTime('-48 hours'))->getTimestamp() * 1000);

        $totalInserted = 0;
        $totalUpdated = 0;

        $moduleLatest = []; 

        foreach ($sites as $site) {
            $siteData = json_decode($site->data, true);
            $mdValues = $this->extractMdFields($siteData);
            $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

            $deviceId = $site->device_id ?? null;

            if (!$deviceId || !$uniqueMdValues) {
                $this->warn("Skipping site '{$site->site_name}' (missing device_id or module_id)");
                continue;
            }

            foreach ($uniqueMdValues as $moduleId) {
                $event = $collection->findOne(
                    [
                        'device_id' => $deviceId,
                        'module_id' => $moduleId,
                        'createdAt' => ['$gte' => $dateThreshold]
                    ],
                    [
                        'sort' => ['createdAt' => -1]
                    ]
                );

                if ($event) {
                    $eventArray = json_decode(json_encode($event), true);
                    if (!isset($moduleLatest[$moduleId]) ||
                        $eventArray['createdAt'] > $moduleLatest[$moduleId]['createdAt']) {
                        $moduleLatest[$moduleId] = $eventArray;
                    }
                }
            }
        }

        $this->info("Collected " . count($moduleLatest) . " latest events across all modules.");

        // 🔹 Sync into MySQL
        foreach ($moduleLatest as $moduleId => $eventArray) {
            DB::transaction(function () use ($sites, $eventArray, $moduleId, &$totalUpdated, &$totalInserted) {
                $deviceId = $eventArray['device_id'];
                $site = $sites->firstWhere('device_id', $deviceId);

                $updatedCount = DB::table('mongodb_frontend')
                    ->where('site_id', $site->id)
                    ->whereJsonContains('data->device_id', $deviceId)
                    ->where(function ($q) use ($moduleId) {
                        $q->whereJsonContains('data->module_id', (int)$moduleId)
                        ->orWhereJsonContains('data->module_id', (string)$moduleId);
                    })
                    ->update([
                        'data'       => json_encode($eventArray),
                        'updated_at' => now(),
                    ]);

                if ($updatedCount > 0) {
                    $totalUpdated += $updatedCount;
                } else {
                    DB::table('mongodb_frontend')->insert([
                        'site_id'    => $site->id,
                        'data'       => json_encode($eventArray),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $totalInserted++;
                }
            });
        }
        
        $duration = microtime(true) - $start;
        $this->info("Sync complete. Inserted {$totalInserted}, updated {$totalUpdated} in {$duration} seconds.");
    }

    private function extractMdFields($data)
    {
        $mdFields = [];

        array_walk_recursive($data, function ($value, $key) use (&$mdFields) {
            if (strtolower($key) === 'md' && !is_null($value)) {
                $mdFields[] = $value;
            }
        });

        return $mdFields;
    }
}