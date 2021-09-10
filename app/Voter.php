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
        'leadership',
        'group'
    ];

    protected $appends = ['majority_votes_stats', 'delegation_one_votes_stats', 'delegation_two_votes_stats'];

    public function majorityVotes() {
        return $this->hasMany(MajorityVote::class, 'voter_id', 'id');
    }

    public function getMajorityVotesStatsAttribute() {
        $correct = $this->majorityVotes()->where('vote', '=', true)->count();
        $incorrect = $this->majorityVotes()->where('vote', '=', false)->count();

        $all = $correct + $incorrect;

        $percentCorrect = $all > 0 ? round(100*$correct/$all, 3) : null;

        return [
            'correct' => $correct,
            'incorrect' => $incorrect,
            'percent_correct' => $percentCorrect
        ];
    }

    public function delegationOneVotes() {
        return $this->hasMany(DelegationOneVote::class, 'voter_id', 'id')
            ->whereHas('election', function($q) {$q->where('type', '=', 'd1');});
    }

    public function delegationTwoVotes() {
        return $this->hasMany(DelegationOneVote::class, 'voter_id', 'id')
            ->whereHas('election', function($q) {$q->where('type', '=', 'd2');});
    }

    public function myDelegationOneVotingDelegate() {
        return $this->hasOne(Voter::class, 'vote_delegation', 'id');
    }

    public function getDelegationOneVotesStatsAttribute() {
        $noOfDelegationOneVotes = $this->delegationOneVotes()->count();

        if ($noOfDelegationOneVotes < 1) {
            return [
                'as_independent'                => null,
                'as_follower'                   => null,
                'as_delegate'                   => null,
                'percent_finals_correct'        => null,
                'finals_correct'                => null,
                'finals_incorrect'              => null,
                'finals_correct_as_independent' => null,
                'finals_correct_as_follower'    => null,
                'finals_correct_as_delegate'    => null
            ];
        }

        $asIndependent = $this->delegationOneVotes()->where('voter_mark', '=', 'i')->count();
        $asFollower = $this->delegationOneVotes()->where('voter_mark', '=', 'f')->count();
        $asDelegate = $this->delegationOneVotes()->where('voter_mark', '=', 'd')->count();
        $myDelegationOneVotesFinalsCorrect = $this->delegationOneVotes()->where('vote_final', '=', 1)->count();
        $myDelegationOneVotesFinalsIncorrect = $noOfDelegationOneVotes - $myDelegationOneVotesFinalsCorrect;
        $myDelegationOneVotesFinalsCorrectAsFollower = $this->delegationOneVotes()->where('vote_final', '=', 1)->where('voter_mark', '=', 'f')->count();
        $myDelegationOneVotesFinalsCorrectAsIndependent = $this->delegationOneVotes()->where('vote_final', '=', 1)->where('voter_mark', '=', 'i')->count();

        $myDelegationOneVotesFinalsCorrectAsDelegate = $myDelegationOneVotesFinalsCorrect
            - $myDelegationOneVotesFinalsCorrectAsFollower
            - $myDelegationOneVotesFinalsCorrectAsIndependent;

        $myDelegationOneVotesPercentFinalsCorrect = 100 * $myDelegationOneVotesFinalsCorrect / $noOfDelegationOneVotes;

        return [
            'as_independent'                => $asIndependent,
            'as_follower'                   => $asFollower,
            'as_delegate'                   => $asDelegate,
            'percent_finals_correct'        => $myDelegationOneVotesPercentFinalsCorrect,
            'finals_correct'                => $myDelegationOneVotesFinalsCorrect,
            'finals_incorrect'              => $myDelegationOneVotesFinalsIncorrect,
            'finals_correct_as_independent' => $myDelegationOneVotesFinalsCorrectAsIndependent,
            'finals_correct_as_follower'    => $myDelegationOneVotesFinalsCorrectAsFollower,
            'finals_correct_as_delegate'    => $myDelegationOneVotesFinalsCorrectAsDelegate
        ];
    }

    public function getDelegationTwoVotesStatsAttribute() {
        $noOfDelegationTwoVotes = $this->delegationTwoVotes()->count();

        if ($noOfDelegationTwoVotes < 1) {
            return [
                'as_independent'                => null,
                'as_follower'                   => null,
                'as_delegate'                   => null,
                'percent_finals_correct'        => null,
                'finals_correct'                => null,
                'finals_incorrect'              => null,
                'finals_correct_as_independent' => null,
                'finals_correct_as_follower'    => null,
                'finals_correct_as_delegate'    => null
            ];
        }

        $asIndependent = $this->delegationTwoVotes()->where('voter_mark', '=', 'i')->count();
        $asFollower = $this->delegationTwoVotes()->where('voter_mark', '=', 'f')->count();
        $asDelegate = $this->delegationTwoVotes()->where('voter_mark', '=', 'd')->where('vote_weight', '>', 1)->count();
        $myDelegationTwoVotesFinalsCorrect = $this->delegationTwoVotes()->where('vote_final', '=', 1)->count();
        $myDelegationTwoVotesFinalsIncorrect = $noOfDelegationTwoVotes - $myDelegationTwoVotesFinalsCorrect;
        $myDelegationTwoVotesFinalsCorrectAsFollower = $this->delegationTwoVotes()->where('vote_final', '=', 1)->where('voter_mark', '=', 'f')->count();
        $myDelegationTwoVotesFinalsCorrectAsIndependent = $this->delegationTwoVotes()->where('vote_final', '=', 1)->where('voter_mark', '=', 'i')->count();

        $myDelegationTwoVotesFinalsCorrectAsDelegate = $myDelegationTwoVotesFinalsCorrect
            - $myDelegationTwoVotesFinalsCorrectAsFollower
            - $myDelegationTwoVotesFinalsCorrectAsIndependent;

        $myDelegationTwoVotesPercentFinalsCorrect = 100 * $myDelegationTwoVotesFinalsCorrect / $noOfDelegationTwoVotes;

        return [
            'as_independent'                => $asIndependent,
            'as_follower'                   => $asFollower,
            'as_delegate'                   => $asDelegate,
            'percent_finals_correct'        => $myDelegationTwoVotesPercentFinalsCorrect,
            'finals_correct'                => $myDelegationTwoVotesFinalsCorrect,
            'finals_incorrect'              => $myDelegationTwoVotesFinalsIncorrect,
            'finals_correct_as_independent' => $myDelegationTwoVotesFinalsCorrectAsIndependent,
            'finals_correct_as_follower'    => $myDelegationTwoVotesFinalsCorrectAsFollower,
            'finals_correct_as_delegate'    => $myDelegationTwoVotesFinalsCorrectAsDelegate
        ];
    }
}
