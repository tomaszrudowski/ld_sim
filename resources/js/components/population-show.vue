<template>
    <div class="p-2">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-1">
                    <div class="card-header">Voters' attributes charts</div>
                    <div class="card-body">
                        <div>
                            <label class="text-info">Auto fetch voters' details after election</label>
                            <input type="checkbox" v-model="select_voters">
                            <br>
                            <label class="text-info">Show voters' details in a table</label>
                            <input type="checkbox" v-model="show_voters_table">
                            <br>
                            <button class="btn btn-sm btn-outline-info" @click.prevent="fetchPopulationDetails">Fetch Voters Data</button>
                        </div>
                        <div v-if="voters_fetched">
                            <div>
                                <line-chart :chart-data="voters_chart_data" :options="voters_chart_options" :styles="voters_chart_styles"></line-chart>
                            </div>
                            <div v-if="show_voters_table">
                                <table class="table table-sm table-responsive-sm">
                                    <thead>
                                    <tr>
                                        <th>Confidence<br><i class="text-muted">(1-100)</i></th>
                                        <th>Leadership<br><i class="text-muted">(1-100)</i></th>
                                        <th>Following<br><i class="text-muted">(1-100)</i></th>
                                        <th>Group</th>
                                        <th>Expertise<br><i class="text-muted">(1-100)</i></th>
                                        <th>Correct<br>(percent)<br><i class="text-muted">(majority)</i></th>
                                        <th>Correct<br><i class="text-muted">(majority)</i></th>
                                        <th>Incorrect<br><i class="text-muted">(majority)</i></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="voter in voters">
                                        <td>{{voter.confidence}}</td>
                                        <td>{{voter.leadership}}</td>
                                        <td>{{voter.following}}</td>
                                        <td>{{voter.group}}</td>
                                        <td>{{voter.expertise}}</td>
                                        <td>
                                            <span v-if="voter.majority_votes_stats.percent_correct">{{voter.majority_votes_stats.percent_correct}}</span>
                                            <span v-else>N/A</span>
                                        </td>
                                        <td>{{voter.majority_votes_stats.correct}}</td>
                                        <td>{{voter.majority_votes_stats.incorrect}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-else>
                            <i>N/A</i>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-6">
                <h4>{{population_name}}</h4>
                <div v-if="population_stats">
                    <div class="card mt-1">
                        <div class="card-header">Actions</div>
                        <div class="card-body">
                            <div class="alert alert-info" v-if="feedback">
                                INFO: {{feedback}}
                                <button class="float-right btn btn-sm btn-outline-info" @click.prevent="resetFeedback">x</button>
                            </div>
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
                        <div>
                            <label class="text-info">Auto fetch ME distribution after election</label>
                            <input type="checkbox" v-model="select_distribution">
                        </div>
                        <div v-if="me_distribution_metadata">
                            <div>
                                Number of elections: <i>{{me_distribution_metadata.number_of_elections}}</i>
                                <br>
                                Total time: <i>{{me_distribution_metadata.total_time}}</i>
                            </div>
                            <div v-if="majority_elections_distribution">
                                <div>
                                    Distribution of correct choices percentage in {{me_distribution_metadata.number_of_elections}} elections:
                                    <br>
                                    Charts: Majority Elections correct answers distribution (grouped by 1 and 10 percent)
                                    <br>
                                    (by 1): {{majority_elections_distribution}}
                                    <br>
                                    (by 10): {{majority_elections_distribution_r_10}}
                                </div>
                                <div class="row">
                                    <bar-chart :chart-data="me_chart_data" class="col-md-6"></bar-chart>
                                    <bar-chart :chart-data="me_chart_data_r_10" class="col-md-6"></bar-chart>
                                </div>
                            </div>
                        </div>
                        <div v-else><i>N/A (fetch distribution first)</i></div>
                    </div>
                </div>
                <div class="card mt-1">
                    <div class="card-header">Voters' details (table)</div>
                    <div class="card-body">

                    </div>
                </div>



            </div>
        </div>
    </div>
</template>

<script>
    import LineChart from "./charts/line-chart";
    import BarChart from "./charts/bar-chart";
    export default {
        name: "population-show",
        components: {LineChart, BarChart},
        data() {
            return {
                select_voters: false,
                show_voters_table: false,
                select_distribution: false,
                feedback : null,
                population_id: route().params.population_id,
                population_stats: null,
                population_name: null,
                voters_fetched: false,
                voters: [],
                last_elections_data: [],
                election_feedback: null,
                majority_elections_distribution: null,
                majority_elections_distribution_r_10: null,
                me_distribution_metadata: null,
                chart_data_fetched: false,
                voters_chart_options: {
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            id: 'left-y-axis',
                            type: 'linear',
                            position: 'left',
                            ticks: {
                                min: 0,
                                max: 100
                            }
                        }, {
                            id: 'right-y-axis',
                            type: 'linear',
                            position: 'right',
                            ticks: {
                                min: -50,
                                max: 50
                            }
                        }]
                    }
                },
                voters_chart_styles: {
                    height: '400px',
                    width: '100%',
                    position: 'relative'
                }
            }
        },
        mounted() {
            if (this.select_voters) {
                this.fetchPopulationDetails();
            }
            this.fetchPopulationStats()
        },
        computed: {
            voters_chart_data() {
                console.log('computing voters chart data');
                let labels = [];
                let expertise = [];
                let confidence = [];
                let following = [];
                let leadership = [];
                let percent_correct = [];
                let diff = [];
                this.voters.forEach((value, idx) => {
                    labels.push(idx);
                    expertise.push(value.expertise);
                    confidence.push(value.confidence);
                    following.push(value.following);
                    leadership.push(value.leadership);
                    percent_correct.push(value.majority_votes_stats.percent_correct);
                    diff.push(value.majority_votes_stats.percent_correct - value.expertise);
                });
                return {
                    labels: labels,
                    datasets: [
                        {
                            label: 'expertise',
                            borderColor: '#01f046',
                            fill: false,
                            data: expertise,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'confidence',
                            borderColor: '#f0da11',
                            fill: false,
                            data: confidence,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'following',
                            borderColor: '#ff7a00',
                            fill: false,
                            data: following,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'leadership',
                            borderColor: '#ff0022',
                            fill: false,
                            data: leadership,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'correct(%)',
                            borderColor: '#0073ff',
                            fill: false,
                            data: percent_correct,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'diff (cor-exp)',
                            borderColor: '#31410f',
                            fill: false,
                            data: diff,
                            yAxisID: 'right-y-axis'
                        }
                    ]
                }
            },
            me_chart_data() {
                console.log('computing me_chart_data');
                let labels = [];
                let dataset = [];
                this.majority_elections_distribution.forEach((value, idx) => {
                    labels.push(idx);
                    dataset.push(value);
                });
                return {
                    labels: labels,
                    datasets: [
                        {
                            label: 'ME by 1 percent',
                            backgroundColor: '#0073ff',
                            data: dataset
                        }
                    ]
                };
            },
            me_chart_data_r_10() {
                console.log('computing me_chart_data rounded to 10');
                let labels = ['0-9','11-19','20-29','30-39','40-49','50-59','60-69','70-79','80-89','90-100'];
                let dataset = [];
                let counter = 0;
                this.majority_elections_distribution_r_10.forEach((value) => {
                    dataset.push(value);
                });
                return {
                    labels: labels,
                    datasets: [
                        {
                            label: 'ME by 10 percent',
                            backgroundColor: '#0073ff',
                            data: dataset
                        }
                    ]
                };
            }
        },
        methods: {
            resetFeedback() {
                this.feedback = null;
            },
            fetchPopulationDetails() {
                this.feedback = 'fetching voters data..';
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
                    if (this.select_distribution) {
                        this.fetchMajorityElectionsDistribution();
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
                    this.majority_elections_distribution_r_10 = response.data.distribution_r_10;
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
