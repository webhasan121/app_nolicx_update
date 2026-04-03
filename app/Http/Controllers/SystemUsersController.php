<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\user_has_refs;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;

class SystemUsersController extends Controller
{
    // users view to system by permission
    public function admin_view()
    {
        $users = User::withoutRole('system')->orderBy('id', 'desc')->get();
        // return $users[0]->role;
        return view('auth.system.users.index', compact('users'));
    }


    /**
     * users edit form to system by permissions
     * 
     * @return view
     */
    public function admin_edit()
    {
        $user = User::withoutRole('system')->where('email', request('email'))->first();
        return view('auth.system.users.edit', compact('user'));
    }

    public function admin_update(Request $request, $id)
    {
        // $user->update(request()->validate([
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user = User::query()->withoutAdmin()->findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('reference')) {
            $reffArray = user_has_refs::all('ref', 'user_id');
            $reference = $request->reference;
            $reff = $reffArray->where('ref', $reference)->first();

            if ($reff) {
                # code...
                $user->reference_accepted_at = Carbon::now();
                $user->reference = $request->reference;
            }
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'User updated successfully.');
    }
}
