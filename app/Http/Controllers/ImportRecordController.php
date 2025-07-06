<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Importer;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

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

        // Obtener registros usando la conexión gwforms
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
        ]);
    }

    /**
     * Muestra un registro específico de la tabla source_table del importer.
     */
    public function show($importerId, $recordId)
    {
        $importer = Importer::findOrFail($importerId);
        $table = $importer->source_table;

        // Obtener el registro específico usando la conexión gwforms
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
        ]);
    }

    
}
