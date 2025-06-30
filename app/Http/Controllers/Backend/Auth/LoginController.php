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
    // public function login(Request $request)
    // {
    //     // Check if returning to Superadmin
    //     if ($request->has('user_id') && session('original_superadmin_id')) {
    //         $superadminId = session('original_superadmin_id');
    //         $originaladminId = session('original_admin_id');
    //         $superadmin = Admin::find($superadminId);
            
    //         $user = Admin::find($request->user_id);
    //         if ($superadmin && $superadmin->hasRole('superadmin')) {
    //             // dd($originaladminId);
    //             Auth::guard('admin')->logout();
    //             Auth::guard('admin')->login($superadmin);
    //             Login::where('user_id', $originaladminId)->update(['status' => 'inactive']);
            
    //             session()->forget('original_superadmin_id');
    //             return redirect()->route('admin.dashboard')->with('success', 'Returned to Superadmin account.');
    //         }
    //         session()->flash('error', 'Unable to return to Superadmin.');
    //         return redirect()->route('admin.login');
    //     }

    //     if ($request->has('user_id')) {
    //         $superadmin = Auth::guard('admin')->user();
        
    //         if ($superadmin && $superadmin->hasRole('superadmin')) {
    //             session(['original_superadmin_id' => $superadmin->id]);
    //             $user = Admin::find($request->user_id);
    //             session(['original_admin_id' => $request->user_id]);
    //             if ($user) {
    //                 Auth::guard('admin')->login($user);
    //                 Login::where('user_id', $user->id)->update(['status' => 'active']);
    //                 return redirect()->route('admin.dashboard');
    //             }
    //             session()->flash('error', 'User not found.');
    //             return back();
    //         }
    //         session()->flash('error', 'Unauthorized access.');
    //         return back();
    //     }
             
    //     // Regular login process
    //     $request->validate([
    //         'email' => 'required|max:50',
    //         'password' => 'required',
    //     ]);

    //     if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
    //         $users = Auth::guard('admin')->user();
    //         Login::where('user_id', $users->id)->delete();
    //         if (!$users->hasRole('superadmin')) {
    //             $data = Login::updateOrCreate(
    //                 ['user_id' => $users->id],
    //                 [
    //                     'role' => 'admin',
    //                     'ip_address' => $request->ip(),
    //                     'status' => 'active',
    //                 ]
    //             );
    //             return redirect('admin/admin-sites');
    //         } else {
    //             $data = Login::updateOrCreate(
    //                 ['user_id' => $users->id],
    //                 [
    //                     'role' => 'admin',
    //                     'ip_address' => $request->ip(),
    //                     'status' => 'active',
    //                 ]
    //             );
    //             return redirect('admin/sites');
    //         }            
    //         // return redirect('admin/sites');
    //         // return redirect('admin/admin-sites');
    //     }
    //     session()->flash('error', 'Invalid email and password');
    //     return back();
    // }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|max:50',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->remember
        )) {
            $user = Auth::guard('admin')->user();

            if ($user->hasRole('superadmin')) {
                return redirect()->route('admin.sites');
            } else {
                // return redirect('admin/admin-sites');
                return redirect()->route('admin.sites');
            }
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

            $emails = json_decode($rawEmails, true);
            if (!is_array($emails)) {
                $emails = explode(',', $rawEmails);
            }

            foreach ($emails as $index => $email) {
                if (trim(strtolower($email)) === trim(strtolower($request->userEmail))) {
                    if (!empty($device->userPassword) && Hash::check($request->userPassword, $device->userPassword)) {
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