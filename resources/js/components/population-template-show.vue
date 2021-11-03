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

    import vSelect from "vue-select";
    import 'vue-select/dist/vue-select.css';

    export default {
        name: "population-template-show",
        components: {vSelect},
        data() {
            return {
                current_election_type_key: null,
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
                template_name: null
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
            }
        }
    }
</script>

<style scoped>

</style>
