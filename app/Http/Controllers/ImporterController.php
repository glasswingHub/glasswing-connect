<?php

namespace App\Http\Controllers;

use App\Models\Importer;
use Illuminate\Http\Request;
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
        return Inertia::render('Importers/Create', [
            'targetTableOptions' => [
                ['value' => 'volunteerings', 'label' => 'Volunteerings'],
                ['value' => 'beneficiaries', 'label' => 'Beneficiaries']
            ]
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
            'active' => 'boolean'
        ]);

        Importer::create([
            'name' => $request->name,
            'source_table' => $request->source_table,
            'target_table' => $request->target_table,
            'active' => $request->active ?? true,
        ]);

        return redirect()->route('importers.index')
            ->with('success', 'Importador creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $importer = Importer::withTrashed()->findOrFail($id);
        
        return Inertia::render('Importers/Show', [
            'importer' => $importer,
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
        
        return Inertia::render('Importers/Edit', [
            'importer' => $importer,
            'targetTableOptions' => [
                ['value' => 'volunteerings', 'label' => 'Volunteerings'],
                ['value' => 'beneficiaries', 'label' => 'Beneficiaries']
            ],
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
            'active' => 'boolean'
        ]);

        $importer->update([
            'name' => $request->name,
            'source_table' => $request->source_table,
            'target_table' => $request->target_table,
            'active' => $request->active ?? true,
        ]);

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
}
