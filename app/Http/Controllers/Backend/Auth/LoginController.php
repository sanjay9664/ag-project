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
            $superadmin = Admin::find($superadminId);
            
            $user = Admin::find($request->user_id);
            if ($superadmin && $superadmin->hasRole('superadmin')) {
                // dd($user);
                Auth::guard('admin')->logout();
                Auth::guard('admin')->login($superadmin);
                Login::where('user_id', $user->id)->update(['status' => 'inactive']);
                // dd("Superadmin login");
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
            $user = Auth::guard('admin')->user();
            Login::where('user_id', $user->id)->delete();
            if (!$user->hasRole('superadmin')) {
                
                Login::create([
                    'user_id' => $user->id,
                    'role' => $user->hasRole('superadmin') ? 'superadmin' : 'admin',
                    'ip_address' => $request->ip(),
                    'status' => 'active',
                ]);
            }
            return redirect()->route('admin.dashboard');
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
}