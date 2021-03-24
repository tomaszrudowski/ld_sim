<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Population extends Model
{
    protected $fillable =[
        'name'
    ];

    protected $appends = [
        //'no_of_voters'
    ];

    public function voters() {
        return $this->hasMany(Voter::class, 'population_id', 'id');
    }

    public function getNoOfVotersAttribute() {
        return $this->voters()->count();
    }

    public function getVotersStatsAttribute() {
        $groupNames = $this->voters()->pluck('group')->unique();
        $groups = array();
        foreach ($groupNames as $name) {
            $newGroup = new \stdClass();
            $newGroup->name = $name;
            $newGroup->no_of_voters = $this->voters()->where('group', '=', $name)->count();
            $newGroup->expertise_average = $this->voters()->where('group', '=', $name)->average('expertise');
            $newGroup->confidence_average = $this->voters()->where('group', '=', $name)->average('confidence');
            $newGroup->following_average = $this->voters()->where('group', '=', $name)->average('following');
            $newGroup->leadership_average = $this->voters()->where('group', '=', $name)->average('leadership');
            $groups[] = $newGroup;
        }
        return [
            'groups' => $groups,
            'no_of_voters' => $this->voters()->count(),
            'expertise_average' => $this->voters()->average('expertise'),
            'confidence_average' => $this->voters()->average('confidence'),
            'following_average' => $this->voters()->average('following'),
            'leadership_average' => $this->voters()->average('leadership')
        ];
    }

    public function elections() {
        return $this->hasMany(Election::class, 'population_id', 'id');
    }

    public function getElectionsStatsAttribute() {
        $noOfCorrectAvgM = $this->elections()->where('type', '=', 'm')->average('total_correct');
        $noOfIncorrectAvgM = $this->elections()->where('type', '=', 'm')->average('total_incorrect');
        $sumM = $noOfCorrectAvgM+ $noOfIncorrectAvgM;
        if ($sumM > 0) {
            $percentCorrectM = 100 * $noOfCorrectAvgM / $sumM;
        } else {
            $percentCorrectM = null;
        }
        $noOfCorrectAvgD1 = $this->elections()->where('type', '=', 'd1')->average('total_correct');
        $noOfIncorrectAvgD1 = $this->elections()->where('type', '=', 'd1')->average('total_incorrect');
        $sumD1 = $noOfCorrectAvgD1 + $noOfIncorrectAvgD1;
        if ($sumD1 > 0) {
            $percentCorrectD1 = 100 * $noOfCorrectAvgD1 / $sumD1;
        } else {
            $percentCorrectD1 = null;
        }

        return [
            [
                'type' => 'm',
                'count' => $this->elections()->where('type', '=', 'm')->count(),
                'no_of_correct_average' => $noOfCorrectAvgM,
                'no_of_incorrect_average' => $noOfIncorrectAvgM,
                'percent_correct' => $percentCorrectM
            ],
            [
                'type' => 'd1',
                'count' => $this->elections()->where('type', '=', 'd1')->count(),
                'no_of_correct_average' => $noOfCorrectAvgD1,
                'no_of_incorrect_average' => $noOfIncorrectAvgD1,
                'percent_correct' => $percentCorrectD1
            ]
        ];
    }
}
