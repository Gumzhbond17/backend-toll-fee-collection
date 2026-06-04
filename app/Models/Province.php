<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// #[Fillable('province_name_la', 'province_name_en', 'is_active', 'created_by', 'updated_by')]
class Province extends Model
{
    use HasFactory;

    protected $table = 'provinces';

    protected $fillable = [
        'province_name_la',
        'province_name_en',
        'is_active',
        'created_by',
        'updated_by',
    ];

    // Relationship to District
    /* public function districts()
    {
        return $this->hasMany(District::class, 'province_id');
    } */

    // Relationship to User (created_by / updated_by)
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
