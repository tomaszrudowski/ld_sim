<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MajorityVote extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'election_id',
        'voter_id',
        'vote'
    ];
}
