<template>
    <div class="p-2">
        <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-6">
                <div class="alert alert-info" v-if="feedback">
                    INFO: {{feedback}}
                    <button class="float-right btn btn-sm btn-outline-info" @click.prevent="resetFeedback">x</button>
                </div>
                <h4>{{population_name}}</h4>
                <div v-if="population_stats">
                    <div class="card mt-1">
                        <div class="card-header">Actions</div>
                        <div class="card-body">
                            <div>
                                Majority elections:<br>
                                <button class="btn btn-sm btn-outline-info" @click.prevent="runMajorityElection(1)">Run majority election</button>
                                <button class="btn btn-sm btn-outline-info" @click.prevent="runMajorityElection(5)">Run 5 majority elections</button>
                                <button class="btn btn-sm btn-outline-info" @click.prevent="runMajorityElection(10)">Run 10 majority elections</button>
                            </div>
                            <div>
                                Majority elections distribution:<br>
                                <button class="btn btn-sm btn-outline-info" @click.prevent="fetchMajorityElectionsDistribution">Fetch majority elections distribution</button>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-1">
                        <div class="card-header">Election stats</div>
                        <div v-if="population_stats.elections_stats" class="card-body">
                            <ul>
                                <li>
                                    Number of Majority Elections (ME): <strong>{{population_stats.elections_stats.m}}</strong>
                                </li>
                                <li>
                                    Avg number of correct choices(ME): {{population_stats.elections_stats.m_no_of_correct_average}}
                                </li>
                                <li>
                                    Avg number of incorrect choices(ME): {{population_stats.elections_stats.m_no_of_incorrect_average}}
                                </li>
                                <li>
                                    Percent of correct choices(ME): {{population_stats.elections_stats.m_percent_correct}}
                                </li>
                            </ul>
                        </div>
                        <div v-else class="card-body"><i>N/A</i></div>
                    </div>
                    <div class="card mt-1">
                        <div class="card-header">Voters stats</div>
                        <div class="card-body">
                            <div v-if="population_stats.voters_stats" >
                                <ul>
                                    <li>
                                        Number of Voters: {{population_stats.voters_stats.no_of_voters}}
                                    </li>
                                    <li>
                                        Avg Expertise: {{population_stats.voters_stats.expertise_average}}
                                    </li>
                                    <li>
                                        Avg Confidence: {{population_stats.voters_stats.confidence_average}}
                                    </li>
                                    <li>
                                        Avg Following: {{population_stats.voters_stats.following_average}}
                                    </li>
                                    <li>
                                        Avg Leadership: {{population_stats.voters_stats.leadership_average}}
                                    </li>
                                </ul>
                                <hr>
                                <div class="row">
                                    <div v-for="group in population_stats.voters_stats.groups"
                                         class="col-md-6">
                                        group: {{group.name}} <br>
                                        number of Voters {{group.no_of_voters}} <br>
                                        avg Expertise {{group.expertise_average}} <br>
                                        avg Confidence {{group.confidence_average}} <br>
                                        avg Following {{group.following_average}} <br>
                                        avg Leadership {{group.leadership_average}} <br>
                                    </div>
                                </div>
                            </div>
                            <div v-else><i>N/A</i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-6 col-sm-6">
                <div class="card mt-1">
                    <div class="card-header">Last elections status</div>
                    <div class="card-body">
                        <div v-if="last_elections_data">
                            <div>
                                Number of elections: <i>{{last_elections_data.number_of_elections}}</i>
                                <br>
                                Total time: <i>{{last_elections_data.total_time}}</i>
                            </div>
                            <table v-if="last_elections_data.elections" class="table table-sm table-responsive-sm">
                                <thead>
                                <tr>
                                    <th>Correct</th>
                                    <th>Incorrect</th>
                                    <th>Percent correct</th>
                                    <th>Voting time</th>
                                    <th>DB time</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="single_election in last_elections_data.elections">
                                    <td>{{single_election.total_correct_choices}}</td>
                                    <td>{{single_election.total_incorrect_choices}}</td>
                                    <td>{{single_election.percent_correct_choices}}</td>
                                    <th>{{single_election.votes_time}}</th>
                                    <td>{{single_election.votes_db_time}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mt-1">
                    <div class="card-header">Majority elections distribution</div>
                    <div class="card-body">
                        <div v-if="me_distribution_metadata">
                            <div>
                                Number of elections: <i>{{me_distribution_metadata.number_of_elections}}</i>
                                <br>
                                Total time: <i>{{me_distribution_metadata.total_time}}</i>
                            </div>
                            <div>
                                Distribution rounded down to int:<br>
                                {{majority_elections_distribution}}
                            </div>
                            <div>
                                Elections ({{me_distribution_metadata.number_of_elections}}) series - sorted percent correct choices:<br>
                                <i class="text-sm-left">{{majority_elections_raw}}</i>
                            </div>
                        </div>
                        <div v-else><i>N/A (fetch distribution)</i></div>
                    </div>
                </div>
                <div class="card mt-1">
                    <div class="card-header">Voters' details</div>
                    <div class="card-body">
                        <div>
                            <label class="text-info">Auto fetch voters' details after election</label>
                            <input type="checkbox" v-model="select_voters">
                            <br>
                            <i class="text-muted">Data in table below will not update automatically if not selected.</i>
                            <hr>
                            <button class="btn btn-sm btn-outline-info" @click.prevent="fetchPopulationDetails">Fetch Voters View</button>
                        </div>
                        <div v-if="voters_fetched">
                            <table class="table table-sm table-responsive-sm">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Expertise<br><i class="text-muted">(1-100)</i></th>
                                    <th>Confidence<br><i class="text-muted">(1-100)</i></th>
                                    <th>Following<br><i class="text-muted">(1-100)</i></th>
                                    <th>Leadership<br><i class="text-muted">(1-100)</i></th>
                                    <th>Group</th>
                                    <th>Correct<br><i class="text-muted">(majority)</i></th>
                                    <th>Incorrect<br><i class="text-muted">(majority)</i></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="voter in voters">
                                    <th>{{voter.id}}</th>
                                    <td>{{voter.expertise}}</td>
                                    <td>{{voter.confidence}}</td>
                                    <td>{{voter.following}}</td>
                                    <td>{{voter.leadership}}</td>
                                    <td>{{voter.group}}</td>
                                    <td>{{voter.majority_votes_stats.correct}}</td>
                                    <td>{{voter.majority_votes_stats.incorrect}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else>
                            <i>N/A</i>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "population-show",
        data() {
            return {
                select_voters: false,
                feedback : null,
                population_id: route().params.population_id,
                population_stats: null,
                population_name: null,
                voters_fetched: false,
                voters: [],
                last_elections_data: [],
                election_feedback: null,
                majority_elections_distribution: null,
                me_distribution_metadata: null,
                majority_elections_raw: null
            }
        },
        mounted() {
            if (this.select_voters) {
                this.fetchPopulationDetails();
            }
            this.fetchPopulationStats()
        },
        methods: {
            resetFeedback() {
                this.feedback = null;
            },
            fetchPopulationDetails() {
                this.feedback = 'fetching voters data..';
                this.voters = null;
                axios.get(route('internal.api.population.get.voters', this.population_id)).then((response) => {
                    this.feedback = 'voters data fetched';
                    this.voters = response.data;
                    this.voters_fetched = true;
                }).catch((err) => {
                    this.feedback = 'voters data fetching error';
                });
            },
            fetchPopulationStats() {
                this.feedback = 'fetching population stats..';
                axios.get(route('internal.api.population.get', this.population_id)).then((response) => {
                    this.feedback = 'population stats fetched';
                    this.population_stats = response.data;
                    this.population_name = response.data.name;
                    if (this.select_voters) {
                        this.fetchPopulationDetails();
                    }
                }).catch((err) => {
                    this.feedback = 'population stats fetching error';
                });
            },
            runMajorityElection(multi) {
                this.feedback = 'running majority elections: (' + multi + ')...';
                this.last_elections_data = [];
                axios.post(route('internal.api.population.majority.run', this.population_id), {number:multi}).then((response) => {
                    this.feedback = 'majority voting done, fetching updated population stats..';
                    this.fetchPopulationStats();
                    this.last_elections_data = response.data;
                }).catch((err) => {
                    this.feedback = 'majority election error';
                });
            },
            fetchMajorityElectionsDistribution() {this.feedback = 'fetching majority distribution...';
                this.me_distribution_metadata = null;
                axios.get(route('internal.api.majority.distribution.get', this.population_id)).then((response) => {
                    this.feedback = 'majority distribution fetched';
                    this.majority_elections_distribution = response.data.distribution;
                    this.majority_elections_raw = response.data.sorted_raw;
                    this.me_distribution_metadata = response.data.metadata;
                }).catch((err) => {
                    this.feedback = 'majority distribution fetching error';
                });
            }
        }
    }
</script>

<style scoped>

</style>
