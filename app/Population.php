<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Population extends Model
{
    protected $fillable =[
        'name',
        'parent_id',
        'stage',
        'election_type'
    ];

    protected $appends = [
        //'no_of_voters'
    ];

    public function voters() {
        return $this->hasMany(Voter::class, 'population_id', 'id');
    }

    public function childPopulations() {
        return $this->hasMany(Population::class, 'parent_id', 'id');
    }

    public function getChildrenCountAttribute() {
        return $this->childPopulations()->count();
    }

    public function getChildPopulationsStatsAttribute() {
        $data = [
            'm' => [
                'info' => 'Majority',
                'count' => 0,
                'learning_stage_count' => 0,
                'performance_stage_count' => 0
            ],
            'd1' => [
                'info' => 'Delegation Performance (d1)',
                'count' => 0,
                'learning_stage_count' => 0,
                'performance_stage_count' => 0
            ],
            'd2' => [
                'info' => 'Delegation Learning (d2)',
                'count' => 0,
                'learning_stage_count' => 0,
                'performance_stage_count' => 0
            ],
            'd3' => [
                'info' => 'Delegation Learning (d3)',
                'count' => 0,
                'learning_stage_count' => 0,
                'performance_stage_count' => 0
            ]
        ];
        foreach ($this->childPopulations as $childPopulation) {
            if (isset($childPopulation->election_type) && isset($data[$childPopulation->election_type])) {
                $data[$childPopulation->election_type]['count']++;

                switch($childPopulation->stage) {
                    case 'l':
                        $data[$childPopulation->election_type]['learning_stage_count']++;
                        break;
                    case 'p':
                        $data[$childPopulation->election_type]['performance_stage_count']++;
                        break;
                    default:
                        break;
                }
            }
        }
        return $data;
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

        $noOfCorrectAvgD2 = $this->elections()->where('type', '=', 'd2')->average('total_correct');
        $noOfIncorrectAvgD2 = $this->elections()->where('type', '=', 'd2')->average('total_incorrect');
        $sumD2 = $noOfCorrectAvgD2 + $noOfIncorrectAvgD2;
        if ($sumD2 > 0) {
            $percentCorrectD2 = 100 * $noOfCorrectAvgD2 / $sumD2;
        } else {
            $percentCorrectD2 = null;
        }

        $noOfCorrectAvgD3 = $this->elections()->where('type', '=', 'd3')->average('total_correct');
        $noOfIncorrectAvgD3 = $this->elections()->where('type', '=', 'd3')->average('total_incorrect');
        $sumD3 = $noOfCorrectAvgD3 + $noOfIncorrectAvgD3;
        if ($sumD3 > 0) {
            $percentCorrectD3 = 100 * $noOfCorrectAvgD3 / $sumD3;
        } else {
            $percentCorrectD3 = null;
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
            ],
            [
                'type' => 'd2',
                'count' => $this->elections()->where('type', '=', 'd2')->count(),
                'no_of_correct_average' => $noOfCorrectAvgD2,
                'no_of_incorrect_average' => $noOfIncorrectAvgD2,
                'percent_correct' => $percentCorrectD2
            ],
            [
                'type' => 'd3',
                'count' => $this->elections()->where('type', '=', 'd3')->count(),
                'no_of_correct_average' => $noOfCorrectAvgD3,
                'no_of_incorrect_average' => $noOfIncorrectAvgD3,
                'percent_correct' => $percentCorrectD3
            ]
        ];
    }
}
