<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Login;
use App\Models\Site;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use MongoDB\Client as MongoClient;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $this->checkAuthorization(auth()->user(), ['dashboard.view']);
        
        $adminEmail = auth()->user()->email;
        $total_sites = Site::where('email', $adminEmail)->count();

        $logins = DB::table('sites')
            ->leftJoin('admins', 'sites.email', '=', 'admins.email') 
            ->leftJoin('logins', 'admins.id', '=', 'logins.user_id') 
            ->select(
                'sites.*', 
                'admins.name', 
                'admins.email', 
                'logins.*',
                'sites.id AS site_id'
            )
            ->get();

        foreach ($logins as $login) {
            if (!empty($login->created_at)) {
                $login->created_at = Carbon::parse($login->created_at);
            }
        }

        // Fetch site details with associated admins
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

        $events = [];

        foreach ($sites as $site) {
            $data = json_decode($site->data, true);

            if (!empty($data)) {
                $mdValues = $this->extractMdFields($data);

                $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
                $client = new \MongoDB\Client($mongoUri);
                $database = $client->isa_qa;
                $collection = $database->device_events;

                $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

                if (!empty($uniqueMdValues)) {
                    foreach ($uniqueMdValues as $moduleId) {
                        $event = $collection->findOne(
                            ['module_id' => $moduleId],
                            ['sort' => ['createdAt' => -1]]
                        );

                        if ($event) {
                            $eventData = (array) $event;
                            $eventData['admin_id'] = $site->id;
                            $events[] = $eventData;
                        }
                    }
                }
            }
        }

        // return $events;

        return view('backend.pages.dashboard.index', [
            'total_admins' => Admin::count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'logins' => $logins,
            'sites' => $sites,
            'total_sites' => $total_sites,
            'events' => $events
        ]);
    }

    private function extractMdFields($data)
    {
        $mdFields = [];
        array_walk_recursive($data, function($value, $key) use (&$mdFields) {
            if ($key === 'md' && !is_null($value)) {
                $mdFields[] = $value;
            }
        });

        return $mdFields;
    }

    public function savedashboarddata(Request $request)
    {
        $request->validate([
            'running_hours_admin' => 'nullable|string|max:255',
            'increase_running_hours' => 'nullable|numeric',
            'site_id' => 'required|integer',
        ]);
    
        $existingData = DB::table('running_hours')->where('site_id', $request->site_id)->first();
    
        $newRunningHours = $request->increase_running_hours;
        // dd($request->all());
        if ($existingData) {
            $newRunningHours += $existingData->increase_running_hours;
    
            DB::table('running_hours')->where('site_id', $request->site_id)->update([
                'increase_running_hours' => $newRunningHours,
                'updated_at' => now()
            ]);
        } else {
            DB::table('running_hours')->insert([
                'site_id' => $request->site_id,
                'increase_running_hours' => $newRunningHours,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    
        return response()->json(['success' => true, 'message' => 'Data saved successfully!']);
    }

    public function apiIndex()
    {
        // Remove dependency on auth()
        $total_sites = Site::count();

        $logins = DB::table('sites')
            ->leftJoin('admins', 'sites.email', '=', 'admins.email') 
            ->leftJoin('logins', 'admins.id', '=', 'logins.user_id') 
            ->select(
                'sites.*', 
                'admins.name', 
                'admins.email', 
                'logins.*',
                'sites.id AS site_id'
            )
            ->get();

        foreach ($logins as $login) {
            if (!empty($login->created_at)) {
                $login->created_at = Carbon::parse($login->created_at);
            }
        }

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

        $events = [];

        foreach ($sites as $site) {
            $data = json_decode($site->data, true);

            if (!empty($data)) {
                $mdValues = $this->extractMdFields($data);

                $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
                $client = new \MongoDB\Client($mongoUri);
                $database = $client->isa_qa;
                $collection = $database->device_events;

                $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

                if (!empty($uniqueMdValues)) {
                    foreach ($uniqueMdValues as $moduleId) {
                        $event = $collection->findOne(
                            ['module_id' => $moduleId],
                            ['sort' => ['createdAt' => -1]]
                        );

                        if ($event) {
                            $eventData = (array) $event;
                            $eventData['admin_id'] = $site->id;
                            $events[] = $eventData;
                        }
                    }
                }
            }
        }

        // Return as JSON response instead of view for API
        return response()->json([
            'total_admins' => Admin::count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
            'logins' => $logins,
            'sites' => $sites,
            'total_sites' => $total_sites,
            'events' => $events
        ]);
    }

    public function apiFetchDeviceStatus(): JsonResponse
    {
        $logins = DB::table('sites')
            ->leftJoin('admins', 'sites.email', '=', 'admins.email') 
            ->leftJoin('logins', 'admins.id', '=', 'logins.user_id') 
            ->select(
                'sites.id',
                'sites.site_name',
                'sites.slug',
                'sites.email',
                'sites.device_id',
                'sites.data'
            )
            ->get();

        $filteredData = [];

        // MongoDB connection
        $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
        $client = new MongoClient($mongoUri);
        $collection = $client->isa_qa->device_events;

        foreach ($logins as $login) {
            $data = json_decode($login->data, true);

            $battery_voltage_md = $data['parameters']['battery_voltage']['md'] ?? null;
            $battery_voltage_add = $data['parameters']['battery_voltage']['add'] ?? null;
            $voltage_l_l_a_md = $data['electric_parameters']['voltage_l_l']['a']['md'] ?? null;
            $voltage_l_l_a_add = $data['electric_parameters']['voltage_l_l']['a']['add'] ?? null;

            $deviceId = $login->device_id;

            $batteryStatus = 'unknown';

            if ($battery_voltage_md && $battery_voltage_add && $deviceId) {
                $latestEvent = $collection->findOne(
                    [
                        'module_id' => (int) $battery_voltage_md,
                        'device_id' => $deviceId
                    ],
                    ['sort' => ['createdAt' => -1]]
                );

                if ($latestEvent && isset($latestEvent[$battery_voltage_add])) {
                    $value = $latestEvent[$battery_voltage_add];

                   if ($value == 10) {
                        $batteryStatus = 'normal';
                    } elseif ($value > 14) {
                        $batteryStatus = "high";
                        $batteryMessage = "DG Battery voltage for Site ID {$login->id} is greater than normal voltage. DG Battery voltage: {$value}";
                    } elseif ($value < 10) {
                        $batteryStatus = "low";
                        $batteryMessage = "DG Battery voltage for Site ID {$login->id} is less than normal voltage. DG Battery voltage: {$value}";
                    } else {
                        $batteryStatus = "normal";
                    }
                }
            }
            
            $deviceStatus = 'unknown';

            if ($voltage_l_l_a_md && $voltage_l_l_a_add && $deviceId) {
                $latestEvent = $collection->findOne(
                    [
                        'module_id' => (int) $voltage_l_l_a_md,
                        'device_id' => $deviceId
                    ],
                    ['sort' => ['createdAt' => -1]]
                );

                if ($latestEvent && isset($latestEvent[$voltage_l_l_a_add])) {
                    $value = $latestEvent[$voltage_l_l_a_add];
                    if ($value == 1) {
                        $deviceStatus = 'on';
                    } elseif ($value == 0) {
                        $deviceStatus = 'off';
                    }
                }
            }

            $filteredData[] = [
                'site_id' => $login->id,
                'site_name' => $login->site_name,
                'slug' => $login->slug,
                'email' => $login->email,
                'device_id' => $deviceId,
                'battery_voltage_md' => $battery_voltage_md,
                'battery_voltage_add' => $battery_voltage_add,
                'voltage_l_l_a_md' => $voltage_l_l_a_md,
                'voltage_l_l_a_add' => $voltage_l_l_a_add,
                'battery_status' => $batteryStatus,
                'device_status' => $deviceStatus
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $filteredData
        ]);
    }
}