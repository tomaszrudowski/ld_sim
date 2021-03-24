<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DelegationOneVote extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'election_id',
        'voter_id',
        'vote_direct',
        'vote_delegation',
        'vote_weight',
        'vote_final'
    ];

    protected $appends = [
        //'final_delegation_one_vote'
    ];

    public function parentDelegationOneVote() {
        return $this->belongsTo(DelegationOneVote::class, 'vote_delegation', 'id');
    }

    public function getFinalDelegationOneVoteAttribute() {
        return $this->parentDelegationOneVote()->exists() ? $this->parentDelegationOneVote->vote_direct : $this->vote_direct;
    }
}
