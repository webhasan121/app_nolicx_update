<?php

namespace App\View\Components\dashboard;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RoleToUser extends Component
{
    public $users, $roles;
    /**
     * Create a new component instance.
     */
    public function __construct()

    {
        $this->users = DB::table('users')->get();
        $this->roles = DB::table('roles')->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        return view('components.dashboard.role-to-user', [
            'users' => $this->users,
            'roles' => $this->roles,
        ]);
    }
}
