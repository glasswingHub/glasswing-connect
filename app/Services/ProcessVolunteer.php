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
    public function execute(Importer $importer, int $recordId, int $beneficiaryType): array
    {
        try {
            // Verificar que el registro pertenece al país correcto antes de procesarlo
            $table = $importer->source_table;
            $query = DB::connection('gwforms')->table($table);
            
            // Obtener la columna marcada como country_key
            $countryKeyColumn = $importer->columnMappings()
                ->where('country_key', true)
                ->first();
            
            $record = null;
            // Aplicar filtro por país si existe country_code en el importer y hay una columna country_key
            if ($importer->country_code && $countryKeyColumn) {
                $query->where($countryKeyColumn->source_column, $importer->country_code);
                $record = $query->where('id', $recordId)->first();
            }
            
            if($record == null){
                Log::error('Error processing volunteer', [
                    'error' => ''
                ]);
                
                return [
                    'success' => false,
                    'message' => 'No se encontró el registro'
                ];
            }

            $columns = $importer->columnMappings()->where('primary_key', false)
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
            
            $volunteer = new Volunteer();

            $uniquenessColumn = $importer->columnMappings()->where('uniqueness_key', true)->first();
            if($uniquenessColumn){
                $volunteer = \App\Models\Volunteer::firstOrNew([$uniquenessColumn->target_column => $record->{$uniquenessColumn->source_column}]);
            }

            foreach($columns as $column){
                $volunteer->{$column['target']} = $record->{$column['source']};
            }

            $volunteer->typeBeneficiary = $beneficiaryType;
            $volunteer->fkCodeCountry = $importer->country_code;
            // $volunteer->imported_at = Carbon::now();
            $volunteer->hourSocial = 2;
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