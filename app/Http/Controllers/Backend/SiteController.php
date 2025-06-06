<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\SiteRequest;
use App\Models\Site;
use App\User;
use App\Models\Admin;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Str;
use MongoDB\BSON\UTCDateTime;
use DateTimeZone;
use Illuminate\Http\Request;
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
        // Fetch site data using the provided slug
        $siteData = Site::where('slug', $slug)->first();
        if (!$siteData) {
            return redirect()->back()->withErrors('Site not found or module_id is missing.');
        }
    
        // Decode the data from JSON to array
        $data = json_decode($siteData->data, true);
        $mdValues = $this->extractMdFields($data);
    
        // MongoDB connection setup
        $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
        $client = new MongoClient($mongoUri);
        $database = $client->isa_qa;
        $collection = $database->device_events;
    
        $events = [];
    
        // Ensure that mdValues are not empty
        if (!empty($mdValues)) {
            // Clean up the mdValues array by removing empty values and ensuring integers
            $uniqueMdValues = array_unique((array) $mdValues);
            $uniqueMdValues = array_filter($uniqueMdValues, function ($value) {
                return !empty($value);
            });
            $uniqueMdValues = array_map('intval', $uniqueMdValues);
            $uniqueMdValues = array_values($uniqueMdValues);
    
            // Fetch events for each unique mdValue (module_id)
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
    
        // Sort the events array by 'createdAt' in descending order
        usort($events, function ($a, $b) {
            $createdAtA = new UTCDateTime($a['created_at_timestamp']);
            $createdAtB = new UTCDateTime($b['created_at_timestamp']);
            return $createdAtB <=> $createdAtA; // Sort in descending order
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
        
        // return $siteData;
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
            // return $events;
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

            $mdValues = $this->extractMdFields($decodedSiteData->toArray());

            $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
            $client = new MongoClient($mongoUri);
            $database = $client->isa_qa;
            $collection = $database->device_events;

            if (!empty($mdValues)) {
                $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

                foreach ($uniqueMdValues as $moduleId) {
                    $event = $collection->findOne(
                        ['module_id' => $moduleId],
                        ['sort' => ['createdAt' => -1]]
                    );

                    if ($event) {
                        $eventData[] = $event;
                    }
                }
            }

            usort($eventData, function ($a, $b) {
                return ($b['created_at_timestamp'] ?? 0) <=> ($a['created_at_timestamp'] ?? 0);
            });

            $latestCreatedAt = !empty($eventData) ? $eventData[0]['createdAt']->toDateTime()
                ->setTimezone(new DateTimeZone('Asia/Kolkata'))
                ->format('d-m-Y H:i:s') : 'N/A';

            foreach ($eventData as &$event) {
                $event['createdAt'] = $event['createdAt']->toDateTime()
                    ->setTimezone(new DateTimeZone('Asia/Kolkata'))
                    ->format('d-m-Y H:i:s');
                $event['latestCreatedAt'] = $latestCreatedAt;
            }

            foreach ($siteData as $site) {
                $matchingEvent = collect($eventData)->first(function ($event) use ($site) {
                    return isset($event['device_id'], $site->device_id) &&
                        trim(strtolower($event['device_id'])) === trim(strtolower($site->device_id));
                });

                $site->updatedAt = isset($matchingEvent['updatedAt']) ? $matchingEvent['updatedAt']->toDateTime()
                    ->setTimezone(new DateTimeZone('Asia/Kolkata'))
                    ->format('d-m-Y H:i:s') : 'N/A';
            }

            if ($request->ajax()) {
                return response()->json([
                    'html' => view('backend.pages.sites.partials.site-table', compact('siteData', 'decodedSiteData', 'eventData', 'latestCreatedAt'))->render()
                ]);
            }

            return view('backend.pages.sites.admin-sites', compact('siteData', 'decodedSiteData', 'eventData', 'latestCreatedAt'));
        } else {
            $user = Auth::guard('admin')->user();
            if (!$user->hasRole('superadmin')) {
                $siteData = Site::where('email', $userEmail)->get();
            } else {
                $siteData = Site::get();
            }

            $decodedSiteData = $siteData->map(function ($site) {
                return json_decode($site->data, true);
            });

            $mdValues = $this->extractMdFields($decodedSiteData->toArray());

            $mongoUri = 'mongodb://isaqaadmin:password@44.240.110.54:27017/isa_qa';
            $client = new MongoClient($mongoUri);
            $database = $client->isa_qa;
            $collection = $database->device_events;

            if (!empty($mdValues)) {
                $uniqueMdValues = array_unique(array_filter(array_map('intval', (array) $mdValues)));

                foreach ($uniqueMdValues as $moduleId) {
                    $event = $collection->findOne(
                        ['module_id' => $moduleId],
                        ['sort' => ['createdAt' => -1]]
                    );

                    if ($event) {
                        $eventData[] = $event;
                    }
                }
            }

            usort($eventData, function ($a, $b) {
                return ($b['created_at_timestamp'] ?? 0) <=> ($a['created_at_timestamp'] ?? 0);
            });

            $latestCreatedAt = !empty($eventData) ? $eventData[0]['createdAt']->toDateTime()
                ->setTimezone(new DateTimeZone('Asia/Kolkata'))
                ->format('d-m-Y H:i:s') : 'N/A';

            foreach ($eventData as &$event) {
                $event['createdAt'] = $event['createdAt']->toDateTime()
                    ->setTimezone(new DateTimeZone('Asia/Kolkata'))
                    ->format('d-m-Y H:i:s');
                $event['latestCreatedAt'] = $latestCreatedAt;
            }

            $sitejsonData = json_decode($siteData->first()->data, true);

            foreach ($siteData as $site) {
                $matchingEvent = collect($eventData)->first(function ($event) use ($site) {
                    return isset($event['device_id'], $site->device_id) &&
                        trim(strtolower($event['device_id'])) === trim(strtolower($site->device_id));
                });

                $updatedAt = 'N/A';
                if ($matchingEvent && isset($matchingEvent['updatedAt'])) {
                    $updatedAt = $matchingEvent['updatedAt']->toDateTime()
                        ->setTimezone(new DateTimeZone('Asia/Kolkata'))
                        ->format('d-m-Y H:i:s');
                }

                $site->updatedAt = $updatedAt;
            }
            // return $eventData;
            return view('backend.pages.sites.admin-sites', compact('siteData', 'sitejsonData', 'eventData', 'latestCreatedAt'));
        }
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


// public function apiStoreDevice(Request $request)
// {
//     $emails = is_array($request->userEmail) 
//         ? $request->userEmail 
//         : explode(',', $request->userEmail);

//     $validator = Validator::make($request->all(), [
//         'deviceName'       => 'required|string|max:255',
//         'deviceId'         => 'required|string|max:255',
//         'moduleId'         => 'required|string|max:255',
//         'eventField'       => 'required|string|max:255',
//         'siteId'           => 'required|string|max:255',
//         'lowerLimit'       => 'nullable|numeric',
//         'upperLimit'       => 'nullable|numeric',
//         'lowerLimitMsg'    => 'nullable|string|max:255',
//         'upperLimitMsg'    => 'nullable|string|max:255',
//         'userEmail'        => ['required', function ($attribute, $value, $fail) use ($emails) {
//             foreach ($emails as $email) {
//                 if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
//                     $fail('One or more email addresses are invalid.');
//                     break;
//                 }
//             }
//         }],
//         'userPassword'     => 'required|string|min:8',
//         'owner_email'      => 'nullable|email|max:255',
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['errors' => $validator->errors()], 422);
//     }

//     // Check for existing device
//     $exists = DB::table('device_events')
//         ->where('deviceName', $request->deviceName)
//         ->where('deviceId', $request->deviceId)
//         ->exists();

//     if ($exists) {
//         return response()->json([
//             'message' => 'Device with this name and ID already exists.'
//         ], 409);
//     }

//     // Insert device record
//     DB::table('device_events')->insert([
//         'deviceName'     => $request->deviceName,
//         'deviceId'       => $request->deviceId,
//         'moduleId'       => $request->moduleId,
//         'eventField'     => $request->eventField,
//         'siteId'         => $request->siteId,
//         'lowerLimit'     => $request->lowerLimit,
//         'upperLimit'     => $request->upperLimit,
//         'lowerLimitMsg'  => $request->lowerLimitMsg,
//         'upperLimitMsg'  => $request->upperLimitMsg,
//         'userEmail'      => is_array($request->userEmail) 
//             ? implode(',', $request->userEmail) 
//             : $request->userEmail,
//         'userPassword'   => Hash::make($request->userPassword),
//         'owner_email'    => $request->owner_email,
//         'created_at'     => now(),
//         'updated_at'     => now()
//     ]);

//     return response()->json([
//         'message' => 'Device event saved successfully'
//     ], 201);
// }



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

    //  public function apiUpdateDevice(Request $request, $deviceId)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'deviceName'       => 'required',
    //         'deviceId'         => 'required',
    //         'moduleId'         => 'required',
    //         'eventField'       => 'required',
    //         'siteId'           => 'required',
    //         'lowerLimit'       => 'nullable',
    //         'upperLimit'       => 'nullable',
    //         'lowerLimitMsg'    => 'nullable',
    //         'upperLimitMsg'    => 'nullable',
    //         'userEmail'        => 'required|email',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $event = DB::table('device_events')->where('deviceId', $deviceId)->first();

    //     if (!$event) {
    //         return response()->json(['message' => 'Device event not found'], 404);
    //     }

    //     DB::table('device_events')->where('deviceId', $deviceId)->update([
    //         'deviceName'     => $request->deviceName,
    //         'moduleId'       => $request->moduleId,
    //         'eventField'     => $request->eventField,
    //         'siteId'         => $request->siteId,
    //         'lowerLimit'     => $request->lowerLimit,
    //         'upperLimit'     => $request->upperLimit,
    //         'lowerLimitMsg'  => $request->lowerLimitMsg,
    //         'upperLimitMsg'  => $request->upperLimitMsg,
    //         'userEmail'      => $request->userEmail,
    //     ]);

    //     return response()->json(['message' => 'Device event updated successfully'], 200);
    // }
    

public function showDeviceForm()
    {
        $data = DB::table('device_events')->get();
        return view('device-update', compact('data'));
    }

    public function apiUpdateDevice(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:device_events,id',
            'deviceName' => 'required|string',
            'deviceId' => 'required|string',
            'moduleId' => 'required|string',
            'eventField' => 'required|string',
            'siteId' => 'required|string',
            'userEmail' => 'required|string',
            'userPassword' => 'required|string|min:6',
        ]);

        DB::table('device_events')->where('id', $request->id)->update([
            'deviceName' => $request->deviceName,
            'deviceId' => $request->deviceId,
            'moduleId' => $request->moduleId,
            'eventField' => $request->eventField,
            'siteId' => $request->siteId,
            'userEmail' => $request->userEmail,
            'userPassword' => Hash::make($request->userPassword),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Device updated successfully']);
    }
   
    


}