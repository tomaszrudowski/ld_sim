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
            $groups[] = $newGroup;
        }
        return [
            'groups' => $groups,
            'no_of_voters' => $this->voters()->count(),
            'expertise_average' => $this->voters()->average('expertise'),
            'confidence_average' => $this->voters()->average('confidence'),
            'following_average' => $this->voters()->average('following')
        ];
    }

    public function elections() {
        return $this->hasMany(Election::class, 'population_id', 'id');
    }

    public function getElectionsStatsAttribute() {
        $noOfCorrectAvg = $this->elections()->where('type', '=', 'm')->average('total_correct');
        $noOfIncorrectAvg = $this->elections()->where('type', '=', 'm')->average('total_incorrect');
        $sum = $noOfCorrectAvg + $noOfIncorrectAvg;
        if ($sum > 0) {
            $percentCorrect = 100 * $noOfCorrectAvg / $sum;
        } else {
            $percentCorrect = null;
        }

        return [
            'm' => $this->elections()->where('type', '=', 'm')->count(),
            'm_no_of_correct_average' => $noOfCorrectAvg,
            'm_no_of_incorrect_average' => $noOfIncorrectAvg,
            'm_percent_correct' => $percentCorrect
        ];
    }
}
