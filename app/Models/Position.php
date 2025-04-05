<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Position extends Model {
    use HasFactory;

    protected $fillable = ['name', 'department_id', 'positions'];

    public function department() {
        return $this->belongsTo(Department::class);
    }
    public function employees()
{
    return $this->hasMany(Employee::class);
}
}

