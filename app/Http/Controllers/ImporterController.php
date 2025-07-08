<?php

namespace App\Http\Controllers;

use App\Models\Importer;
use App\Models\ImporterColumn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ImporterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Importer::withTrashed()->select('id', 'name', 'source_table', 'target_table', 'active', 'deleted_at')
            ->orderBy('name');
        
        if($request->has('search')){
            $query->whereRaw('name LIKE ?', ['%'.$request->search.'%'])
                ->orWhereRaw('source_table LIKE ?', ['%'.$request->search.'%']);
        }
        
        $importers = $query->paginate(10);

        return Inertia::render('Importers/Index', [
            'importers' => $importers,
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::where('active', true)->orderBy('name')->select('id', 'name', 'email')->get();
        
        return Inertia::render('Importers/Create', [
            'targetTableOptions' => $this->getTargetTableOptions(),
            'sourceTableOptions' => $this->getSourceTableOptions(),
            'users' => $users,
            'selectedUserIds' => [],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'source_table' => 'required|string|max:255',
            'target_table' => 'required|in:volunteerings,beneficiaries',
            'active' => 'boolean',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
            'column_mappings' => 'array',
            'column_mappings.*.source_column' => 'required_with:column_mappings.*.target_column|string',
            'column_mappings.*.target_column' => 'required_with:column_mappings.*.source_column|string',
            'column_mappings.*.display_name' => 'nullable|string|max:255',
            'column_mappings.*.primary_key' => 'boolean',
            'column_mappings.*.show_in_list' => 'boolean',
        ]);

        $importer = Importer::create([
            'name' => $request->name,
            'source_table' => $request->source_table,
            'target_table' => $request->target_table,
            'active' => $request->active ?? true,
        ]);

        // Asociar usuarios seleccionados
        if ($request->has('user_ids') && is_array($request->user_ids)) {
            $importer->users()->sync($request->user_ids);
        }

        // Store column mappings if provided
        if ($request->has('column_mappings') && is_array($request->column_mappings)) {
            foreach ($request->column_mappings as $mapping) {
                if (!empty($mapping['source_column']) && !empty($mapping['target_column'])) {
                    ImporterColumn::create([
                        'importer_id' => $importer->id,
                        'source_column' => $mapping['source_column'],
                        'target_column' => $mapping['target_column'],
                        'display_name' => $mapping['display_name'] ?? null,
                        'primary_key' => $mapping['primary_key'] ?? false,
                        'show_in_list' => $mapping['show_in_list'] ?? true,
                    ]);
                }
            }
        }

        return redirect()->route('importers.index')
            ->with('success', 'Importador creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $importer = Importer::withTrashed()->findOrFail($id);
        $columnMappings = $importer->columnMappings()->get(['id', 'source_column', 'target_column', 'display_name', 'primary_key', 'show_in_list']);
        $associatedUsers = $importer->users()->select('users.id', 'users.name', 'users.email')->get();
        
        return Inertia::render('Importers/Show', [
            'importer' => $importer,
            'columnMappings' => $columnMappings,
            'associatedUsers' => $associatedUsers,
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $importer = Importer::withTrashed()->findOrFail($id);
        $columnMappings = $importer->columnMappings()->get(['id', 'source_column', 'target_column', 'display_name', 'primary_key', 'show_in_list']);
        $users = User::where('active', true)->orderBy('name')->select('id', 'name', 'email')->get();
        $selectedUserIds = $importer->users()->pluck('users.id');
        
        return Inertia::render('Importers/Edit', [
            'importer' => $importer,
            'columnMappings' => $columnMappings,
            'targetTableOptions' => $this->getTargetTableOptions(),
            'sourceTableOptions' => $this->getSourceTableOptions(),
            'users' => $users,
            'selectedUserIds' => $selectedUserIds,
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $importer = Importer::withTrashed()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'source_table' => 'required|string|max:255',
            'target_table' => 'required|in:volunteerings,beneficiaries',
            'active' => 'boolean',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
            'column_mappings' => 'array',
            'column_mappings.*.source_column' => 'required_with:column_mappings.*.target_column|string',
            'column_mappings.*.target_column' => 'required_with:column_mappings.*.source_column|string',
            'column_mappings.*.display_name' => 'nullable|string|max:255',
            'column_mappings.*.primary_key' => 'boolean',
            'column_mappings.*.show_in_list' => 'boolean',
        ]);

        $importer->update([
            'name' => $request->name,
            'source_table' => $request->source_table,
            'target_table' => $request->target_table,
            'active' => $request->active ?? true,
        ]);

        // Asociar usuarios seleccionados
        if ($request->has('user_ids') && is_array($request->user_ids)) {
            $importer->users()->sync($request->user_ids);
        }

        // Update column mappings
        if ($request->has('column_mappings')) {
            // Delete existing mappings
            $importer->columnMappings()->delete();
            
            // Create new mappings
            if (is_array($request->column_mappings)) {
                foreach ($request->column_mappings as $mapping) {
                    if (!empty($mapping['source_column']) && !empty($mapping['target_column'])) {
                        ImporterColumn::create([
                            'importer_id' => $importer->id,
                            'source_column' => $mapping['source_column'],
                            'target_column' => $mapping['target_column'],
                            'display_name' => $mapping['display_name'] ?? null,
                            'primary_key' => $mapping['primary_key'] ?? false,
                            'show_in_list' => $mapping['show_in_list'] ?? true,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('importers.index')
            ->with('success', 'Importador actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $importer = Importer::findOrFail($id);
        $importer->delete();

        return redirect()->route('importers.index')
            ->with('success', 'Importador eliminado exitosamente');
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(string $id)
    {
        $importer = Importer::withTrashed()->findOrFail($id);
        $importer->restore();

        return redirect()->route('importers.index')
            ->with('success', 'Importador restaurado exitosamente');
    }

    /**
     * Permanently delete the specified resource from storage.
     */
    public function forceDelete(string $id)
    {
        $importer = Importer::withTrashed()->findOrFail($id);
        $importer->forceDelete();

        return redirect()->route('importers.index')
            ->with('success', 'Importador eliminado permanentemente');
    }

    /**
     * Get target table options for the form.
     */
    private function getTargetTableOptions(): array
    {
        return [
            ['value' => 'volunteerings', 'label' => 'Volunteerings'],
            ['value' => 'beneficiaries', 'label' => 'Beneficiaries']
        ];
    }

    /**
     * Get source table options from the gwforms database.
     */
    private function getSourceTableOptions(): array
    {
        return collect(DB::connection('gwforms')
            ->select("SELECT TABLE_NAME as name FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME"))
            ->map(function ($table) {
                return [
                    'value' => $table->name,
                    'label' => $table->name
                ];
            })
            ->toArray();
    }

    /**
     * Get table columns from the gwforms database.
     */
    public function getSourceTableColumns(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string'
        ]);

        try {
            $columns = collect(DB::connection('gwforms')
                ->select("SELECT COLUMN_NAME as name, DATA_TYPE as type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? ORDER BY ORDINAL_POSITION", [$request->table_name]))
                ->map(function ($column) {
                    return [
                        'value' => $column->name,
                        'label' => $column->name . ' (' . $column->type . ')'
                    ];
                })
                ->toArray();

            return response()->json($columns);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las columnas de la tabla'], 500);
        }
    }

    public function getTargetTableColumns(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string|in:volunteerings,beneficiaries'
        ]);

        try {
            $columns = collect(DB::connection('gwdata')
                ->select("SELECT COLUMN_NAME as name, DATA_TYPE as type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION", [$request->table_name]))
                ->map(function ($column) {
                    return [
                        'value' => $column->name,
                        'label' => $column->name . ' (' . $column->type . ')'
                    ];
                })
                ->toArray();

            return response()->json($columns);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las columnas de la tabla'], 500);
        }
    }

    /**
     * Get column mappings for a specific importer.
     */
    public function getColumnMappings(string $id)
    {
        $importer = Importer::findOrFail($id);
        $mappings = $importer->columnMappings()->get(['id', 'source_column', 'target_column']);
        
        return response()->json($mappings);
    }
}
