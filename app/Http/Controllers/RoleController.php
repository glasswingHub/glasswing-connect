<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Role::with('permissions')->withCount('users')->orderBy('name');
        
        if($request->has('search')){
            $query->where('name', 'like', '%'.$request->search.'%');
        }
        
        $roles = $query->paginate(10);

        return Inertia::render('Roles/Index', [
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all()->map(function($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'label' => ucwords(str_replace('_', ' ', $permission->name))
            ];
        });

        return Inertia::render('Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id'
        ]);

        $role = Role::create([
            'name' => $request->name,
        ]);

        if ($request->has('permission_ids')) {
            $role->syncPermissions($request->permission_ids);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::with('permissions')->with('users')->findOrFail($id);
        
        return Inertia::render('Roles/Show', [
            'role' => $role
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        $permissions = Permission::all()->map(function($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'label' => ucwords(str_replace('_', ' ', $permission->name))
            ];
        });

        $selectedPermissions = $role->permissions->pluck('id')->toArray();

        return Inertia::render('Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'selectedPermissions' => $selectedPermissions
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id'
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permission_ids ?? []);

        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        
        // Verificar si el rol está siendo usado por algún usuario
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar el rol porque está asignado a usuarios');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado exitosamente');
    }
} 