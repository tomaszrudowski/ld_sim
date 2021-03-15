<?php

namespace App\Http\Controllers;

use App\Election;
use App\MajorityElection;
use App\MajorityVote;
use App\Population;
use App\Voter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class APIController extends Controller
{
    public function generatePopulation(Request $request)
    {
        try {
            $attributes = $request->validate([
                'name' => 'nullable|string',
                'size_a' => 'required|integer|min:0|max:1000',
                'size_b' => 'required|integer|min:0|max:1000',
                'init_expertise_a' => 'required|integer|min:0|max:100',
                'init_expertise_b' => 'required|integer|min:0|max:100',
                'spread_expertise_a' => 'required|integer|min:0|max:100',
                'spread_expertise_b' => 'required|integer|min:0|max:100',
                'init_confidence_a' => 'required|integer|min:0|max:100',
                'init_confidence_b' => 'required|integer|min:0|max:100',
                'spread_confidence_a' => 'required|integer|min:0|max:100',
                'spread_confidence_b' => 'required|integer|min:0|max:100',
                'init_following_a' => 'required|integer|min:0|max:100',
                'init_following_b' => 'required|integer|min:0|max:100',
                'spread_following_a' => 'required|integer|min:0|max:100',
                'spread_following_b' => 'required|integer|min:0|max:100',
                'init_leadership_a' => 'required|integer|min:0|max:100',
                'init_leadership_b' => 'required|integer|min:0|max:100',
                'spread_leadership_a' => 'required|integer|min:0|max:100',
                'spread_leadership_b' => 'required|integer|min:0|max:100'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Incorrect payload',
                'val_errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $start = microtime(true);

        $population = new Population();
        $population->save();
        $population->name = isset($attributes['name']) ? $attributes['name'] : 'Population ' . $population->id;
        $population->update();

        $minValue = 1;
        $maxValue = 100;

        $minExpertiseValueA = $attributes['init_expertise_a'] - $attributes['spread_expertise_a'];
        $minExpertiseValueA = $minExpertiseValueA < $minValue ? $minValue : $minExpertiseValueA;
        $maxExpertiseValueA = $attributes['init_expertise_a'] + $attributes['spread_expertise_a'];
        $maxExpertiseValueA = $maxExpertiseValueA > $maxValue ? $maxValue : $maxExpertiseValueA;
        $minExpertiseValueB = $attributes['init_expertise_b'] - $attributes['spread_expertise_b'];
        $minExpertiseValueB = $minExpertiseValueB < $minValue ? $minValue : $minExpertiseValueB;
        $maxExpertiseValueB = $attributes['init_expertise_b'] + $attributes['spread_expertise_b'];
        $maxExpertiseValueB = $maxExpertiseValueB > $maxValue ? $maxValue : $maxExpertiseValueB;

        $minConfidenceValueA = $attributes['init_confidence_a'] - $attributes['spread_confidence_a'];
        $minConfidenceValueA = $minConfidenceValueA < $minValue ? $minValue : $minConfidenceValueA;
        $maxConfidenceValueA = $attributes['init_confidence_a'] + $attributes['spread_confidence_a'];
        $maxConfidenceValueA = $maxConfidenceValueA > $maxValue ? $maxValue : $maxConfidenceValueA;
        $minConfidenceValueB = $attributes['init_confidence_b'] - $attributes['spread_confidence_b'];
        $minConfidenceValueB = $minConfidenceValueB < $minValue ? $minValue : $minConfidenceValueB;
        $maxConfidenceValueB = $attributes['init_confidence_b'] + $attributes['spread_confidence_b'];
        $maxConfidenceValueB = $maxConfidenceValueB > $maxValue ? $maxValue : $maxConfidenceValueB;

        $minFollowingValueA = $attributes['init_following_a'] - $attributes['spread_following_a'];
        $minFollowingValueA = $minFollowingValueA < $minValue ? $minValue : $minFollowingValueA;
        $maxFollowingValueA = $attributes['init_following_a'] + $attributes['spread_following_a'];
        $maxFollowingValueA = $maxFollowingValueA > $maxValue ? $maxValue : $maxFollowingValueA;
        $minFollowingValueB = $attributes['init_following_b'] - $attributes['spread_following_b'];
        $minFollowingValueB = $minFollowingValueB < $minValue ? $minValue : $minFollowingValueB;
        $maxFollowingValueB = $attributes['init_following_b'] + $attributes['spread_following_b'];
        $maxFollowingValueB = $maxFollowingValueB > $maxValue ? $maxValue : $maxFollowingValueB;

        $minLeadershipValueA = $attributes['init_leadership_a'] - $attributes['spread_leadership_a'];
        $minLeadershipValueA = $minLeadershipValueA < $minValue ? $minValue : $minLeadershipValueA;
        $maxLeadershipValueA = $attributes['init_leadership_a'] + $attributes['spread_leadership_a'];
        $maxLeadershipValueA = $maxLeadershipValueA > $maxValue ? $maxValue : $maxLeadershipValueA;
        $minLeadershipValueB = $attributes['init_leadership_b'] - $attributes['spread_leadership_b'];
        $minLeadershipValueB = $minLeadershipValueB < $minValue ? $minValue : $minLeadershipValueB;
        $maxLeadershipValueB = $attributes['init_leadership_b'] + $attributes['spread_leadership_b'];
        $maxLeadershipValueB = $maxLeadershipValueB > $maxValue ? $maxValue : $maxLeadershipValueB;

        $data = new \stdClass();
        $data->meta = new \stdClass();
        $data->meta->size_a = $attributes['size_a'];
        $data->meta->min_expertise_a = $minExpertiseValueA;
        $data->meta->max_expertise_a = $maxExpertiseValueA;
        $data->meta->min_confidence_a = $minConfidenceValueA;
        $data->meta->max_confidence_a = $maxConfidenceValueA;
        $data->meta->min_following_a = $minFollowingValueA;
        $data->meta->max_following_a = $maxFollowingValueA;
        $data->meta->min_leadership_a = $minLeadershipValueA;
        $data->meta->max_leadership_a = $maxLeadershipValueA;
        $data->meta->size_b = $attributes['size_b'];
        $data->meta->min_expertise_b = $minExpertiseValueB;
        $data->meta->max_expertise_b = $maxExpertiseValueB;
        $data->meta->min_confidence_b = $minConfidenceValueB;
        $data->meta->max_confidence_b = $maxConfidenceValueB;
        $data->meta->min_following_b = $minFollowingValueB;
        $data->meta->max_following_b = $maxFollowingValueB;
        $data->meta->min_leadership_b = $minLeadershipValueB;
        $data->meta->max_leadership_b = $maxLeadershipValueB;
/*
        $data->random_expertise_array_a = array();
        $data->random_expertise_array_b = array();
        $data->random_confidence_array_a = array();
        $data->random_confidence_array_b = array();
        $data->random_following_array_a = array();
        $data->random_following_array_b = array();


        for ($i = $minValue; $i <= $maxValue; $i++) {
            $data->random_expertise_array_a[$i] = 0;
            $data->random_expertise_array_b[$i] = 0;
            $data->random_confidence_array_a[$i] = 0;
            $data->random_confidence_array_b[$i] = 0;
            $data->random_following_array_a[$i] = 0;
            $data->random_following_array_b[$i] = 0;
        }
*/
        $data->meta->init_vars = microtime(true) - $start;

        for ($i = 0; $i < $attributes['size_a']; $i++) {
            $randomExpertiseValue = random_int($minExpertiseValueA, $maxExpertiseValueA);
            //$data->random_expertise_array_a[$randomExpertiseValue]++;
            $randomConfidenceValue = random_int($minConfidenceValueA, $maxConfidenceValueA);
            //$data->random_confidence_array_a[$randomConfidenceValue]++;
            $randomFollowingValue = random_int($minFollowingValueA, $maxFollowingValueA);
            //$data->random_following_array_a[$randomFollowingValue]++;
            $randomLeadershipValue = random_int($minLeadershipValueA, $maxLeadershipValueA);
            Voter::create([
                'population_id' => $population->id,
                'expertise'     => $randomExpertiseValue,
                'confidence'    => $randomConfidenceValue,
                'following'     => $randomFollowingValue,
                'leadership'    => $randomLeadershipValue,
                'group'         => 'A'
            ]);
        }

        $data->meta->done_group_a = microtime(true) - $data->meta->init_vars;

        for ($i = 0; $i < $attributes['size_b']; $i++) {
            $randomExpertiseValue = random_int($minExpertiseValueB, $maxExpertiseValueB);
            //$data->random_expertise_array_b[$randomExpertiseValue]++;
            $randomConfidenceValue = random_int($minConfidenceValueB, $maxConfidenceValueB);
            //$data->random_confidence_array_b[$randomConfidenceValue]++;
            $randomFollowingValue = random_int($minFollowingValueB, $maxFollowingValueB);
            //$data->random_following_array_b[$randomFollowingValue]++;
            $randomLeadershipValue = random_int($minLeadershipValueB, $maxLeadershipValueB);
            Voter::create([
                'population_id' => $population->id,
                'expertise'     => $randomExpertiseValue,
                'confidence'    => $randomConfidenceValue,
                'following'     => $randomFollowingValue,
                'leadership'    => $randomLeadershipValue,
                'group'         => 'B'

            ]);
        }

        $data->meta->done_group_b = microtime(true) - $data->meta->done_group_a;

        $data->meta->total_time = round(microtime(true) - $start, 3);

        return response()->json($data, Response::HTTP_OK);
    }

    public function getPopulations(Request $request)
    {
        $data = Population::with('voters', 'elections')
            ->orderBy('id', 'desc')
            ->get()
            ->makeHidden(['elections', 'voters']);

        $data->each(function($item) {
           $item->append(['elections_stats', 'voters_stats']);
        });

        return response()->json($data, Response::HTTP_OK);
    }

    public function getPopulation($population, Request $request)
    {
        $data = Population::where('id', '=', $population)
            ->with('elections', 'voters')
            ->firstOrFail()
            ->append(['elections_stats', 'voters_stats'])
            ->makeHidden('elections', 'voters');

        return response()->json($data, Response::HTTP_OK);
    }

    public function getMajorityElectionsDistribution($population, Request $request)
    {
        $metadata = new \stdClass();
        $startTime = microtime(true);

        $populationData = Population::where('id', '=', $population)
            ->with(['elections' => function($q) {
                $q->where('type', '=', 'm');
            }])
            ->firstOrFail();

        $distribution = array();
        $distributionRounded10 = array();
        //$raw = array();
        for($i=0;$i<101;$i++){
            $distribution[$i] = 0;
        }
        for($i=0;$i<11;$i++){
            $distributionRounded10[$i] = 0;
        }
        foreach ($populationData->elections as $election) {
            $sum = $election->total_correct + $election->total_incorrect;
            if ($sum > 0) {
                $percentCorrect = $election->total_correct / $sum;
                $distribution[floor(100 * $percentCorrect)]++;
                $distributionRounded10[floor(10 * $percentCorrect)]++;
                //$raw[] = round($percentCorrect, 2);
            }
        }
        $distributionRounded10[9] = $distributionRounded10[9] + $distributionRounded10[10];
        unset($distributionRounded10[10]);
        //$sortedRaw = sort($raw);
        $metadata->total_time = round(microtime(true) - $startTime, 3);
        $metadata->number_of_elections = $populationData->elections->count();
        return response()->json([
            'metadata' => $metadata,
            //'sorted_raw' => $raw,
            'distribution' => $distribution,
            'distribution_r_10' => $distributionRounded10
        ], Response::HTTP_OK);
    }

    public function getVoters($population, Request $request)
    {
        $data = Population::where('id', '=', $population)
            ->with('voters')
            ->firstOrFail();

        return response()->json($data->voters, Response::HTTP_OK);
    }

    public function runMajorityElections($population, Request $request) {
        $data = new \stdClass();
        $data->elections_stats = array();

        $attributes = $request->validate([
            'number' => 'nullable|integer|min:1'
        ]);

        $numberOfElections = isset($attributes['number']) ? $attributes['number'] : 1;


        $existingPopulation = Population::where('id', '=', $population)
            ->with('voters')
            ->firstOrFail();

        $startTime = microtime(true);

        for( $i = 0; $i < $numberOfElections; $i++) {
            $singleElectionStats = $this->runSingleMajorityElection($existingPopulation);
            $data->elections[] = $singleElectionStats;
        }

        $data->total_time = round(microtime(true) - $startTime, 3);
        $data->number_of_elections = $numberOfElections;

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * @param $populationID
     * @return \stdClass
     * @throws \Exception
     */
    private function runSingleMajorityElection($populationID): \stdClass
    {
        $startTime = microtime(true);
        $election = Election::create([
            'population_id' => $populationID->id,
            'type' => 'm'
        ]);

        $minValue = 1;
        $maxValue = 100;
        $votes = array();
        $electionStats = new \stdClass();
        $correctChoices = 0;
        $incorrectChoices = 0;

        foreach ($populationID->voters as $voter) {
            $random = random_int($minValue, $maxValue);
            $vote = $random <= $voter->expertise;
            $vote ? $correctChoices++ : $incorrectChoices++;
            $votes[] = [
                'election_id' => $election->id,
                'voter_id' => $voter->id,
                'vote' => $vote
            ];
        }

        $votesTime = microtime(true);
        $electionStats->votes_time = round($votesTime - $startTime, 3);

        MajorityVote::insert($votes);

        $electionStats->total_correct_choices = round($correctChoices, 2);
        $electionStats->total_incorrect_choices = round($incorrectChoices, 2);

        $sum = $correctChoices + $incorrectChoices;
        if ($sum > 0) {
            $percentCorrect = 100 * $correctChoices / $sum;
        } else {
            $percentCorrect = null;
        }

        $electionStats->percent_correct_choices = round($percentCorrect, 2);

        $election->total_correct = $correctChoices;
        $election->total_incorrect = $incorrectChoices;
        $election->save();

        $dbTime = microtime(true);
        $electionStats->votes_db_time = round($dbTime - $votesTime,3);
        return $electionStats;
    }
}
