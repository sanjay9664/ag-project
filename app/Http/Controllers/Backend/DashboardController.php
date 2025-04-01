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
            ->distinct('sites.id')
            ->get();
    

        foreach ($logins as $login) {
            $login->created_at = Carbon::parse($login->created_at);
        }

        $sites = DB::table('sites')
            ->leftJoin('admins', 'sites.email', '=', 'admins.email')
            ->selectRaw('
                MIN(sites.id) as id, 
                sites.site_name, 
                sites.slug, 
                sites.email, 
                sites.data, 
                sites.increase_running_hours_status, 
                admins.id as admin_id, 
                admins.name as admin_name
            ')
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
            $data = json_decode($site->data, true); // Decoding JSON

            if (!empty($data)) {
                $mdValues = $this->extractMdFields($data);

                // MongoDB connection using MongoDB\Client
                $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
                $client = new \MongoDB\Client($mongoUri);
                $database = $client->isa_qa;
                $collection = $database->device_events;

                // Extract unique, non-empty, numeric module IDs
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
}