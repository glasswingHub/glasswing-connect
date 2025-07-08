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
    public function execute(Importer $importer, int $recordId): array
    {
        try {
            $table = $importer->source_table;
            $record = DB::connection('gwforms')
                ->table($table)
                ->where('id', $recordId)
                ->first();


            
            if($record == null){
                Log::error('Error processing beneficiary', [
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
            
            // $beneficiary = \App\Models\Beneficiary::firstOrNew(['DNI' => $data->identidad]);
            $beneficiary = new Beneficiary();

            foreach($columns as $column){
                $beneficiary->{$column['target']} = $record->{$column['source']};
            }

            // $beneficiary->date_imported = Carbon::now();
            $beneficiary->fkCodeCountry = "+503";
            $beneficiary->fkIdTypeBeneficiarity = 1;
            
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