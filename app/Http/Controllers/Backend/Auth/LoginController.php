<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\Login;
use App\User;
use Illuminate\Support\Str;
use DB,Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::ADMIN_DASHBOARD;

    /**
     * show login form for admin guard
     *
     * @return void
     */
    public function showLoginForm()
    {
        return view('backend.auth.login');
    }


    /**
     * login admin
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        // Check if returning to Superadmin
        if ($request->has('user_id') && session('original_superadmin_id')) {
            $superadminId = session('original_superadmin_id');
            $originaladminId = session('original_admin_id');
            $superadmin = Admin::find($superadminId);
            
            $user = Admin::find($request->user_id);
            if ($superadmin && $superadmin->hasRole('superadmin')) {
                // dd($originaladminId);
                Auth::guard('admin')->logout();
                Auth::guard('admin')->login($superadmin);
                Login::where('user_id', $originaladminId)->update(['status' => 'inactive']);
            
                session()->forget('original_superadmin_id');
                return redirect()->route('admin.dashboard')->with('success', 'Returned to Superadmin account.');
            }
            session()->flash('error', 'Unable to return to Superadmin.');
            return redirect()->route('admin.login');
        }

        if ($request->has('user_id')) {
            $superadmin = Auth::guard('admin')->user();
        
            if ($superadmin && $superadmin->hasRole('superadmin')) {
                session(['original_superadmin_id' => $superadmin->id]);
                $user = Admin::find($request->user_id);
                session(['original_admin_id' => $request->user_id]);
                if ($user) {
                    Auth::guard('admin')->login($user);
                    Login::where('user_id', $user->id)->update(['status' => 'active']);
                    return redirect()->route('admin.dashboard');
                }
                session()->flash('error', 'User not found.');
                return back();
            }
            session()->flash('error', 'Unauthorized access.');
            return back();
        }
             
        // Regular login process
        $request->validate([
            'email' => 'required|max:50',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $users = Auth::guard('admin')->user();
            Login::where('user_id', $users->id)->delete();
            if (!$users->hasRole('superadmin')) {
                $data = Login::updateOrCreate(
                    ['user_id' => $users->id],
                    [
                        'role' => 'admin',
                        'ip_address' => $request->ip(),
                        'status' => 'active',
                    ]
                );
                return redirect('admin/admin-sites');
            } else {
                $data = Login::updateOrCreate(
                    ['user_id' => $users->id],
                    [
                        'role' => 'admin',
                        'ip_address' => $request->ip(),
                        'status' => 'active',
                    ]
                );
                return redirect('admin/sites');
            }            
            // return redirect('admin/sites');
            // return redirect('admin/admin-sites');
        }
        session()->flash('error', 'Invalid email and password');
        return back();
    }

    /**
     * logout admin guard
     *
     * @return void
     */

    public function logout()
    {
        $user = Auth::guard('admin')->user();
    
        if ($user) {
            Login::where('user_id', $user->id)->update(['status' => 'inactive']);
        }
    
        session()->forget('original_superadmin_id');
        Auth::guard('admin')->logout();
    
        return redirect()->route('admin.login');
    }     

    // For Api Use
    // public function Apilogin(Request $request)
    // {
    //     $request->validate([
    //     'email' => 'required|email',
    //     'password' => 'required',
    //     ]);

    //     if (!Auth::attempt($request->only('email', 'password'))) {
    //         return response()->json([
    //             'message' => 'Invalid credentials'
    //         ], 401);
    //     }

    //     $user = Auth::user();

    //     $token = $user->createToken('api-token')->plainTextToken;

    //     return response()->json([
    //         'message' => 'Logged in successfully',
    //         'user' => $user,
    //         'token' => $token,
    //     ]);
    // }

    

// public function Apilogin(Request $request)
// {
//     $request->validate([
//         'userEmail' => 'required|email',
//         'userPassword' => 'required|string',
//     ]);

//     // Fetch user from device_events table
//     $user = DB::table('device_events')->where('userEmail', $request->userEmail)->first();

//     if (!$user) {
//         return response()->json([
//             'message' => 'Invalid credentials (user not found)'
//         ], 401);
//     }

//     // If passwords are hashed in the DB
//     if (!Hash::check($request->userPassword, $user->userPassword)) {
//         return response()->json([
//             'message' => 'Invalid credentials (incorrect password)'
//         ], 401);
//     }

//     // Generate a random token (not stored)
//     $token = Str::random(60);

// return response()->json([
//     'message' => 'Logged in successfully',
//     'user'    => $user,
//     'token'   => $token,
// ]);

// }


// public function Apilogin(Request $request)
// {
//     $request->validate([
//         'userEmail' => 'required|email',
//         'userPassword' => 'required|string',
//     ]);

//     $devices = DB::table('device_events')->get();

//     foreach ($devices as $device) {
//         $rawEmails = $device->userEmail;

//         // Support both JSON array and comma-separated strings
//         $emails = json_decode($rawEmails, true);

//         if (!is_array($emails)) {
//             // fallback: treat as comma string
//             $emails = explode(',', $rawEmails);
//         }

//         foreach ($emails as $index => $email) {
//             if (trim(strtolower($email)) === trim(strtolower($request->userEmail))) {
//                 if (!empty($device->userPassword) && Hash::check($request->userPassword, $device->userPassword)) {
//                     $token = Str::random(60);

//                     return response()->json([
//                         'message' => '✅ Logged in successfully',
//                         'user' => $device,
//                         'matched_email_index' => $index,
//                         'token' => $token,
//                     ]);
//                 } else {
//                     return response()->json([
//                         'message' => '❌ Incorrect password',
//                     ], 401);
//                 }
//             }
//         }
//     }

//     return response()->json([
//         'message' => '❌ Invalid credentials (email not found)',
//     ], 401);
// }

// public function Apilogin(Request $request)
// {
//     $request->validate([
//         'userEmail' => 'required|email',
//         'userPassword' => 'required|string',
//     ]);

//     $devices = DB::table('device_events')->get();

//     foreach ($devices as $device) {
//         $rawEmails = $device->userEmail;

//         // Decode JSON or fallback to comma
//         $emails = json_decode($rawEmails, true);
//         if (!is_array($emails)) {
//             $emails = explode(',', $rawEmails);
//         }

//         foreach ($emails as $index => $email) {
//             if (trim(strtolower($email)) === trim(strtolower($request->userEmail))) {
//                 // ✅ Password check
//                 if (!empty($device->userPassword) && Hash::check($request->userPassword, $device->userPassword)) {
//                     // Create a filtered device object
//                     $filteredDevice = (array) $device;
//                     $filteredDevice['userEmail'] = $email; // ✅ Only matched email returned

//                     return response()->json([
//                         'message' => '✅ Logged in successfully',
//                         'user' => $filteredDevice,
//                         'matched_email_index' => $index,
//                         'token' => Str::random(60),
//                     ]);
//                 } else {
//                     return response()->json([
//                         'message' => '❌ Incorrect password',
//                     ], 401);
//                 }
//             }
//         }
//     }

//     return response()->json([
//         'message' => '❌ Invalid credentials (email not found)',
//     ], 401);
// }

public function Apilogin(Request $request)
{
    $request->validate([
        'userEmail'    => 'required|email',
        'userPassword' => 'required|string',
    ]);

    $devices = DB::table('device_events')->get();

    $matchedDevices = [];

    foreach ($devices as $device) {
        $rawEmails = $device->userEmail;

        // Decode JSON or fallback to comma-separated string
        $emails = json_decode($rawEmails, true);
        if (!is_array($emails)) {
            $emails = explode(',', $rawEmails);
        }

        foreach ($emails as $index => $email) {
            if (trim(strtolower($email)) === trim(strtolower($request->userEmail))) {
                // ✅ Password check
                if (!empty($device->userPassword) && Hash::check($request->userPassword, $device->userPassword)) {
                    // Include only the matched email
                    $filteredDevice = (array) $device;
                    $filteredDevice['userEmail'] = $email;
                    $filteredDevice['matched_email_index'] = $index;

                    $matchedDevices[] = $filteredDevice;
                }
            }
        }
    }

    if (count($matchedDevices) > 0) {
        return response()->json([
            'message' => '✅ Login successful',
            'devices' => $matchedDevices,
            'token'   => Str::random(60),
        ]);
    }

    return response()->json([
        'message' => '❌ Invalid credentials (email or password incorrect)',
    ], 401);
}





    public function Apilogout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out'
        ]);
    }
}