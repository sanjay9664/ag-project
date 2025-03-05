<?php


declare(strict_types=1);
namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRequest;
use App\Models\Admin;
use App\Models\Login;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Auth;

class AdminsController extends Controller
{
    public function index(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.view']);
        $user = Auth::guard('admin')->user();
        
        if ($user->hasRole('superadmin')) {
            return view('backend.pages.admins.index', [
                'admins' => Admin::all(),
            ]);
        } else {
            return view('backend.pages.admins.index', [
                'admins' => Admin::where('admin_id', Auth::id())->get(),
            ]);
        }
    }

    public function create(): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.create']);

        return view('backend.pages.admins.create', [
            'roles' => Role::all(),
        ]);
    }

    public function store(AdminRequest $request): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.create']);

        $user = Auth::guard('admin')->user();

        // Allow both superadmin and admin to create new admins
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            $admin = Admin::create([
                'admin_id' => Auth::id(),
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Assign roles if provided
            if ($request->filled('roles')) {
                $admin->assignRole($request->roles);
            }

            session()->flash('success', __('Admin has been created.'));
        } else {
            session()->flash('error', __('You are not authorized to create an admin.'));
        }

        return redirect()->route('admin.admins.index');
    }

    public function edit(int $id): Renderable
    {
        $this->checkAuthorization(auth()->user(), ['admin.edit']);

        $admin = Admin::findOrFail($id);
        return view('backend.pages.admins.edit', [
            'admin' => $admin,
            'roles' => Role::all(),
        ]);
    }

    public function update(AdminRequest $request, int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.edit']);

        $admin = Admin::findOrFail($id);
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->username = $request->username;
        if ($request->password) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        $admin->roles()->detach();
        if ($request->roles) {
            $admin->assignRole($request->roles);
        }

        session()->flash('success', 'Admin has been updated.');
        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->checkAuthorization(auth()->user(), ['admin.delete']);
    
        $admin = Admin::findOrFail($id);
        $login = Login::where('user_id', $id)->first();
    
        if ($login) {
            $login->delete();
        }
    
        $admin->delete();
    
        session()->flash('success', 'Admin and associated login record have been deleted.');
        return back();
    }
    
}