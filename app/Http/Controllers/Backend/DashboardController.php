<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;
use Illuminate\Support\Facades\Mail;
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
                // $client = new Client("mongodb://localhost:27017");
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

    public function getModeStatus()
{
    return response()->json([
        "3,1106" => 1 // or 0
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
        // Helper to get nested values
        function array_get_nested($array, $path, $default = null) {
            if (!is_array($array)) {
                return $default;
            }

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

        try {
            $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
            $mongoClient = new MongoClient($mongoUri);
            $collection = $mongoClient->isa_qa->device_events;
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to connect to MongoDB: ' . $e->getMessage()
            ], 500);
        }

        $filteredData = [];

        foreach ($deviceEvents as $event) {
            try {
                $data = json_decode($event->data, true) ?? [];
                
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
                        $batteryValue = (float)$latestBattery[$battery_voltage_add];
                        $batteryStatus = ($batteryValue >= 10 && $batteryValue <= 14) ? 'normal' :
                                        ($batteryValue > 14 ? 'high' : 'low');
                    }

                    if (isset($latestBattery['createdAt']) && $latestBattery['createdAt'] instanceof \MongoDB\BSON\UTCDateTime) {
                        $mongoCreatedAt = Carbon::parse($latestBattery['createdAt']->toDateTime())
                            ->setTimezone('Asia/Kolkata')
                            ->format('Y-m-d H:i:s');
                    }
                }

                if ($voltage_l_l_a_md && $voltage_l_l_a_add && $event->deviceId) {
                    $latestDevice = $collection->findOne(
                        ['module_id' => (int)$voltage_l_l_a_md, 'device_id' => $event->deviceId],
                        ['sort' => ['createdAt' => -1]]
                    );

                    if ($latestDevice && isset($latestDevice[$voltage_l_l_a_add])) {
                        $deviceValue = (float)$latestDevice[$voltage_l_l_a_add];
                        $deviceStatus = $deviceValue >= 1 ? 'on' : 'off';
                    }

                    if (!$mongoCreatedAt && isset($latestDevice['createdAt']) && $latestDevice['createdAt'] instanceof \MongoDB\BSON\UTCDateTime) {
                        $mongoCreatedAt = Carbon::parse($latestDevice['createdAt']->toDateTime())
                            ->setTimezone('Asia/Kolkata')
                            ->format('Y-m-d H:i:s');
                    }
                    if (!$mongoUpdatedAt && isset($latestDevice['updatedAt']) && $latestDevice['updatedAt'] instanceof \MongoDB\BSON\UTCDateTime) {
        $mongoUpdatedAt = Carbon::parse($latestDevice['updatedAt']->toDateTime())
            ->setTimezone('Asia/Kolkata')
            ->format('Y-m-d H:i:s');
    }
            }

            $filteredData[] = [
    'deviceName'     => $event->deviceName ?? '',
    'deviceId'       => $event->deviceId ?? '',
    'moduleId'       => $event->moduleId ?? '',
    'siteId'         => $event->actualSiteId ?? '',
    'lowerLimit'     => $event->lowerLimit ?? null,
    'upperLimit'     => $event->upperLimit ?? null,
    'lowerLimitMsg'  => $event->lowerLimitMsg ?? '',
    'upperLimitMsg'  => $event->upperLimitMsg ?? '',
    'batteryValue'   => $batteryValue,
    'batteryStatus'  => $batteryStatus,
    'deviceValue'    => $deviceValue,
    'deviceStatus'   => $deviceStatus,
    'userEmail'      => is_array(json_decode($event->userEmail, true)) 
                        ? json_decode($event->userEmail, true)
                        : explode(',', $event->userEmail ?? ''),
    'ownerEmail'     => $event->owner_email ?? '',
    'created_at'     => $mongoCreatedAt,
    'updated_at'     => $mongoUpdatedAt
];


        } catch (\Exception $e) {
            continue; // Skip this record but continue processing others
        }
    }

    return response()->json([
        'status' => true,
        'data' => $filteredData
    ]);
}

public function apiSendEmailIfMatch(Request $request)
{
   
    $to = $request->input('to');
    $site = $request->input('site');
    $subject = $request->input('subject');
    $messageBody = $request->input('message');

    // You can add manual checks if you want:
    if (!$to || !$subject || !$messageBody) {
        return response()->json(['error' => 'Missing required fields'], 400);
    }

    // Send the email
    Mail::raw($messageBody, function ($message) use ($to, $subject) {
        $message->to($to)->subject($subject);
    });


    return response()->json(['status' => 'Email sent successfully']);
}

}