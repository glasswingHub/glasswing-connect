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
        $query = Importer::withTrashed()->select('id', 'name', 'source_table', 'target_table', 'active', 'deleted_at', 'country_code', 'description', 'configured')
            ->orderBy('name');
        
        if($request->has('search')){
            $query->whereRaw('name LIKE ?', ['%'.$request->search.'%'])
                ->orWhereRaw('source_table LIKE ?', ['%'.$request->search.'%']);
        }
        
        $importers = $query->paginate(10);

        return Inertia::render('Importers/Index', [
            'importers' => $importers
        ]);
    }
}
