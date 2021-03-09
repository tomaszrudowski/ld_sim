<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voter extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'population_id',
        'expertise',
        'confidence',
        'following',
        'group'
    ];

    protected $appends = ['majority_votes_stats'];

    public function majorityVotes() {
        return $this->hasMany(MajorityVote::class, 'voter_id', 'id');
    }

    public function getMajorityVotesStatsAttribute() {

        return [
            'correct' => $this->majorityVotes()->where('vote', '=', true)->count(),
            'incorrect' => $this->majorityVotes()->where('vote', '=', false)->count()
        ];
    }
}
