<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Registration;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Registration::where('Importado', null)
                            ->orderBy('created_at', 'desc')
                            ->orderBy('id', 'desc');
        if($request->has('search')){
            $query->where('nombres', 'like', '%'.$request->search.'%')
                ->orWhere('apellidos', 'like', '%'.$request->search.'%');
        }
        $registrations = $query->paginate(10);

                            

        return Inertia::render('Registrations/Index', [
            'registrations' => $registrations,
        ]);
    }

    public function show($id)
    {
        $registration = Registration::findOrFail($id);
        return Inertia::render('Registrations/Show', [
            'registration' => $registration,
        ]);
    }

    public function import($id)
    {
        $registration = Registration::findOrFail($id);
        return Inertia::render('Registrations/Import', [
            'registration' => $registration,
        ]);
    }

    public function process_import(Request $request, Registration $registration)
    {



        return response()->json([
            'message' => 'Procesamiento realizado con éxito.',
            'registration_id' => $registration->id
        ]);
    }
}