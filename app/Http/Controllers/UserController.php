<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::withTrashed()->with('roles')->select('id', 'name', 'email', 'active', 'deleted_at')
            ->orderBy('name');
        if($request->has('search')){
            $query->whereRaw('name LIKE ?', ['%'.$request->search.'%'])
                ->orWhereRaw('email LIKE ?', ['%'.$request->search.'%']);
        }
        $users = $query->paginate(10);

        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener todos los roles disponibles
        $roles = Role::all()->map(function($role) {
            return [
                'value' => $role->name,
                'label' => ucfirst($role->name)
            ];
        });
        
        return Inertia::render('Users/Create', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'active' => 'boolean',
            'role' => 'nullable|string|exists:roles,name'
        ]);

        // Generar contraseña aleatoria
        $password = Str::random(12);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'active' => $request->active ?? true,
        ]);

        // Asignar rol al usuario si se proporciona
        if ($request->has('role') && $request->role) {
            $user->assignRole($request->role);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente. Contraseña generada: ' . $password);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Obtener el rol actual del usuario
        $currentRole = $user->roles->first();
        
        return Inertia::render('Users/Show', [
            'user' => $user,
            'currentRole' => $currentRole ? $currentRole->name : null
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Verificar si el usuario intenta editar su propio registro
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes editar tu propio usuario desde esta sección.');
        }
        
        // Obtener todos los roles disponibles
        $roles = Role::all()->map(function($role) {
            return [
                'value' => $role->name,
                'label' => ucfirst($role->name)
            ];
        });
        
        // Obtener el rol actual del usuario
        $currentRole = $user->roles->first();
        
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'currentRole' => $currentRole ? $currentRole->name : ''
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        // Verificar si el usuario intenta actualizar su propio registro
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes actualizar tu propio usuario desde esta sección.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'active' => 'boolean',
            'role' => 'nullable|string|exists:roles,name'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'active' => $request->active ?? true,
        ]);

        // Actualizar el rol del usuario
        if ($request->has('role') && $request->role) {
            // Remover todos los roles existentes
            $user->syncRoles([$request->role]);
        } else {
            // Si no se selecciona ningún rol, remover todos los roles
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Verificar si el usuario intenta eliminar su propio registro
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar tu propio usuario desde esta sección.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')
            ->with('success', 'Usuario restaurado exitosamente');
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        // Verificar si el usuario intenta eliminar permanentemente su propio registro
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'No puedes eliminar permanentemente tu propio usuario desde esta sección.');
        }

        $user->forceDelete();

        return redirect()->route('users.index')
            ->with('success', 'Usuario eliminado permanentemente');
    }
}
