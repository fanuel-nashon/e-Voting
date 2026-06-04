<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = ['student_id', 'candidate_id', 'position_id'];

    public function student()   { return $this->belongsTo(Student::class); }
    public function candidate() { return $this->belongsTo(Candidate::class); }
    public function position()  { return $this->belongsTo(Position::class); }
}
