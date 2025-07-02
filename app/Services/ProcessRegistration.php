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
                ->select('EXEC spImportacionJuventud2 ?', [
                    $registration->id
                ])[0];

            
            if($data == null){
                Log::error('Error processing registration', [
                    'error' => ''
                ]);
                
                return [
                    'success' => false,
                    'message' => 'No se encontró el registro'
                ];
            }

            if($data->importado == "1"){
                Log::error('Error processing registration', [
                    'error' => ''
                ]);
                
                return [
                    'success' => false,
                    'message' => 'El registro ya ha sido importado',
                ];
            }
            
            $birthDate = Carbon::createFromFormat('Y-m-d',$registration->fechaNac);

            $beneficiary = \App\Models\Beneficiary::firstOrNew(['DNI' => $data->identidad]);

            $beneficiary->DNI = $data->identidad;
            $beneficiary->year = $data->Anio;
            $beneficiary->name = $data->nombres;
            $beneficiary->surname = $data->apellidos;
            $beneficiary->fechaNac = $birthDate->format('Y-m-d');
            $beneficiary->phone_participante = $data->telefono;
            $beneficiary->genre_id = $data->sexo;
            $beneficiary->address = $data->direccion;
            $beneficiary->fkIdTypeBeneficiarity = $data->tipoParticipante;
            $beneficiary->first_year_of_participation = $data->Nuevo;
            $beneficiary->imported_by = 9999;
            $beneficiary->fkCodeCountry = "+503";
            $beneficiary->date_imported = Carbon::now();

            
            if(!$beneficiary->save()){
                Log::error('Error processing registration', [
                    'error' => '',
                ]);

                return [
                    'success' => false,
                    'message' => 'Error al importar el registro',
                ];
            }

            $beneficiarySchool = \App\Models\BeneficiarySchool::firstOrNew(['fkIdBeneficiary' => $beneficiary->id]);
            
            $beneficiarySchool->fkCode = $data->code;
            $beneficiarySchool->fkIdBeneficiary = $beneficiary->id;
            $beneficiarySchool->fkIdLevel = $data->idnivel;
            $beneficiarySchool->school_beneficiaries_state_id = 1;
            $beneficiarySchool->school_beneficiaries_approved_id = 1;
            if($data->turno != "0"){
                $beneficiarySchool->school_beneficiaries_turn_id = $data->turno;
            }

            if(!$beneficiarySchool->save()){
                Log::error('Error processing registration', [
                    'error' => '',
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Error al importar el registro',
                ];
            }
 
            $beneficiaryGroup = \App\Models\BeneficiaryGroup::firstOrNew(['fkIdBeneficiary' => $beneficiary->id]);

            $beneficiaryGroup->fkIdBeneficiary = $beneficiary->id;
            $beneficiaryGroup->voluntary_state_id = 1;
            $beneficiaryGroup->fkIdGroupschool = $groupId;
            $beneficiaryGroup->save();

            if(!$beneficiaryGroup->save()){
                Log::error('Error processing registration', [
                    'error' => '',
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Error al importar el registro',
                ];
            }

            $registration->Importado = 1;
            // $registration->userId = null;
            $registration->updated_at = Carbon::now();
            
            if($registration->save()){
                Log::info('Registration processed successfully', [
                    'data' => $registration,
                    'result' => $registration
                ]);

                return [
                    'success' => true,
                    'message' => 'Registration processed successfully',
                ];
            }

            Log::error('Error processing registration', [
                'error' => '',
            ]);

            return [
                'success' => false,
                'message' => 'Error al importar el registro',
            ];

        } catch (Exception $e) {
            Log::error('Error processing registration', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error processing registration: ' . $e->getMessage(),
            ];
        }
    }
} 