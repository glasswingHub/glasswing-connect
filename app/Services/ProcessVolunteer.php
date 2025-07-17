<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Importer;
use App\Models\Volunteer;
use App\Models\VolunteerPersonalReference;
use App\Models\VolunteerCommitment;
use Carbon\Carbon;

class ProcessVolunteer
{
    public $importer;
    public $record;
    public $volunteer;
    public $volunteerPersonalReference;
    public $volunteerCommitment;
    public $response;

    public function __construct(Importer $importer, $recordId, int $beneficiaryType){
        $this->importer = $importer;
        $this->recordId = $recordId;
        $this->record = null;
        $this->beneficiaryType = $beneficiaryType;
        $this->response = [
            'success' => false,
            'message' => ''
        ];
        $this->columns = array(
            (new Volunteer())->getTable() => array(),
            (new VolunteerPersonalReference())->getTable() => array(),
            (new VolunteerCommitment())->getTable() => array(),
        );

        $this->volunteer = null;
        $this->volunteerPersonalReference = null;
        $this->volunteerCommitment = null;
    }

    
    public function execute(): array
    {
        try {
            if(!$this->getRecord()){
                return $this->response;
            }

            if(!$this->getColumns()){
                return $this->response;
            }
            
            if(!$this->processVolunteer()){
                return $this->response;
            }

            if(!$this->processVolunteerPersonalReference()){
                return $this->response;
            }

            if(!$this->processVolunteerCommitment()){
                return $this->response;
            }
            
            if($this->volunteer && $this->volunteerPersonalReference && $this->volunteerCommitment){
                $this->record->import = 1;
                $this->record->save();
                $this->setResponse(true, 'Volunteer processed successfully');
                return $this->response;
            }

            return $this->response;
        } catch (Exception $e) {
            $this->setResponse(false, 'Error processing volunteer: ' . $e->getMessage());
            return $this->response;
        }
    }

    private function setResponse(bool $success, string $message){
        $this->response['success'] = $success;
        $this->response['message'] = $message;
    }

    private function getRecord(){
        $table = $this->importer->source_table;
        $query = DB::connection('gwforms')->table($table);
        
        $countryKeyColumn = $this->importer->columnMappings()
            ->where('country_key', true)
            ->first();
        
        if($this->importer->country_code && $countryKeyColumn){
            $query->where($countryKeyColumn->source_column, $this->importer->country_code);
            $this->record = $query->where('id', $this->recordId)->first();
        };

        if($this->record){
            return true;
        } else {
            $this->setResponse(false, 'No se encontró el registro');
            return false;
        }
    }

    private function getColumns(){
        $emptyColumns = 0;
        foreach($this->columns as $table => $columns){
            $columns = $this->getTableColumns($table);
            $this->columns[$table] = $columns;
            if(count($columns) == 0){
                $emptyColumns++;
            }
        }

        if($emptyColumns == 0){
            return true;
        } else {
            $this->setResponse(false, 'No se encontraron columnas para el importador');
            return false;
        }
    }

    private function processVolunteer(){
        $volunteer = new Volunteer();

        $columns = $this->columns[$volunteer->getTable()];

        $uniquenessColumn = $this->importer->columnMappings()->where('target_table', 'volunteers')->where('uniqueness_key', true)->first();
        if($uniquenessColumn){
            $volunteer = Volunteer::firstOrNew([$uniquenessColumn->target_column => $this->record->{$uniquenessColumn->source_column}]);
        }

        foreach($columns as $column){
            $volunteer->{$column['target']} = $this->record->{$column['source']};
        }

        $volunteer->typeBeneficiary = $this->beneficiaryType;
        $volunteer->fkCodeCountry = $this->importer->country_code;
        $volunteer->year = Carbon::now()->year;
        $volunteer->imported_at = Carbon::now();
        $volunteer->imported_by = 999999;
        $volunteer->code = 0;
        
        if($volunteer->save()){
            $this->volunteer = $volunteer;
            return true;
        } else {
            $this->setResponse(false, 'Error al guardar el voluntario');
            return false;
        }
    }

    private function processVolunteerPersonalReference(){
        $volunteerPersonalReference = new VolunteerPersonalReference();

        $columns = $this->columns[$volunteerPersonalReference->getTable()];
        
        foreach($columns as $column){
            $volunteerPersonalReference->{$column['target']} = $this->record->{$column['source']};
        }

        $volunteerPersonalReference->idVoluntario = $this->volunteer->id;

        if($volunteerPersonalReference->save()){
            $this->volunteerPersonalReference = $volunteerPersonalReference;
            return true;
        } else {
            $this->setResponse(false, 'Error al guardar la referencia personal del voluntario');
            return false;
        }
    }

    private function processVolunteerCommitment(){
        $volunteerCommitment = new VolunteerCommitment();

        $columns = $this->columns[$volunteerCommitment->getTable()];

        foreach($columns as $column){
            $volunteerCommitment->{$column['target']} = $this->record->{$column['source']};
        }

        $volunteerCommitment->fkIdVoluntary = $this->volunteer->id;

        if($volunteerCommitment->save()){
            $this->volunteerCommitment = $volunteerCommitment;
            return true;
        } else {
            $this->setResponse(false, 'Error al guardar el compromiso del voluntario');
            return false;
        }
    }

    private function getTableColumns(String $table){
        return $this->importer->columnMappings()->where('target_table', $table)->where('primary_key', false)
            ->get()
            ->map(function ($item) {
                return [
                    'source' => $item->source_column,
                    'target' => $item->target_column,
                ];
            });
    }
} 