<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminAccountController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return view('account.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $companies = DB::connection('external')->table('member_companies')->get();
        $roles = Role::all();
        return view('account.create', compact('companies', 'roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $this->validateUser($request, true);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'company_name' => $validated['company_name'],
            'department' => $validated['department'],
            'is_active' => $validated['is_active'],
        ]);

        $user->assignRole($validated['role']);

        return $this->redirectSuccess('Akun berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $companies = DB::connection('external')->table('member_companies')->get();
        $roles = Role::all();
        return view('account.edit', compact('user', 'companies', 'roles'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $this->validateUser($request, false, $user->id);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_name' => $validated['company_name'],
            'department' => $validated['department'],
            'is_active' => $validated['is_active'],
        ]);

        $user->syncRoles($validated['role']);

        return $this->redirectSuccess('Akun berhasil diperbarui!');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->redirectSuccess('Akun berhasil dihapus!');
    }

    /**
     * Show the form for changing user password.
     */
    public function changePassword(User $user)
    {
        return view('account.change-password', compact('user'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => 'required|min:8|confirmed'
        ]);

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->redirectSuccess('Password berhasil diubah!');
    }

    /**
     * Validate user input for store/update.
     */
    protected function validateUser(Request $request, bool $isCreate = true, int $userId = null): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email' . ($userId ? ',' . $userId : ''),
            'company_name' => 'required|string|max:255',
            'department' => 'nullable|string|max:100',
            'role' => 'required|exists:roles,name',
            'is_active' => 'required|boolean',
        ];

        if ($isCreate) {
            $rules['password'] = 'required|min:8';
        }

        return $request->validate($rules);
    }

    /**
     * Return success redirect response with flash message.
     */
    protected function redirectSuccess(string $message)
    {
        return redirect()->route('admin.users.index')->with([
            'alert' => $message,
            'alert_title' => 'Berhasil',
            'alert_type' => 'success',
        ]);
    }
}
