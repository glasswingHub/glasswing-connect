<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Importer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'source_table',
        'target_table',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Get the column mappings for this importer.
     */
    public function columnMappings()
    {
        return $this->hasMany(ImporterColumn::class);
    }
}
