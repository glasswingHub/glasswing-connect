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
        'active',
        'country_code',
        'description'
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

    /**
     * Get the users associated with this importer.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
