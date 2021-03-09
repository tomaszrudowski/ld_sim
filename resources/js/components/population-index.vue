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
                                Add population
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
                                {{population.name}} (voters: {{population.voters_stats.no_of_voters}}, elections: {{population.elections_stats.m}})
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
                            Voters' details and new elections available in
                            <a :href="getLink(current_population.id)">detail view</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">Election stats</div>
                        <div v-if="current_population.elections_stats" class="card-body">
                            <ul>
                                <li>
                                    Number of Majority Elections (ME): {{current_population.elections_stats.m}}
                                </li>
                                <li>
                                    Avg number of correct choices(ME): {{current_population.elections_stats.m_no_of_correct_average}}
                                </li>
                                <li>
                                    Avg number of incorrect choices(ME): {{current_population.elections_stats.m_no_of_incorrect_average}}
                                </li>
                                <li>
                                    Percent of correct choices(ME): {{current_population.elections_stats.m_percent_correct}}
                                </li>
                            </ul>
                        </div>
                        <div v-else class="card-body"><i>N/A</i></div>
                    </div>
                    <div class="card">
                        <div class="card-header">Voters stats</div>
                        <div v-if="current_population.voters_stats" class="card-body">
                            <ul>
                                <li>
                                    Number of Voters: {{current_population.voters_stats.no_of_voters}}
                                </li>
                                <li>
                                    Avg Expertise: {{current_population.voters_stats.expertise_average}}
                                </li>
                                <li>
                                    Avg Confidence: {{current_population.voters_stats.confidence_average}}
                                </li>
                                <li>
                                    Avg Following: {{current_population.voters_stats.following_average}}
                                </li>
                            </ul>
                            <hr>
                            <div class="row">
                                <div v-for="group in current_population.voters_stats.groups"
                                     class="col-md-6">
                                    group: {{group.name}} <br>
                                    number of Voters {{group.no_of_voters}} <br>
                                    avg Expertise {{group.expertise_average}} <br>
                                    avg Confidence {{group.confidence_average}} <br>
                                    avg Following {{group.following_average}} <br>
                                </div>
                            </div>
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
    export default {
        name: "population-index",
        components: {PopulationCreate},
        data() {
            return {
                current_population: null,
                feedback: null,
                creationFeedback: null,
                populations: []
            }
        },
        mounted() {
            this.feedback = 'Fetching population index..';
            this.fetchPopulationIndex();
            this.feedback = 'Population index fetched (newest first).';
            Bus.$on('PopulationCreated', (refresh, data) => {
                if (data) {
                    this.creationFeedback = "Population created, time: " + data.total_time;
                    this.fetchPopulationIndex();
                }
                if (refresh === true) {
                    this.feedback = 'Population added reloading index..';
                    this.fetchPopulationIndex(true);
                    this.feedback = 'Index reloaded. Newest population selected.';
                }
            });
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
                axios.get(route('internal.api.population.index')).then((response) => {
                    this.populations = response.data;
                    if(selectFirst && this.populations[0]) {
                        this.current_population = this.populations[0]
                    }
                }).catch((err) => {
                    this.feedback = 'population data error';
                });
            },
            getLink(populationId) {
                return route('population.show', populationId);
            },
            addPopulation() {
                console.log('add population');
            }
        }
    }
</script>

<style scoped>

</style>
