<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'employee_id', 'department_id', 'designation', 'phone'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}