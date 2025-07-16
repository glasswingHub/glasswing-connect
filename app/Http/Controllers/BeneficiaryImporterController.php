<?php

namespace App\Http\Controllers;

use App\Models\Importer;
use App\Models\ImporterColumn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class BeneficiaryImporterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Importer::withTrashed()->select('id', 'name', 'source_table', 'target_table', 'active', 'deleted_at', 'country_code', 'description', 'configured')
            ->orderBy('name');
        
        if($request->has('search')){
            $query->whereRaw('name LIKE ?', ['%'.$request->search.'%'])
                ->orWhereRaw('source_table LIKE ?', ['%'.$request->search.'%']);
        }
        
        $importers = $query->paginate(10);

        return Inertia::render('BeneficiaryImporters/Index', [
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
        
        return Inertia::render('BeneficiaryImporters/Create', [
            'sourceTableOptions' => $this->getSourceTableOptions(),
            'countries' => $this->getCountries(),
            'users' => $users,
            'selectedUserIds' => [],
            'targetTableColumns' => $this->getTargetTableColumns(),
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
            'active' => 'boolean',
            'country_code' => 'required|string|max:255',
            'description' => 'nullable|string',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
            'column_mappings' => 'array',
            'column_mappings.*.source_column' => 'required_with:column_mappings.*.target_column|string',
            'column_mappings.*.target_column' => 'required_with:column_mappings.*.source_column|string',
            'column_mappings.*.display_name' => 'nullable|string|max:255',
            'column_mappings.*.primary_key' => 'boolean',
            'column_mappings.*.show_in_list' => 'boolean',
            'column_mappings.*.country_key' => 'boolean',
            'column_mappings.*.uniqueness_key' => 'boolean',
        ]);

        $importer = Importer::create([
            'name' => $request->name,
            'source_table' => $request->source_table,
            'target_table' => 'beneficiaries',
            'active' => $request->active ?? true,
            'country_code' => $request->country_code,
            'description' => $request->description,
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
                        'display_name' => $mapping['display_name'] ?? $mapping['source_column'],
                        'primary_key' => $mapping['primary_key'] ?? false,
                        'show_in_list' => $mapping['show_in_list'] ?? true,
                        'country_key' => $mapping['country_key'] ?? false,
                        'uniqueness_key' => $mapping['uniqueness_key'] ?? false,
                    ]);
                }
            }
        }

        $countryKeyCount = $importer->columnMappings()->where('country_key', true)->count();
        // $primaryKeyCount = $importer->columnMappings()->where('primary_key', true)->count();
        $importer->configured = $countryKeyCount == 1; // && $primaryKeyCount == 1;
        // $importer->configured = $importer->configured && $importer->columnMappings()->whereIn('target_column', ['name', 'surname', 'fechaNac'])->count() == 3;

        if($importer->active){
            $importer->active = $importer->configured;
        }
        $importer->save();

        return redirect()->route('importers.index')
            ->with('success', 'Importador creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $importer = Importer::withTrashed()->findOrFail($id);
        $columnMappings = $importer->columnMappings()->get(['id', 'source_column', 'target_column', 'display_name', 'primary_key', 'show_in_list', 'country_key', 'uniqueness_key']);
        $associatedUsers = $importer->users()->select('users.id', 'users.name', 'users.email')->get();
        
        // Get country name if country_code exists
        $countryName = null;
        if ($importer->country_code) {
            $countryName = $this->getCountryNameByCode($importer->country_code);
        }
        
        return Inertia::render('BeneficiaryImporters/Show', [
            'importer' => $importer,
            'columnMappings' => $columnMappings,
            'associatedUsers' => $associatedUsers,
            'countryName' => $countryName,
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
        $columnMappings = $importer->columnMappings()->get(['id', 'source_column', 'target_column', 'display_name', 'primary_key', 'show_in_list', 'country_key', 'uniqueness_key']);
        $users = User::where('active', true)->orderBy('name')->select('id', 'name', 'email')->get();
        $selectedUserIds = $importer->users()->pluck('users.id');
        
        return Inertia::render('BeneficiaryImporters/Edit', [
            'importer' => $importer,
            'columnMappings' => $columnMappings,
            'sourceTableOptions' => $this->getSourceTableOptions(),
            'countries' => $this->getCountries(),
            'users' => $users,
            'selectedUserIds' => $selectedUserIds,
            'targetTableColumns' => $this->getTargetTableColumns(),
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
            'active' => 'boolean',
            'country_code' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'user_ids' => 'array',
            'user_ids.*' => 'exists:users,id',
            'column_mappings' => 'array',
            'column_mappings.*.source_column' => 'required_with:column_mappings.*.target_column|string',
            'column_mappings.*.target_column' => 'required_with:column_mappings.*.source_column|string',
            'column_mappings.*.display_name' => 'nullable|string|max:255',
            'column_mappings.*.primary_key' => 'boolean',
            'column_mappings.*.show_in_list' => 'boolean',
            'column_mappings.*.country_key' => 'boolean',
            'column_mappings.*.uniqueness_key' => 'boolean',
        ]);

        $importer->update([
            'name' => $request->name,
            'source_table' => $request->source_table,
            'active' => $request->active ?? true,
            'country_code' => $request->country_code,
            'description' => $request->description,
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
                            'display_name' => $mapping['display_name'] ?? $mapping['source_column'],
                            'primary_key' => $mapping['primary_key'] ?? false,
                            'show_in_list' => $mapping['show_in_list'] ?? true,
                            'country_key' => $mapping['country_key'] ?? false,
                            'uniqueness_key' => $mapping['uniqueness_key'] ?? false,
                        ]);
                    }
                }
            }
        }

        $countryKeyCount = $importer->columnMappings()->where('country_key', true)->count();
        // $primaryKeyCount = $importer->columnMappings()->where('primary_key', true)->count();
        $importer->configured = $countryKeyCount == 1; // && $primaryKeyCount == 1;
        // $importer->configured = $importer->configured && $importer->columnMappings()->whereIn('target_column', ['name', 'surname', 'fechaNac'])->count() == 3;

        if($importer->active){
            $importer->active = $importer->configured;
        }
        $importer->save();

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

    /**
     * Get column mappings for a specific importer.
     */
    public function getColumnMappings(string $id)
    {
        $importer = Importer::findOrFail($id);
        $mappings = $importer->columnMappings()->orderBy('target_column', 'asc')->get(['id', 'source_column', 'target_column', 'display_name', 'primary_key', 'show_in_list', 'country_key', 'uniqueness_key']);
        
        return response()->json($mappings);
    }
    
    public function getCountries()
    {
        $countries = collect(DB::connection('gwdata')
            ->select("SELECT codeCountry, name FROM countries ORDER BY name"))
            ->map(function ($country) {
                return [
                    'value' => $country->codeCountry,
                    'label' => $country->name
                ];
            })
            ->toArray();

        return $countries;
    }

    /**
     * Get country name by country code.
     */
    private function getCountryNameByCode(string $countryCode): ?string
    {
        try {
            $countries = DB::connection('gwdata')
                ->select("SELECT name FROM countries WHERE codeCountry = ?", [$countryCode]);

            return !empty($countries) ? $countries[0]->name : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getTargetTableColumns()
    {
        $table_name = 'beneficiaries';
        $columns = [];

        try {
            $columns = collect(DB::connection('gwdata')
                ->select("SELECT COLUMN_NAME as name, DATA_TYPE as type FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION", [$table_name]))
                ->map(function ($column) {
                    return [
                        'value' => $column->name,
                        'label' => $column->name . ' (' . $column->type . ')'
                    ];
                })
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Error al obtener las columnas de la tabla: ' . $e->getMessage());
        }

        return $columns;
    }
}
