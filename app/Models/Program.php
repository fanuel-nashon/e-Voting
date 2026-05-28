<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = ['name', 'faculty_id'];

    public function faculty()
    {
        return $this->belongsTo(Faculty::class);
    }

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
