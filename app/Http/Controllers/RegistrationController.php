<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Registration;

class RegistrationController extends Controller
{
    public function index()
    {
        $registrations = Registration::paginate(10);
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