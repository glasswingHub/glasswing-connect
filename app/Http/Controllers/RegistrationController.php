<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Registration;
use App\Services\ProcessRegistration;
use Illuminate\Support\Facades\Session;

class RegistrationController extends Controller
{
    
    public function setGroup(Request $request)
    {
        $selectedOption = $request->input('group');
        Session::put('group', $selectedOption);
        
        return response()->json([
            'success' => true,
            'message' => 'Opción seleccionada guardada correctamente'
        ]);
    }
    
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
            'groups' => $this->groups(),
            'currentGroup' => $this->currentGroup(),
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
            'groups' => $this->groups(),
            'currentGroup' => $this->currentGroup(),
        ]);
    }

    public function process_import(Request $request, Registration $registration)
    {

        $processRegistration = new ProcessRegistration();
        $result = $processRegistration->execute($registration, $this->currentGroup()['code']);
        
        return response()->json([
            'message' => $result['message'],
            'success' => $result['success']
        ]);
    }

    private function currentGroup(){
        return Session::get('group');
    }

    private function groups(){
        return [
            ['code' => 11238, 'value' => 'Criptonomía 1']
        ];
    }
}