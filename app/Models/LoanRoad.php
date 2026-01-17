<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanRoad extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'detail',
        'total',
        'start_date',
        'sales_commission',
        'length',
        'latitude',
        'inactive',
        'user_id',
        'supervisor_id'
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
            'start_date' => 'date',
            'sales_commission' => 'decimal',
            'length' => 'decimal',
            'latitude' => 'decimal',
            'inactive' => 'boolean',
        ];
    }

    /**
     *
     * Relación inversa de: $user->loanRoads()
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     *
     * Relación inversa de: $user->supervisedRoads()
     */
    public function supervisor()
    {

        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     *
     * Relación uno a muchos.
     */
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

}
