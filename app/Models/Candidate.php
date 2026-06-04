<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = ['name', 'image', 'position_id'];

    public function position()   { return $this->belongsTo(Position::class); }
    public function votes()      { return $this->hasMany(Vote::class); }
    public function acceptance() { return $this->hasOne(CandidateAcceptance::class); }
}
