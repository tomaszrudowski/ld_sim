<?php

namespace App\Http\Controllers;

use App\DelegationOneVote;
use App\Election;
use App\ExtensionDelegationElection;
use App\MajorityVote;
use App\Population;
use App\Voter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class APIController extends Controller
{
    public function generatePopulationTemplate(Request $request)
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
        $population->name = isset($attributes['name']) ? $attributes['name'] : 'Population Template ' . $population->id;
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

    public function getChildPopulations(Population $population) {
        $data = Population::with('voters', 'elections')
            ->where('parent_id', '=', $population->id)
            ->orderBy('id', 'asc')
            ->get()
            ->makeHidden(['elections', 'voters']);

        $data->each(function($item) {
            $item->append(['elections_stats', 'voters_stats']);
        });

        return response()->json($data, Response::HTTP_OK);
    }

    public function generateChildPopulation(Population $template, Request $request) {
        try {
            $attributes = $request->validate([
                'election_type' => 'required|string|in:m,d1,d2,d3'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Incorrect payload',
                'val_errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $nextChildId = $template->childPopulations()->where('election_type', '=', $attributes['election_type'])->count() + 1;

        $newPopulation = new Population();
        if (!isset($template->parent_id)) {
            $newPopulationName = "[". $template->name . "].(" . $attributes['election_type'] . "-" . $nextChildId . ")";
        } else {
            $newPopulationName = $template->name . ".(" . $attributes['election_type'] . "-" . $nextChildId . ")";
        }
        $newPopulation->election_type = $attributes['election_type'];
        $newPopulation->name = $newPopulationName;
        $newPopulation->parent_id = $template->id;
        $newPopulation->stage = $newPopulation->election_type === "d1" ? 'p' : 'l'; // 'performance'/'learning' default stage for child population
        $newPopulation->save();

        foreach ($template->voters as $parentPopulationVoter) {
            Voter::create([
                'population_id' => $newPopulation->id,
                'expertise'     => $parentPopulationVoter->expertise,
                'confidence'    => $parentPopulationVoter->confidence,
                'following'     => $parentPopulationVoter->following,
                'leadership'    => $parentPopulationVoter->leadership,
                'reputation'    => $parentPopulationVoter->reputation,
                'group'         => $parentPopulationVoter->group
            ]);
        }
    }

    public function getPopulationTemplates(Request $request)
    {
        $data = Population::with('voters', 'elections', 'childPopulations')
            ->whereNull('parent_id')
            ->orderBy('id', 'desc')
            ->get()
            ->makeHidden(['elections', 'voters', 'childPopulations']);

        $data->each(function($item) {
           $item->append(['elections_stats', 'voters_stats', 'children_count', 'child_populations_stats']);
        });

        return response()->json($data, Response::HTTP_OK);
    }

    public function getPopulationTemplate(int $template, Request $request) {
        $data = Population::where('id', '=', $template)
            ->whereNull('parent_id')
            ->with('elections', 'voters', 'childPopulations')
            ->firstOrFail()
            ->append(['elections_stats', 'voters_stats', 'children_count', 'child_populations_stats'])
            ->makeHidden('elections', 'voters');

        $data->childPopulations->each(function($item) {
            $item->append(['elections_stats']);
        });

        return response()->json($data, Response::HTTP_OK);
    }

    public function getPopulation(int $template, int $population, Request $request)
    {
        $data = Population::where('id', '=', $population)
            ->where('parent_id', '=', $template)
            ->with('elections', 'voters')
            ->firstOrFail()
            ->append(['elections_stats', 'voters_stats'])
            ->makeHidden('elections', 'voters');

        return response()->json($data, Response::HTTP_OK);
    }

    public function changeToPerformanceStage(int $template, int $population, Request $request)
    {
        $childPopulation = Population::where('id', '=', $population)
            ->where('parent_id', '=', $template)
            ->firstOrFail();

        $childPopulation->stage = 'p';

        if ($childPopulation->save()) {
            return response()->json(null, Response::HTTP_NO_CONTENT);
        }

        return response()->json(['error' => 'Stage not saved'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getMajorityElectionsDistribution(int $template, int $population, Request $request)
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

    public function getVoters(int $template, int $population, Request $request)
    {
        $data = Population::where('id', '=', $population)
            ->with('voters')
            ->firstOrFail();

        return response()->json($data->voters, Response::HTTP_OK);
    }

    /**
     * @deprecated
     *
     * @param $population
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function runMajorityElections(int $template, int $population, Request $request) {
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
     * @param $population
     * @return \stdClass
     * @throws \Exception
     */
    private function runSingleMajorityElection($population): \stdClass
    {
        $startTime = microtime(true);
        $election = Election::create([
            'population_id' => $population->id,
            'type' => 'm'
        ]);

        $minValue = 1;
        $maxValue = 100;
        $votes = array();
        $electionStats = new \stdClass();
        $electionStats->type = "m";
        $correctChoices = 0;
        $incorrectChoices = 0;

        foreach ($population->voters as $voter) {
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

    public function runElections(int $template, int $population, Request $request) {
        $data = new \stdClass();
        $data->elections_stats = array();

        $attributes = $request->validate([
            'type'      => 'required|string',
            'number'    => 'nullable|integer|min:1'
        ]);

        $numberOfElections = isset($attributes['number']) ? $attributes['number'] : 1;
        switch ($attributes['type']) {
            case 'm' :
                $electionMethod = 'runSingleMajorityElection';
                break;
            case 'd1' :
                $electionMethod = 'runSingleDelegationElectionVersion1';
                break;
            case 'd2' :
                $electionMethod = 'runSingleDelegationElectionVersion2';
                break;
            case 'd3' :
                $electionMethod = 'runSingleDelegationElectionVersion3';
                break;
            default :
                return response()->json('unknown election type', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $existingPopulation = Population::where('id', '=', $population)
            ->where('parent_id', '=', $template)
            ->with('voters')
            ->firstOrFail();

        $startTime = microtime(true);
        $elections = array();

        for( $i = 0; $i < $numberOfElections; $i++) {
            $singleElectionStats = $this->$electionMethod($existingPopulation);
            $elections[] = $singleElectionStats;
        }
        $data->number_of_elections = $numberOfElections;
        $data->elections_type = $attributes['type'];
        /*
        usort($elections, function($a, $b) {
            if ($a->total_correct_choices == $b->total_correct_choices) {
                return 0;
            }
            return ($a->total_correct_choices > $b->total_correct_choices) ? -1 : 1;
        });
        */

        $data->total_time = round(microtime(true) - $startTime, 3);

        $data->elections = $elections;

        return response()->json($data, Response::HTTP_OK);
    }

    /**
     * Performance stage
     * No changes to voters' attributes
     *
     * @param Population $population
     * @return \stdClass
     * @throws \Exception
     */
    private function runSingleDelegationElectionVersion1(Population $population) {
        return $this->runSingleDelegationElection($population, false, 'd1', false);
    }

    /**
     * Learning stage.
     * Modify Reputation only after each election
     *
     * @param Population $population
     * @return \stdClass
     * @throws \Exception
     */
    private function runSingleDelegationElectionVersion2 (Population $population) {
        return $this->runSingleDelegationElection($population, true, 'd2', false);
    }

    /**
     * Learning stage.
     * Modify Reputation, Following and Leadership after each election
     *
     * @param Population $population
     * @return \stdClass
     * @throws \Exception
     */
    private function runSingleDelegationElectionVersion3 (Population $population) {
        return $this->runSingleDelegationElection($population, true, 'd3', true);
    }
    /*
    private function runSingleDelegationElectionVersion1(Population $population) {
        $startTime = microtime(true);
        $election = Election::create([
            'population_id' => $population->id,
            'type' => 'd1'
        ]);

        $votes = array();
        $electionStats = new \stdClass();
        $electionStats->type = "d1";
        $asDelegate = 0;
        $asFollower = 0;
        $asIndependent = 0;

        $delegatesIDs = array();

        $minValue = 1;
        $maxValue = 100;
        $correctChoices = 0;
        $incorrectChoices = 0;

        $noOfVotes = $population->voters->count();
        $electionStats->no_of_votes = $noOfVotes;

        $followersVotes = [];

        foreach ($population->voters as $voter) {
            $tresholdFollowing = $voter->following;
            $tresholdIndependent = 100;
            $tresholdLeadership = 100 + $voter->leadership;

            $randomBehaviour = random_int(0, $tresholdLeadership);

            $randomExpertise = random_int($minValue, $maxValue);
            $voteDirect = $randomExpertise <= $voter->expertise;
            $voteDirect ? $correctChoices++ : $incorrectChoices++;

            $newVote = [
                'election_id' => $election->id,
                'voter_id' => $voter->id,
                'vote_direct' => $voteDirect,
                'vote_delegation' => null,
                'vote_weight' => 1,
                'vote_final' => $voteDirect
            ];


            if ($randomBehaviour <= $tresholdFollowing) {
                $newVote['voter_mark'] = 'f';
                $asFollower++;
                $followersVotes[$voter->id] = $newVote;
            } elseif (($randomBehaviour <= $tresholdIndependent)) {
                $newVote['voter_mark'] =  'i';
                $asIndependent++;
                $votes[$voter->id] = $newVote;
            } else {
                $newVote['voter_mark'] =  'd';
                $asDelegate++;
                $delegatesIDs[] = $voter->id;
                $votes[$voter->id] = $newVote;
            }
        }

        $electionStats->initial_correct = $correctChoices;
        $electionStats->initial_incorrect = $incorrectChoices;

        $prepareTime = microtime(true);

        $followersDistribution = array();
        foreach ($delegatesIDs as $delegateID) {
            $followersDistribution[$delegateID] = 0;
        }

        DelegationOneVote::insert($votes);

        $mappedToVoterID = array();

        // replace own expertise test if follower and there are delegates
        if ($asDelegate > 0) {

            $savedVotes = DelegationOneVote::where('election_id', '=', $election->id)
                ->where('voter_mark', '=', 'd')
                ->get();

            foreach ($savedVotes as $savedVote) {
                $mappedToVoterID[$savedVote->voter_id] = $savedVote;
            }

            foreach ($followersVotes as $key => $item) {
                if($item['voter_mark'] == 'f' ) {
                    // choose delegate
                    $delegateID = $delegatesIDs[array_rand($delegatesIDs)];
                    $followersVotes[$key]['vote_delegation'] = $mappedToVoterID[$delegateID]->id;//$delegateID;
                    $followersDistribution[$delegateID]++;
                    // revert expertise choice
                    $item['vote_direct'] ? $correctChoices-- : $incorrectChoices--;
                    //$followersVotes[$key]['vote_direct'] = null; // keep track of own expertise choice
                    $followersVotes[$key]['vote_weight'] = 0;
                    // add delegate choice
                    $mappedToVoterID[$delegateID]->vote_weight++;
                    $mappedToVoterID[$delegateID]->vote_direct ? $correctChoices++ : $incorrectChoices++;
                    $followersVotes[$key]['vote_final'] = $mappedToVoterID[$delegateID]->vote_final;
                }
            }
            // todo update for delegates who has followers
        }

        foreach ($mappedToVoterID as $key => $item) {
            if ($followersDistribution[$key] > 0) {
                $item->save();
            }
        }

        $election->total_correct = $correctChoices;
        $election->total_incorrect = $incorrectChoices;

        $electionStats->total_correct_choices = $correctChoices;
        $electionStats->total_incorrect_choices = $incorrectChoices;

        if ($noOfVotes > 0) {
            $electionStats->percent_initial_correct_choices = 100 * $electionStats->initial_correct / $noOfVotes;
            $electionStats->percent_correct_choices = 100 * $election->total_correct / $noOfVotes;
        } else {
            $electionStats->percent_initial_correct_choices = null;
            $electionStats->percent_correct_choices = null;
        }

        $votesTime = microtime(true);

        $election->save();

        $electionExtension = ExtensionDelegationElection::create([
            'election_id'       => $election->id,
            'initial_correct'   => $electionStats->initial_correct,
            'initial_incorrect' => $electionStats->initial_incorrect,
            'as_delegate'       => $asDelegate,
            'as_follower'       => $asFollower,
            'as_independent'    => $asIndependent
        ]);

        DelegationOneVote::insert($followersVotes);

        $databaseTime = microtime(true);

        $electionStats->votes_time = round($votesTime - $startTime, 5);
        $electionStats->votes_db_time = round($databaseTime - $votesTime, 5);

        $electionStats->as_delegate = $asDelegate;
        $electionStats->as_follower = $asFollower;
        $electionStats->as_independent = $asIndependent;

        $electionStats->delegates = $delegatesIDs;
        $electionStats->followers_distribution = $followersDistribution;
        $electionStats->data = array_values($votes);

        return $electionStats;
    }
    */
    /**
     * Run delegation election, default in performance stage (d1)
     * @param Population $population
     * @param bool $modifyReputation
     * @param string $type
     * @param bool $modifyAttributes
     * @return \stdClass
     * @throws \Exception
     */
    private function runSingleDelegationElection(
        Population $population,
        $modifyReputation = false,
        $type = 'd1',
        $modifyAttributes = false
    ) {
        $startTime = microtime(true);
        $election = Election::create([
            'population_id' => $population->id,
            'type' => $type
        ]);

        $votes = array();
        $electionStats = new \stdClass();
        $electionStats->type = "d2";
        $asDelegate = 0;
        $asFollower = 0;
        $asIndependent = 0;

        $weightedDelegatesIDs = array();

        $lastInsertedWeight = 0;
        $followersDistribution = array();

        $minValue = 1;
        $maxValue = 100;
        $correctChoices = 0;
        $incorrectChoices = 0;

        $noOfVotes = $population->voters->count();
        $electionStats->no_of_votes = $noOfVotes;

        $followersVotes = [];

        $votingWeightA = 0;
        $votingWeightB = 0;

        foreach ($population->voters as $voter) {
            $voterWeight = $voter->reputation > 0 ? $voter->reputation : 1;
            if ($voter->group == "A") {
                $votingWeightA += $voterWeight;
            } else {
                $votingWeightB += $voterWeight;
            }
            $tresholdFollowing = $voter->following;
            $tresholdIndependent = $tresholdFollowing + $voter->confidence;
            $tresholdLeadership = $tresholdIndependent + $voter->leadership;

            $randomBehaviour = random_int(0, $tresholdLeadership);

            $randomExpertise = random_int($minValue, $maxValue);
            $voteDirect = $randomExpertise <= $voter->expertise;
            $voteDirect ? $correctChoices++ : $incorrectChoices++;

            $newVote = [
                'election_id' => $election->id,
                'voter_id' => $voter->id,
                'vote_direct' => $voteDirect,
                'vote_delegation' => null,
                'vote_weight' => 1,
                'vote_final' => $voteDirect
            ];

            if ($randomBehaviour <= $tresholdFollowing) {
                $newVote['voter_mark'] = 'f';
                $asFollower++;
                $followersVotes[$voter->id] = $newVote;
            } elseif (($randomBehaviour <= $tresholdIndependent)) {
                $newVote['voter_mark'] =  'i';
                $asIndependent++;
                $votes[$voter->id] = $newVote;
            } else {
                $newVote['voter_mark'] =  'd';
                $asDelegate++;
                $reputationWeight = $voter->reputation > 0 ? $voter->reputation : 1;    // minimum weight = 1
                $weightedDelegatesIDs[$voter->id] = $lastInsertedWeight + $reputationWeight;
                $lastInsertedWeight = $weightedDelegatesIDs[$voter->id];
                $followersDistribution[$voter->id] = 0;
                $votes[$voter->id] = $newVote;
            }
        }

        $electionStats->initial_correct = $correctChoices;
        $electionStats->initial_incorrect = $incorrectChoices;

        $prepareTime = microtime(true);

        DelegationOneVote::insert($votes);

        $delegatesSavedVotes = array();

        // replace own expertise test if follower and there are delegates
        if ($asDelegate > 0) {

            $savedVotes = DelegationOneVote::where('election_id', '=', $election->id)
                ->where('voter_mark', '=', 'd')
                ->get();

            foreach ($savedVotes as $savedVote) {
                $delegatesSavedVotes[$savedVote->voter_id] = $savedVote;
            }

            foreach ($followersVotes as $key => $item) {
                if($item['voter_mark'] == 'f' ) {
                    // choose delegate
                    $randomDelegation = random_int(1, $lastInsertedWeight);
                    $delegateID = $this->findDelegateID($weightedDelegatesIDs, $randomDelegation);

                    $followersVotes[$key]['vote_delegation'] = $delegatesSavedVotes[$delegateID]->id;//$delegateID;
                    $followersDistribution[$delegateID]++;
                    // revert expertise choice
                    $item['vote_direct'] ? $correctChoices-- : $incorrectChoices--;
                    //$followersVotes[$key]['vote_direct'] = null; // keep track of own expertise choice
                    $followersVotes[$key]['vote_weight'] = 0;
                    // add delegate choice
                    $delegatesSavedVotes[$delegateID]->vote_weight++;
                    $delegatesSavedVotes[$delegateID]->vote_direct ? $correctChoices++ : $incorrectChoices++;
                    $followersVotes[$key]['vote_final'] = $delegatesSavedVotes[$delegateID]->vote_final;
                }
            }

        }

        // Adjust attributes of all voters (skip leadership/following adjustments)
        // if in learning stage
        if ($population->stage === 'l') {
            foreach ($population->voters as $voter) {
                $previousReputation = $voter->reputation;
                $voterID = $voter->id;
                if (isset($delegatesSavedVotes[$voterID])) {
                    // is delegate
                    $noOfFollowers = $followersDistribution[$delegateID];
                    if ($noOfFollowers > 0) {
                        // save weight adjustments for delegate's vote
                        $delegatesSavedVotes[$voterID]->save();
                        // adjust voter reputation
                        if ($delegatesSavedVotes[$voterID]->vote_final > 0) {
                            $voter->reputation += (2 * $noOfFollowers);
                            if($modifyAttributes && $voter->leadership < 100)
                                $voter->leadership++;
                        } else {
                            $voter->reputation -= (2 * $noOfFollowers);
                            if($modifyAttributes && $voter->leadership > 1)
                                $voter->leadership--;
                        }
                    } else {
                        if($modifyAttributes && $voter->leadership > 1)
                            $voter->leadership--;
                    }
                }  elseif ($modifyAttributes && isset($followersVotes[$voterID])) {
                    // is follower
                    if ($followersVotes[$voterID]['vote_final']) {
                        if (!$followersVotes[$voterID]['vote_direct']) {
                            if($voter->following < 100)
                                $voter->following++;
                        }
                    } elseif ($followersVotes[$voterID]['vote_direct']) {
                        if($voter->following > 1)
                            $voter->following--;
                    }
                }

                // balance voter reputation over time between elections
                if ($voter->reputation < 0) {
                    $voter->reputation++;
                } elseif ($voter->reputation > 0) {
                    $voter->reputation--;
                }

                if ($previousReputation != $voter->reputation) {
                    $voter->save();
                }
            }
        }


        $election->total_correct = $correctChoices;
        $election->total_incorrect = $incorrectChoices;

        $electionStats->total_correct_choices = $correctChoices;
        $electionStats->total_incorrect_choices = $incorrectChoices;

        if ($noOfVotes > 0) {
            $electionStats->percent_initial_correct_choices = 100 * $electionStats->initial_correct / $noOfVotes;
            $electionStats->percent_correct_choices = 100 * $election->total_correct / $noOfVotes;
        } else {
            $electionStats->percent_initial_correct_choices = null;
            $electionStats->percent_correct_choices = null;
        }

        $votesTime = microtime(true);

        $election->save();

        $electionExtension = ExtensionDelegationElection::create([
            'election_id'       => $election->id,
            'initial_correct'   => $electionStats->initial_correct,
            'initial_incorrect' => $electionStats->initial_incorrect,
            'as_delegate'       => $asDelegate,
            'as_follower'       => $asFollower,
            'as_independent'    => $asIndependent,
            'weight_a'          => $votingWeightA,
            'weight_b'          => $votingWeightB
        ]);

        DelegationOneVote::insert($followersVotes);

        $databaseTime = microtime(true);

        $electionStats->votes_time = round($votesTime - $startTime, 5);
        $electionStats->votes_db_time = round($databaseTime - $votesTime, 5);

        $electionStats->as_delegate = $asDelegate;
        $electionStats->as_follower = $asFollower;
        $electionStats->as_independent = $asIndependent;

        $electionStats->delegates = array_keys($weightedDelegatesIDs);
        $electionStats->cumulative_delegates_reputation = $weightedDelegatesIDs;
        $electionStats->followers_distribution = $followersDistribution;
        $electionStats->data = array_values($votes);

        return $electionStats;
    }



    private function findDelegateID(array $weightedDelegatesIDs, int $randomDelegation) {
        foreach ($weightedDelegatesIDs as $id => $cumulativeReputation) {
            if ($randomDelegation <= $cumulativeReputation) {
                return $id;
            }
        }
        return null;
    }

    public function getVoterStats(int $template, int $population, $voter) {
        $data = Voter::where('id', '=', $voter)
            ->with('delegationOneVotes.parentDelegationOneVote')
            ->firstOrFail()
            ->makeHidden('delegationOneVotes')
        ;

        return response()->json($data,200);
    }

    public function getVotersStats(int $template, int $population) {

        $population = Population::where('id', '=', $population)
            ->with('voters.delegationOneVotes')
            ->firstOrFail();

        $population->voters->makeHidden('delegationOneVotes');

        return response()->json($population->voters,200);
    }

    public function getElectionsTimeline (int $template, Population $population, Request $request) {
        $data = new \stdClass();

        try {
            $attributes = $request->validate([
                'type' => 'required|string|in:m,d1,d2,d3',
                'moving_average' => 'nullable|integer'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Unknown election type'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $movingAverage = isset($attributes['moving_average']) ? $attributes['moving_average'] : 0;

        $data->elections_type = $attributes['type'];
        $data->no_of_voters = $population->noOfVoters;

        if ($data->no_of_voters < 1) {
            return response()->json(['error' => 'No voters in population'], Response::HTTP_NOT_FOUND);
        }

        $elections = Election::where('population_id', '=', $population->id)
            ->where('type', '=', $attributes['type'])
            ->select(
                DB::raw(sprintf('100 * total_correct / %d AS percent', $data->no_of_voters))
            )
            ->pluck('percent');

        $data->no_of_elections = $elections->count();
        $data->moving_average = $movingAverage;

        if($movingAverage > 0) {
            $flattenData = [];
            if ($data->no_of_elections >= $movingAverage) {
                $asArray = $elections->toArray();
                for ($i = $movingAverage - 1; $i < $data->no_of_elections; $i++) {
                    $sumOfValues = 0;
                    for ($j = 0; $j < $movingAverage; $j++) {
                        $sumOfValues += $asArray[$i - $j];
                    }
                    $flattenData[] = $sumOfValues / $movingAverage;
                }
            }
            $data->elections = $flattenData;
        } else {
            $data->elections = $elections;
        }

        return response()->json($data, Response::HTTP_OK);
    }

    public function getWeightsTimeline (int $template, Population $population, Request $request) {
        $data = new \stdClass();

        try {
            $attributes = $request->validate([
                'type' => 'required|string|in:d2,d3',
                'moving_average' => 'nullable|integer'
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'Unknown election type'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $movingAverage = isset($attributes['moving_average']) ? $attributes['moving_average'] : 0;

        $data->elections_type = $attributes['type'];
        $data->no_of_voters = $population->noOfVoters;

        if ($data->no_of_voters < 1) {
            return response()->json(['error' => 'No voters in population'], Response::HTTP_NOT_FOUND);
        }

        $countA = $population->voters()->where('group', '=', 'A')->count();
        $countB = $population->voters()->where('group', '=', 'B')->count();

        $elections = Election::where('population_id', '=', $population->id)
            ->where('type', '=', $attributes['type'])
            ->with('extension')->get();

        $data->no_of_elections = $elections->count();
        $data->moving_average = $movingAverage;

        $weightsTimeline = array();
        foreach ($elections as $election) {
            $newWeight = new \stdClass();
            if ($countA > 0) {

                $newWeight->weight_a = $election->extension->weight_a;
                $newWeight->avg_weight_a = $election->extension->weight_a / $countA;
            } else {

                $newWeight->weight_a = 0;
                $newWeight->avg_weight_a = 0;
            }

            if ($countB > 0) {
                $newWeight->weight_b = $election->extension->weight_b;
                $newWeight->avg_weight_b = $election->extension->weight_b / $countB;
            } else {
                $newWeight->weight_b = 0;
                $newWeight->avg_weight_b = 0;
            }
            array_push($weightsTimeline, $newWeight);
        }
        $data->weigths = $weightsTimeline;
/*
        if($movingAverage > 0) {
            $flattenData = [];
            if ($data->no_of_elections >= $movingAverage) {
                $asArray = $elections->toArray();
                for ($i = $movingAverage - 1; $i < $data->no_of_elections; $i++) {
                    $sumOfValues = 0;
                    for ($j = 0; $j < $movingAverage; $j++) {
                        $sumOfValues += $asArray[$i - $j];
                    }
                    $flattenData[] = $sumOfValues / $movingAverage;
                }
            }
            $data->elections = $flattenData;
        } else {
            $data->elections = $elections;
        }*/

        return response()->json($data, Response::HTTP_OK);
    }


}
