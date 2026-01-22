<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SiteRequest;
use App\Models\Site;
use App\User;
use App\Models\Admin;
use App\Models\MongodbData;
use App\Models\MongodbFrontend;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use MongoDB\Client as MongoClient;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Pool;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\Cache;
use DateTimeZone;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Auth,DB,Validator;

class SiteController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['site.view']);
        
        $user = Auth::guard('admin')->user();
        
        if ($user->hasRole('superadmin')) {
            return view('backend.pages.sites.index', [
                'sites' => Site::all(),
            ]);
        } else {
            return view('backend.pages.sites.index', [
                'sites' => Site::where('email', $user->email)->get(),
            ]);
        }
    }    

    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['site.create']);

        $user_emails = Admin::select('email', 'username')->where('username', '!=', 'superadmin')->get();
        return view('backend.pages.sites.create', compact('user_emails'));
    }

    protected function generateUniqueSlug($siteName)
    {
        $slug = Str::slug($siteName);
        $originalSlug = $slug;

        $count = 1;
        while (Site::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    public function store(SiteRequest $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['site.create']);
    
        $site = new Site();
        $site->site_name = $request->site_name;
        $site->slug = $this->generateUniqueSlug($request->site_name);
        $site->email = $request->email;
        $site->device_id = $request->device_id;
        $site->alternate_device_id = $request->alternate_device_id;
        $site->clusterID = $request->clusterID;
        $site->increase_running_hours_status = $request->has('increase_running_hours_status') ? 1 : 0;
    
        $additionalData = [
            'site_name' => $request->input('site_name'),
            'asset_name' => $request->input('asset_name'),
            'group' => $request->input('group'),
            'generator' => $request->input('generator'),
            'serial_number' => $request->input('serial_number'),
            'model' => $request->input('model'),
            'brand' => $request->input('brand'),
            'capacity' => $request->input('capacity'),
            'run_status' => [
                'md' => $request->input('run_status_md'),
                'add' => $request->input('run_status_add'),
            ],
            'active_power_kw' => [
                'md' => $request->input('active_power_kw_md'),
                'add' => $request->input('active_power_kw_add'),
            ],
            'active_power_kva' => [
                'md' => $request->input('active_power_kva_md'),
                'add' => $request->input('active_power_kva_add'),
            ],
            'power_factor' => [
                'md' => $request->input('power_factor_md'),
                'add' => $request->input('power_factor_add'),
            ],
            'total_kwh' => [
                'md' => $request->input('total_kwh_md'),
                'add' => $request->input('total_kwh_add'),
            ],
            'start_md' => [
                'md' => $request->input('start_md'),
                'add' => $request->input('start_add'),
                'argument' => $request->input('start_arg'),
            ],
            'stop_md' => [
                'md' => $request->input('stop_md'),
                'add' => $request->input('stop_add'),
                'argument' => $request->input('stop_arg'),
            ],
             'auto_md' => [
                'md' => $request->input('auto_md'),
                'add' => $request->input('auto_add'),
                'argument' => $request->input('auto_arg'),
            ],
             'manual_md' => [
                'md' => $request->input('manual_md'),
                'add' => $request->input('manual_add'),
                'argument' => $request->input('manual_arg1'),
            ],
             'fault_reset_md' => [
                'md' => $request->input('fault_reset_md'),  
                'add' => $request->input('fault_reset_add'),
                'argument' => $request->input('fault_reset_arg1'),
            ],
             'mode_md' => [
                'md' => $request->input('mode_md'),
                'add' => $request->input('mode_add'),
                
            ],
            'parameters' => [
                'coolant_temperature' => [
                    'md' => $request->input('coolant_temperature_md'),
                    'add' => $request->input('coolant_temperature_add'),
                ],
                'oil_temperature' => [
                    'md' => $request->input('oil_temperature_md'),
                    'add' => $request->input('oil_temperature_add'),
                ],
                'oil_pressure' => [
                    'md' => $request->input('oil_pressure_md'),
                    'add' => $request->input('oil_pressure_add'),
                ],
                'rpm' => [
                    'md' => $request->input('rpm_md'),
                    'add' => $request->input('rpm_add'),
                ],
                'number_of_starts' => [
                    'md' => $request->input('number_of_starts_md'),
                    'add' => $request->input('number_of_starts_add'),
                ],
                'battery_voltage' => [
                    'md' => $request->input('battery_voltage_md'),
                    'add' => $request->input('battery_voltage_add'),
                ],
                'fuel' => [
                    'md' => $request->input('fuel_md'),
                    'add' => $request->input('fuel_add'),
                ],
            ],
            'running_hours' => [
                'value' => $request->input('running_hours_value'),
                'md' => $request->input('running_hours_md'),
                'add' => $request->input('running_hours_add'),
                'admin_run_hours' => $request->input('admin_run_hours'),
                'increase_minutes' => $request->input('increase_minutes'),
            ],
            'readOn' => [
                    'md' => $request->input('readOn_md'),
                    'add' => $request->input('readOn_add'),
                ],
            'readstatus' => [
                    'md' => $request->input('readstatus_md'),
                    'add' => $request->input('readstatus_add'),
                ],
            'electric_parameters' => [
                'voltage_l_l' => [
                    'a' => [
                        'md' => $request->input('voltage_l_l_a_md'),
                        'add' => $request->input('voltage_l_l_a_add'),
                    ],
                    'b' => [
                        'md' => $request->input('voltage_l_l_b_md'),
                        'add' => $request->input('voltage_l_l_b_add'),
                    ],
                    'c' => [
                        'md' => $request->input('voltage_l_l_c_md'),
                        'add' => $request->input('voltage_l_l_c_add'),
                    ],
                ],
                'voltage_l_n' => [
                    'a' => [
                        'md' => $request->input('voltage_l_n_a_md'),
                        'add' => $request->input('voltage_l_n_a_add'),
                    ],
                    'b' => [
                        'md' => $request->input('voltage_l_n_b_md'),
                        'add' => $request->input('voltage_l_n_b_add'),
                    ],
                    'c' => [
                        'md' => $request->input('voltage_l_n_c_md'),
                        'add' => $request->input('voltage_l_n_c_add'),
                    ],
                ],
                'current' => [
                    'a' => [
                        'md' => $request->input('current_a_md'),
                        'add' => $request->input('current_a_add'),
                    ],
                    'b' => [
                        'md' => $request->input('current_b_md'),
                        'add' => $request->input('current_b_add'),
                    ],
                    'c' => [
                        'md' => $request->input('current_c_md'),
                        'add' => $request->input('current_c_add'),
                    ],
                ],
                'pf_data' => [
                    'md' => $request->input('pf_data_md'),
                    'add' => $request->input('pf_data_add'),
                ],
                'frequency' => [
                    'md' => $request->input('frequency_md'),
                    'add' => $request->input('frequency_add'),
                ],
            ],
            'alarm_status' => [
                'coolant_temperature_status' => [
                    'md' =>  $request->input('coolant_temperature_md_status'),
                    'add' => $request->input('coolant_temperature_add_status'),
                ],
                'fuel_level_status' => [
                    'md' =>  $request->input('fuel_level_md_status'),
                    'add' => $request->input('fuel_level_add_status'),
                ],
                'emergency_stop_status'=> [
                    'md' =>  $request->input('emergency_stop_md_status'),
                    'add' => $request->input('emergency_stop_add_status'),
                ],
                'high_coolant_temperature_status' => [
                    'md' => $request->input('high_coolant_temperature_md_status'),
                    'add' => $request->input('high_coolant_temperature_add_status'),
                ],
                'under_speed_status' => [
                    'md' => $request->input('under_speed_md_status'),
                    'add' => $request->input('under_speed_add_status'),
                ],
                'fail_to_start_status' => [
                    'md' => $request->input('fail_to_start_md_status'),
                    'add' => $request->input('fail_to_start_add_status'),
                ],
                'loss_of_speed_sensing_status' => [
                    'md' => $request->input('loss_of_speed_sensing_md_status'),
                    'add' => $request->input('loss_of_speed_sensing_add_status'),
                ],
                'oil_pressure_status' => [
                    'md' => $request->input('oil_pressure_md_status'),
                    'add' => $request->input('oil_pressure_add_status'),
                ],
                'battery_level_status' => [
                    'md' => $request->input('battery_level_md_status'),
                    'add' => $request->input('battery_level_add_status'),
                ],
                'low_oil_pressure_status' => [
                    'md' => $request->input('low_oil_pressure_md_status'),
                    'add' => $request->input('low_oil_pressure_add_status'),
                ],
                'high_oil_temperature_status' => [
                    'md' => $request->input('high_oil_temperature_md_status'),
                    'add' => $request->input('high_oil_temperature_add_status'),
                ],
                'over_speed_status' => [
                    'md' => $request->input('over_speed_md_status'),
                    'add' => $request->input('over_speed_add_status'),
                ],
                'fail_to_come_to_rest_status' => [
                    'md' => $request->input('fail_to_come_to_rest_md_status'),
                    'add' => $request->input('fail_to_come_to_rest_add_status'),
                ],
                'generator_low_voltage_status' => [
                    'md' => $request->input('generator_low_voltage_md_status'),
                    'add' => $request->input('generator_low_voltage_add_status'),
                ]
            ],
        ];
    
        // dd($additionalData);
        $site->data = json_encode($additionalData);
        $site->save();
    
        session()->flash('success', __('Site has been created.'));
        return redirect()->route('admin.sites.index');
    }      


    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['site.edit']);

        $site = Site::findOrFail($id);
        $siteData = json_decode($site->data, true);
        $user_emails = Admin::select('email', 'username')->where('username', '!=', 'superadmin')->get();
        return view('backend.pages.sites.edit', [
            'site' => $site,
            'siteData' => $siteData, 
            'roles' => Role::all(),
            'user_emails' => $user_emails,
        ]);
    }

    public function update(SiteRequest $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['site.update']);
    
        $site = Site::findOrFail($id);
        $site->site_name = $request->site_name;
        $site->slug = $this->generateUniqueSlug($request->site_name);
        $site->email = $request->email;
        $site->device_id = $request->device_id;
        $site->alternate_device_id = $request->alternate_device_id;
        $site->clusterID = $request->clusterID;
        $site->increase_running_hours_status = $request->input('increase_running_hours_status', 0);

        $additionalData = [
            'site_name' => $request->input('site_name'),
            'asset_name' => $request->input('asset_name'),
            'group' => $request->input('group'),
            'generator' => $request->input('generator'),
            'serial_number' => $request->input('serial_number'),
            'model' => $request->input('model'),
            'brand' => $request->input('brand'),
            'capacity' => $request->input('capacity'),
            'run_status' => [
                'md' => $request->input('run_status_md'),
                'add' => $request->input('run_status_add'),
            ],
            'active_power_kw' => [
                'md' => $request->input('active_power_kw_md'),
                'add' => $request->input('active_power_kw_add'),
            ],
            'active_power_kva' => [
                'md' => $request->input('active_power_kva_md'),
                'add' => $request->input('active_power_kva_add'),
            ],
            'power_factor' => [
                'md' => $request->input('power_factor_md'),
                'add' => $request->input('power_factor_add'),
            ],
            'total_kwh' => [
                'md' => $request->input('total_kwh_md'),
                'add' => $request->input('total_kwh_add'),
            ],
            'start_md' => [
                'md' => $request->input('start_md'),
                'add' => $request->input('start_add'),
                'argument' => $request->input('start_arg'),
            ],
            'stop_md' => [
                'md' => $request->input('stop_md'),
                'add' => $request->input('stop_add'),
                'argument' => $request->input('stop_arg'),
            ],
             'auto_md' => [
                'md' => $request->input('auto_md'),
                'add' => $request->input('auto_add'),
                'argument' => $request->input('auto_arg'),
            ],
             'manual_md' => [
                'md' => $request->input('manual_md'),
                'add' => $request->input('manual_add'),
                'argument' => $request->input('manual_arg1'),
            ],
            'fault_reset_md' => [
                'md' => $request->input('fault_reset_md'),  
                'add' => $request->input('fault_reset_add'),
                'argument' => $request->input('fault_reset_arg1'),
            ],
            'mode_md' => [
                'md' => $request->input('mode_md'),
                'add' => $request->input('mode_add'),
                
            ],
            'parameters' => [
                'coolant_temperature' => [
                    'md' => $request->input('coolant_temperature_md'),
                    'add' => $request->input('coolant_temperature_add'),
                ],
                'oil_temperature' => [
                    'md' => $request->input('oil_temperature_md'),
                    'add' => $request->input('oil_temperature_add'),
                ],
                'oil_pressure' => [
                    'md' => $request->input('oil_pressure_md'),
                    'add' => $request->input('oil_pressure_add'),
                ],
                'rpm' => [
                    'md' => $request->input('rpm_md'),
                    'add' => $request->input('rpm_add'),
                ],
                'number_of_starts' => [
                    'md' => $request->input('number_of_starts_md'),
                    'add' => $request->input('number_of_starts_add'),
                ],
                'battery_voltage' => [
                    'md' => $request->input('battery_voltage_md'),
                    'add' => $request->input('battery_voltage_add'),
                ],
                'fuel' => [
                    'md' => $request->input('fuel_md'),
                    'add' => $request->input('fuel_add'),
                ],
            ],
            'running_hours' => [
                'value' => $request->input('running_hours_value'),
                'md' => $request->input('running_hours_md'),
                'add' => $request->input('running_hours_add'),
                'admin_run_hours' => $request->input('admin_run_hours'),
                'increase_minutes' => $request->input('increase_minutes'),
            ],
            'readOn' => [
                    'md' => $request->input('readOn_md'),
                    'add' => $request->input('readOn_add'),
                ],
            'readstatus' => [
                    'md' => $request->input('readstatus_md'),
                    'add' => $request->input('readstatus_add'),
                ],
            'electric_parameters' => [
                'voltage_l_l' => [
                    'a' => [
                        'md' => $request->input('voltage_l_l_a_md'),
                        'add' => $request->input('voltage_l_l_a_add'),
                    ],
                    'b' => [
                        'md' => $request->input('voltage_l_l_b_md'),
                        'add' => $request->input('voltage_l_l_b_add'),
                    ],
                    'c' => [
                        'md' => $request->input('voltage_l_l_c_md'),
                        'add' => $request->input('voltage_l_l_c_add'),
                    ],
                ],
                'voltage_l_n' => [
                    'a' => [
                        'md' => $request->input('voltage_l_n_a_md'),
                        'add' => $request->input('voltage_l_n_a_add'),
                    ],
                    'b' => [
                        'md' => $request->input('voltage_l_n_b_md'),
                        'add' => $request->input('voltage_l_n_b_add'),
                    ],
                    'c' => [
                        'md' => $request->input('voltage_l_n_c_md'),
                        'add' => $request->input('voltage_l_n_c_add'),
                    ],
                ],
                'current' => [
                    'a' => [
                        'md' => $request->input('current_a_md'),
                        'add' => $request->input('current_a_add'),
                    ],
                    'b' => [
                        'md' => $request->input('current_b_md'),
                        'add' => $request->input('current_b_add'),
                    ],
                    'c' => [
                        'md' => $request->input('current_c_md'),
                        'add' => $request->input('current_c_add'),
                    ],
                ],
                'pf_data' => [
                    'md' => $request->input('pf_data_md'),
                    'add' => $request->input('pf_data_add'),
                ],
                'frequency' => [
                    'md' => $request->input('frequency_md'),
                    'add' => $request->input('frequency_add'),
                ],
            ],
            'alarm_status' => [
                'coolant_temperature_status' => [
                    'md' =>  $request->input('coolant_temperature_md_status'),
                    'add' => $request->input('coolant_temperature_add_status'),
                ],
                'fuel_level_status' => [
                    'md' =>  $request->input('fuel_level_md_status'),
                    'add' => $request->input('fuel_level_add_status'),
                ],
                'emergency_stop_status'=> [
                    'md' =>  $request->input('emergency_stop_md_status'),
                    'add' => $request->input('emergency_stop_add_status'),
                ],
                'high_coolant_temperature_status' => [
                    'md' => $request->input('high_coolant_temperature_md_status'),
                    'add' => $request->input('high_coolant_temperature_add_status'),
                ],
                'under_speed_status' => [
                    'md' => $request->input('under_speed_md_status'),
                    'add' => $request->input('under_speed_add_status'),
                ],
                'fail_to_start_status' => [
                    'md' => $request->input('fail_to_start_md_status'),
                    'add' => $request->input('fail_to_start_add_status'),
                ],
                'loss_of_speed_sensing_status' => [
                    'md' => $request->input('loss_of_speed_sensing_md_status'),
                    'add' => $request->input('loss_of_speed_sensing_add_status'),
                ],
                'oil_pressure_status' => [
                    'md' => $request->input('oil_pressure_md_status'),
                    'add' => $request->input('oil_pressure_add_status'),
                ],
                'battery_level_status' => [
                    'md' => $request->input('battery_level_md_status'),
                    'add' => $request->input('battery_level_add_status'),
                ],
                'low_oil_pressure_status' => [
                    'md' => $request->input('low_oil_pressure_md_status'),
                    'add' => $request->input('low_oil_pressure_add_status'),
                ],
                'high_oil_temperature_status' => [
                    'md' => $request->input('high_oil_temperature_md_status'),
                    'add' => $request->input('high_oil_temperature_add_status'),
                ],
                'over_speed_status' => [
                    'md' => $request->input('over_speed_md_status'),
                    'add' => $request->input('over_speed_add_status'),
                ],
                'fail_to_come_to_rest_status' => [
                    'md' => $request->input('fail_to_come_to_rest_md_status'),
                    'add' => $request->input('fail_to_come_to_rest_add_status'),
                ],
                'generator_low_voltage_status' => [
                    'md' => $request->input('generator_low_voltage_md_status'),
                    'add' => $request->input('generator_low_voltage_add_status'),
                ]            
            ],
        ];
    
        // dd($additionalData);
        $site->data = json_encode($additionalData);
        $site->save();
    
        session()->flash('success', __('Site has been Updated.'));
        return redirect()->route('admin.sites.index');
    }
                                                                        
    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['site.delete']);

        $site = Site::findOrFail($id);
        $site->delete();
        session()->flash('success', 'Site has been deleted.');
        return back();
    }

    public function show(Request $request, $slug)
    {
        $siteData = Site::where('slug', $slug)->first();
        if (!$siteData) {
            return redirect()->back()->withErrors('Site not found or module_id is missing.');
        }
    
        $data = json_decode($siteData->data, true);
        $mdValues = $this->extractMdFields($data);
    
        $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
        $client = new MongoClient($mongoUri);
        $database = $client->isa_qa;
        $collection = $database->device_events;
    
        $events = [];
    
        if (!empty($mdValues)) {
            $uniqueMdValues = array_unique((array) $mdValues);
            $uniqueMdValues = array_filter($uniqueMdValues, function ($value) {
                return !empty($value);
            });
            $uniqueMdValues = array_map('intval', $uniqueMdValues);
            $uniqueMdValues = array_values($uniqueMdValues);
    
            foreach ($uniqueMdValues as $moduleId) {
                $event = $collection->findOne(
                    ['module_id' => $moduleId],
                    ['sort' => ['createdAt' => -1]]
                );
                if ($event) {
                    $events[] = $event;
                }
            }
        }
    
        if (empty($events)) {
            return redirect()->back()->withErrors('No data found for the specified module_id values.');
        }
    
        // usort($events, function ($a, $b) {
        //     $createdAtA = new UTCDateTime($a['created_at_timestamp']);
        //     $createdAtB = new UTCDateTime($b['created_at_timestamp']);
        //     return $createdAtB <=> $createdAtA; 
        // });
        usort($events, function ($a, $b) {
            $createdAtA = new UTCDateTime((int) round($a['created_at_timestamp'] * 1000));
            $createdAtB = new UTCDateTime((int) round($b['created_at_timestamp'] * 1000));
            return $createdAtB <=> $createdAtA;
        });
        
        $latestCreatedAt = $events[0]['createdAt'];
        
        $latestCreatedAtFormatted = $latestCreatedAt->toDateTime()->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('d-m-Y H:i:s');
        
        foreach ($events as &$event) {
            $event['createdAt'] = $event['createdAt']->toDateTime()->setTimezone(new DateTimeZone('Asia/Kolkata'))->format('d-m-Y H:i:s');
            
            $event['latestCreatedAt'] = $latestCreatedAtFormatted;
        }
        
        header('Content-Type: application/json');
        
        $eventsData = json_encode($events, JSON_PRETTY_PRINT);
        
        $sitejsonData = json_decode($siteData['data']);
       
        $user = Auth::guard('admin')->user();
       
        $role = $request->query('role');
    
        if ($role == 'superadmin') {
            return view('backend.pages.sites.superadmin-site-details', [
                'siteData' => $siteData,
                'sitejsonData' => $sitejsonData,
                'eventData' => $events,
                'latestCreatedAt' => $latestCreatedAtFormatted,
            ]);
        }

        if ($role == 'admin') {
            // return $sitejsonData;
            return view('backend.pages.sites.site-details', [
                'siteData' => $siteData,
                'sitejsonData' => $sitejsonData,
                'eventData' => $events,
                'latestCreatedAt' => $latestCreatedAtFormatted,
            ]);
            
            
        }
        
        if ($user->hasRole('superadmin')) {
            
            return view(
                'backend.pages.sites.superadmin-site-details',
                [
                'siteData' => $siteData,
                'sitejsonData' => $sitejsonData,
                'eventData' => $events,
                'latestCreatedAt' => $latestCreatedAtFormatted,
                ]
                );
            } else {
            return view(
            'backend.pages.sites.site-details',
                [
                'siteData' => $siteData,
                'sitejsonData' => $sitejsonData,
                'eventData' => $events,
                'latestCreatedAt' => $latestCreatedAtFormatted,
                ]
            );
        }
    }

    // public function AdminSites(Request $request)     
    // {
    //     $role = $request->query('role');
    //     $bankName = $request->query('bank_name');
    //     $location = $request->query('location');

    //     $siteData = collect();
    //     $eventData = [];
    //     $latestCreatedAt = null;

    //     $user = Auth::user();
    //     if (!$user) {
    //         return redirect()->route('admin.login')->withErrors('You must be logged in.');
    //     }

    //     $userEmail = $user->email;

    //     if (!empty($location)) {
    //         $query = DB::table('sites')->where('email', $userEmail);

    //         if (!empty($bankName) && $bankName !== 'Select Bank') {
    //             $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.generator')) = ?", [$bankName]);
    //         }
    //         if (!empty($location) && $location !== 'Select Location') {
    //             $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.group')) = ?", [$location]);
    //         }
            
    //         $siteData = $query->get();

    //         $decodedSiteData = $siteData->map(function ($site) {
    //             return json_decode($site->data, true);
    //         });

    //         $mdValues = $this->extractMdFields($decodedSiteData->toArray());

    //         $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
    //         $client = new MongoClient($mongoUri);
    //         $database = $client->isa_qa;
    //         $collection = $database->device_events;

    //         if (!empty($mdValues)) {
    //             $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

    //             foreach ($uniqueMdValues as $moduleId) {
    //                 $event = $collection->findOne(
    //                     ['module_id' => $moduleId],
    //                     ['sort' => ['createdAt' => -1]]
    //                 );

    //                 if ($event) {
    //                     $eventData[] = $event;
    //                 }
    //             }
    //         }

    //         usort($eventData, function ($a, $b) {
    //             return ($b['created_at_timestamp'] ?? 0) <=> ($a['created_at_timestamp'] ?? 0);
    //         });

    //         $latestCreatedAt = !empty($eventData) ? $eventData[0]['createdAt']->toDateTime()
    //             ->setTimezone(new DateTimeZone('Asia/Kolkata'))
    //             ->format('d-m-Y H:i:s') : 'N/A';

    //         foreach ($eventData as &$event) {
    //             $event['createdAt'] = $event['createdAt']->toDateTime()
    //                 ->setTimezone(new DateTimeZone('Asia/Kolkata'))
    //                 ->format('d-m-Y H:i:s');
    //             $event['latestCreatedAt'] = $latestCreatedAt;
    //         }

    //         foreach ($siteData as $site) {
    //             $matchingEvent = collect($eventData)->first(function ($event) use ($site) {
    //                 return isset($event['device_id'], $site->device_id) &&
    //                     trim(strtolower($event['device_id'])) === trim(strtolower($site->device_id));
    //             });

    //             $site->updatedAt = isset($matchingEvent['updatedAt']) ? $matchingEvent['updatedAt']->toDateTime()
    //                 ->setTimezone(new DateTimeZone('Asia/Kolkata'))
    //                 ->format('d-m-Y H:i:s') : 'N/A';
    //         }

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'html' => view('backend.pages.sites.partials.site-table', compact('siteData', 'decodedSiteData', 'eventData', 'latestCreatedAt'))->render()
    //             ]);
    //         }

    //         return view('backend.pages.sites.admin-sites', compact('siteData', 'decodedSiteData', 'eventData', 'latestCreatedAt'));
    //     } else {
    //         ini_set('max_execution_time', 120);
    //         $start = microtime(true);

    //         $user = Auth::guard('admin')->user();
    //         $userEmail = $user->email;

    //         $siteData = $user->hasRole('superadmin')
    //             ? Site::select(['id', 'site_name', 'slug', 'email', 'data', 'device_id', 'clusterID'])->get()
    //             : Site::where('email', $userEmail)->select(['id', 'site_name', 'slug', 'email', 'data', 'device_id', 'clusterID'])->get();

    //         $mdValues = $this->extractMdFields(
    //             $siteData->pluck('data')->map(fn($data) => json_decode($data, true))->toArray()
    //         );

    //         // $eventData = [];
    //         // if (!empty($mdValues)) {
    //         //     $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

    //         //     if (!empty($uniqueMdValues)) {
    //         //         $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
    //         //         $mongoClient = new \MongoDB\Client($mongoUri);
    //         //         $collection = $mongoClient->isa_qa->device_events;

    //         //         $dateThreshold = new \MongoDB\BSON\UTCDateTime((new \DateTime('-48 hours'))->getTimestamp() * 1000);
    //         //         $cacheKey = 'event_data_' . sha1(implode(',', $uniqueMdValues));

    //         //         $eventData = Cache::remember($cacheKey, 300, function () use ($collection, $uniqueMdValues, $dateThreshold) {
    //         //             $cursor = $collection->find([
    //         //                 'module_id' => ['$in' => array_values($uniqueMdValues)],
    //         //                 'createdAt' => ['$gte' => $dateThreshold]
    //         //             ], [
    //         //                 'sort' => ['createdAt' => -1]
    //         //             ]);

    //         //             $moduleLatest = [];
    //         //             foreach ($cursor as $event) {
    //         //                 $moduleId = $event['module_id'];
    //         //                 if (!isset($moduleLatest[$moduleId])) {
    //         //                     $moduleLatest[$moduleId] = $event;
    //         //                 }
    //         //             }
    //         //             return array_values($moduleLatest);
    //         //         });
    //         //     }
    //         // }
            
    //         // $eventData = MongodbFrontend::get()->pluck('data');
    //         $eventData = MongodbFrontend::pluck('data')->toArray();

    //         // return $eventData;
            
    //         usort($eventData, fn($a, $b) => ($b['created_at_timestamp'] ?? 0) <=> ($a['created_at_timestamp'] ?? 0));

    //         // $latestCreatedAt = !empty($eventData)
    //         //     ? $eventData[0]['createdAt']->toDateTime()
    //         //         ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
    //         //         ->format('d-m-Y H:i:s')
    //         //     : 'N/A';
    //         if (!empty($eventData)) {
    //             $timestamp = (int) ($eventData[0]['createdAt']['$date']['$numberLong'] ?? 0);
    //             $latestCreatedAt = $timestamp
    //                 ? (new \DateTime('@' . ($timestamp / 1000)))
    //                     ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
    //                     ->format('d-m-Y H:i:s')
    //                 : 'N/A';
    //         } else {
    //             $latestCreatedAt = 'N/A';
    //         }
            
    //         foreach ($eventData as &$event) {
    //             $event['createdAt'] = $event['createdAt']->toDateTime()
    //                 ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
    //                 ->format('d-m-Y H:i:s');
    //             $event['latestCreatedAt'] = $latestCreatedAt;
    //         }

    //         $eventMap = collect($eventData)->mapWithKeys(function ($event) {
    //             $key = strtolower(trim($event['device_id'] ?? ''));
    //             return [$key => $event];
    //         });

    //         foreach ($siteData as $site) {
    //             $deviceId = strtolower(trim($site->device_id ?? ''));
    //             $matchingEvent = $eventMap[$deviceId] ?? null;

    //             $updatedAt = 'N/A';

    //             if (
    //                 $matchingEvent &&
    //                 isset($matchingEvent['updatedAt']) &&
    //                 $matchingEvent['updatedAt'] instanceof \MongoDB\BSON\UTCDateTime
    //             ) {
    //                 $updatedAt = $matchingEvent['updatedAt']
    //                     ->toDateTime()
    //                     ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
    //                     ->format('d-m-Y H:i:s');
    //             }

    //             $site->updatedAt = $updatedAt;
    //         }

    //         $sitejsonData = json_decode($siteData->first()->data ?? '{}', true);
    //         // return $eventData;
       
    //         return view('backend.pages.sites.admin-sites', compact('siteData', 'sitejsonData', 'eventData', 'latestCreatedAt'));
    //     }

    // }


    // old code 
//  public function AdminSites(Request $request)     
//     {
//         $role = $request->query('role');
//         $bankName = $request->query('bank_name');
//         $location = $request->query('location');

//         $siteData = collect();
//         $eventData = [];
//         $latestCreatedAt = null;

//         $user = Auth::user();
//         if (!$user) {
//             return redirect()->route('admin.login')->withErrors('You must be logged in.');
//         }

//         $userEmail = $user->email;

//         if (!empty($location)) {
//             $query = DB::table('sites')->where('email', $userEmail);

//             if (!empty($bankName) && $bankName !== 'Select Bank') {
//                 $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.generator')) = ?", [$bankName]);
//             }
//             if (!empty($location) && $location !== 'Select Location') {
//                 $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.group')) = ?", [$location]);
//             }
            
//             $siteData = $query->get();

//             $decodedSiteData = $siteData->map(function ($site) {
//                 return json_decode($site->data, true);
//             });

//             $mdValues = $this->extractMdFields($decodedSiteData->toArray());

//             $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
//             $client = new MongoClient($mongoUri);
//             $database = $client->isa_qa;
//             $collection = $database->device_events;

//             if (!empty($mdValues)) {
//                 $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

//                 foreach ($uniqueMdValues as $moduleId) {
//                     $event = $collection->findOne(
//                         ['module_id' => $moduleId],
//                         ['sort' => ['createdAt' => -1]]
//                     );

//                     if ($event) {
//                         $eventData[] = $event;
//                     }
//                 }
//             }

//             usort($eventData, function ($a, $b) {
//                 return ($b['created_at_timestamp'] ?? 0) <=> ($a['created_at_timestamp'] ?? 0);
//             });

//             $latestCreatedAt = !empty($eventData) ? $eventData[0]['createdAt']->toDateTime()
//                 ->setTimezone(new DateTimeZone('Asia/Kolkata'))
//                 ->format('d-m-Y H:i:s') : 'N/A';

//             foreach ($eventData as &$event) {
//                 $event['createdAt'] = $event['createdAt']->toDateTime()
//                     ->setTimezone(new DateTimeZone('Asia/Kolkata'))
//                     ->format('d-m-Y H:i:s');
//                 $event['latestCreatedAt'] = $latestCreatedAt;
//             }

//             foreach ($siteData as $site) {
//                 $matchingEvent = collect($eventData)->first(function ($event) use ($site) {
//                     return isset($event['device_id'], $site->device_id) &&
//                         trim(strtolower($event['device_id'])) === trim(strtolower($site->device_id));
//                 });

//                 $site->updatedAt = isset($matchingEvent['updatedAt']) ? $matchingEvent['updatedAt']->toDateTime()
//                     ->setTimezone(new DateTimeZone('Asia/Kolkata'))
//                     ->format('d-m-Y H:i:s') : 'N/A';
//             }

//             if ($request->ajax()) {
//                 return response()->json([
//                     'html' => view('backend.pages.sites.partials.site-table', compact('siteData', 'decodedSiteData', 'eventData', 'latestCreatedAt'))->render()
//                 ]);
//             }

//             return view('backend.pages.sites.admin-sites', compact('siteData', 'decodedSiteData', 'eventData', 'latestCreatedAt'));
//         } else {
//             ini_set('max_execution_time', 120);
//             $start = microtime(true);

//             $user = Auth::guard('admin')->user();
//             $userEmail = $user->email;

//             $siteData = $user->hasRole('superadmin')
//                 ? Site::select(['id', 'site_name', 'slug', 'email', 'data', 'device_id', 'clusterID'])->get()
//                 : Site::where('email', $userEmail)->select(['id', 'site_name', 'slug', 'email', 'data', 'device_id', 'clusterID'])->get();

//             $mdValues = $this->extractMdFields(
//                 $siteData->pluck('data')->map(fn($data) => json_decode($data, true))->toArray()
//             );

//             $eventData = MongodbFrontend::pluck('data')->toArray();

//             // return $eventData;
            
//             usort($eventData, fn($a, $b) => ($b['created_at_timestamp'] ?? 0) <=> ($a['created_at_timestamp'] ?? 0));

//             if (!empty($eventData)) {
//                 $timestamp = (int) ($eventData[0]['createdAt']['$date']['$numberLong'] ?? 0);
//                 $latestCreatedAt = $timestamp
//                     ? (new \DateTime('@' . ($timestamp / 1000)))
//                         ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
//                         ->format('d-m-Y H:i:s')
//                     : 'N/A';
//             } else {
//                 $latestCreatedAt = 'N/A';
//             }
            
//             foreach ($eventData as &$event) {
//                 $timestamp = (int) ($event['createdAt']['$date']['$numberLong'] ?? 0);

//                 $event['createdAt'] = $timestamp
//                     ? (new \DateTime('@' . ($timestamp / 1000)))
//                         ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
//                         ->format('d-m-Y H:i:s')
//                     : 'N/A';

//                 $event['latestCreatedAt'] = $latestCreatedAt;
//             }

//             $eventMap = collect($eventData)->mapWithKeys(function ($event) {
//                 $key = strtolower(trim($event['device_id'] ?? ''));
//                 return [$key => $event];
//             });

//             foreach ($siteData as $site) {
//                 $deviceId = strtolower(trim($site->device_id ?? ''));
//                 $matchingEvent = $eventMap[$deviceId] ?? null;

//                 $updatedAt = 'N/A';

//                 // Decode JSON safely
//                 $siteJson = json_decode($site->data ?? '{}', true);
//                 $siteMd   = $siteJson['parameters']['battery_voltage']['md'] ?? null;
//                 $siteAdd  = $siteJson['parameters']['battery_voltage']['add'] ?? null;

//                 // Debugging - show values being checked
//                 // echo "<pre>DeviceID: {$deviceId}, Md: {$siteMd}, Add: {$siteAdd}</pre>";

//                 if (
//                     $matchingEvent &&
//                     isset($matchingEvent['module_id']) &&
//                     (string) $matchingEvent['module_id'] === (string) $siteMd
//                 ) {
//                     // convert all keys to strings for comparison
//                     $eventKeys = array_map('strval', array_keys($matchingEvent));

//                     if (in_array((string) $siteAdd, $eventKeys, true)) {
//                         $batUpdated = $matchingEvent['updatedAt'] ?? null;

//                         if (is_array($batUpdated) && isset($batUpdated['$date']['$numberLong'])) {
//                             $timestamp = (int) $batUpdated['$date']['$numberLong'];
//                             $timestamp = (int)$batUpdated['$date']['$numberLong'];
//                             if ($timestamp > 0) {
//                                 $updatedAt = (new \DateTime('@' . ($timestamp / 1000)))
//                                     ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
//                                     ->format('d-m-Y H:i:s');
//                             }
//                         } elseif ($batUpdated instanceof \MongoDB\BSON\UTCDateTime) {
//                             $updatedAt = $batUpdated->toDateTime()
//                             ->setTimezone(new \DateTimeZone('Asia/Kolkata'))
//                             ->format('d-m-Y H:i:s');
//                         }

//                         echo "<pre style='color:green;'>✅ Matched {$siteAdd} — UpdatedAt: {$updatedAt}</pre>";
//                     } else {
//                         echo "<pre style='color:red;'>❌ Add key '{$siteAdd}' not found in event keys: "
//                             . implode(', ', $eventKeys) . "</pre>";
//                     }

//                     $site->updatedAt = $updatedAt;
//                 }
//             }
            
//             $sitejsonData = json_decode($siteData->first()->data ?? '{}', true);
//             // return $eventData;
       
//             return view('backend.pages.sites.admin-sites', compact('siteData', 'sitejsonData', 'eventData', 'latestCreatedAt'));
//         }

//     }

//     public function fetchStatuses(Request $request)
//     {
//         $siteIds = $request->input('site_ids', []);
//         $sites = Site::whereIn('id', $siteIds)->select('id', 'device_id', 'clusterID')->get();

//         $httpClient = new Client();
//         $dgRequests = [];
//         $controllerRequests = [];
//         $dgResults = [];
//         $controllerResults = [];

//         foreach ($sites as $site) {
//             if ($site->device_id) {
//                 $dgRequests[$site->id] = new GuzzleRequest('GET', "http://app.sochiot.com/api/config-engine/device/status/uuid/{$site->device_id}");
//             }
//             if ($site->clusterID) {
//                 $controllerRequests[$site->id] = new GuzzleRequest('GET', "http://app.sochiot.com/api/config-engine/gateway/status/uuid/{$site->clusterID}");
//             }
//         }

//         // Pool for DG
//         $dgPool = new Pool($httpClient, $dgRequests, [
//             'concurrency' => 10,
//             'fulfilled' => function ($response, $siteId) use (&$dgResults) {
//                 $dgResults[$siteId] = strtoupper(trim($response->getBody()->getContents()));
//             },
//             'rejected' => function () {}
//         ]);

//         // Pool for Controller
//         $ctrlPool = new Pool($httpClient, $controllerRequests, [
//             'concurrency' => 10,
//             'fulfilled' => function ($response, $siteId) use (&$controllerResults) {
//                 $controllerResults[$siteId] = strtoupper(trim($response->getBody()->getContents()));
//             },
//             'rejected' => function () {}
//         ]);

//         $dgPool->promise()->wait();
//         $ctrlPool->promise()->wait();

//         $statuses = [];
//         foreach ($siteIds as $siteId) {
//             $statuses[$siteId] = [
//                 'dg_status' => $dgResults[$siteId] ?? 'OFFLINE',
//                 'controller_status' => $controllerResults[$siteId] ?? 'OFFLINE',
//             ];
//         }

//         return response()->json($statuses);
//     }

//     public function fetchLatestData($slug)
//     {
//         $siteData = Site::where('slug', $slug)->first();
        
//         if (!$siteData) {
//             return response()->json(['error' => 'Site not found or module_id is missing.'], 404);
//         }
    
//         $data = json_decode($siteData->data, true);
//         $mdValues = $this->extractMdFields($data);
//         $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
//         $client = new MongoClient($mongoUri);
//         $database = $client->isa_qa;
//         $collection = $database->device_events;
    
//         $events = [];
//         if (!empty($mdValues)) {
//             $uniqueMdValues = array_unique((array) $mdValues);
//             $uniqueMdValues = array_filter($uniqueMdValues, function ($value) {
//                 return !empty($value);
//             });
//             $uniqueMdValues = array_map('intval', $uniqueMdValues);
//             $uniqueMdValues = array_values($uniqueMdValues);
        
//             foreach ($uniqueMdValues as $moduleId) {
//                 $event = $collection->findOne(
//                     ['module_id' => $moduleId],
//                     ['sort' => ['createdAt' => -1]]
//                 );
//                 if ($event) {
//                     $events[] = $event;
//                 }
//             }
//         }
    
//         $eventsData = json_encode($events, JSON_PRETTY_PRINT);
       
//         return response()->json(['eventData' => $events]);
//     }    

//     private function extractMdFields($data)
//     {
//         $mdFields = [];

//         array_walk_recursive($data, function ($value, $key) use (&$mdFields) {
//             if ($key === 'md' && !is_null($value)) {
//                 $mdFields[] = $value;
//             }
//         });

//         return $mdFields;
//     }

//     // For Api
//     public function apiSites()
//     {
//         $user = Auth::guard('admin_api')->user();

//         if (!$user) {
//             return response()->json(['message' => 'Unauthorized'], 401);
//         }

//         if ($user->hasRole('superadmin')) {
//             $sites = Site::all();
//         } else {
//             $sites = Site::where('email', $user->email)->get();
//         }

//         return response()->json([
//             'success' => true,
//             'data' => $sites,
//         ]);
//     }

//     public function apiStoreDevice(Request $request)
//     {
//         $emails = is_array($request->userEmail) 
//             ? $request->userEmail 
//             : explode(',', $request->userEmail);

//         $validator = Validator::make($request->all(), [
//             'deviceName'       => 'required|string|max:255',
//             'deviceId'         => 'required|string|max:255',
//             'moduleId'         => 'required|string|max:255',
//             'eventField'       => 'required|string|max:255',
//             'siteId'           => 'required|string|max:255',
//             'lowerLimit'       => 'nullable|numeric',
//             'upperLimit'       => 'nullable|numeric',
//             'lowerLimitMsg'    => 'nullable|string|max:255',
//             'upperLimitMsg'    => 'nullable|string|max:255',
//             'userEmail'        => ['required', function ($attribute, $value, $fail) use ($emails) {
//                 foreach ($emails as $email) {
//                     if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
//                         $fail('One or more email addresses are invalid.');
//                         break;
//                     }
//                 }
//             }],
//             'userPassword'     => 'required|string|min:8',
//             'userPassword'     => 'required|string|min:8',
//             'owner_email'      => 'nullable|email|max:255',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['errors' => $validator->errors()], 422);
//         }

//         // Check for existing device
//         $exists = DB::table('device_events')
//             ->where('deviceName', $request->deviceName)
//             ->where('deviceId', $request->deviceId)
//             ->exists();

//         if ($exists) {
//             return response()->json([
//                 'message' => 'Device with this name and ID already exists.'
//             ], 409);
//         }

//         // Insert device record
//         DB::table('device_events')->insert([
//             'deviceName'     => $request->deviceName,
//             'deviceId'       => $request->deviceId,
//             'moduleId'       => $request->moduleId,
//             'eventField'     => $request->eventField,
//             'siteId'         => $request->siteId,
//             'lowerLimit'     => $request->lowerLimit,
//             'upperLimit'     => $request->upperLimit,
//             'lowerLimitMsg'  => $request->lowerLimitMsg,
//             'upperLimitMsg'  => $request->upperLimitMsg,
//             'userEmail'      => is_array($request->userEmail) 
//                 ? implode(',', $request->userEmail) 
//                 : $request->userEmail,
//             'userPassword'   => Hash::make($request->userPassword),
//             'owner_email'    => $request->owner_email,
//             'created_at'     => now(),
//             'updated_at'     => now()
//         ]);

//         return response()->json([
//             'message' => 'Device event saved successfully'
//         ], 201);
//     }

//     public function apiFetchDevice(Request $request)
//     {
//         $data = DB::table('device_events')->get();
//          return view('backend.pages.notification.dg-list', ['data' => $data]);
//     }

//     public function NotificationCreate(Request $request)
//     {
//          $data = DB::table('device_events')->get();
//         return view('backend.pages.notification.create-site', ['data' => $data]);
//     }

//     public function NotificationEdit(Request $request)
//     {
//         return view('backend.pages.notification.edit-site');
//     }
    
//     public function apiUpdateDevice(Request $request)
//     {
//         $device = DeviceEvent::find($request->id);

//         if (!$device) {
//             return response()->json(['message' => 'Device not found.'], 404);
//         }

//         $emails = is_array($request->userEmail)
//             ? $request->userEmail
//             : explode(',', $request->userEmail);

//         // Update fields
//         $device->deviceName      = $request->deviceName;
//         $device->deviceId        = $request->deviceId;
//         $device->moduleId        = $request->moduleId;
//         $device->eventField      = $request->eventField;
//         $device->siteId          = $request->siteId;
//         $device->lowerLimit      = $request->lowerLimit;
//         $device->upperLimit      = $request->upperLimit;
//         $device->lowerLimitMsg   = $request->lowerLimitMsg;
//         $device->upperLimitMsg   = $request->upperLimitMsg;
//         $device->userEmail       = json_encode($emails); // ✅ Save as JSON array
//         $device->userPassword    = $request->userPassword;
//         $device->owner_email     = $request->owner_email;

//         $device->save();

//         return response()->json(['message' => 'Device updated successfully.']);
//     }

//     public function showDeviceForm()
//     {
//         $data = DB::table('device_events')->get();
//         return view('device-update', compact('data'));
//     }

//     public function startProcess(Request $request)
//     {
//         $data = $request->only(['argValue', 'moduleId', 'cmdField', 'cmdArg', 'actionType']);
//         $action = $data['actionType'] ?? 'unknown';

//         try {
//             $apiUrl = 'http://app.sochiot.com:8082/api/config-engine/device/command/push/remote';

//             $response = Http::post($apiUrl, [
//                 'argValue' => $data['argValue'],
//                 'moduleId' => $data['moduleId'],
//                 'cmdField' => $data['cmdField'],
//                 'cmdArg' => $data['cmdArg'],
//             ]);

//             return response()->json([
//                 'message' => ucfirst($action) . ' process completed successfully.',
//                 'external_response' => $response->json(),
//             ]);
//         } catch (\Exception $e) {
//             return response()->json([
//                 'message' => ucfirst($action) . ' process failed.',
//                 'error' => $e->getMessage(),
//             ], 500);
//         }
//     }
// }

// start code here 

public function AdminSites(Request $request)     
{
    $role = $request->query('role');
    $bankName = $request->query('bank_name');
    $location = $request->query('location');

    $siteData = collect();
    $eventData = [];
    $latestCreatedAt = null;

    $user = Auth::user();
    if (!$user) {
        return redirect()->route('admin.login')->withErrors('You must be logged in.');
    }

    $userEmail = $user->email;

    if (!empty($location)) {
        $query = DB::table('sites')->where('email', $userEmail);

        if (!empty($bankName) && $bankName !== 'Select Bank') {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.generator')) = ?", [$bankName]);
        }
        if (!empty($location) && $location !== 'Select Location') {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(data, '$.group')) = ?", [$location]);
        }
        
        $siteData = $query->get();

        $decodedSiteData = $siteData->map(function ($site) {
            return json_decode($site->data, true);
        });

        // Get ALL events without filtering by module_id
        $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
        $client = new MongoClient($mongoUri);
        $database = $client->isa_qa;
        $collection = $database->device_events;

        // Fetch all events with limit to prevent memory issues
        $allEvents = $collection->find([], [
            'sort' => ['createdAt' => -1],
            'limit' => 5000
        ]);
        
        foreach ($allEvents as $event) {
            $eventData[] = $event;
        }

        // Sort events using enhanced timestamp extraction
        usort($eventData, function ($a, $b) {
            $timestampA = $this->extractTimestamp($a);
            $timestampB = $this->extractTimestamp($b);
            return $timestampB <=> $timestampA;
        });

        // Calculate latestCreatedAt using enhanced method
        if (!empty($eventData)) {
            $latestTimestamp = $this->extractTimestamp($eventData[0]);
            if ($latestTimestamp > 0) {
                $latestCreatedAt = $this->formatDateTime($latestTimestamp);
            } else {
                $latestCreatedAt = 'N/A';
            }
        } else {
            $latestCreatedAt = 'N/A';
        }

        // Process ALL events for display with perfect date formatting
        foreach ($eventData as &$event) {
            $timestamp = $this->extractTimestamp($event);
            if ($timestamp > 0) {
                $event['createdAt'] = $this->formatDateTime($timestamp);
            } else {
                $event['createdAt'] = 'N/A';
            }
            $event['latestCreatedAt'] = $latestCreatedAt;
        }

        // Set data for ALL sites - use actual SQL data
        foreach ($siteData as $site) {
            $this->setActualSiteData($site, $eventData);
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('backend.pages.sites.partials.site-table', compact('siteData', 'decodedSiteData', 'eventData', 'latestCreatedAt'))->render()
            ]);
        }

        return view('backend.pages.sites.admin-sites', compact('siteData', 'decodedSiteData', 'eventData', 'latestCreatedAt'));
    } else {
        ini_set('max_execution_time', 180);

        $user = Auth::guard('admin')->user();
        $userEmail = $user->email;

        // Get ALL sites for the user with actual data
        $siteData = $user->hasRole('superadmin')
            ? Site::select(['id', 'site_name', 'slug', 'email', 'data', 'device_id', 'clusterID', 'created_at', 'updated_at'])->get()
            : Site::where('email', $userEmail)->select(['id', 'site_name', 'slug', 'email', 'data', 'device_id', 'clusterID', 'created_at', 'updated_at'])->get();

        \Log::info("Total sites found: " . $siteData->count());

        // Get ALL event data without any filtering
        $eventData = MongodbFrontend::get()
            ->map(function ($doc) {
                return $doc->data;
            })->toArray();

        \Log::info("Total events found: " . count($eventData));
        
        // Sort events by timestamp using enhanced method
        usort($eventData, function ($a, $b) {
            $timestampA = $this->extractTimestamp($a);
            $timestampB = $this->extractTimestamp($b);
            return $timestampB <=> $timestampA;
        });

        // Calculate latestCreatedAt using enhanced method
        if (!empty($eventData)) {
            $latestTimestamp = $this->extractTimestamp($eventData[0]);
            if ($latestTimestamp > 0) {
                $latestCreatedAt = $this->formatDateTime($latestTimestamp);
            } else {
                $latestCreatedAt = 'N/A';
            }
        } else {
            $latestCreatedAt = 'N/A';
        }
        
        // Process ALL events for display with perfect date formatting
        foreach ($eventData as &$event) {
            $timestamp = $this->extractTimestamp($event);
            if ($timestamp > 0) {
                $event['createdAt'] = $this->formatDateTime($timestamp);
            } else {
                $event['createdAt'] = 'N/A';
            }
            $event['latestCreatedAt'] = $latestCreatedAt;
        }

        // Process ALL sites - ALWAYS show actual SQL data
        foreach ($siteData as $site) {
            $this->setActualSiteData($site, $eventData);
        }
        
        $sitejsonData = json_decode($siteData->first()->data ?? '{}', true);
        
      
        // Final debug log
        \Log::info("AdminSites completed - Sites: " . $siteData->count() . ", Events: " . count($eventData));
        
        // Return ALL data to the view
        return view('backend.pages.sites.admin-sites', compact('siteData', 'sitejsonData', 'eventData', 'latestCreatedAt'));
    }
}

/**
 * Set actual site data from SQL database
 */
private function setActualSiteData($site, $eventData)
{
    // Always decode site data from SQL first - this is the ACTUAL data
    $siteJson = json_decode($site->data ?? '{}', true);
    
    // Set ALL actual data from SQL database
    $site->actual_fuel_level = $this->getActualFuelLevel($siteJson);
    $site->actual_running_hours = $this->getActualRunningHours($siteJson);
    $site->actual_bank_name = $this->getActualBankName($siteJson);
    $site->actual_location = $this->getActualLocation($siteJson);
    $site->actual_status = $this->getActualStatus($siteJson);
    
    // For backward compatibility, also set the main fields
    $site->fuel_level = $site->actual_fuel_level;
    $site->running_hours = $site->actual_running_hours;
    $site->bank_name = $site->actual_bank_name;
    $site->location = $site->actual_location;
    $site->status = $site->actual_status;
    
    // Try to get updatedAt from events with perfect date formatting
    $matchingEvent = $this->findMatchingEvent($site, $eventData);
    if ($matchingEvent) {
        $site->updatedAt = $this->extractUpdatedAtWithPerfectFormat($matchingEvent);
        \Log::info("✅ Event data used for: {$site->site_name}");
    } else {
        // If no event found, use SQL's updated_at or created_at
        $site->updatedAt = $this->getSiteLastUpdated($site);
        \Log::info("ℹ️ Using SQL data for: {$site->site_name}");
    }
    
    // Debug: Log all actual data
    \Log::debug("ACTUAL DATA - Site: {$site->site_name}, Fuel: {$site->actual_fuel_level}, Hours: {$site->actual_running_hours}, Bank: {$site->actual_bank_name}, Location: {$site->actual_location}");
}

/**
 * Get actual fuel level from SQL data
 */
private function getActualFuelLevel($siteJson)
{
    $fuelLevel = $siteJson['parameters']['fuel']['add'] ?? '0';
    
    // Handle different fuel level formats
    if (is_numeric($fuelLevel)) {
        $fuelValue = (float) $fuelLevel;
    } else {
        // Extract numeric value from string
        $fuelValue = (float) preg_replace('/[^0-9.]/', '', $fuelLevel);
    }
    
    // Format as percentage
    $formattedFuel = round($fuelValue) . '%';
    
    // Add Low Fuel warning if applicable
    if ($fuelValue <= 20) {
        return $formattedFuel . '<br><small style="color:red;">Low Fuel</small>';
    }
    
    return $formattedFuel;
}

/**
 * Get actual running hours from SQL data
 */
private function getActualRunningHours($siteJson)
{
    $runningHours = $siteJson['running_hours']['value'] ?? '0';
    
    // Handle different running hours formats
    if (is_numeric($runningHours)) {
        $hours = (int) $runningHours;
        $minutes = round(($runningHours - $hours) * 60);
        return $hours . ' hrs ' . $minutes . ' mins';
    } else {
        // Return as is if it's already formatted
        return $runningHours;
    }
}

/**
 * Get actual bank name from SQL data
 */
private function getActualBankName($siteJson)
{
    return $siteJson['generator'] ?? 'N/A';
}

/**
 * Get actual location from SQL data
 */
private function getActualLocation($siteJson)
{
    return $siteJson['group'] ?? 'N/A';
}

/**
 * Get actual status from SQL data
 */
private function getActualStatus($siteJson)
{
    $fuelLevel = $siteJson['parameters']['fuel']['add'] ?? '0';
    $fuelValue = (float) preg_replace('/[^0-9.]/', '', $fuelLevel);
    
    if ($fuelValue <= 20) {
        return 'Low Fuel';
    }
    
    // Check other status parameters
    $runStatus = $siteJson['run_status']['add'] ?? null;
    if ($runStatus === '1' || $runStatus === 1) {
        return 'Running';
    }
    
    $alarmStatus = $this->checkAlarmStatus($siteJson);
    if ($alarmStatus !== '—') {
        return $alarmStatus;
    }
    
    return '—';
}

/**
 * Check alarm status from SQL data
 */
private function checkAlarmStatus($siteJson)
{
    $alarms = $siteJson['alarm_status'] ?? [];
    
    $activeAlarms = [];
    
    // Check each alarm status
    $alarmChecks = [
        'emergency_stop_status' => 'Emergency Stop',
        'high_coolant_temperature_status' => 'High Coolant Temp',
        'low_oil_pressure_status' => 'Low Oil Pressure',
        'high_oil_temperature_status' => 'High Oil Temp',
        'over_speed_status' => 'Over Speed',
        'under_speed_status' => 'Under Speed',
        'battery_level_status' => 'Low Battery',
        'fail_to_start_status' => 'Fail to Start'
    ];
    
    foreach ($alarmChecks as $alarmKey => $alarmName) {
        $alarmValue = $alarms[$alarmKey]['add'] ?? null;
        if ($alarmValue === '1' || $alarmValue === 1) {
            $activeAlarms[] = $alarmName;
        }
    }
    
    if (!empty($activeAlarms)) {
        return implode(', ', $activeAlarms);
    }
    
    return '—';
}

/**
 * Get site's last updated time from SQL
 */
/**
 * Get site's last updated time from SQL
 */
private function getSiteLastUpdated($site)
{
    // Use SQL's updated_at if available (it's a Carbon instance)
    if (!empty($site->updated_at)) {
        return $this->formatDateTime($site->updated_at->getTimestamp() * 1000);
    }
    
    // Fallback to created_at (also a Carbon instance)
    if (!empty($site->created_at)) {
        return $this->formatDateTime($site->created_at->getTimestamp() * 1000);
    }
    
    return 'N/A';
}

/**
 * Perfect date and time formatting
 */
private function formatDateTime($timestampMillis)
{
    try {
        // Convert milliseconds to seconds
        $timestampSeconds = $timestampMillis / 1000;
        
        $dateTime = new \DateTime('@' . intval($timestampSeconds));
        $dateTime->setTimezone(new \DateTimeZone('Asia/Kolkata'));
        
        // Format: "16-10-2025 14:30:25"
        return $dateTime->format('d-m-Y H:i:s');
        
    } catch (\Exception $e) {
        \Log::error("Date formatting error for timestamp: " . $timestampMillis . " - " . $e->getMessage());
        return 'N/A';
    }
}

/**
 * Extract updatedAt with perfect formatting
 */
private function extractUpdatedAtWithPerfectFormat($event)
{
    $batUpdated = $event['updatedAt'] ?? null;

    if (is_array($batUpdated) && isset($batUpdated['$date']['$numberLong'])) {
        $timestamp = (int) $batUpdated['$date']['$numberLong'];
        return $this->formatDateTime($timestamp);
    } elseif ($batUpdated instanceof \MongoDB\BSON\UTCDateTime) {
        $timestamp = (int) $batUpdated->toDateTime()->getTimestamp() * 1000;
        return $this->formatDateTime($timestamp);
    } elseif (is_string($batUpdated)) {
        try {
            $date = new \DateTime($batUpdated);
            $timestamp = $date->getTimestamp() * 1000;
            return $this->formatDateTime($timestamp);
        } catch (\Exception $e) {
            return 'N/A';
        }
    } elseif (is_numeric($batUpdated)) {
        $timestamp = (int) $batUpdated;
        // Auto-detect if it's seconds or milliseconds
        if ($timestamp < 10000000000) {
            $timestamp = $timestamp * 1000; // Convert seconds to milliseconds
        }
        return $this->formatDateTime($timestamp);
    }

    return 'N/A';
}

/**
 * Enhanced timestamp extraction for sorting
 */
private function extractTimestamp($event)
{
    // Check all possible timestamp fields and formats
    $timestampFields = [
        'createdAt',
        'created_at_timestamp', 
        'timestamp',
        'created_at',
        'updatedAt',
        'updated_at',
        'time',
        'date'
    ];
    
    foreach ($timestampFields as $field) {
        if (isset($event[$field])) {
            $value = $event[$field];
            
            // MongoDB Extended JSON format
            if (is_array($value) && isset($value['$date'])) {
                $dateValue = $value['$date'];
                if (isset($dateValue['$numberLong'])) {
                    return (int) $dateValue['$numberLong'];
                }
                if (is_numeric($dateValue)) {
                    return (int) $dateValue;
                }
            }
            
            // MongoDB BSON UTCDateTime object
            if ($value instanceof \MongoDB\BSON\UTCDateTime) {
                return (int) $value->toDateTime()->getTimestamp() * 1000;
            }
            
            // Numeric timestamp
            if (is_numeric($value)) {
                $timestamp = (int) $value;
                // Auto-detect seconds vs milliseconds
                if ($timestamp < 10000000000) {
                    return $timestamp * 1000;
                }
                return $timestamp;
            }
            
            // String date
            if (is_string($value)) {
                try {
                    $date = new \DateTime($value);
                    return (int) $date->getTimestamp() * 1000;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }
    
    return 0;
}

// Keep the existing findMatchingEvent method
private function findMatchingEvent($site, $eventData)
{
    $deviceId = strtolower(trim($site->device_id ?? ''));
    $siteJson = json_decode($site->data ?? '{}', true);
    $siteMd = $siteJson['parameters']['battery_voltage']['md'] ?? null;

    // Strategy 1: Exact device_id match
    if (!empty($deviceId)) {
        foreach ($eventData as $event) {
            $eventDeviceId = strtolower(trim($event['device_id'] ?? ''));
            if ($eventDeviceId === $deviceId) {
                return $event;
            }
        }
    }

    // Strategy 2: Module ID match
    if ($siteMd) {
        foreach ($eventData as $event) {
            $eventModuleId = $event['module_id'] ?? null;
            if ($eventModuleId && (string)$eventModuleId === (string)$siteMd) {
                return $event;
            }
        }
    }

    // Strategy 3: Partial device_id match
    if (!empty($deviceId)) {
        foreach ($eventData as $event) {
            $eventDeviceId = strtolower(trim($event['device_id'] ?? ''));
            if (!empty($eventDeviceId) && (
                str_contains($eventDeviceId, $deviceId) || 
                str_contains($deviceId, $eventDeviceId)
            )) {
                return $event;
            }
        }
    }

    return null;
}

// end code time stram  

  public function fetchStatuses(Request $request)
    {
        $siteIds = $request->input('site_ids', []);
        $sites = Site::whereIn('id', $siteIds)->select('id', 'device_id', 'clusterID')->get();

        $httpClient = new Client();
        $dgRequests = [];
        $controllerRequests = [];
        $dgResults = [];
        $controllerResults = [];

        foreach ($sites as $site) {
            if ($site->device_id) {
                $dgRequests[$site->id] = new GuzzleRequest('GET', "http://app.sochiot.com/api/config-engine/device/status/uuid/{$site->device_id}");
            }
            if ($site->clusterID) {
                $controllerRequests[$site->id] = new GuzzleRequest('GET', "http://app.sochiot.com/api/config-engine/gateway/status/uuid/{$site->clusterID}");
            }
        }

        // Pool for DG
        $dgPool = new Pool($httpClient, $dgRequests, [
            'concurrency' => 10,
            'fulfilled' => function ($response, $siteId) use (&$dgResults) {
                $dgResults[$siteId] = strtoupper(trim($response->getBody()->getContents()));
            },
            'rejected' => function () {}
        ]);

        // Pool for Controller
        $ctrlPool = new Pool($httpClient, $controllerRequests, [
            'concurrency' => 10,
            'fulfilled' => function ($response, $siteId) use (&$controllerResults) {
                $controllerResults[$siteId] = strtoupper(trim($response->getBody()->getContents()));
            },
            'rejected' => function () {}
        ]);

        $dgPool->promise()->wait();
        $ctrlPool->promise()->wait();

        $statuses = [];
        foreach ($siteIds as $siteId) {
            $statuses[$siteId] = [
                'dg_status' => $dgResults[$siteId] ?? 'OFFLINE',
                'controller_status' => $controllerResults[$siteId] ?? 'OFFLINE',
            ];
        }

        return response()->json($statuses);
    }

    public function fetchLatestData($slug)
    {
        $siteData = Site::where('slug', $slug)->first();
        
        if (!$siteData) {
            return response()->json(['error' => 'Site not found or module_id is missing.'], 404);
        }
    
        $data = json_decode($siteData->data, true);
        $mdValues = $this->extractMdFields($data);
        $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
        $client = new MongoClient($mongoUri);
        $database = $client->isa_qa;
        $collection = $database->device_events;
    
        $events = [];
        if (!empty($mdValues)) {
            $uniqueMdValues = array_unique((array) $mdValues);
            $uniqueMdValues = array_filter($uniqueMdValues, function ($value) {
                return !empty($value);
            });
            $uniqueMdValues = array_map('intval', $uniqueMdValues);
            $uniqueMdValues = array_values($uniqueMdValues);
        
            foreach ($uniqueMdValues as $moduleId) {
                $event = $collection->findOne(
                    ['module_id' => $moduleId],
                    ['sort' => ['createdAt' => -1]]
                );
                if ($event) {
                    $events[] = $event;
                }
            }
        }
    
        $eventsData = json_encode($events, JSON_PRETTY_PRINT);
       
        return response()->json(['eventData' => $events]);
    }    

    private function extractMdFields($data)
    {
        $mdFields = [];

        array_walk_recursive($data, function ($value, $key) use (&$mdFields) {
            if ($key === 'md' && !is_null($value)) {
                $mdFields[] = $value;
            }
        });

        return $mdFields;
    }

    // For Api
    public function apiSites()
    {
        $user = Auth::guard('admin_api')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->hasRole('superadmin')) {
            $sites = Site::all();
        } else {
            $sites = Site::where('email', $user->email)->get();
        }

        return response()->json([
            'success' => true,
            'data' => $sites,
        ]);
    }

    public function apiStoreDevice(Request $request)
    {
        $emails = is_array($request->userEmail) 
            ? $request->userEmail 
            : explode(',', $request->userEmail);

        $validator = Validator::make($request->all(), [
            'deviceName'       => 'required|string|max:255',
            'deviceId'         => 'required|string|max:255',
            'moduleId'         => 'required|string|max:255',
            'eventField'       => 'required|string|max:255',
            'siteId'           => 'required|string|max:255',
            'lowerLimit'       => 'nullable|numeric',
            'upperLimit'       => 'nullable|numeric',
            'lowerLimitMsg'    => 'nullable|string|max:255',
            'upperLimitMsg'    => 'nullable|string|max:255',
            'userEmail'        => ['required', function ($attribute, $value, $fail) use ($emails) {
                foreach ($emails as $email) {
                    if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                        $fail('One or more email addresses are invalid.');
                        break;
                    }
                }
            }],
            'userPassword'     => 'required|string|min:8',
            'userPassword'     => 'required|string|min:8',
            'owner_email'      => 'nullable|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check for existing device
        $exists = DB::table('device_events')
            ->where('deviceName', $request->deviceName)
            ->where('deviceId', $request->deviceId)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Device with this name and ID already exists.'
            ], 409);
        }

        // Insert device record
        DB::table('device_events')->insert([
            'deviceName'     => $request->deviceName,
            'deviceId'       => $request->deviceId,
            'moduleId'       => $request->moduleId,
            'eventField'     => $request->eventField,
            'siteId'         => $request->siteId,
            'lowerLimit'     => $request->lowerLimit,
            'upperLimit'     => $request->upperLimit,
            'lowerLimitMsg'  => $request->lowerLimitMsg,
            'upperLimitMsg'  => $request->upperLimitMsg,
            'userEmail'      => is_array($request->userEmail) 
                ? implode(',', $request->userEmail) 
                : $request->userEmail,
            'userPassword'   => Hash::make($request->userPassword),
            'owner_email'    => $request->owner_email,
            'created_at'     => now(),
            'updated_at'     => now()
        ]);

        return response()->json([
            'message' => 'Device event saved successfully'
        ], 201);
    }

    public function apiFetchDevice(Request $request)
    {
        $data = DB::table('device_events')->get();
         return view('backend.pages.notification.dg-list', ['data' => $data]);
    }

    public function NotificationCreate(Request $request)
    {
         $data = DB::table('device_events')->get();
        return view('backend.pages.notification.create-site', ['data' => $data]);
    }

    public function NotificationEdit(Request $request)
    {
        return view('backend.pages.notification.edit-site');
    }
    
    public function apiUpdateDevice(Request $request)
    {
        $device = DeviceEvent::find($request->id);

        if (!$device) {
            return response()->json(['message' => 'Device not found.'], 404);
        }

        $emails = is_array($request->userEmail)
            ? $request->userEmail
            : explode(',', $request->userEmail);

        // Update fields
        $device->deviceName      = $request->deviceName;
        $device->deviceId        = $request->deviceId;
        $device->moduleId        = $request->moduleId;
        $device->eventField      = $request->eventField;
        $device->siteId          = $request->siteId;
        $device->lowerLimit      = $request->lowerLimit;
        $device->upperLimit      = $request->upperLimit;
        $device->lowerLimitMsg   = $request->lowerLimitMsg;
        $device->upperLimitMsg   = $request->upperLimitMsg;
        $device->userEmail       = json_encode($emails); // ✅ Save as JSON array
        $device->userPassword    = $request->userPassword;
        $device->owner_email     = $request->owner_email;

        $device->save();

        return response()->json(['message' => 'Device updated successfully.']);
    }

    public function showDeviceForm()
    {
        $data = DB::table('device_events')->get();
        return view('device-update', compact('data'));
    }

    public function startProcess(Request $request)
    {
        // dd($request->all());
        $data = $request->only(['argValue', 'moduleId', 'cmdField', 'cmdArg', 'actionType']);
        $action = $data['actionType'] ?? 'unknown';

        try {
            $apiUrl = 'http://app.sochiot.com:8082/api/config-engine/device/command/push/remote';

            $response = Http::post($apiUrl, [
                'argValue' => $data['argValue'],
                'moduleId' => $data['moduleId'],
                'cmdField' => $data['cmdField'],
                'cmdArg' => $data['cmdArg'],
            ]);

            return response()->json([
                'message' => ucfirst($action) . ' process completed successfully.',
                'external_response' => $response->json(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => ucfirst($action) . ' process failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}