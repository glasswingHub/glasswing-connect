<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Registration;
use App\Services\ProcessRegistration;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Registration::where('Importado', null)
                            ->where('pais', 7)
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
        // WHEN @idsede = 666 AND @diplomado = 114 THEN 9588  -->	S.S. / Diplomado de Estudio
        // WHEN @idsede = 666 AND @diplomado = 118 THEN 9587  -->	S.S. / Diplomado de Empleo
        // WHEN @idsede = 666 AND @diplomado = 119 THEN 9589  -->	S.S. / Diplomado de Emprendimiento
        // WHEN @idsede = 1128 AND @diplomado = 114 THEN 9638  -->	Santa Ana / Diplomado de Estudio
        // WHEN @idsede = 1128 AND @diplomado = 118 THEN 9637  -->	Santa Ana / Diplomado de Empleo
        // WHEN @idsede = 1128 AND @diplomado = 119 THEN 9639  -->	Santa Ana / Diplomado de Emprendimiento
        // WHEN @idsede = 2968 AND @diplomado = 114 THEN 7745  -->	San Miguel / Diplomado de Estudio
        // WHEN @idsede = 2968 AND @diplomado = 118 THEN 7746  -->	San Miguel / Diplomado de Empleo
        // WHEN @idsede = 2968 AND @diplomado = 119 THEN 7747  -->	San Miguel / Diplomado de Emprendimiento
        $groupId = 9588;

        $processRegistration = new ProcessRegistration();
        $result = $processRegistration->execute($registration, $groupId);
        
        \Illuminate\Support\Facades\Log::info('>>>>>>> RegistrationController#process_import');
        \Illuminate\Support\Facades\Log::info(print_r($result, true));

        return response()->json([
            'message' => 'Procesamiento realizado con éxito.',
            'registration_id' => $registration->id
        ]);
    }
}