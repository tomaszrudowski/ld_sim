<template>
    <div class="p-2">
        <div class="row">
            <div class="col-md-3 col-lg-3">
                <div class="col-md-12" v-if="current_template">
                    <div class="card" >
                        <div class="card-header">{{current_template.name}}</div>
                        <div class="card-body">
                            <div>
                                <h5>Reputation modifiers:</h5>
                                <div class="col-md-12 col-lg-12">
                                    <ul>
                                        <li class="text-info">Forgetting percent: {{current_template.forgetting_factor}}</li>
                                        <li class="text-info">Follower multiplier: {{current_template.follower_factor}}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">Display options</div>
                        <div class="card-body">
                            <div class="col-md-12 col-lg-12">
                                <input type="checkbox" v-model="show_child_populations_details">
                                <label class="text-info">Show child population details</label>
                            </div>
                            <div class="col-md-12 col-lg-12">
                                <input type="checkbox" v-model="show_child_populations_in_main">
                                <label class="text-info">Show stats in main frame</label>
                            </div>
                            <div class="col-md-12 col-lg-12">
                                <input type="checkbox" v-model="show_analytics_weights">
                                <label class="text-info">Show weights analytics</label>
                            </div>
                        </div>
                    </div>
                    <div class="card" v-if="!show_child_populations_in_main">
                        <div class="card-header">Child Populations stats</div>
                        <div v-if="current_template.child_populations_stats" class="card-body">
                            <h5>
                                Number of child populations locked to election type
                            </h5>
                            <table class="table table-striped table-borderless">
                                <thead>
                                <th>Action</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>L</th>
                                <th>P</th>
                                </thead>
                                <tbody>
                                <tr v-for="(election_type, key) in current_template.child_populations_stats">
                                    <td>
                                        <button class="btn btn-success btn-sm" @click.prevent="addChildPopulation(key)">
                                            Add +
                                        </button>
                                    </td>
                                    <td>{{key}}</td>
                                    <td>{{election_type.info}}</td>
                                    <td>{{election_type.learning_stage_count}}</td>
                                    <td>{{election_type.performance_stage_count}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="card-body"><i>N/A</i></div>
                    </div>
                </div>

                <div v-else><i>Template not loaded</i></div>
            </div>
            <div class="col-md-9 col-lg-9">
                <div class="row">
                    <div class="alert alert-info col-md-12 col-lg-12" v-if="feedback">
                        INFO: {{feedback}}
                        <button class="float-right btn btn-sm btn-outline-info" @click.prevent="resetFeedback">x</button>
                    </div>
                </div>
                <div v-if="current_template">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="card" v-if="show_child_populations_in_main">
                                <div class="card-header">Child Populations stats</div>
                                <div v-if="current_template.child_populations_stats" class="card-body">
                                    <p>
                                        Number of child populations locked to election type:
                                    </p>
                                    <table class="table table-striped table-borderless">
                                        <thead>
                                        <th>Action</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Total</th>
                                        <th>Learning</th>
                                        <th>Performance</th>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(election_type, key) in current_template.child_populations_stats">
                                            <td>
                                                <button @click.prevent="addChildPopulation(key)">
                                                    Add child population
                                                </button>
                                            </td>
                                            <td>{{key}}</td>
                                            <td>{{election_type.info}}</td>
                                            <td>{{election_type.count}}</td>
                                            <td>{{election_type.learning_stage_count}}</td>
                                            <td>{{election_type.performance_stage_count}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else class="card-body"><i>N/A</i></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12" >
                            <div class="card" v-if="show_analytics_weights">
                                <div class="card-header">Analytics. Elections timeline (weights)</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 col-lg-4">
                                            <v-select :options="election_type_selector"
                                                      id="election_timeline_selector"
                                                      v-model="current_election_timeline_key"
                                                      placeholder="Choose election type"
                                                      label="text">
                                            </v-select>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <button :disabled="!current_election_timeline_key"
                                                    v-on:click.prevent="fetchWeightsTimeline"
                                                    v-bind:class="{'btn-primary' : current_election_timeline_key }"
                                                    class="btn-xs">
                                                <i v-if="current_election_timeline_key">Fetch {{current_election_timeline_key.text}} weights timeline</i>
                                                <i v-else>Select election type</i>
                                            </button>
                                        </div>
                                        <div class="col-md-4 col-lg-4">
                                            <input type="checkbox" v-model="auto_fetch_weights_timeline">
                                            <label class="text-info">Auto update weights timeline after election</label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row" v-if="analytics_weights_timeline" >
                                        <div class="col-md-12 col-lg-12">
                                            <h5>Timeline for delegation weights</h5>
                                            Number of child populations: <strong>{{analytics_weights_timeline.no_of_child_populations}}</strong> locked to election type - <strong>{{analytics_weights_timeline.report_metadata.election_type}}</strong>,
                                            <br>
                                            Number of voters: <strong>(A:{{analytics_weights_timeline.no_of_voters_a}}, B:{{analytics_weights_timeline.no_of_voters_b}})</strong>
                                            <br>
                                            Number of elections: <strong>{{analytics_weights_timeline.min_election_count}}</strong>
                                            <i>(maximum number in a single child population: {{analytics_weights_timeline.max_election_count}}</i>)

                                            <line-chart :chart-data="weights_timeline_chart_data"
                                                        :options="{
                                                    maintainAspectRatio: false,
                                                    scales: {
                                                        yAxes: [
                                                            {id: 'left-y-axis',type: 'linear',position: 'left'},
                                                            {id: 'right-y-axis',type: 'linear',position: 'right',ticks: {min: 0, max:1}}
                                                        ]
                                                    }
                                               }"
                                                        :styles="h_500_chart_styles"
                                            ></line-chart>
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
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="card" v-if="show_child_populations_details">
                                <div class="card-header">Child Populations</div>
                                <div class="card-body">
                                    <div class="col-md-12 col-lg-12">
                                        <h5>Run quick elections of population primary type for selected child population</h5>
                                        <div class="row">
                                            <div class="col-md-9 col-lg-9">
                                                <h6>Last elections status: </h6>
                                                <div v-if="last_elections_data" class="text-info">
                                                    Population: ({{last_elections_data.population_name}}),
                                                    Election type: ({{last_elections_data.elections_type}}),
                                                    Number of elections: ({{last_elections_data.number_of_elections}}),
                                                    Time: ({{last_elections_data.total_time}})
                                                </div>
                                                <div v-else><i class="text-muted">N/A (run elections first)</i></div>
                                            </div>
                                            <div class="col-md-3 col-lg-3">
                                                <label class="text-info">Number of elections: </label>
                                                <input type="number" min="1" max="100" step="0" v-model="custom_number_elections" style="width:70px">
                                            </div>
                                        </div>
                                    </div>

                                    <table class="table table-striped">
                                        <thead>
                                        <th>Type</th>
                                        <th>Name</th>
                                        <th>Stage</th>
                                        <th v-for="election_type in election_type_selector">
                                            {{election_type.text}}
                                        </th>
                                        <th>Run elections</th>
                                        </thead>
                                        <tbody>
                                        <tr v-bind:class="{'bg-info': (current_child_population != null && current_child_population.id == child_population.id)}"
                                                v-for="(child_population, key) in current_template.child_populations">
                                            <td v-on:click.prevent="childPopulationRowClicked(child_population)"
                                            >{{child_population.election_type}}</td>
                                            <td>
                                                <a class="btn btn-primary" :href="getChildPopulationLink(child_population.id)">{{child_population.name}}</a>
                                            </td>
                                            <td>{{child_population.stage}}</td>
                                            <td v-on:click.prevent="childPopulationRowClicked(child_population)"
                                                v-for="election_type in child_population.elections_stats">
                                                <span v-if="election_type.count > 0">Count: {{election_type.count}} (avg corr: {{election_type.percent_correct.toFixed(2)}})</span>
                                                <i v-else>N/A</i>
                                            </td>
                                            <td>
                                                <button :disabled="running_elections_lock" class="btn btn-sm btn-success" @click.prevent="runElections(key, custom_number_elections)">
                                                    Run {{custom_number_elections}} ({{child_population.election_type}}) election<span v-if="custom_number_elections > 1">s</span>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else><i>N/A Template not loaded</i></div>
            </div>
        </div>
    </div>
</template>

<script>

    import LineChart from "./charts/line-chart";
    import vSelect from "vue-select";
    import 'vue-select/dist/vue-select.css';

    export default {
        name: "population-template-show",
        components: {LineChart, vSelect},
        data() {
            return {
                current_election_timeline_key: null,
                auto_fetch_weights_timeline: false,
                analytics_weights_timeline: null,
                show_child_populations_details: true,
                show_child_populations_in_main: false,
                show_analytics_weights: false,
                current_child_population: null,
                custom_number_elections: 1,
                running_elections_lock: false,
                last_elections_data: null,
                election_type_selector : [
                    {
                        value: 'm',
                        text: 'Majority (m)',
                        info: 'Majority election, check expertise only'
                    },
                    {
                        value: 'd1',
                        text: 'Delegation (d1)',
                        info: 'Delegation voting, no adjustments (performance stage)'
                    },
                    {
                        value: 'd2',
                        text: 'Delegation (d2)',
                        info: 'Delegation voting, include reputation adjustments (learning stage)'
                    },
                    {
                        value: 'd3',
                        text: 'Delegation (d3)',
                        info: 'Delegation voting, include reputation and voters attributes adjustments (learning stage)'
                    }
                ],
                current_template: null,
                feedback: null,
                template_id: route().params.template_id,
                template_name: null,
                h_500_chart_styles: {
                    height: '500px',
                    width: '100%',
                    position: 'relative'
                }
            }
        },
        computed: {
            weights_timeline_chart_data() {
                console.log('computing weights chart data');
                let labels = [];
                let avg_sum_weight_a = [];
                let min_sum_weight_a = [];
                let max_sum_weight_a = [];
                let avg_sum_weight_b = [];
                let min_sum_weight_b = [];
                let max_sum_weight_b = [];
                let share_sum_weight_b = [];
                let min_share_sum_weight_b = [];
                let max_share_sum_weight_b = [];
                this.analytics_weights_timeline.weights.forEach((value,idx) => {
                    labels.push(idx);
                    avg_sum_weight_a.push(value.avg_sum_weight_a);
                    min_sum_weight_a.push(value.min_sum_weight_a);
                    max_sum_weight_a.push(value.max_sum_weight_a);
                    avg_sum_weight_b.push(value.avg_sum_weight_b);
                    min_sum_weight_b.push(value.min_sum_weight_b);
                    max_sum_weight_b.push(value.max_sum_weight_b);
                    share_sum_weight_b.push(value.share_sum_weight_b);
                    min_share_sum_weight_b.push(value.min_share_sum_weight_b);
                    max_share_sum_weight_b.push(value.max_share_sum_weight_b);
                });
                return {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Group A (avg sum Weight)',
                            borderColor: '#169c03',
                            fill: false,
                            data: avg_sum_weight_a,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'Group A (min sum Weight)',
                            borderColor: '#819c67',
                            fill: false,
                            data: min_sum_weight_a,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'Group A (max sum Weight)',
                            borderColor: '#819c67',
                            fill: false,
                            data: max_sum_weight_a,
                            yAxisID: 'left-y-axis'
                        },

                        {
                            label: 'Group B (avg sum Weight)',
                            borderColor: '#00259b',
                            fill: false,
                            data: avg_sum_weight_b,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'Group B (min sum Weight)',
                            borderColor: '#517e9b',
                            fill: false,
                            data: min_sum_weight_b,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'Group B (max sum Weight)',
                            borderColor: '#517e9b',
                            fill: false,
                            data: max_sum_weight_b,
                            yAxisID: 'left-y-axis'
                        },
                        {
                            label: 'Group B - sum Weight share',
                            borderColor: '#b73c33',
                            fill: false,
                            data: share_sum_weight_b,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'Group B - min Weight share',
                            borderColor: '#b78365',
                            fill: false,
                            data: min_share_sum_weight_b,
                            yAxisID: 'right-y-axis'
                        },
                        {
                            label: 'Group B - max Weight share',
                            borderColor: '#b78365',
                            fill: false,
                            data: max_share_sum_weight_b,
                            yAxisID: 'right-y-axis'
                        }
                    ]
                }
            }
        },
        mounted() {
            this.fetchPopulationTemplate();
        },
        methods: {
            childPopulationRowClicked(childPopulation) {
                if (this.current_child_population != null && childPopulation.id == this.current_child_population.id) {
                    this.current_child_population = null;
                } else {
                    this.current_child_population = childPopulation;
                }
            },
            resetFeedback() {
                this.feedback = null;
            },
            fetchPopulationTemplate() {
                this.feedback = 'fetching population template details..';
                axios.get(route('internal.api.template.get', this.template_id)).then((response) => {
                    this.feedback = 'population template details fetched';
                    this.current_template = response.data;
                }).catch((err) => {
                    this.feedback = 'population template fetching error';
                })
            },
            addChildPopulation(key) {
                this.feedback = 'Adding child population, binding ' + key;
                axios.post(
                    route('internal.api.populations.post', this.current_template.id),
                    {"election_type":key}
                ).then((response) => {
                    this.feedback = 'Child population added';
                    this.fetchPopulationTemplate();
                }).catch((err) => {
                    this.feedback = 'Adding child population error';
                })
            },
            getChildPopulationLink(populationId) {
                return route('population.show', {"template_id":this.current_template.id, "population_id":populationId});
            },
            runElections(key, multi) {
                let population = this.current_template.child_populations[key];
                if (this.running_elections_lock) {
                    this.feedback = 'already running elections, please wait...';
                } else {
                    this.running_elections_lock = true;
                    this.feedback = 'running ' + population.election_type + '-type elections: (' + multi + ')...';
                    this.last_elections_data = null;
                    this.current_child_population = population;
                    axios.post(
                        route(
                            'internal.api.elections.run',
                            {
                                "template" : population.parent_id,
                                "population" : population.id,
                                "omit_details" : 1
                            }),
                        {
                            type: population.election_type,
                            number:multi
                        }).then((response) => {
                        this.feedback = 'voting done, fetching updated population stats..';
                        this.last_elections_data = response.data;
                        this.last_elections_data.population_name = population.name;
                        this.updateChildPopulationStats(key);
                        console.log(this.auto_fetch_weights_timeline);
                        console.log(this.show_analytics_weights);
                        console.log(this.current_election_timeline_key);
                        console.log(population.election_type)
                        console.log(this.current_election_timeline_key == population.election_type);
                        if(this.auto_fetch_weights_timeline && this.show_analytics_weights && this.current_election_timeline_key.value == population.election_type) {
                            console.log("OK");
                            this.fetchWeightsTimeline();
                        }
                        this.running_elections_lock = false;
                    }).catch((err) => {
                        this.feedback = 'election error';
                        this.running_elections_lock = false;
                    });
                }
            },
            updateChildPopulationStats(key) {
                let population = this.current_template.child_populations[key];
                this.feedback = 'updating child population stats..';
                axios.get(route(
                    'internal.api.population.get',
                    {
                        "template":population.parent_id,
                        "population":population.id
                    }
                ), {
                    params: {'omit_voters': 1}
                }).then((response) => {
                    this.feedback = 'child population stats updated';
                    this.current_template.child_populations[key] = response.data;
                }).catch((err) => {
                    this.feedback = 'population stats fetching error';
                });
            },
            fetchWeightsTimeline() {
                axios.get(
                    route(
                        'internal.api.template.analytics.weights',
                        {
                            "template":this.current_template.id
                        }
                    ), {
                        params: {
                            'election_type': this.current_election_timeline_key.value
                        }
                    }).then((response) => {
                    this.analytics_weights_timeline = response.data;
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
