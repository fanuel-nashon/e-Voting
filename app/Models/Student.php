<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['reg_no', 'name', 'program_id', 'faculty_id', 'user_id'];

    public function user()     { return $this->belongsTo(User::class); }
    public function faculty()  { return $this->belongsTo(Faculty::class); }
    public function program()  { return $this->belongsTo(Program::class); }
    public function votes()    { return $this->hasMany(Vote::class); }
}
