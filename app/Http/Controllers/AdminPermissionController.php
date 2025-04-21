<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class AdminPermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('account.permission.index', [
            'permissions' => Permission::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('account.permission.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name]);

        return redirect('/admin/permissions')->with([
            'alert' => 'Penambahan Hak Akses Berhasil Ditambahkan!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('account.permission.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id
        ]);

        $permission->update(['name' => $request->name]);

        return redirect('/admin/permissions')->with([
            'alert' => 'Hak Akses Berhasil Diperbaharui!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        Permission::destroy($permission->id);
        return redirect('/admin/permissions')->with([
            'alert' => 'Hak Akses Berhasil Dihapus!',
            'alert_title' => 'Berhasil',
            'alert_type' => 'success'
        ]);
    }
}
