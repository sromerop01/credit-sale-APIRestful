<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'identification',
        'name',
        'address',
        'phone',
        'detail',
        'delinquent',
        'quota',
        'interest',
        'order',
        'loan_road_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'identification' => 'integer',
            'delinquent' => 'boolean',
            'quota' => 'integer',
            'interest' => 'decimal',
            'order' => 'integer',
        ];
    }
}
