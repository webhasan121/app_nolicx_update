<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Spatie\Permission\Models\Permission;

class PermissionsToUser extends Component
{
    /**
     * Create a new component instance.
     */
    public $userPermissions;
    public function __construct($userPermissions)
    {
        //
        $this->userPermissions = $userPermissions;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        
        $permissions = Permission::all();
        return view('components.permissions-to-user', ['permissions' => $permissions, 'userPermissions' => $this->userPermissions]);
    }
}
