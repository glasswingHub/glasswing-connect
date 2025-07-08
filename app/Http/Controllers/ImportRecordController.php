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
        $importer = Importer::findOrFail($importerId);
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

        $query = DB::connection('gwforms')->table($table);
        
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
        $importer = Importer::findOrFail($importerId);
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


        $record = DB::connection('gwforms')
            ->table($table)
            ->where('id', $recordId)
            ->first();


        if (!$record) {
            abort(404, 'Registro no encontrado');
        }

        return Inertia::render('ImportRecords/Show', [
            'importer' => $importer,
            'record' => $record,
            'columns' => $visibleColumns,
        ]);
    }

    public function process_import($importerId, $recordId)
    {
        $importer = Importer::findOrFail($importerId);

        switch($importer->target_table){
            case 'volunteerings':
                $processRecord = new ProcessVolunteer();
                break;
            default: # case 'beneficiaries':
                $processRecord = new ProcessBeneficiary();
                break;
        }    
        
        $result = $processRecord->execute($importer, $recordId);
        
        return response()->json([
            'message' => $result['message'],
            'success' => $result['success']
        ]);
    }
}
