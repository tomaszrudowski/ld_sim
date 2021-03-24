<template>
    <div class="p-2">
        <div class="row">
            <div class="col-md-2 col-lg-2">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">{{population_name}} Actions</div>
                        <div class="card-body">
                            <div>
                                Majority elections:<br>
                                <i class="text-muted text-sm-left">Based on own Expertise.</i><br>
                                <button :disabled="running_elections_lock" class="btn btn-sm btn-outline-info" @click.prevent="runElections('m', 1)">
                                    Run 1 election <i>(type m)</i>
                                </button>
                                <button :disabled="running_elections_lock"class="btn btn-sm btn-outline-info" @click.prevent="runElections('m', 5)">
                                    Run 5 elections <i>(type m)</i>
                                </button>
                                <button :disabled="running_elections_lock"class="btn btn-sm btn-outline-info" @click.prevent="runElections('m', 10)">
                                    Run 10 elections <i>(type m)</i>
                                </button>
                            </div>
                            <div>
                                Majority elections distribution:<br>
                                <button :disabled="running_elections_lock" class="btn btn-sm btn-outline-info" @click.prevent="fetchMajorityElectionsDistribution">Fetch majority elections distribution</button>
                            </div>
                            <div>
                                Delegation elections <i>(type d1)</i> :<br>
                                <i class="text-muted text-sm-left">Three options, being: delegate/follower/independent (chance based on Leadership and Following), delegates and independents use own Expertise (single delegation level).</i><br>
                                <button :disabled="running_elections_lock" class="btn btn-sm btn-outline-info" @click.prevent="runElections('d1', 1)">
                                    Run 1 election <i>(type d1)</i>
                                </button>
                                <button :disabled="running_elections_lock" class="btn btn-sm btn-outline-info" @click.prevent="runElections('d1', 5)">
                                    Run 5 elections <i>(type d1)</i>
                                </button>
                                <button :disabled="running_elections_lock" class="btn btn-sm btn-outline-info" @click.prevent="runElections('d1', 10)">
                                    Run 10 elections <i>(type d1)</i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-10 col-lg-10">
                <div class="row">
                    <div class="alert alert-info col-md-12 col-lg-12" v-if="feedback">
                        INFO: {{feedback}}
                        <button class="float-right btn btn-sm btn-outline-info" @click.prevent="resetFeedback">x</button>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">Voters' attributes charts</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button class="btn btn-sm btn-outline-info" @click.prevent="fetchPopulationDetails">Fetch Voters Data</button>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-info">Auto update voters' details after election</label>
                                        <input type="checkbox" v-model="auto_fetch_voters">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="text-info">Show voters graph</label>
                                        <input type="checkbox" v-model="show_voters_graph">
                                    </div>
                                </div>
                                <div v-if="voters_fetched">
                                    <div v-if="show_voters_graph">
                                        <line-chart :chart-data="voters_chart_data" :options="voters_chart_options" :styles="h_300_chart_styles"></line-chart>
                                    </div>
                                    <i v-else>select show graph</i>
                                </div>
                                <div v-else>
                                    <i>N/A</i>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="population_stats" class="row mt-1">
                    <div class="col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">Elections timeline</div>
                            <div class="card-body">
                                <div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <v-select :options="election_timeline_selector"
                                                      id="election_timeline_selector"
                                                      v-model="current_election_timeline_key"
                                                      placeholder="Choose election type"
                                                      label="text">
                                            </v-select>
                                            <br>
                                            <button :disabled="!current_election_timeline_key"
                                                    v-on:click.prevent="fetchElectionsTimeline"
                                                    v-bind:class="{'btn-primary' : current_election_timeline_key }"
                                                    class="btn-xs">
                                                <i v-if="current_election_timeline_key">Fetch {{current_election_timeline_key.text}} timeline</i>
                                                <i v-else>Select election type</i>
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-info">Auto update timeline after election</label>
                                            <input type="checkbox" v-model="auto_fetch_elections_timeline">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-info">Show timeline graph</label>
                                            <input type="checkbox" v-model="show_timeline_graph">
                                        </div>
                                    </div>
                                    <hr>
                                    <div v-if="elections_timeline" >
                                        Election type: <i>{{elections_timeline.elections_type}}</i>,
                                        Number of elections: <i>{{elections_timeline.no_of_elections}}</i>,
                                        Number of voters: <i>{{elections_timeline.no_of_voters}}</i>
                                        <div v-if="show_timeline_graph">
                                            <line-chart :chart-data="election_timeline_chart_data"
                                                        :options="{
                                                    maintainAspectRatio: false,
                                                    scales: {
                                                        yAxes: [{id: 'left-y-axis',type: 'linear',position: 'left',ticks: {min: 0, max:100}}]
                                                    }
                                               }"
                                                        :styles="h_300_chart_styles"
                                            ></line-chart>
                                        </div>
                                        <div v-else>
                                            <i>select show graph</i>
                                        </div>
                                    </div>
                                    <div v-else><i>N/A</i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6 col-lg-6 col-sm-6">

                        <div v-if="population_stats" class="row mt-1">
                            <div class="col-md-6 col-lg-6">
                                <div class="card">
                                    <div class="card-header">Election stats</div>
                                    <div v-if="population_stats.elections_stats" class="card-body">
                                        <bar-chart :chart-data="population_election_stats_chart_data" :options="{
                                            maintainAspectRatio: true,
                                            scales: {
                                                yAxes: [{id: 'left-y-axis',type: 'linear',position: 'left',ticks: {min: 0,max: 100}}]
                                                }
                                        }"></bar-chart>
                                    </div>
                                    <div v-else class="card-body"><i>N/A</i></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="card">
                                    <div class="card-header">Voters stats</div>
                                    <div v-if="population_stats.voters_stats" class="card-body">
                                        <bar-chart :chart-data="population_voters_stats_chart_data" :options="{
                                            maintainAspectRatio: true,
                                            scales: {
                                                yAxes: [{id: 'left-y-axis',type: 'linear',position: 'left',ticks: {min: 0,max: 100}}]
                                                }
                                        }"
                                        ></bar-chart>
                                    </div>
                                    <div v-else class="card-body"><i>N/A</i></div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-md-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">Majority elections distribution</div>
                                    <div class="card-body">
                                        <div>
                                            <label class="text-info">Auto update ME distribution after elections</label>
                                            <input type="checkbox" v-model="auto_fetch_distribution">
                                        </div>
                                        <div v-if="me_distribution_metadata">
                                            <div v-if="majority_elections_distribution">
                                                <div class="row">
                                                    <bar-chart :chart-data="me_chart_data"  :options="{maintainAspectRatio: true}" class="col-md-6"></bar-chart>
                                                    <bar-chart :chart-data="me_chart_data_r_10"  :options="{maintainAspectRatio: true}"  class="col-md-6"></bar-chart>
                                                </div>
                                                <div>
                                                    Number of elections: <i>{{me_distribution_metadata.number_of_elections}}</i>
                                                    <br>
                                                    Total time: <i>{{me_distribution_metadata.total_time}}</i>
                                                    <br>
                                                    Distribution of correct choices percentage in {{me_distribution_metadata.number_of_elections}} elections:
                                                    <br>
                                                    Charts: Majority Elections correct answers distribution (grouped by 1 and 10 percent)
                                                    <br>
                                                    (by 1): {{majority_elections_distribution}}
                                                    <br>
                                                    (by 10): {{majority_elections_distribution_r_10}}
                                                </div>
                                            </div>
                                        </div>
                                        <div v-else><i>N/A (fetch distribution first)</i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-6">
                        <div v-if="population_stats" class="row mt-1">
                            <div class="col-md-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">Last elections status</div>
                                    <div class="card-body">
                                        <div v-if="last_elections_data">
                                            <div>
                                                Election type: <i>{{last_elections_data.elections_type}}</i>
                                                Number of elections: <i>{{last_elections_data.number_of_elections}}</i>
                                                <br>
                                                Total time: <i>{{last_elections_data.total_time}}</i>
                                            </div>
                                            <bar-chart :chart-data="last_elections_chart_data"
                                                       :options="{
                                                    maintainAspectRatio: false,
                                                    scales: {
                                                        yAxes: [{id: 'left-y-axis',type: 'linear',position: 'left',ticks: {min: 0}}]
                                                    }
                                               }"
                                                       :styles="{height: 200}"
                                            ></bar-chart>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import LineChart from "./charts/line-chart";
    import BarChart from "./charts/bar-chart";
    import vSelect from "vue-select";
    import 'vue-select/dist/vue-select.css';

    export default {
        name: "population-show",
        components: {LineChart, BarChart, vSelect},
        data() {
            return {
                current_election_timeline_key: null,
                election_timeline_selector : [
                    {
                        value: 'm',
                        text: 'Majority (m)'
                    },
                    {
                        value: 'd1',
                        text: 'Delegation version 1 (d1)'
                    }
                ],
                elections_timeline: null,
                auto_fetch_elections_timeline: false,
                show_timeline_graph: true,
                running_elections_lock: false,
                auto_fetch_voters: false,
                show_voters_graph: true,
                auto_fetch_distribution: false,
                feedback : null,
                population_id: route().params.population_id,
                population_stats: null,
                population_name: null,
                voters_fetched: false,
                voters: [],
                last_elections_data: null,
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
                            position: 'right'/*,
                            ticks: {
                                min: -50,
                                max: 50
                            }*/
                        }]
                    }
                },
                h_300_chart_styles: {
                    height: '300px',
                    width: '100%',
                    position: 'relative'
                }
            }
        },
        mounted() {
            if (this.auto_fetch_voters) {
                this.fetchPopulationDetails();
            }
            this.fetchPopulationStats()
        },
        computed: {
            population_election_stats_chart_data() {
                console.log('computing population_election_stats');
                let colors = [
                    '#205702',
                    '#b7b30e',
                    '#97510a',
                    '#b22430',
                    '#45026a'
                ];
                let color_idx = 0;
                let labels = [
                    'correct (avg #)', 'incorrect (avg #)', 'correct (%)'
                ];
                let datasets = [];
                let no_of_voters = this.population_stats.voters_stats ? this.population_stats.voters_stats.no_of_voters : 0;
                this.population_stats.elections_stats.forEach(election_type => {
                    let election_type_set = [];
                    election_type_set.push(election_type.no_of_correct_average);
                    election_type_set.push(election_type.no_of_incorrect_average);
                    election_type_set.push(election_type.percent_correct);
                    datasets.push({
                        label: election_type.type + '(#:' + election_type.count + ')',
                        backgroundColor: colors[color_idx],
                        data: election_type_set,
                        yAxisID: 'left-y-axis'
                    });
                    color_idx = color_idx < 4 ? color_idx + 1 : 0;
                });
                return {
                    labels: labels,
                    datasets: datasets
                }
            },
            population_voters_stats_chart_data() {
                console.log('computing population_voters_stats_chart_data');
                let colors = [
                    '#205702',
                    '#b7b30e',
                    '#97510a',
                    '#b22430',
                    '#45026a'
                ];
                let color_idx = 0;
                let labels = [
                    'Expertise (avg)', 'Confidence (avg)', 'Following (avg)', 'Leadership (avg)'
                ];
                let groups = [];
                this.population_stats.voters_stats.groups.forEach(voters_group => {
                    let voters_group_set = [];
                    voters_group_set.push(voters_group.expertise_average);
                    voters_group_set.push(voters_group.confidence_average);
                    voters_group_set.push(voters_group.following_average);
                    voters_group_set.push(voters_group.leadership_average);
                    groups.push({
                        label: 'group:' + voters_group.name + ' (#' + voters_group.no_of_voters + ')',
                        backgroundColor: colors[color_idx],
                        data: voters_group_set,
                        yAxisID: 'left-y-axis'
                    });
                    color_idx = color_idx < 4 ? color_idx + 1 : 0;
                });
                return {
                    labels: labels,
                    datasets: groups
                }
            },
            voters_chart_data() {
                console.log('computing voters chart data');
                let labels = [];
                let expertise = [];
                let confidence = [];
                let following = [];
                let leadership = [];
                let m_percent_correct = [];
                let d1_percent_correct = [];
                let as_independent = [];
                let as_follower = [];
                let as_delegate = [];
                let diff = [];
                this.voters.forEach((value, idx) => {
                    labels.push(idx);
                    expertise.push(value.expertise);
                    confidence.push(value.confidence);
                    following.push(value.following);
                    leadership.push(value.leadership);
                    m_percent_correct.push(value.majority_votes_stats.percent_correct);
                    //diff.push(value.majority_votes_stats.percent_correct - value.expertise);
                    d1_percent_correct.push(value.majority_votes_stats.percent_correct);
                    as_independent.push(value.delegation_one_votes_stats.as_independent);
                    as_follower.push(value.delegation_one_votes_stats.as_follower);
                    as_delegate.push(value.delegation_one_votes_stats.as_delegate);
                });
                return {
                    labels: labels,
                    datasets: [
                        {
                            label: 'expertise',
                            borderColor: '#039876',
                            fill: false,
                            data: expertise,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'confidence',
                            borderColor: '#479c38',
                            fill: false,
                            data: confidence,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'following',
                            borderColor: '#029689',
                            fill: false,
                            data: following,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'leadership',
                            borderColor: '#01439b',
                            fill: false,
                            data: leadership,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'percent correct (M)',
                            borderColor: '#9b4e44',
                            fill: false,
                            data: m_percent_correct,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'percent correct (D1)',
                            borderColor: '#966c44',
                            fill: false,
                            data: d1_percent_correct,
                            yAxisID: 'left-y-axis'
                        },/*
                        {
                            label: 'diff cor-exp (M)',
                            borderColor: '#51121d',
                            fill: false,
                            data: diff,
                            yAxisID: 'right-y-axis'
                        },*/
                        {
                            label: 'as independent (D1)',
                            borderColor: '#ebf04b',
                            fill: false,
                            data: as_independent,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'as follower (D1)',
                            borderColor: '#ffe136',
                            fill: false,
                            data: as_follower,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'as delegate (D1)',
                            borderColor: '#b7b30e',
                            fill: false,
                            data: as_delegate,
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
            },
            last_elections_chart_data() {
                console.log('computing last_elections_chart_data');
                let colors = [
                    '#205702',
                    '#b7b30e',
                    '#97510a',
                    '#b22430',
                    '#45026a'
                ];
                let color_idx = 0;
                let labels;
                let datasets = [];

                let election_type = this.last_elections_data.elections_type;
                switch (election_type) {
                    case 'd1':
                        labels = [
                            'Correct (#)', 'Incorrect (#)', 'Correct (%)',
                            'Delegates (#)', 'Followers (#)', 'Independent (#)'
                        ];
                        break;
                    default:
                        labels = [
                            'Correct (#)', 'Incorrect (#)', 'Correct (%)'
                        ];
                }
                this.last_elections_data.elections.forEach((item, idx) => {
                    let election_set = [];
                    election_set.push(item.total_correct_choices);
                    election_set.push(item.total_incorrect_choices);
                    election_set.push(item.percent_correct_choices);
                    switch (election_type) {
                        case 'd1':
                            election_set.push(item.as_delegate);
                            election_set.push(item.as_follower);
                            election_set.push(item.as_independent);
                            break;
                        default:
                            // basic chart dataset
                    }
                    datasets.push({
                        label: 'election ' + (idx + 1),
                        backgroundColor: colors[color_idx],
                        data: election_set,
                        yAxisID: 'left-y-axis'
                    });
                    color_idx = color_idx < 4 ? color_idx + 1 : 0;
                });
                return {
                    labels: labels,
                    datasets: datasets
                };
            },
            election_timeline_chart_data() {
                console.log('computing election trend chart data');
                let labels = [];
                let percent_correct = [];
                let expertise = [];
                let avg_expertise = this.population_stats.voters_stats.expertise_average;
                this.elections_timeline.elections.forEach((value,idx) => {
                   labels.push(idx);
                   percent_correct.push(value);
                   expertise.push(avg_expertise);
                });
                return {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Timeline for percent correct in type-' + this.elections_timeline.elections_type + ' elections',
                            borderColor: '#479c38',
                            fill: false,
                            data: percent_correct,
                            yAxisID: 'left-y-axis'
                        },

                        {
                            label: 'Average population Expertise',
                            borderColor: '#666666',
                            fill: false,
                            data: expertise,
                            yAxisID: 'left-y-axis'
                        }
                    ]
                }
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
                    if (this.auto_fetch_voters) {
                        this.fetchPopulationDetails();
                    }
                    if (this.auto_fetch_distribution) {
                        this.fetchMajorityElectionsDistribution();
                    }
                    if (this.auto_fetch_elections_timeline) {
                        this.fetchElectionsTimeline();
                    }
                }).catch((err) => {
                    this.feedback = 'population stats fetching error';
                });
            },
            runElections(type, multi) {
                if (this.running_elections_lock) {
                    this.feedback = 'already running elections, please wait...';
                } else {
                    this.running_elections_lock = true;
                    this.feedback = 'running ' + type + '-type elections: (' + multi + ')...';
                    this.last_elections_data = null;
                    axios.post(route('internal.api.elections.run', this.population_id), {type: type,number:multi}).then((response) => {
                        this.feedback = 'voting done, fetching updated population stats..';
                        this.fetchPopulationStats();
                        console.log(response.data);
                        this.last_elections_data = response.data;
                        this.running_elections_lock = false;
                    }).catch((err) => {
                        this.feedback = 'election error';
                        this.running_elections_lock = false;
                    });
                }
            },
            fetchMajorityElectionsDistribution() {
                this.feedback = 'fetching majority distribution...';
                this.me_distribution_metadata = null;
                axios.get(route('internal.api.majority.distribution.get', this.population_id)).then((response) => {
                    this.feedback = 'majority distribution fetched';
                    this.majority_elections_distribution = response.data.distribution;
                    this.majority_elections_distribution_r_10 = response.data.distribution_r_10;
                    this.me_distribution_metadata = response.data.metadata;
                }).catch((err) => {
                    this.feedback = 'majority distribution fetching error';
                });
            },
            fetchElectionsTimeline() {
                axios.get(route('internal.api.population.get.elections.timeline', this.population_id), {
                    params: {'type': this.current_election_timeline_key.value}
                }).then((response) => {
                    this.elections_timeline = response.data;
                    this.feedback = 'election timeline fetched';
                }).catch((err) => {
                    this.feedback = 'election timeline fetching error';
                });
            }
        }
    }
</script>

<style scoped>

</style>
