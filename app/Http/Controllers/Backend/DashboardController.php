<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
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

        $logins = DB::table('logins')
            ->leftJoin('admins', 'logins.user_id', '=', 'admins.id')
            ->select('logins.*', 'admins.name', 'admins.email')
            ->get();

        foreach ($logins as $login) {
            $login->created_at = Carbon::parse($login->created_at);
        }

        $sites = DB::table('sites')
            ->leftJoin('admins', 'sites.email', '=', 'admins.email')
            ->select('sites.*', 'admins.id as admin_id', 'admins.name', 'admins.email')
            ->get();

        $events = [];
        foreach ($sites as $site) {
            $data = json_decode($site->data, true);
            
            if ($data) {
                $mdValues = $this->extractMdFields($data);

                // MongoDB connection using MongoDB\Client
                $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
                $client = new \MongoDB\Client($mongoUri);
                $database = $client->isa_qa;
                $collection = $database->device_events;

                if (!empty($mdValues)) {
                    $uniqueMdValues = array_unique((array) $mdValues);
                    $uniqueMdValues = array_filter($uniqueMdValues, function ($value) {
                        return !empty($value);
                    });
                    $uniqueMdValues = array_map('intval', $uniqueMdValues);
                    $uniqueMdValues = array_values($uniqueMdValues);
                    
                    if (is_array($uniqueMdValues) && count($uniqueMdValues) > 0) {
                        foreach ($uniqueMdValues as $moduleId) {
                            $event = $collection->findOne(
                                ['module_id' => $moduleId],
                                ['sort' => ['createdAt' => -1]]
                            );
                            if ($event) {
                                $eventData = (array) $event;
                                $eventData['admin_id'] = $site->admin_id;
                                $events[] = $eventData;
                            }
                        }
                    } else {
                        return redirect()->back()->withErrors('Invalid or empty module_id values.');
                    }
                } else {
                    return redirect()->back()->withErrors('No module_id found in the site data.');
                }
            }
        }

        // if (empty($events)) {
        //     return redirect()->back()->withErrors('No events found.');
        // }

        // return $events;
        
        return view(
            'backend.pages.dashboard.index',
            [
                'total_admins' => Admin::count(),
                'total_roles' => Role::count(),
                'total_permissions' => Permission::count(),
                'logins' => $logins,
                'sites' => $sites,
                'total_sites' => $total_sites,
                'events' => $events
            ]
        );
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
}