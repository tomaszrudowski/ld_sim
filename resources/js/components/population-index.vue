<template>
    <div class="p-2">
        <div class="row">
            <div class="col-md-6 col-lg-6 col-sm-6">
                <div class="card">
                    <div class="card-header">Status</div>
                    <div class="card-body">
                        <div class="alert alert-info" v-if="feedback">
                            INFO: {{feedback}}
                            <button class="float-right btn btn-sm btn-outline-info" @click.prevent="resetFeedback">x</button>
                        </div>
                        <div class="alert alert-primary" v-if="creationFeedback">
                            {{creationFeedback}}
                            <button class="float-right btn btn-sm btn-outline-info" @click.prevent="resetCreationFeedback">x</button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Populations</div>

                    <div class="card-body">
                        <div>
                            <button v-on:click.prevent="addPopulation"
                                    data-target="#create-population-modal"
                                    data-toggle="modal"
                            >
                                Add population template
                            </button>
                        </div>
                        <hr>
                        <div>
                            <i class="text-info">Select population for details.</i>
                        </div>
                        <div class="btn-group-vertical">
                            <span v-on:click.prevent="selectPopulation(population)"
                                    v-for="population in populations"
                                    class="btn btn-outline-info"
                                    v-bind:class="{'btn-info text-white': (current_population != null && current_population.id == population.id)}"
                            >
                                {{population.name}} <i>voters: {{population.voters_stats.no_of_voters}},</i>
                                <br><i>Number of child-populations: {{population.children_count}}</i>
                                <br><i>Number of elections run on template: </i>
                                <br>
                            <i v-for="election in population.elections_stats">(type-{{election.type}}: # {{election.count}})</i>
                            </span>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-6 col-sm-6">
                <div v-if="current_population">
                    <div class="card">
                        <div class="card-header">{{current_population.name}}</div>

                        <div class="card-body">
                            Voters' and child-populations details and new elections available in
                            <a :href="getLink(current_population.id)">Population Template detail view</a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">Child Populations stats</div>
                        <div v-if="current_population.child_populations_stats" class="card-body">
                            <p>
                                Number of child populations locked to election type:
                            </p>
                            <table>
                                <thead>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Total</th>
                                    <th>Learning</th>
                                    <th>Performance</th>
                                </thead>
                                <tbody>
                                <tr v-for="(election_type, key) in current_population.child_populations_stats">
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

                    <div class="card">
                        <div class="card-header">Voters stats</div>
                        <div v-if="current_population.voters_stats" class="card-body">
                            <bar-chart :chart-data="population_voters_stats_chart_data"
                            :options="{
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
                                }]
                            }}"
                            :styles="{height: 200}"></bar-chart>
                        </div>
                        <div v-else class="card-body"><i>N/A</i></div>
                    </div>

                    <div class="card">
                        <div class="card-header">Population Template election stats</div>
                        <div v-if="current_population.elections_stats" class="card-body">
                            <bar-chart :chart-data="population_election_stats_chart_data"
                                       :options="{
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
                                }]
                            }}"
                                       :styles="{height: 200}"></bar-chart>
                        </div>
                        <div v-else class="card-body"><i>N/A</i></div>
                    </div>
                </div>

                <div v-else>
                    <i class="text-muted">no population selected</i>
                </div>

            </div>
        </div>

        <population-create></population-create>
    </div>
</template>

<script>
    import PopulationCreate from "./population-create";
    import BarChart from "./charts/bar-chart";
    export default {
        name: "population-index",
        components: {PopulationCreate, BarChart},
        data() {
            return {
                current_population: null,
                feedback: null,
                creationFeedback: null,
                populations: [],
                h_400_chart_styles: {
                    height: '400px',
                    width: '100%',
                    position: 'relative'
                }
            }
        },
        mounted() {
            this.feedback = 'Fetching population index..';
            this.fetchPopulationIndex();
            this.feedback = 'Population index fetched (newest first).';
            Bus.$on('TemplateCreated', (refresh, data) => {
                if (data) {
                    this.creationFeedback = "Population template created, time: " + data.total_time;
                    this.fetchPopulationIndex();
                }
                if (refresh === true) {
                    this.feedback = 'Population template added reloading index..';
                    this.fetchPopulationIndex(true);
                    this.feedback = 'Index reloaded. Newest population template selected.';
                }
            });
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
                let no_of_voters = this.current_population.voters_stats ? this.current_population.voters_stats.no_of_voters : 0;
                this.current_population.elections_stats.forEach(election_type => {
                    let election_type_set = [];
                    election_type_set.push(election_type.no_of_correct_average);
                    election_type_set.push(election_type.no_of_incorrect_average);
                    election_type_set.push(election_type.percent_correct);
                    datasets.push({
                        label: election_type.type + '(#:' + election_type.count + ')',
                        backgroundColor: colors[color_idx],
                        data: election_type_set
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
                    'count', 'Expertise (avg)', 'Confidence (avg)', 'Following (avg)', 'Leadership (avg)'
                ];
                let groups = [];
                this.current_population.voters_stats.groups.forEach(voters_group => {
                    let voters_group_set = [];
                    voters_group_set.push(voters_group.no_of_voters);
                    voters_group_set.push(voters_group.expertise_average);
                    voters_group_set.push(voters_group.confidence_average);
                    voters_group_set.push(voters_group.following_average);
                    voters_group_set.push(voters_group.leadership_average);
                    groups.push({
                        label: 'group:' + voters_group.name,
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
        },
        methods: {
            resetFeedback() {
                this.feedback = null;
            },
            resetCreationFeedback() {
                this.creationFeedback = null;
            },
            selectPopulation(population) {
                if (this.current_population && (this.current_population.id == population.id)) {
                    this.current_population = null;
                } else {
                    this.current_population = population;
                }
            },
            fetchPopulationIndex(selectFirst = false) {
                this.populations = [];
                axios.get(route('internal.api.templates.index')).then((response) => {
                    this.populations = response.data;
                    if(selectFirst && this.populations[0]) {
                        this.current_population = this.populations[0]
                    }
                    console.log(this.current_population);
                }).catch((err) => {
                    this.feedback = 'population data error';
                });
            },
            getLink(templateId) {
                return route('template.show', templateId);
            },
            addPopulation() {
                console.log('add population');
            }
        }
    }
</script>

<style scoped>

</style>
