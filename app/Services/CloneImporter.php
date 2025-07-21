<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Importer;
use App\Models\ImporterColumn;

class CloneImporter
{
    public $originalImporter;
    public $originalColumns;
    public $importer;
    public $columns;
    public $volunteerCommitment;
    public $response;
    public $user;

    public function __construct(Importer $originalImporter){
        $this->originalImporter = $originalImporter;
        $this->originalColumns = $originalImporter->columnMappings()->orderBy('id', 'asc')->get();
        $this->response = [
            'success' => false,
            'message' => ''
        ];
        $this->importer = null;
        $this->columns = array();
    }

    
    public function execute(): array
    {
        try {
            if(!$this->saveImporter()){
                return $this->response;
            }

            if(!$this->saveColumns()){
                return $this->response;
            }
            
            if($this->importer && count($this->columns) > 0){
                $this->setResponse(true, 'Importer cloned successfully');
                return $this->response;
            }

            return $this->response;
        } catch (Exception $e) {
            $this->setResponse(false, 'Error cloning importer: ' . $e->getMessage());
            return $this->response;
        }
    }

    private function setResponse(bool $success, string $message){
        $this->response['success'] = $success;
        $this->response['message'] = $message;
    }

    private function saveImporter(){
        $importer = new Importer();
        $importer->name = $this->originalImporter->name;
        $importer->source_table = $this->originalImporter->source_table;
        $importer->target_table = $this->originalImporter->target_table;
        $importer->country_code = $this->originalImporter->country_code;
        $importer->description = $this->originalImporter->description;
        $importer->active = false;
        $importer->configured = $this->originalImporter->configured;

        if($importer->save()){
            $this->importer = $importer;
            return true;
        } else {
            $this->setResponse(false, 'Error al guardar el importador');
            return false;
        }
    }

    private function saveColumns(){
        foreach($this->originalColumns as $originalColumn){
            $column = new ImporterColumn();
            $column->importer_id = $this->importer->id;
            $column->source_column = $originalColumn->source_column;
            $column->target_column = $originalColumn->target_column;
            $column->display_name = $originalColumn->display_name;
            $column->primary_key = $originalColumn->primary_key;
            $column->show_in_list = $originalColumn->show_in_list;
            $column->country_key = $originalColumn->country_key;
            $column->uniqueness_key = $originalColumn->uniqueness_key;
            $column->target_table = $originalColumn->target_table;
            $column->source_table = $originalColumn->source_table;
            $column->beneficiary_type_key = $originalColumn->beneficiary_type_key;
            if($column->save()){
                $this->columns[] = $column;
            }
        }

        if(count($this->columns) == count($this->originalColumns)){
            return true;
        } else {
            $this->setResponse(false, 'Error al guardar las columnas');
            return false;
        }
    }
} 