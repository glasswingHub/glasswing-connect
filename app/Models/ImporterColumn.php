<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImporterColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'importer_id',
        'source_column',
        'target_column',
        'display_name',
        'primary_key',
        'show_in_list',
        'country_key',
        'uniqueness_key',
    ];

    /**
     * Get the importer that owns the column mapping.
     */
    public function importer()
    {
        return $this->belongsTo(Importer::class);
    }
} 