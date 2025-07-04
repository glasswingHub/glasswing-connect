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
    ];

    /**
     * Get the importer that owns the column mapping.
     */
    public function importer()
    {
        return $this->belongsTo(Importer::class);
    }
} 