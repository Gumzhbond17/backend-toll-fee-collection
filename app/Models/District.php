<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $fillable = [
        'province_id',
        'district_name_la',
        'district_name_en',
        'created_by',
        'updated_by',
    ];

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
