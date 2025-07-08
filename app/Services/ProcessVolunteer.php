<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Importer;
use App\Models\Volunteer;
use Carbon\Carbon;

class ProcessVolunteer
{
    public function execute(Importer $importer, int $recordId): array
    {
        try {
            $table = $importer->source_table;
            $record = DB::connection('gwforms')
                ->table($table)
                ->where('id', $recordId)
                ->first();


            
            if($record == null){
                Log::error('Error processing volunteer', [
                    'error' => ''
                ]);
                
                return [
                    'success' => false,
                    'message' => 'No se encontró el registro'
                ];
            }

            $columns = $importer->columnMappings()
                ->get()
                ->map(function ($item) use ($record) {
                    return [
                        'source' => $item->source_column,
                        'target' => $item->target_column,
                    ];
                });

            if($columns == null){
                Log::error('Error processing volunteer', [
                    'error' => ''
                ]);
                
                return [
                    'success' => false,
                    'message' => 'No se encontraron columnas para el importador'
                ];
            }

            // if($record->importado == "1"){
            //     Log::error('Error processing beneficiary', [
            //         'error' => ''
            //     ]);
                
            //     return [
            //         'success' => false,
            //         'message' => 'El registro ya ha sido importado',
            //     ];
            // }
            
            // $beneficiary = \App\Models\Beneficiary::firstOrNew(['DNI' => $data->identidad]);
            $volunteer = new Volunteer();

            foreach($columns as $column){
                $volunteer->{$column['target']} = $record->{$column['source']};
            }

            // $volunteer->date_imported = Carbon::now();
            $volunteer->typeBeneficiary = 1;
            $volunteer->code = $record->id;
            $volunteer->surname = "surname";
            $volunteer->fechaNac = Carbon::now();
            $volunteer->year = 2025;
            $volunteer->hourSocial = 2;
            $volunteer->fkCodeCountry = "+503";
            $volunteer->origin = 1;
            
            if(!$volunteer->save()){
                Log::error('Error processing volunteer', [
                    'error' => '',
                ]);

                return [
                    'success' => false,
                    'message' => 'Error al importar el registro',
                ];
            }

            // $record->Importado = 1;
            // $record->updated_at = Carbon::now();
            
            if($volunteer->save()){
                Log::info('Volunteer processed successfully', [
                    'data' => $volunteer,
                    'result' => $volunteer
                ]);

                return [
                    'success' => true,
                    'message' => 'Volunteer processed successfully',
                ];
            }

            Log::error('Error processing volunteer', [
                'error' => '',
            ]);

            return [
                'success' => false,
                'message' => 'Error al importar el voluntario',
            ];

        } catch (Exception $e) {
            Log::error('Error processing volunteer', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error processing volunteer: ' . $e->getMessage(),
            ];
        }
    }
} 