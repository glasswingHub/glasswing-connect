<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Importer;
use App\Models\Beneficiary;
use Carbon\Carbon;

class ProcessBeneficiary
{
    public function execute(Importer $importer, $recordId, int $beneficiaryType): array
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
                Log::error('Error processing beneficiary', [
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
                Log::error('Error processing beneficiary', [
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
            
            $beneficiary = new Beneficiary();

            $uniquenessColumn = $importer->columnMappings()->where('uniqueness_key', true)->first();
            if($uniquenessColumn){
                $beneficiary = \App\Models\Beneficiary::firstOrNew([$uniquenessColumn->target_column => $record->{$uniquenessColumn->source_column}]);
            }

            foreach($columns as $column){
                $beneficiary->{$column['target']} = $record->{$column['source']};
            }

            $beneficiary->fkCodeCountry = $importer->country_code;
            $beneficiary->fkIdTypeBeneficiarity = $beneficiaryType;
            // $beneficiary->imported_at = Carbon::now();
            
            if(!$beneficiary->save()){
                Log::error('Error processing beneficiary', [
                    'error' => '',
                ]);

                return [
                    'success' => false,
                    'message' => 'Error al importar el registro',
                ];
            }

            // $record->Importado = 1;
            // $record->updated_at = Carbon::now();
            
            if($beneficiary->save()){
                Log::info('Beneficiary processed successfully', [
                    'data' => $beneficiary,
                    'result' => $beneficiary
                ]);

                return [
                    'success' => true,
                    'message' => 'Beneficiary processed successfully',
                ];
            }

            Log::error('Error processing beneficiary', [
                'error' => '',
            ]);

            return [
                'success' => false,
                'message' => 'Error al importar el beneficiario',
            ];

        } catch (Exception $e) {
            Log::error('Error processing beneficiary', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error processing beneficiary: ' . $e->getMessage(),
            ];
        }
    }
} 