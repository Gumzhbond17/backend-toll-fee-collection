<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('dept_name_la', 'dept_name_en', 'is_active', 'created_by', 'updated_by')]
class Department extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
