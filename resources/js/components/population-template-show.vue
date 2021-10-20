<template>
    <div class="p-2">
        <div class="row">
            <div class="col-md-3 col-lg-3">

            </div>
            <div class="col-md-9 col-lg-9">
                <div class="row">
                    <div class="alert alert-info col-md-12 col-lg-12" v-if="feedback">
                        INFO: {{feedback}}
                        <button class="float-right btn btn-sm btn-outline-info" @click.prevent="resetFeedback">x</button>
                    </div>
                </div>
                <div class="row" v-if="current_template">
                    <div class="alert alert-info col-md-12 col-lg-12">
                        <div class="card">
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
                    <div class="alert alert-info col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-header">Child Populations</div>
                            <div v-if="current_template" class="card-body">
                                <div class="row">
                                    <div class="col-md-4 col-lg-4">
                                        <v-select :options="election_type_selector"
                                                  id="election_type_selector"
                                                  v-model="current_election_type_key"
                                                  placeholder="Choose election type"
                                                  label="text">
                                        </v-select>
                                    </div>
                                    <div class="col-md-8 col-lg-8">
                                        <button :disabled="!current_election_type_key"
                                                v-on:click.prevent="addChildPopulation"
                                                v-bind:class="{'btn-primary' : current_election_type_key }"
                                                class="btn-xs">
                                            <i v-if="current_election_type_key">Add child population - bind election type: {{current_election_type_key.text}}</i>
                                            <i v-else>Select election type for child population</i>
                                        </button>
                                        <i class="text-info" v-if="current_election_type_key">{{current_election_type_key.info}})</i>
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
                                    </thead>
                                    <tbody>
                                    <tr v-for="child_population in current_template.child_populations">
                                        <td>{{child_population.election_type}}</td>
                                        <td>
                                            <a :href="getChildPopulationLink(child_population.id)">{{child_population.name}}</a>
                                        </td>
                                        <td>{{child_population.stage}}</td>
                                        <td v-for="election_type in child_population.elections_stats">
                                            <span v-if="election_type.count > 0">Count: {{election_type.count}} (average correct: {{election_type.percent_correct.toFixed(2)}})</span>
                                            <i v-else>N/A</i>
                                        </td>
                                        <td>{{child_population.elections_count}}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else class="card-body"><i>N/A</i></div>
                        </div>
                    </div>
                </div>
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
                election_type_selector : [
                    {
                        value: 'm',
                        text: 'Majority (m)',
                        info: 'Majority election, check expertise only'
                    },
                    {
                        value: 'd1',
                        text: 'Delegation version 1 (d1)',
                        info: 'Delegation voting, no adjustments (performance stage)'
                    },
                    {
                        value: 'd2',
                        text: 'Delegation version 2 (d2)',
                        info: 'Delegation voting, include reputation adjustments (learning stage)'
                    },
                    {
                        value: 'd3',
                        text: 'Delegation version 3 (d3)',
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
            }
        }
    }
</script>

<style scoped>

</style>
