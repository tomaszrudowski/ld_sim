<template>
    <div class="p-2">
        <div class="row">
            <div class="col-md-3 col-lg-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">{{population_name}} Actions</div>
                        <div class="card-body" >
                            <div>
                                <a :href="getTemplateLink(population_stats.parent_id)">Back to template</a>
                            </div>
                            <hr>
                            <div v-if="stage_name">
                                <div v-if="population_stats.stage === 'l'">
                                    <h5>Learning stage</h5>
                                    <h5>{{election_name}}</h5>
                                    <div class="col-md-12 col-lg-12">
                                        <button v-if="population_stats.stage === 'l'" @click.prevent="switchToPerformanceStage()">
                                            Finish Learning stage
                                        </button>
                                    </div>
                                    <h5>Reputation modifiers:</h5>
                                    <div class="col-md-12 col-lg-12">
                                        <ul>
                                            <li class="text-info">Forgetting percent: {{population_stats.forgetting_percent}}</li>
                                            <li class="text-info">Follower multiplier: {{population_stats.follower_factor}}</li>
                                        </ul>
                                    </div>
                                    <h5>Elections</h5>
                                    <div class="col-md-12 col-lg-12">
                                        <label class="text-info">Number of elections: </label>
                                        <input type="number" min="1" max="100" step="0" v-model="custom_number_elections" style="width:70px">
                                        <button :disabled="running_elections_lock" class="btn btn-sm btn-outline-info" @click.prevent="runElections(population_stats.election_type, custom_number_elections)">
                                            Run {{custom_number_elections}} election<span v-if="custom_number_elections > 1">s</span>
                                        </button>
                                    </div>
                                </div>
                                <div v-else-if="population_stats.stage === 'p'">
                                    <h5>Performance stage</h5>
                                    <h5>{{election_name}}</h5>
                                    <h5>Elections</h5>
                                    <div>
                                        <label class="text-info">Number of elections: </label>
                                        <input type="number" min="1" max="100" step="0" v-model="custom_number_elections" style="width:70px">
                                        <button :disabled="running_elections_lock" class="btn btn-sm btn-outline-info" @click.prevent="runElections('d1', custom_number_elections)">
                                            Run {{custom_number_elections}} election<span v-if="custom_number_elections > 1">s</span>
                                        </button>
                                    </div>
                                </div>
                                <div v-else>
                                    <i class="text-warning">Unrecognized stage code.</i>
                                </div>
                                <h5>Chart display options</h5>
                                <div class="col-md-12 col-lg-12">
                                    <input type="checkbox" v-model="show_population_stats">
                                    <label class="text-info">Show population stats</label>
                                </div>

                                <div class="col-md-12 col-lg-12">
                                    <input type="checkbox" v-model="show_last_election_chart">
                                    <label class="text-info">Show last elections chart</label>
                                </div>

                                <div class="col-md-12 col-lg-12">
                                    <input type="checkbox" v-model="show_voters_graph">
                                    <label class="text-info">Show voters graph</label>
                                </div>

                                <div class="col-md-12 col-lg-12">
                                    <input type="checkbox" v-model="show_timeline_graph">
                                    <label class="text-info">Show timeline graph (results)</label>
                                </div>

                                <div class="col-md-12 col-lg-12">
                                    <input type="checkbox" v-model="show_weights_timeline_graph">
                                    <label class="text-info">Show timeline graph (weights)</label>
                                </div>

                            </div>
                            <div v-else>
                                <i>Population stage not defined. Cannot run elections.</i>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">Voters' attributes charts</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <input type="checkbox" v-model="auto_fetch_voters">
                                    <label class="text-info">Auto update voters' details after election</label>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <button class="btn btn-sm btn-outline-info" @click.prevent="fetchPopulationDetails">Fetch Voters Data</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">Elections timeline (type selector)</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <v-select :options="election_timeline_selector"
                                              id="election_timeline_selector"
                                              v-model="current_election_timeline_key"
                                              placeholder="Choose election type"
                                              label="text">
                                    </v-select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">Elections timeline (results)</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <label class="text-info">Moving average</label>
                                    <input type="number" min="0" step="1" v-model="moving_average" style="width:70px">
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <input type="checkbox" v-model="auto_fetch_elections_timeline">
                                    <label class="text-info">Auto update timeline after election</label>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <button :disabled="!current_election_timeline_key"
                                            v-on:click.prevent="fetchElectionsTimeline"
                                            v-bind:class="{'btn-primary' : current_election_timeline_key }"
                                            class="btn-xs">
                                        <i v-if="current_election_timeline_key">Fetch {{current_election_timeline_key.text}} results timeline</i>
                                        <i v-else>Select election type</i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">Elections timeline (weights)</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 col-lg-12">
                                    <input type="checkbox" v-model="auto_fetch_weights_timeline">
                                    <label class="text-info">Auto update weights timeline after election</label>
                                </div>
                                <div class="col-md-12 col-lg-12">
                                    <button :disabled="!current_election_timeline_key"
                                            v-on:click.prevent="fetchWeightsTimeline"
                                            v-bind:class="{'btn-primary' : current_election_timeline_key }"
                                            class="btn-xs">
                                        <i v-if="current_election_timeline_key">Fetch {{current_election_timeline_key.text}} weights timeline</i>
                                        <i v-else>Select election type</i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 col-lg-9">
                <div class="row mt-1">
                    <div class="alert alert-info col-md-12 col-lg-12" v-if="feedback">
                        <button class="btn btn-sm btn-outline-info" @click.prevent="resetFeedback">x</button>
                        INFO: {{feedback}}
                    </div>
                </div>
                <div v-if="population_stats" class="row mt-1">
                    <div class="col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">Last elections status</div>
                            <div class="card-body">
                                <div class="row" v-if="last_elections_data">
                                    <div class="col-md-4 col-lg-4 col-sm-4">
                                        Election type: <i>{{last_elections_data.elections_type}}</i>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-sm-4">
                                        Number of elections: <i>{{last_elections_data.number_of_elections}}</i>
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-sm-4">
                                        Total time: <i>{{last_elections_data.total_time}}</i>
                                    </div>
                                </div>
                                <div v-else-if="running_elections_lock"><i class="text-info">Elections in progress....</i></div>
                                <div v-else><i>N/A (run elections first)</i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="population_stats" class="row mt-1">
                    <div class="col-md-12">
                        <div class="card" v-if="show_voters_graph">
                            <div class="card-header">Voters' attributes charts</div>
                            <div class="card-body">
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
                        <div class="card" v-if="show_timeline_graph">
                            <div class="card-header">Elections timeline (results)</div>
                            <div class="card-body">
                                <div v-if="elections_timeline" >
                                    <hr>
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
                                                    :styles="h_500_chart_styles"
                                        ></line-chart>
                                    </div>
                                    <div v-else>
                                        <i>select show graph</i>
                                    </div>
                                </div>
                                <div v-else-if="!current_election_timeline_key">
                                    <i>N/A use Election Timeline Selector to mark election type</i>
                                </div>
                                <div v-else><i>N/A yet. Use "Fetch Timeline Results" or mark "Auto Update" and run elections</i></div>
                            </div>
                        </div>
                        <div class="card" v-if="show_weights_timeline_graph">
                            <div class="card-header">Elections timeline (weights)</div>
                            <div class="card-body">
                                <div v-if="weights_timeline" >
                                    <hr>
                                    Election type: <i>{{weights_timeline.elections_type}}</i>,
                                    Number of elections: <i>{{weights_timeline.no_of_elections}}</i>,
                                    Number of voters: <i>{{weights_timeline.no_of_voters}} (A:{{weights_timeline.no_of_voters_a}}, B:{{weights_timeline.no_of_voters_b}})</i>
                                    <div v-if="show_weights_timeline_graph">
                                        <h5>Timeline for delegation weights</h5>
                                        <line-chart :chart-data="weights_timeline_chart_data"
                                                    :options="{
                                                    maintainAspectRatio: false,
                                                    scales: {
                                                        yAxes: [
                                                            {id: 'left-y-axis',type: 'linear',position: 'left'},
                                                            {id: 'right-y-axis',type: 'linear',position: 'right',ticks: {min: 0, max:100}}
                                                        ]
                                                    }
                                               }"
                                                    :styles="h_500_chart_styles"
                                        ></line-chart>
                                    </div>
                                    <div v-else>
                                        <i>select show graph</i>
                                    </div>
                                </div>

                                <div v-else-if="!current_election_timeline_key">
                                    <i>N/A use Election Timeline Selector to mark election type</i>
                                </div>
                                <div v-else><i>N/A yet. Use "Fetch Timeline Weights" or mark "Auto Update" and run elections</i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-6 col-lg-6 col-sm-6" v-if="show_population_stats">
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
                        <div class="row mt-1" v-if="show_majority_distribution">
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
                </div>
                <div class="row mt-1">
                    <div class="col-md-12 col-lg-12" v-if="show_last_election_chart">
                        <div v-if="population_stats" class="row mt-1">
                            <div class="col-md-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">Last elections chart</div>
                                    <div class="card-body">
                                        <div v-if="last_elections_data">
                                            <bar-chart v-if="show_last_election_chart"
                                                       :chart-data="last_elections_chart_data"
                                                       :options="{
                                                    maintainAspectRatio: false,
                                                    scales: {
                                                        yAxes: [{id: 'left-y-axis',type: 'linear',position: 'left',ticks: {min: 0}}]
                                                    }
                                               }"
                                                       :styles="{height: 200}"
                                            ></bar-chart>
                                        </div>
                                        <div v-else><i>N/A (run elections first)</i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                show_population_stats: false,
                show_majority_distribution: false,
                show_last_election_chart: false,
                show_voters_graph: false,
                show_timeline_graph: false,
                show_weights_timeline_graph: false,
                custom_number_elections: 1,
                current_election_timeline_key: null,
                election_timeline_selector : [
                    {
                        value: 'm',
                        text: 'Majority (m)'
                    },
                    {
                        value: 'd1',
                        text: 'Delegation version 1 (d1)'
                    },
                    {
                        value: 'd2',
                        text: 'Delegation version 2 (d2)'
                    },
                    {
                        value: 'd3',
                        text: 'Delegation version 3 (d3)'
                    }
                ],
                elections_timeline: null,
                moving_average: 0,
                auto_fetch_elections_timeline: false,
                weights_timeline: null,
                auto_fetch_weights_timeline: false,
                running_elections_lock: false,
                auto_fetch_voters: false,
                auto_fetch_distribution: false,
                feedback : null,
                population_id: route().params.population_id,
                template_id: route().params.template_id,
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
                },
                h_500_chart_styles: {
                    height: '500px',
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
            election_name() {
                if (this.population_stats) {
                    return this.election_timeline_selector.filter(election_type => {
                        return election_type.value === this.population_stats.election_type;
                    })[0].text;
                }
                return null;
            },
            stage_name() {
                if (this.population_stats) {
                    switch (this.population_stats.stage) {
                        case 'l': return "Learning";
                        case 'p': return "Performance";
                        default: return "Unrecognized stage";
                    }
                }
                return null;
            },
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
                let reputation = [];
                let m_percent_correct = [];
                let d1_percent_correct = [];
                let d1_as_independent = [];
                let d1_as_follower = [];
                let d1_as_delegate = [];
                let d2_percent_correct = [];
                let d2_as_independent = [];
                let d2_as_follower = [];
                let d2_as_delegate = [];
                let diff = [];
                this.voters.forEach((value, idx) => {
                    labels.push(idx);
                    expertise.push(value.expertise);
                    confidence.push(value.confidence);
                    following.push(value.following);
                    leadership.push(value.leadership);
                    reputation.push(value.reputation);
                    m_percent_correct.push(value.majority_votes_stats.percent_correct);
                    //diff.push(value.majority_votes_stats.percent_correct - value.expertise);
                    d1_percent_correct.push(value.delegation_one_votes_stats.percent_finals_correct);
                    d1_as_independent.push(value.delegation_one_votes_stats.as_independent);
                    d1_as_follower.push(value.delegation_one_votes_stats.as_follower);
                    d1_as_delegate.push(value.delegation_one_votes_stats.as_delegate);
                    d2_percent_correct.push(value.delegation_two_votes_stats.percent_finals_correct);
                    d2_as_independent.push(value.delegation_two_votes_stats.as_independent);
                    d2_as_follower.push(value.delegation_two_votes_stats.as_follower);
                    d2_as_delegate.push(value.delegation_two_votes_stats.as_delegate);
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
                            borderColor: '#2f779b',
                            fill: false,
                            data: leadership,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'percent correct (M)',
                            borderColor: '#9a9b69',
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
                            data: d1_as_independent,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'as follower (D1)',
                            borderColor: '#ffe136',
                            fill: false,
                            data: d1_as_follower,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'as delegate (D1)',
                            borderColor: '#b7b30e',
                            fill: false,
                            data: d1_as_delegate,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'percent correct (D2)',
                            borderColor: '#964625',
                            fill: false,
                            data: d2_percent_correct,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'as independent (D2)',
                            borderColor: '#f0ba4e',
                            fill: false,
                            data: d2_as_independent,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'as follower (D2)',
                            borderColor: '#ff8843',
                            fill: false,
                            data: d2_as_follower,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'as delegate (with followers) (D2)',
                            borderColor: '#b75135',
                            fill: false,
                            data: d2_as_delegate,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'reputation',
                            borderColor: '#000000',
                            fill: false,
                            data: reputation,
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
            },
            weights_timeline_chart_data() {
                console.log('computing weights chart data');
                let labels = [];
                let avg_weight_a = [];
                let avg_weight_b = [];
                let reputation_a = [];
                let reputation_b = [];
                let weight_share_b = [];
                this.weights_timeline.weights.forEach((value,idx) => {
                    labels.push(idx);
                    avg_weight_a.push(value.avg_weight_a);
                    avg_weight_b.push(value.avg_weight_b);
                    reputation_a.push(value.reputation_a);
                    reputation_b.push(value.reputation_b);
                    weight_share_b.push(value.weight_share_b);
                });
                return {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Group A (avg Weight per voter)',
                            borderColor: '#6b9c3a',
                            fill: false,
                            data: avg_weight_a,
                            yAxisID: 'left-y-axis'
                        },

                        {
                            label: 'Group B (avg Weight per voter)',
                            borderColor: '#01439b',
                            fill: false,
                            data: avg_weight_b,
                            yAxisID: 'left-y-axis'
                        },

                        {
                            label: 'Group A - Reputation sum',
                            borderColor: '#929c84',
                            fill: false,
                            data: reputation_a,
                            yAxisID: 'left-y-axis'
                        },

                        {
                            label: 'Group B - Reputation sum',
                            borderColor: '#6c739b',
                            fill: false,
                            data: reputation_b,
                            yAxisID: 'left-y-axis'
                        },

                        {
                            label: 'Group B - Weight share: wB/(wA+wB)',
                            borderColor: '#b77959',
                            backgroundColor: '#b77959',
                            fill: true,
                            data: weight_share_b,
                            yAxisID: 'right-y-axis'
                        }
                    ]
                }
            }
        },
        methods: {
            resetFeedback() {
                this.feedback = null;
            },
            getTemplateLink(templateId) {
                return route('template.show', templateId);
            },
            fetchPopulationDetails() {
                this.feedback = 'fetching voters data..';
                axios.get(route(
                    'internal.api.population.get.voters',
                    {
                        "template":this.template_id,
                        "population":this.population_id
                    }
                )).then((response) => {
                    this.feedback = 'voters data fetched';
                    this.voters = response.data;
                    this.voters_fetched = true;
                }).catch((err) => {
                    this.feedback = 'voters data fetching error';
                });
            },
            fetchPopulationStats() {
                this.feedback = 'fetching population stats..';
                axios.get(route(
                    'internal.api.population.get',
                    {
                        "template":this.template_id,
                        "population":this.population_id
                    }
                    )).then((response) => {
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
                    if (this.auto_fetch_weights_timeline) {
                        this.fetchWeightsTimeline();
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
                    axios.post(
                        route(
                            'internal.api.elections.run',
                            {
                                "template":this.template_id,
                                "population":this.population_id
                            }),
                        {
                            type: type,
                            number:multi
                        }).then((response) => {
                        this.feedback = 'voting done, fetching updated population stats..';
                        console.log(this);
                        this.fetchPopulationStats();
                        this.last_elections_data = response.data;
                        this.running_elections_lock = false;
                    }).catch((err) => {
                        this.feedback = 'election error';
                        this.running_elections_lock = false;
                    });
                }
            },
            switchToPerformanceStage() {
                axios.post(route(
                    'internal.api.population.stage.update',
                    {
                        "template":this.template_id,
                        "population":this.population_id
                    }
                )).then((response) => {
                    this.population_stats.stage = 'p';
                    this.feedback = "Changed population stage. Only elections that do not alter attributes are available.";
                }).catch((err) => {
                    this.feedback = "Error while changing stage.";
                });
            },
            fetchMajorityElectionsDistribution() {
                this.feedback = 'fetching majority distribution...';
                this.me_distribution_metadata = null;
                axios.get(
                    route(
                        'internal.api.majority.distribution.get',
                        {
                            "template":this.template_id,
                            "population":this.population_id
                        }
                    )).then((response) => {
                    this.feedback = 'majority distribution fetched';
                    this.majority_elections_distribution = response.data.distribution;
                    this.majority_elections_distribution_r_10 = response.data.distribution_r_10;
                    this.me_distribution_metadata = response.data.metadata;
                }).catch((err) => {
                    this.feedback = 'majority distribution fetching error';
                });
            },
            fetchElectionsTimeline() {
                axios.get(
                    route(
                        'internal.api.population.get.elections.timeline',
                        {
                            "template":this.template_id,
                            "population":this.population_id
                        }
                    ), {
                        params: {
                            'type': this.current_election_timeline_key.value,
                            'moving_average': this.moving_average
                        }
                    }).then((response) => {
                    this.elections_timeline = response.data;
                    this.feedback = 'election timeline fetched';
                }).catch((err) => {
                    this.feedback = 'election timeline fetching error';
                });
            },
            fetchWeightsTimeline() {
                console.log(this.current_election_timeline_key);
                axios.get(
                    route(
                        'internal.api.population.get.weights.timeline',
                        {
                            "template":this.template_id,
                            "population":this.population_id
                        }
                    ), {
                        params: {
                            'type': this.current_election_timeline_key.value,
                            'moving_average': this.moving_average
                        }
                    }).then((response) => {
                    this.weights_timeline = response.data;
                    this.feedback = 'weights timeline fetched';
                }).catch((err) => {
                    this.feedback = 'weights timeline fetching error';
                });
            }
        }
    }
</script>

<style scoped>

</style>
