<?php

namespace App\Http\Controllers;

use App\Models\ExternalCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('account.index', [
            'users' => User::with('roles')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = DB::connection('external')->table('member_companies')->get();
        $roles = Role::all();
        return view('account.create', compact('companies', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'company_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'role' => 'required|exists:roles,name',
            'is_active' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_name' => $validated['company_name'],
            'department' => $validated['department'],
            'is_active' => $validated['is_active'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with([
            'alert' => 'Akun berhasil dibuat!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success',
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $companies = DB::connection('external')->table('member_companies')->get();
        $roles = Role::all();
        return view('account.edit', compact('user', 'companies', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'company_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'role' => 'required|exists:roles,name',
            'is_active' => 'required|boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_name' => $validated['company_name'],
            'department' => $validated['department'],
            'is_active' => $validated['is_active'],
        ]);

        $user->syncRoles($validated['role']);

        return redirect()->route('users.index')->with([
            'alert' => 'Akun berhasil diperbarui!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success',
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        User::destroy($user->id);
        return redirect('/admin/users')->with([
            'alert' => 'Akun Berhasil Dihapus!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success'
        ]);
    }

    public function changePassword(User $user)
    {
        return view('account.change-password', compact('user'));
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed'
        ]);

        $user->update(['password' => Hash::make($request->new_password)]);

        return redirect('/admin/users')->with([
            'alert' => 'Password berhasil diubah!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success'
        ]);
    }
}
