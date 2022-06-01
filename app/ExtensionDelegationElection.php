<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExtensionDelegationElection extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'election_id',
        'initial_correct',
        'initial_incorrect',
        'as_delegate',
        'as_follower',
        'as_independent',
        'weight_a',
        'weight_b',
        'reputation_a',
        'reputation_b'
    ];
}
