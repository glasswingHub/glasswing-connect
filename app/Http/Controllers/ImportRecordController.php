<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Importer;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Services\ProcessBeneficiary;
use App\Services\ProcessVolunteer;

class ImportRecordController extends Controller
{

    /**
     * Muestra los registros de la tabla source_table del importer.
     */
    public function records($importerId)
    {
        $importer = Importer::where('active', true)->findOrFail($importerId);
        $table = $importer->source_table;

        // Paginación (puedes ajustar el tamaño de página)
        $perPage = 15;
        $page = request('page', 1);

        // Obtener las columnas que deben mostrarse en la lista
        $visibleColumns = $importer->columnMappings()
            ->where('show_in_list', true)
            ->get()
            ->map(function ($item) {
                return [
                    'key' => $item->source_column,
                    'label' => $item->display_name,
                ];
            });

        $query = DB::connection('gwforms')->table($table)->where('import', null);
        
        // Obtener la columna marcada como country_key
        $countryKeyColumn = $importer->columnMappings()
            ->where('country_key', true)
            ->first();
        
        // Aplicar filtro por país si existe country_code en el importer y hay una columna country_key
        if ($importer->country_code && $countryKeyColumn) {
            $query->where($countryKeyColumn->source_column, $importer->country_code);
        }
        
        $total = $query->count();
        $records = $query->forPage($page, $perPage)->get();

        // Estructura de paginación manual
        $pagination = [
            'data' => $records,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];

        return Inertia::render('ImportRecords/Index', [
            'importer' => $importer,
            'records' => $pagination,
            'columns' => $visibleColumns,
        ]);
    }

    /**
     * Muestra un registro específico de la tabla source_table del importer.
     */
    public function show($importerId, $recordId)
    {
        $importer = Importer::where('active', true)->findOrFail($importerId);
        $table = $importer->source_table;

        // Obtener las columnas que deben mostrarse en la lista
        $visibleColumns = $importer->columnMappings()
            ->where('show_in_list', true)
            ->get()
            ->map(function ($item) {
                return [
                    'key' => $item->source_column,
                    'label' => $item->display_name,
                ];
            });


        $query = DB::connection('gwforms')->table($table);
        
        // Obtener la columna marcada como country_key
        $countryKeyColumn = $importer->columnMappings()
            ->where('country_key', true)
            ->first();

        $beneficiaryTypeKeyColumn = $importer->columnMappings()
            ->where('beneficiary_type_key', true)
            ->first();

        $record = null;
        
        // Aplicar filtro por país si existe country_code en el importer y hay una columna country_key
        if ($importer->country_code && $countryKeyColumn) {
            $query->where($countryKeyColumn->source_column, $importer->country_code);
            $record = $query->where('id', $recordId)->first();
        }

        if (!$record) {
            abort(404, 'Registro no encontrado');
        }

        $beneficiaryTypes = $this->getBeneficiaryTypes($importer->target_table);

        return Inertia::render('ImportRecords/Show', [
            'importer' => $importer,
            'record' => $record,
            'columns' => $visibleColumns,
            'beneficiaryTypes' => $beneficiaryTypes,
            'beneficiaryType' => $beneficiaryTypeKeyColumn ? $record->{$beneficiaryTypeKeyColumn->source_column} : null,
        ]);
    }

    public function process_import(Request $request, $importerId, $recordId)
    {
        $importer = Importer::where('active', true)->findOrFail($importerId);

        switch($importer->target_table){
            case 'volunteerings':
                $processRecord = new ProcessVolunteer($importer, $recordId, $request->beneficiary_type, auth()->user());
                break;
            default: # case 'beneficiaries':
                $processRecord = new ProcessBeneficiary($importer, $recordId, $request->beneficiary_type);
                break;
        }    
        
        $result = $processRecord->execute();
        
        return response()->json([
            'message' => $result['message'],
            'success' => $result['success']
        ]);
    }

    private function getBeneficiaryTypes($targetTable)
    {
        switch($targetTable){
            case 'volunteerings':
                return collect(DB::connection('gwdata')->select("SELECT id, name FROM type_beneficiarios WHERE typePerson = ?", [2]))->map(function ($item) {
                    return [
                        'value' => $item->id,
                        'label' => $item->name
                    ];
                });
            default: # case 'beneficiaries':
                return collect(DB::connection('gwdata')->select("SELECT id, name FROM type_beneficiarios WHERE typePerson = ?", [1]))->map(function ($item) {
                    return [
                        'value' => $item->id,
                        'label' => $item->name
                    ];
                });
        }
    }
}
