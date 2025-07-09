<?php

namespace App\Http\Controllers;

use App\Models\Importer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ImportController extends Controller
{
    public function index()
    {
        $importers = auth()->user()->importers()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return Inertia::render('Imports/Index', [
            'importers' => $importers
        ]);
    }
}
