<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Registration;
use Carbon\Carbon;

class ProcessRegistration
{
    public function execute(Registration $registration, int $groupId): array
    {
        try {
            // Call the stored procedure using the sqlsrv connection
            $data = DB::connection('gwforms')
                ->select('EXEC spImportacionJuventud ?', [
                    $registration->id
                    ;

            \Illuminate\Support\Facades\Log::info('>>>>>>> ProcessRegistration');
            \Illuminate\Support\Facades\Log::info(print_r($data, true));
            
            if($data == null){
                Log::error('Error processing registration', [
                    'error' => '',
                    'data' => $registration
                ]);
                
                return [
                    'success' => false,
                    'message' => 'No se encontró el registro',
                    'data' => null
                ];
            }

            if($data->importado == "1"){
                Log::error('Error processing registration', [
                    'error' => '',
                    'data' => $registration
                ]);
                
                return [
                    'success' => false,
                    'message' => 'El registro ya ha sido importado',
                    'data' => null
                ];
            }
            
            $birthDate = Carbon::createFromFormat('Y-m-d',$registration->birthDate);

            $beneficiary = new \App\Models\Beneficiary();
            $beneficiary->DNI = $data->identidad;
            $beneficiary->year = $data->Anio;
            $beneficiary->name = $data->nombres;
            $beneficiary->surname = $data->apellidos;
            $beneficiary->fechaNac = $birthDate;
            $beneficiary->phone_participante = $data->telefono;
            $beneficiary->genre_id = $data->sexo;
            $beneficiary->address = $data->direccion;
            $beneficiary->fkIdTypeBeneficiarity = $data->tipoParticipante;
            $beneficiary->first_year_of_participation = $data->Nuevo;
            // $beneficiary->imported_by = null;
            $beneficiary->fkCodeCountry = "+503";
            $beneficiary->date_imported = Carbon::now();

            
            if(!$beneficiary->save()){
                Log::error('Error processing registration', [
                    'error' => '',
                    'data' => $registration
                ]);

                return [
                    'success' => false,
                    'message' => 'Error al importar el registro',
                    'data' => null
                ];
            }

            $beneficiarySchool = new \App\Models\BeneficiarySchool();
            $beneficiaryGroup = new \App\Models\BeneficiaryGroup();
            
            $beneficiarySchool->fkCode = $data->sede;
            $beneficiarySchool->fkIdBeneficiary = $beneficiary->id;
            $beneficiarySchool->fkIdLevel = $data->grado;
            $beneficiarySchool->school_beneficiaries_state_id = 1;
            $beneficiarySchool->school_beneficiaries_approved_id = 1;
            if($data->turno != "0"){
                $beneficiarySchool->school_beneficiaries_turn_id = $data->turno;
            }

            if(!$beneficiarySchool->save()){
                Log::error('Error processing registration', [
                    'error' => '',
                    'data' => $registration
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Error al importar el registro',
                    'data' => null
                ];
            }

            $beneficiarySchool->save();
 
            $beneficiaryGroup->fkIdBeneficiary = $beneficiary->id;
            $beneficiaryGroup->voluntary_state_id = 1;
            $beneficiaryGroup->fkIdGroupschool = $groupId;
            $beneficiaryGroup->save();

            if(!$beneficiaryGroup->save()){
                Log::error('Error processing registration', [
                    'error' => '',
                    'data' => $registration
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Error al importar el registro',
                    'data' => null
                ];
            }

            $registration->Importado = 1;
            // $registration->userId = null;
            $registration->updated_at = Carbon::now();
            
            // if($registration->save()){
            if(true){
                Log::info('Registration processed successfully', [
                    'data' => $registration,
                    'result' => $result
                ]);

                return [
                    'success' => true,
                    'message' => 'Registration processed successfully',
                    'data' => $result
                ];
            }

            Log::error('Error processing registration', [
                'error' => '',
                'data' => $registration
            ]);

            return [
                'success' => false,
                'message' => 'Error al importar el registro',
                'data' => null
            ];

        } catch (Exception $e) {
            Log::error('Error processing registration', [
                'error' => $e->getMessage(),
                'data' => $registration
            ]);

            return [
                'success' => false,
                'message' => 'Error processing registration: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }
} 