<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// use App\Http\Controllers\Controller;

use Illuminate\Routing\Controller;

class RoleController extends Controller
{
    private $user;
    public function __construct()
    {
        $this->user == Auth::user();
        // Check user has permission to certain task
        // $this->middleware('permission:role_list')->only('admin_list');
        // $this->middleware('permission:role_list', ['only' => ['admin_list']]);
    }


    public function admin_list(Request $request)
    {
        // dd($request->user()->getAllPermissions()[0]['name']);
        // return $request->user()->can('role_list');

        /**
         * get all role with user and permission count in single query
         */
        $roles = Role::get();
        // how to get count in single query
        // dd($roles[0]->users->count());

        $perm = Permission::orderBy('id', 'desc')->get();
        $role = Role::all();
        return view('auth.system.role.index', compact('perm', 'role'));
    }



    /**
     * admin edit method, get a role and return the role for edit
     * 
     * @return \Illuminate\Http\Response
     */
    public function admin_edit(Request $request)
    {
        $role = Role::findorFail(decrypt($request->role));
        $users = User::withoutRole('system')->withoutRole($role->name)->orderBy('id', 'desc')->get();
        $permissions = Permission::all();
        return view('auth.system.role.edit', compact('role', 'users', 'permissions'));
    }


    /**
     * method give role to role
     * 
     * @param Role, @param Permissions
     * @return back;
     */
    public function system_give_permission_to_role(Role $role)
    {
        DB::table('role_has_permissions')->where('role_id', $role->id)->delete();
        $role->givePermissionTo(request('permissions'));
        return redirect()->back();
    }


    /**
     * method give role to user
     * 
     * @param User, @param Permissions
     * @return back;
     */
    public function system_give_permission_to_user(User $user)
    {
        // dd(request()->all());
        // DB::table('role_has_permissions')->where('role_id', $user->id)->delete();
        $user->syncPermissions(request('permissions'));
        return redirect()->back();
    }


    /**
     * method sync role to multiple
     * 
     * @param User, @param Role
     * @return back;
     */
    public function multiple_user_to_single_role()
    {

        if (empty(request('user'))) {
            return redirect()->back()->withInput();
        }
        foreach (request()->get('user') as $key => $users) {
            $user = User::findOrFail($users);

            if (request()->has('force_delete')) {

                if ($user && $user->hasRole(request('role'))) {
                    $user->removeRole(request('role'));
                }
            } else {

                if ($user && !$user->hasRole(request('role'))) {
                    $user->assignRole(request('role'));
                }
            }
        }



        return redirect()->back();
    }


    /**
     * multipe roles to single users
     * 
     */
    public function multiple_role_to_single_user()
    {
        // dd(request('user'));
        if (!empty(request('user')) && !empty(request('role'))) {
            foreach (request('user') as $key => $value) {
                $user = User::findOrFail($value);
                $user->syncRoles(request('role'));
            }
            return redirect()->back();
        } else {
            return redirect()->back()->withInput();
        }
    }
}
