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
use MongoDB\Client;
// use MongoDB\Client;
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

    // public function apiFetchDeviceStatus(): JsonResponse
    // {
    //     $logins = DB::table('sites')
    //         ->leftJoin('admins', 'sites.email', '=', 'admins.email') 
    //         ->leftJoin('logins', 'admins.id', '=', 'logins.user_id') 
    //         ->select(
    //             'sites.id',
    //             'sites.site_name',
    //             'sites.slug',
    //             'sites.email',
    //             'sites.device_id',
    //             'sites.data'
    //         )
    //         ->get();

    //     $filteredData = [];

    //     // MongoDB connection
    //     $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
    //     $client = new MongoClient($mongoUri);
    //     $collection = $client->isa_qa->device_events;

    //     foreach ($logins as $login) {
    //         $data = json_decode($login->data, true);

    //         $battery_voltage_md = $data['parameters']['battery_voltage']['md'] ?? null;
    //         $battery_voltage_add = $data['parameters']['battery_voltage']['add'] ?? null;
    //         $voltage_l_l_a_md = $data['electric_parameters']['voltage_l_l']['a']['md'] ?? null;
    //         $voltage_l_l_a_add = $data['electric_parameters']['voltage_l_l']['a']['add'] ?? null;

    //         $deviceId = $login->device_id;

    //         $batteryStatus = 'unknown';

    //         if ($battery_voltage_md && $battery_voltage_add && $deviceId) {
    //             $latestEvent = $collection->findOne(
    //                 [
    //                     'module_id' => (int) $battery_voltage_md,
    //                     'device_id' => $deviceId
    //                 ],
    //                 ['sort' => ['createdAt' => -1]]
    //             );

    //             if ($latestEvent && isset($latestEvent[$battery_voltage_add])) {
    //                 $value = $latestEvent[$battery_voltage_add];

    //                if ($value == 10) {
    //                     $batteryStatus = 'normal';
    //                 } elseif ($value > 14) {
    //                     $batteryStatus = "high";
    //                     $batteryMessage = "DG Battery voltage for Site ID {$login->id} is greater than normal voltage. DG Battery voltage: {$value}";
    //                 } elseif ($value < 10) {
    //                     $batteryStatus = "low";
    //                     $batteryMessage = "DG Battery voltage for Site ID {$login->id} is less than normal voltage. DG Battery voltage: {$value}";
    //                 } else {
    //                     $batteryStatus = "normal";
    //                 }
    //             }
    //         }
            
            // $deviceStatus = 'unknown';

            // if ($voltage_l_l_a_md && $voltage_l_l_a_add && $deviceId) {
            //     $latestEvent = $collection->findOne(
            //         [
            //             'module_id' => (int) $voltage_l_l_a_md,
            //             'device_id' => $deviceId
            //         ],
            //         ['sort' => ['createdAt' => -1]]
            //     );

            //     if ($latestEvent && isset($latestEvent[$voltage_l_l_a_add])) {
            //         $value = $latestEvent[$voltage_l_l_a_add];
            //         if ($value == 1) {
            //             $deviceStatus = 'on';
            //         } elseif ($value == 0) {
            //             $deviceStatus = 'off';
            //         }
            //     }
            // }

    //         $filteredData[] = [
    //             'site_id' => $login->id,
    //             'site_name' => $login->site_name,
    //             'slug' => $login->slug,
    //             'email' => $login->email,
    //             'device_id' => $deviceId,
    //             'battery_voltage_md' => $battery_voltage_md,
    //             'battery_voltage_add' => $battery_voltage_add,
    //             'voltage_l_l_a_md' => $voltage_l_l_a_md,
    //             'voltage_l_l_a_add' => $voltage_l_l_a_add,
    //             'battery_status' => $batteryStatus,
    //             'device_status' => $deviceStatus
    //         ];
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'data' => $filteredData
    //     ]);
    // }


//  public function apiFetchDeviceStatus(): JsonResponse
// {
//     $sites = DB::table('sites')
//         ->leftJoin('admins', 'sites.email', '=', 'admins.email') 
//         ->leftJoin('logins', 'admins.id', '=', 'logins.user_id') 
//         ->select(
//             'sites.id as siteId',
//             'sites.site_name as deviceName',
//             'sites.slug',
//             'sites.email as userEmail',
//             'sites.device_id as deviceId',
//             'sites.data',
//             'sites.created_at',
//             'sites.updated_at'
//         )
//         ->get();

//     $filteredData = [];

//     // MongoDB connection
//     $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
//     $client = new MongoClient($mongoUri);
//     $collection = $client->isa_qa->device_events;

//     foreach ($sites as $site) {
//         $data = json_decode($site->data, true);

//         $battery_voltage_md = $data['parameters']['battery_voltage']['md'] ?? null;
//         $battery_voltage_add = $data['parameters']['battery_voltage']['add'] ?? null;
//         $voltage_l_l_a_md = $data['electric_parameters']['voltage_l_l']['a']['md'] ?? null;
//         $voltage_l_l_a_add = $data['electric_parameters']['voltage_l_l']['a']['add'] ?? null;

//         // Get limits from data if available
//         $lowerLimit = $data['parameters']['battery_voltage']['limits']['lower'] ?? null;
//         $upperLimit = $data['parameters']['battery_voltage']['limits']['upper'] ?? null;
//         $lowerLimitMsg = $data['parameters']['battery_voltage']['messages']['lower'] ?? null;
//         $upperLimitMsg = $data['parameters']['battery_voltage']['messages']['upper'] ?? null;

//         $batteryStatus = 'unknown';
//         $batteryValue = null;

//         if ($battery_voltage_md && $battery_voltage_add && $site->deviceId) {
//             $latestEvent = $collection->findOne(
//                 [
//                     'module_id' => (int) $battery_voltage_md,
//                     'device_id' => $site->deviceId
//                 ],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestEvent && isset($latestEvent[$battery_voltage_add])) {
//                 $batteryValue = $latestEvent[$battery_voltage_add];
                
//                 if ($batteryValue == 10) {
//                     $batteryStatus = 'normal';
//                 } elseif ($batteryValue > 14) {
//                     $batteryStatus = "high";
//                 } elseif ($batteryValue < 10) {
//                     $batteryStatus = "low";
//                 } else {
//                     $batteryStatus = "normal";
//                 }
//             }
//         }
        
//         $deviceStatus = 'unknown';
//         $deviceValue = null;

//         if ($voltage_l_l_a_md && $voltage_l_l_a_add && $site->deviceId) {
//             $latestEvent = $collection->findOne(
//                 [
//                     'module_id' => (int) $voltage_l_l_a_md,
//                     'device_id' => $site->deviceId
//                 ],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestEvent && isset($latestEvent[$voltage_l_l_a_add])) {
//                 $deviceValue = $latestEvent[$voltage_l_l_a_add];
//                 $deviceStatus = $deviceValue == 1 ? 'on' : 'off';
//             }
//         }

//         $filteredData[] = [
//             'deviceName' => $site->deviceName,
//             'deviceId' => $site->deviceId,
//             'moduleId' => $battery_voltage_md,
//             'eventField' => $battery_voltage_add,
//             'siteId' => $site->siteId,
//             'lowerLimit' => $lowerLimit,
//             'upperLimit' => $upperLimit,
//             'lowerLimitMsg' => $lowerLimitMsg,
//             'upperLimitMsg' => $upperLimitMsg,
//             'userEmail' => $site->userEmail,
//             'batteryValue' => $batteryValue,
//             'batteryStatus' => $batteryStatus,
//             'deviceValue' => $deviceValue,
//             'deviceStatus' => $deviceStatus,
//             'created_at' => $site->created_at,
//             'updated_at' => $site->updated_at
//         ];
//     }

//     return response()->json([
//         'status' => true,
//         'data' => $filteredData
//     ]);
// }
 
//  public function apiFetchDeviceStatus(): JsonResponse 
// {
//     $deviceEvents = DB::table('device_events')
//         ->leftJoin('admins', 'device_events.userEmail', '=', 'admins.email')
//         ->leftJoin('logins', 'admins.id', '=', 'logins.user_id')
//         ->select(
//             'device_events.id as siteId',
//             'device_events.deviceName',
//             'device_events.deviceId',
//             'device_events.eventField',
//             'device_events.userEmail',
//             'device_events.created_at',
//             'device_events.updated_at'
//         )
//         ->get();

//     $filteredData = [];

//     // MongoDB connection
//     $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
//     $client = new MongoDB\Client($mongoUri);
//     $collection = $client->isa_qa->device_events;

//     foreach ($deviceEvents as $event) {
//         $data = json_decode($event->data, true);

//         $battery_voltage_md = $data['parameters']['battery_voltage']['md'] ?? null;
//         $battery_voltage_add = $data['parameters']['battery_voltage']['add'] ?? null;
//         $voltage_l_l_a_md = $data['electric_parameters']['voltage_l_l']['a']['md'] ?? null;
//         $voltage_l_l_a_add = $data['electric_parameters']['voltage_l_l']['a']['add'] ?? null;

//         // Limits & messages
//         $lowerLimit = $data['parameters']['battery_voltage']['limits']['lower'] ?? null;
//         $upperLimit = $data['parameters']['battery_voltage']['limits']['upper'] ?? null;
//         $lowerLimitMsg = $data['parameters']['battery_voltage']['messages']['lower'] ?? null;
//         $upperLimitMsg = $data['parameters']['battery_voltage']['messages']['upper'] ?? null;

//         $batteryValue = null;
//         $batteryStatus = 'unknown';

//         if ($battery_voltage_md && $battery_voltage_add && $event->deviceId) {
//             $latestEvent = $collection->findOne(
//                 [
//                     'module_id' => (int) $battery_voltage_md,
//                     'device_id' => $event->deviceId
//                 ],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestEvent && isset($latestEvent[$battery_voltage_add])) {
//                 $batteryValue = $latestEvent[$battery_voltage_add];

//                 if ($batteryValue == 10) {
//                     $batteryStatus = 'normal';
//                 } elseif ($batteryValue > 14) {
//                     $batteryStatus = "high";
//                 } elseif ($batteryValue < 10) {
//                     $batteryStatus = "low";
//                 } else {
//                     $batteryStatus = "normal";
//                 }
//             }
//         }

//         $deviceValue = null;
//         $deviceStatus = 'unknown';

//         if ($voltage_l_l_a_md && $voltage_l_l_a_add && $event->deviceId) {
//             $latestEvent = $collection->findOne(
//                 [
//                     'module_id' => (int) $voltage_l_l_a_md,
//                     'device_id' => $event->deviceId
//                 ],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestEvent && isset($latestEvent[$voltage_l_l_a_add])) {
//                 $deviceValue = $latestEvent[$voltage_l_l_a_add];
//                 $deviceStatus = $deviceValue == 1 ? 'on' : 'off';
//             }
//         }

//         $filteredData[] = [
//             'deviceName' => $event->deviceName,
//             'deviceId' => $event->deviceId,
//             'moduleId' => $battery_voltage_md,
//             'eventField' => $battery_voltage_add,
//             'siteId' => $event->siteId,
//             'lowerLimit' => $lowerLimit,
//             'upperLimit' => $upperLimit,
//             'lowerLimitMsg' => $lowerLimitMsg,
//             'upperLimitMsg' => $upperLimitMsg,
//             'userEmail' => $event->userEmail,
//             'batteryValue' => $batteryValue,
//             'batteryStatus' => $batteryStatus,
//             'deviceValue' => $deviceValue,
//             'deviceStatus' => $deviceStatus,
//             'created_at' => $event->created_at,
//             'updated_at' => $event->updated_at
//         ];
//     }

//     return response()->json([
//         'status' => true,
//         'data' => $filteredData
//     ]);
// }


// comming to the data 
// public function apiFetchDeviceStatus(): JsonResponse
// {
//     $deviceEvents = DB::table('device_events')
//         ->leftJoin('admins', 'device_events.userEmail', '=', 'admins.email')
//         ->leftJoin('logins', 'admins.id', '=', 'logins.user_id')
//         ->select(
//             'device_events.id as siteId',
//             'device_events.deviceName',
//             'device_events.deviceId',
//             'device_events.eventField',
//             'device_events.userEmail',
//             'device_events.created_at',
//             'device_events.updated_at',
//             'device_events.siteId'
//         )
//         ->get();

//     $filteredData = [];

//     // ✅ MongoDB connection
//     $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
//     $client = new MongoClient($mongoUri);
//     $collection = $client->isa_qa->device_events;

//     foreach ($deviceEvents as $event) {
//         $data = json_decode($event->siteId, true);

//         // Safe checks
//         $battery_voltage_md = $data['parameters']['battery_voltage']['md'] ?? null;
//         $battery_voltage_add = $data['parameters']['battery_voltage']['add'] ?? null;
//         $voltage_l_l_a_md = $data['electric_parameters']['voltage_l_l']['a']['md'] ?? null;
//         $voltage_l_l_a_add = $data['electric_parameters']['voltage_l_l']['a']['add'] ?? null;

//         $lowerLimit = $data['parameters']['battery_voltage']['limits']['lower'] ?? null;
//         $upperLimit = $data['parameters']['battery_voltage']['limits']['upper'] ?? null;
//         $lowerLimitMsg = $data['parameters']['battery_voltage']['messages']['lower'] ?? null;
//         $upperLimitMsg = $data['parameters']['battery_voltage']['messages']['upper'] ?? null;

//         $batteryValue = null;
//         $batteryStatus = 'unknown';

//         if ($battery_voltage_md && $battery_voltage_add && $event->deviceId) {
//             $latestEvent = $collection->findOne(
//                 [
//                     'module_id' => (int) $battery_voltage_md,
//                     'device_id' => $event->deviceId
//                 ],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestEvent && isset($latestEvent[$battery_voltage_add])) {
//                 $batteryValue = $latestEvent[$battery_voltage_add];
//                 $batteryStatus = ($batteryValue == 10) ? 'normal' :
//                                  (($batteryValue > 14) ? 'high' :
//                                  (($batteryValue < 10) ? 'low' : 'normal'));
//             }
//         }

//         $deviceValue = null;
//         $deviceStatus = 'unknown';

//         if ($voltage_l_l_a_md && $voltage_l_l_a_add && $event->deviceId) {
//             $latestEvent = $collection->findOne(
//                 [
//                     'module_id' => (int) $voltage_l_l_a_md,
//                     'device_id' => $event->deviceId
//                 ],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestEvent && isset($latestEvent[$voltage_l_l_a_add])) {
//                 $deviceValue = $latestEvent[$voltage_l_l_a_add];
//                 $deviceStatus = $deviceValue == 1 ? 'on' : 'off';
//             }
//         }

//         $filteredData[] = [
//             'deviceName' => $event->deviceName,
//             'deviceId' => $event->deviceId,
//             'moduleId' => $battery_voltage_md,
//             'eventField' => $battery_voltage_add,
//             'siteId' => $event->siteId,
//             'lowerLimit' => $lowerLimit,
//             'upperLimit' => $upperLimit,
//             'lowerLimitMsg' => $lowerLimitMsg,
//             'upperLimitMsg' => $upperLimitMsg,
//             'userEmail' => $event->userEmail,
//             'batteryValue' => $batteryValue,
//             'batteryStatus' => $batteryStatus,
//             'deviceValue' => $deviceValue,
//             'deviceStatus' => $deviceStatus,
//             'created_at' => $event->created_at,
//             'updated_at' => $event->updated_at
//         ];
//     }

//     return response()->json([
//         'status' => true,
//         'data' => $filteredData
//     ]);
// }



// public function apiFetchDeviceStatus(): JsonResponse
// {
//     $deviceEvents = DB::table('device_events')
//         ->leftJoin('admins', 'device_events.userEmail', '=', 'admins.email')
//         ->leftJoin('logins', 'admins.id', '=', 'logins.user_id')
//         ->select(
//             'device_events.id as siteId',
//             'device_events.deviceName',
//             'device_events.deviceId',
//             'device_events.moduleId',
//             'device_events.eventField',
//             'device_events.siteId as actualSiteId',
//             'device_events.lowerLimit',
//             'device_events.upperLimit',
//             'device_events.lowerLimitMsg',
//             'device_events.upperLimitMsg',
//             'device_events.userEmail',
//             'device_events.created_at',
//             'device_events.updated_at'
//         )
//         ->get();

//     $filteredData = [];

//     // ✅ MongoDB connection
//     $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
//     $client = new MongoClient($mongoUri);
//     $collection = $client->isa_qa->device_events;

//     foreach ($deviceEvents as $event) {
//         $batteryValue = null;
//         $batteryStatus = 'unknown';

//        if ($event->moduleId && $event->eventField && $event->deviceId) {
//     $latestEvent = $collection->findOne(
//         [
//             'module_id' => (int) $event->moduleId,
//             'device_id' => $event->deviceId
//         ],
//         ['sort' => ['createdAt' => -1]]
//     );

//     if ($latestEvent) {
//         // ✅ Battery Value
//         if (isset($latestEvent[$event->eventField])) {
//             $batteryValue = $latestEvent[$event->eventField];
//             $batteryStatus = ($batteryValue == 10) ? 'normal' :
//                              (($batteryValue > 14) ? 'high' :
//                              (($batteryValue < 10) ? 'low' : 'normal'));
//         }

//         // ✅ Device Value (check for running_status or voltage field)
//         if (isset($latestEvent['running_status'])) {
//             $deviceValue = $latestEvent['running_status'];
//             $deviceStatus = $deviceValue == 1 ? 'on' : 'off';
//         } elseif (isset($latestEvent['voltage_l_l.a'])) {
//             $deviceValue = $latestEvent['voltage_l_l.a'];
//             $deviceStatus = $deviceValue > 0 ? 'on' : 'off';
//         }
//     }
// }


//         $filteredData[] = [
//             'deviceName' => $event->deviceName,
//             'deviceId' => $event->deviceId,
//             'moduleId' => $event->moduleId,
//             'eventField' => $event->eventField,
//             'siteId' => $event->actualSiteId,
            // 'lowerLimit' => $event->lowerLimit,
            // 'upperLimit' => $event->upperLimit,
            // 'lowerLimitMsg' => $event->lowerLimitMsg,
            // 'upperLimitMsg' => $event->upperLimitMsg,
//             'userEmail' => $event->userEmail,
//             'batteryValue' => $batteryValue,
//             'batteryStatus' => $batteryStatus,
//             // 'deviceValue' => null, // if you have another field to check status, add logic here
//             'deviceStatus' => 'unknown', // as above
//             'created_at' => $event->created_at,
//             'updated_at' => $event->updated_at
//         ];
//     }

//     return response()->json([
//         'status' => true,
//         'data' => $filteredData
//     ]);
// }

// *******************************************************
// public function apiFetchDeviceStatus()
// {
//     // Helper function to safely get nested keys from an array
//     function array_get_nested($array, $path, $default = null) {
//         $keys = explode('.', $path);
//         foreach ($keys as $key) {
//             if (is_array($array) && array_key_exists($key, $array)) {
//                 $array = $array[$key];
//             } else {
//                 return $default;
//             }
//         }
//         return $array;
//     }

//     $deviceEvents = DB::table('device_events')
//         ->leftJoin('admins', 'device_events.userEmail', '=', 'admins.email')
//         ->leftJoin('sites', 'device_events.siteId', '=', 'sites.id')  // join site table
//         ->select(
//             'device_events.id as siteId',
//             'device_events.deviceName',
//             'device_events.deviceId',
//             'device_events.moduleId',
//             'device_events.eventField',
//             'device_events.lowerLimit',
//             'device_events.upperLimit',
//             'device_events.lowerLimitMsg',
//             'device_events.userEmail',
//             'device_events.upperLimitMsg',
//             'device_events.siteId as actualSiteId',
//             'sites.data',      // JSON data from site table
//             'device_events.created_at',
//             'device_events.updated_at'
//         )
//         ->get();

//     $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
//     $client = new Client($mongoUri);
//     $collection = $client->isa_qa->device_events;

//     $filteredData = [];

//     foreach ($deviceEvents as $event) {
//         // Check if 'data' is a non-empty string before decoding
//         if (!empty($event->data) && is_string($event->data)) {
//             $data = json_decode($event->data, true);

//             // Handle JSON decode errors gracefully
//             if (json_last_error() !== JSON_ERROR_NONE) {
//                 $data = [];
//             }
//         } else {
//             $data = [];
//         }

//         // Safely get battery voltage and device parameters from JSON
//         $battery_voltage_md = array_get_nested($data, 'parameters.battery_voltage.md');
//         $battery_voltage_add = array_get_nested($data, 'parameters.battery_voltage.add');
//         $voltage_l_l_a_md = array_get_nested($data, 'electric_parameters.voltage_l_l.a.md');
//         $voltage_l_l_a_add = array_get_nested($data, 'electric_parameters.voltage_l_l.a.add');

//         // Safely get limits and messages
//         $lowerLimit = array_get_nested($data, 'parameters.battery_voltage.limits.lower');
//         $upperLimit = array_get_nested($data, 'parameters.battery_voltage.limits.upper');
//         $lowerLimitMsg = array_get_nested($data, 'parameters.battery_voltage.messages.lower');
//         $upperLimitMsg = array_get_nested($data, 'parameters.battery_voltage.messages.upper');

//         // Battery Status
//         $batteryValue = null;
//         $batteryStatus = 'unknown';

//         if ($battery_voltage_md && $battery_voltage_add && $event->deviceId) {
//             $latestEvent = $collection->findOne(
//                 [
//                     'module_id' => (int) $battery_voltage_md,
//                     'device_id' => $event->deviceId
//                 ],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestEvent && isset($latestEvent[$battery_voltage_add])) {
//                 $batteryValue = $latestEvent[$battery_voltage_add];
//                 $batteryStatus = ($batteryValue == 10) ? 'normal' :
//                                  (($batteryValue > 14) ? 'high' :
//                                  (($batteryValue < 10) ? 'low' : 'normal'));
//             }
//         }

//         // Device Status
//         $deviceValue = null;
//         $deviceStatus = 'unknown';

//         if ($voltage_l_l_a_md && $voltage_l_l_a_add && $event->deviceId) {
//             $latestEvent = $collection->findOne(
//                 [
//                     'module_id' => (int) $voltage_l_l_a_md,
//                     'device_id' => $event->deviceId
//                 ],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestEvent && isset($latestEvent[$voltage_l_l_a_add])) {
//                 $deviceValue = $latestEvent[$voltage_l_l_a_add];
//                 $deviceStatus = $deviceValue == 1 ? 'on' : 'off';
//             }
//         }

//         $filteredData[] = [
//             'deviceName' => $event->deviceName,
//             'deviceId' => $event->deviceId,
//             'moduleId' => $event->moduleId,
//             'siteId' => $event->actualSiteId,
//             'lowerLimit' => $event->lowerLimit,
//             'upperLimit' => $event->upperLimit,
//             'lowerLimitMsg' => $event->lowerLimitMsg,
//             'upperLimitMsg' => $event->upperLimitMsg,
//             'batteryValue' => $batteryValue,
//             'batteryStatus' => $batteryStatus,
//             'deviceValue' => $deviceValue,
//             'deviceStatus' => $deviceStatus,
//             'userEmail' => $event->userEmail,
//             'created_at' => $event->created_at,
//             'updated_at' => $event->updated_at
//         ];
//     }

//     return response()->json([
//         'status' => true,
//         'data' => $filteredData
//     ]);
// }
// **************************************************************

// public function apiFetchDeviceStatus(): JsonResponse
// {
//     // Helper function to safely get nested keys from an array
//     function array_get_nested($array, $path, $default = null) {
//         $keys = explode('.', $path);
//         foreach ($keys as $key) {
//             if (is_array($array) && array_key_exists($key, $array)) {
//                 $array = $array[$key];
//             } else {
//                 return $default;
//             }
//         }
//         return $array;
//     }

//     $deviceEvents = DB::table('device_events')
//         ->leftJoin('admins', 'device_events.userEmail', '=', 'admins.email')
//         ->leftJoin('sites', 'device_events.siteId', '=', 'sites.id')
//         ->select(
//             'device_events.id as siteId',
//             'device_events.deviceName',
//             'device_events.deviceId',
//             'device_events.moduleId',
//             'device_events.eventField',
//             'device_events.lowerLimit',
//             'device_events.upperLimit',
//             'device_events.lowerLimitMsg',
//             'device_events.userEmail',
//             'device_events.upperLimitMsg',
//             'device_events.siteId as actualSiteId',
//             'sites.data'
//         )
//         ->get();

//     $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
//     $client = new Client($mongoUri);
//     $collection = $client->isa_qa->device_events;

//     $filteredData = [];

//     foreach ($deviceEvents as $event) {
//         // Parse JSON from 'data' column
//         $data = (!empty($event->data) && is_string($event->data)) ? json_decode($event->data, true) : [];
//         if (json_last_error() !== JSON_ERROR_NONE) {
//             $data = [];
//         }

//         // Extract metadata
//         $battery_voltage_md = array_get_nested($data, 'parameters.battery_voltage.md');
//         $battery_voltage_add = array_get_nested($data, 'parameters.battery_voltage.add');
//         $voltage_l_l_a_md = array_get_nested($data, 'electric_parameters.voltage_l_l.a.md');
//         $voltage_l_l_a_add = array_get_nested($data, 'electric_parameters.voltage_l_l.a.add');

//         $batteryValue = null;
//         $batteryStatus = 'unknown';
//         $deviceValue = null;
//         $deviceStatus = 'unknown';

//         $mongoCreatedAt = null;
//         $mongoUpdatedAt = null;

//         // Get battery status
//         if ($battery_voltage_md && $battery_voltage_add && $event->deviceId) {
//             $latestBattery = $collection->findOne(
//                 ['module_id' => (int)$battery_voltage_md, 'device_id' => $event->deviceId],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestBattery) {
//                 if (isset($latestBattery[$battery_voltage_add])) {
//                     $batteryValue = $latestBattery[$battery_voltage_add];
//                     $batteryStatus = ($batteryValue == 10) ? 'normal' :
//                                      (($batteryValue > 14) ? 'high' :
//                                      (($batteryValue < 10) ? 'low' : 'normal'));
//                 }

//                 if (isset($latestBattery['createdAt'])) {
//                     $mongoCreatedAt = Carbon::parse($latestBattery['createdAt']->toDateTime())
//                         ->setTimezone('Asia/Kolkata')
//                         ->format('Y-m-d H:i:s');
//                 }
//                 if (isset($latestBattery['updatedAt'])) {
//                     $mongoUpdatedAt = Carbon::parse($latestBattery['updatedAt']->toDateTime())
//                         ->setTimezone('Asia/Kolkata')
//                         ->format('Y-m-d H:i:s');
//                 }
//             }
//         }

//         // Get device status
//         if ($voltage_l_l_a_md && $voltage_l_l_a_add && $event->deviceId) {
//             $latestDevice = $collection->findOne(
//                 ['module_id' => (int)$voltage_l_l_a_md, 'device_id' => $event->deviceId],
//                 ['sort' => ['createdAt' => -1]]
//             );

//             if ($latestDevice) {
//                 if (isset($latestDevice[$voltage_l_l_a_add])) {
//                     $deviceValue = $latestDevice[$voltage_l_l_a_add];
//                     $deviceStatus = ($deviceValue == 1) ? 'on' : 'off';
//                 }

//                 if (!$mongoCreatedAt && isset($latestDevice['createdAt'])) {
//                     $mongoCreatedAt = Carbon::parse($latestDevice['createdAt']->toDateTime())
//                         ->setTimezone('Asia/Kolkata')
//                         ->format('Y-m-d H:i:s');
//                 }

//                 if (!$mongoUpdatedAt && isset($latestDevice['updatedAt'])) {
//                     $mongoUpdatedAt = Carbon::parse($latestDevice['updatedAt']->toDateTime())
//                         ->setTimezone('Asia/Kolkata')
//                         ->format('Y-m-d H:i:s');
//                 }
//             }
//         }

//         $filteredData[] = [
//             'deviceName' => $event->deviceName,
//             'deviceId' => $event->deviceId,
//             'moduleId' => $event->moduleId,
//             'siteId' => $event->actualSiteId,
//             'lowerLimit' => $event->lowerLimit,
//             'upperLimit' => $event->upperLimit,
//             'lowerLimitMsg' => $event->lowerLimitMsg,
//             'upperLimitMsg' => $event->upperLimitMsg,
//             'batteryValue' => $batteryValue,
//             'batteryStatus' => $batteryStatus,
//             'deviceValue' => $deviceValue,
//             'deviceStatus' => $deviceStatus,
//             'userEmail' => $event->userEmail,
//             'created_at' => $mongoCreatedAt,
//             'updated_at' => $mongoUpdatedAt
//         ];
//     }

//     return response()->json([
//         'status' => true,
//         'data' => $filteredData
//     ]);
// }



    public function apiFetchDeviceStatus(): JsonResponse
    {
        // Helper to get nested values
        function array_get_nested($array, $path, $default = null) {
            $keys = explode('.', $path);
            foreach ($keys as $key) {
                if (is_array($array) && array_key_exists($key, $array)) {
                    $array = $array[$key];
                } else {
                    return $default;
                }
            }
            return $array;
        }

        $deviceEvents = DB::table('device_events')
            ->leftJoin('admins', 'device_events.userEmail', '=', 'admins.email')
            ->leftJoin('sites', 'device_events.siteId', '=', 'sites.id')
            ->select(
                'device_events.id as siteId',
                'device_events.deviceName',
                'device_events.deviceId',
                'device_events.moduleId',
                'device_events.eventField',
                'device_events.lowerLimit',
                'device_events.upperLimit',
                'device_events.lowerLimitMsg',
                'device_events.upperLimitMsg',
                'device_events.userEmail',
                'device_events.owner_email',
                'device_events.siteId as actualSiteId',
                'sites.data'
            )
            ->get();

        $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
        $client = new Client($mongoUri);
        $collection = $client->isa_qa->device_events;

        $filteredData = [];

        foreach ($deviceEvents as $event) {
            $data = (!empty($event->data) && is_string($event->data)) ? json_decode($event->data, true) : [];
            if (json_last_error() !== JSON_ERROR_NONE) {
                $data = [];
            }

            $battery_voltage_md = array_get_nested($data, 'parameters.battery_voltage.md');
            $battery_voltage_add = array_get_nested($data, 'parameters.battery_voltage.add');
            $voltage_l_l_a_md = array_get_nested($data, 'electric_parameters.voltage_l_l.a.md');
            $voltage_l_l_a_add = array_get_nested($data, 'electric_parameters.voltage_l_l.a.add');

            $batteryValue = null;
            $batteryStatus = 'unknown';
            $deviceValue = null;
            $deviceStatus = 'unknown';

            $mongoCreatedAt = null;
            $mongoUpdatedAt = null;

            if ($battery_voltage_md && $battery_voltage_add && $event->deviceId) {
                $latestBattery = $collection->findOne(
                    ['module_id' => (int)$battery_voltage_md, 'device_id' => $event->deviceId],
                    ['sort' => ['createdAt' => -1]]
                );

                if ($latestBattery && isset($latestBattery[$battery_voltage_add])) {
                    $batteryValue = $latestBattery[$battery_voltage_add];
                    $batteryStatus = ($batteryValue == 10) ? 'normal' :
                                     (($batteryValue > 14) ? 'high' :
                                     (($batteryValue < 10) ? 'low' : 'normal'));
                }

                if (isset($latestBattery['createdAt'])) {
                    $mongoCreatedAt = Carbon::parse($latestBattery['createdAt']->toDateTime())->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                }
                if (isset($latestBattery['updatedAt'])) {
                    $mongoUpdatedAt = Carbon::parse($latestBattery['updatedAt']->toDateTime())->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                }
            }

            if ($voltage_l_l_a_md && $voltage_l_l_a_add && $event->deviceId) {
                $latestDevice = $collection->findOne(
                    ['module_id' => (int)$voltage_l_l_a_md, 'device_id' => $event->deviceId],
                    ['sort' => ['createdAt' => -1]]
                );

                if ($latestDevice && isset($latestDevice[$voltage_l_l_a_add])) {
                    $deviceValue = $latestDevice[$voltage_l_l_a_add];
                    $deviceStatus = ($deviceValue == 1) ? 'on' : 'off';
                }

                if (!$mongoCreatedAt && isset($latestDevice['createdAt'])) {
                    $mongoCreatedAt = Carbon::parse($latestDevice['createdAt']->toDateTime())->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                }
                if (!$mongoUpdatedAt && isset($latestDevice['updatedAt'])) {
                    $mongoUpdatedAt = Carbon::parse($latestDevice['updatedAt']->toDateTime())->setTimezone('Asia/Kolkata')->format('Y-m-d H:i:s');
                }
            }

            $filteredData[] = [
                'deviceName'     => $event->deviceName,
                'deviceId'       => $event->deviceId,
                'moduleId'       => $event->moduleId,
                'siteId'         => $event->actualSiteId,
                'lowerLimit'     => $event->lowerLimit,
                'upperLimit'     => $event->upperLimit,
                'lowerLimitMsg'  => $event->lowerLimitMsg,
                'upperLimitMsg'  => $event->upperLimitMsg,
                'batteryValue'   => $batteryValue,
                'batteryStatus'  => $batteryStatus,
                'deviceValue'    => $deviceValue,
                'deviceStatus'   => $deviceStatus,
                'userEmail'      => $event->userEmail,
                'ownerEmail'     => $event->owner_email,
                'created_at'     => $mongoCreatedAt,
                'updated_at'     => $mongoUpdatedAt
            ];
        }

        return response()->json([
            'status' => true,
            'data' => $filteredData
        ]);
    }




}