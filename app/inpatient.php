<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inpatient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'ward_id',
        'patient_inventory',
        'disease',
        'duration',
        'condition',
        'certified_officer'
    ];

    /**
     * Get the patient associated with the inpatient record.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the ward associated with the inpatient record.
     */
    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'duration' => 'integer',
    ];
}