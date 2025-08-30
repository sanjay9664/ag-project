<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use MongoDB\Client;
use Illuminate\Support\Facades\DB;
use App\Models\MongodbData;
use App\Models\Site;
use Carbon\Carbon;

class SyncMongoToSql extends Command
{
    protected $signature = 'sync:mongodb';
    protected $description = 'Sync MongoDB event data into SQL table';

    public function handle()
    {
        $this->info("Starting MongoDB to SQL sync...");

        $client = new Client('mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa');
        $collection = $client->isa_qa->device_events;

        $sites = DB::table('sites')
            ->leftJoin('admins', 'sites.email', '=', 'admins.email')
            ->select(
                'sites.id',
                'sites.site_name',
                'sites.slug',
                'sites.email',
                'sites.data',
                'sites.increase_running_hours_status',
                'admins.id as admin_id',
                'admins.name as admin_name'
            )
            ->groupBy(
                'sites.id',
                'sites.email',
                'sites.site_name',
                'sites.slug',
                'sites.data',
                'sites.increase_running_hours_status',
                'admins.email',
                'admins.id',
                'admins.name'
            )
            ->get();

        foreach ($sites as $site) {
            $data = json_decode($site->data, true);

            if (empty($data)) {
                $this->warn("No 'data' field for site: {$site->site_name}");
                continue;
            }

            $mdValues = $this->extractMdFields($data);
            $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

            foreach ($uniqueMdValues as $moduleId) {
                $event = $collection->findOne(
                    ['module_id' => $moduleId],
                    ['sort' => ['createdAt' => -1]]
                );

                if ($event) {
                    $eventArray = json_decode(json_encode($event), true);
                    $eventArray['admin_id'] = $site->id;

                    $deviceId = $eventArray['device_id'] ?? null;
                    $moduleId = $eventArray['module_id'] ?? null;

                    if ($deviceId && $moduleId) {
                        DB::transaction(function () use ($site, $deviceId, $moduleId, $eventArray) {
                            $records = MongodbData::where('site_id', $site->id)->get();

                            $toUpdate = $records->filter(function ($item) use ($deviceId, $moduleId) {
                                $json = json_decode($item->data, true);

                                return !empty($json['device_id']) 
                                    && !empty($json['module_id']) 
                                    && $json['device_id'] == $deviceId 
                                    && $json['module_id'] == $moduleId;
                            });

                            if ($toUpdate->isNotEmpty()) {
                                foreach ($toUpdate as $row) {
                                    $row->update([
                                        'data' => json_encode($eventArray),
                                        'updated_at' => now(),
                                    ]);
                                }

                                echo "Updated {$toUpdate->count()} existing rows for site '{$site->site_name}' - device {$deviceId}, module {$moduleId}\n";
                            } else {
                                MongodbData::create([
                                    'site_id' => $site->id,
                                    'data' => json_encode($eventArray),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);

                                echo "Inserted fresh data for site '{$site->site_name}' - device {$deviceId}, module {$moduleId}\n";
                            }
                        });
                    } else {
                        $this->warn("Skipped site '{$site->site_name}' because device_id/module_id missing in event");
                    }
                }
            }
        }

        $this->info('MongoDB data sync completed successfully.');
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
