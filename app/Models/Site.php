<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $table = 'sites';

    protected $fillable = [
        'site_name',
        'district_id',
        'province_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    // Relationship to District
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    // Relationship to Province
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    // Relationship to User (created_by / updated_by)
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
