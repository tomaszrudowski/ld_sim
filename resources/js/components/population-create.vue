<template>
    <div class="modal fade" id="create-population-modal" tabindex="-1" role="dialog" aria-labelledby="create-population-modal-label">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">

                    <h4 class="modal-title" id="editOfficeModalLabel">
                        Add new population
                    </h4>
                </div>
                <div class="modal-body">
                    <div v-if="errorFeedback" class="alert alert-danger">
                        {{errorFeedback.toString()}}
                        <button class="float-right btn btn-sm btn-outline-info" @click.prevent="resetErrorFeedback">x</button>
                    </div>
                    <form>
                        <div class="container-fluid">
                            <div class="row form-group">
                                <div class="col-md-6 form-inline">
                                    <label for="size_a"
                                           :class="['col-md-6', validationErrors['name'] ? 'text-danger' : '']"
                                    >Name (optional):</label>
                                    <input type="text"
                                           class="form-control col-md-6"
                                           id="name"
                                           v-model="population.name">
                                    <span v-for="val_error in validationErrors['name']" class="text-danger">{{ val_error}}</span>
                                </div>
                            </div>
                            <hr>
                            <div>
                                <i class="text-info">
                                    Size of one group could be set to 0 if homogeneous population required.
                                </i>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6 form-inline">
                                    <label for="size_a"
                                           :class="['col-md-6', validationErrors['size_a'] ? 'text-danger' : '']"
                                    >Size A:</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="size_a"
                                           v-model="population.size_a">
                                    <span v-for="val_error in validationErrors['size_a']" class="text-danger">{{ val_error}}</span>
                                </div>
                                <div class="col-md-6 form-inline">
                                    <label for="size_a"
                                           :class="['col-md-6', validationErrors['size_b'] ? 'text-danger' : '']"
                                    >Size B:</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="size_b"
                                           v-model="population.size_b">
                                    <span v-for="val_error in validationErrors['size_b']" class="text-danger">{{ val_error}}</span>
                                </div>
                            </div>
                            <div>
                                <i class="text-info">
                                    Attributes should be in between 1 (worst) to 100 (best).
                                </i>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6 form-inline">
                                    <label for="init_expertise_a"
                                           :class="['col-md-6', 'col-form-label', validationErrors['init_expertise_a'] ? 'text-danger' : '']"
                                    >Expertise A (init):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="init_expertise_a"
                                           v-model="population.init_expertise_a">
                                    <span v-for="val_error in validationErrors['init_expertise_a']" class="text-danger">{{ val_error}}</span>
                                </div>
                                <div class="col-md-6 form-inline">
                                    <label for="init_expertise_b"
                                           :class="['col-md-6', 'col-form-label', validationErrors['init_expertise_b'] ? 'text-danger' : '']"
                                    >Expertise B (init):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="init_expertise_b"
                                           v-model="population.init_expertise_b">
                                    <span v-for="val_error in validationErrors['init_expertise_b']" class="text-danger">{{ val_error}}</span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6 form-inline">
                                    <label for="spread_expertise_a"
                                           :class="['col-md-6', 'col-form-label', validationErrors['spread_expertise_a'] ? 'text-danger' : '']"
                                    >Expertise A (spread):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="spread_expertise_a"
                                           v-model="population.spread_expertise_a">
                                    <span v-for="val_error in validationErrors['spread_expertise_a']" class="text-danger">{{ val_error}}</span>
                                </div>
                                <div class="col-md-6 form-inline">
                                    <label for="spread_expertise_b"
                                           :class="['col-md-6', 'col-form-label', validationErrors['spread_expertise_b'] ? 'text-danger' : '']"
                                    >Expertise B (spread):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="spread_expertise_b"
                                           v-model="population.spread_expertise_b">
                                    <span v-for="val_error in validationErrors['spread_expertise_b']" class="text-danger">{{ val_error}}</span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6 form-inline">
                                    <label for="init_confidence_a"
                                           :class="['col-md-6', 'col-form-label', validationErrors['init_confidence_a'] ? 'text-danger' : '']"
                                    >Confidence A (init):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="init_confidence_a"
                                           v-model="population.init_confidence_a">
                                    <span v-for="val_error in validationErrors['init_confidence_a']" class="text-danger">{{ val_error}}</span>
                                </div>
                                <div class="col-md-6 form-inline">
                                    <label for="init_confidence_b"
                                           :class="['col-md-6', 'col-form-label', validationErrors['init_confidence_b'] ? 'text-danger' : '']"
                                    >Confidence B (init):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="init_confidence_b"
                                           v-model="population.init_confidence_b">
                                    <span v-for="val_error in validationErrors['init_confidence_b']" class="text-danger">{{ val_error}}</span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6 form-inline">
                                    <label for="spread_confidence_a"
                                           :class="['col-md-6', 'col-form-label', validationErrors['spread_confidence_a'] ? 'text-danger' : '']"
                                    >Confidence A (spread):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="spread_confidence_a"
                                           v-model="population.spread_confidence_a">
                                    <span v-for="val_error in validationErrors['spread_confidence_a']" class="text-danger">{{ val_error}}</span>
                                </div>
                                <div class="col-md-6 form-inline">
                                    <label for="spread_confidence_b"
                                           :class="['col-md-6', 'col-form-label', validationErrors['spread_confidence_b'] ? 'text-danger' : '']"
                                    >Confidence B (spread):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="spread_confidence_b"
                                           v-model="population.spread_confidence_b">
                                    <span v-for="val_error in validationErrors['spread_confidence_b']" class="text-danger">{{ val_error}}</span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6 form-inline">
                                    <label for="init_following_a"
                                           :class="['col-md-6', 'col-form-label', validationErrors['init_following_a'] ? 'text-danger' : '']"
                                    >Following A (init):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="init_following_a"
                                           v-model="population.init_following_a">
                                    <span v-for="val_error in validationErrors['init_following_a']" class="text-danger">{{ val_error}}</span>
                                </div>
                                <div class="col-md-6 form-inline">
                                    <label for="init_following_b"
                                           :class="['col-md-6', 'col-form-label', validationErrors['init_following_b'] ? 'text-danger' : '']"
                                    >Following B (init):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="init_following_b"
                                           v-model="population.init_following_b">
                                    <span v-for="val_error in validationErrors['init_following_b']" class="text-danger">{{ val_error}}</span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6 form-inline">
                                    <label for="spread_following_a"
                                           :class="['col-md-6', 'col-form-label', validationErrors['spread_following_a'] ? 'text-danger' : '']"
                                    >Following A (spread):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="spread_following_a"
                                           v-model="population.spread_following_a">
                                    <span v-for="val_error in validationErrors['spread_following_a']" class="text-danger">{{ val_error}}</span>
                                </div>
                                <div class="col-md-6 form-inline">
                                    <label for="spread_following_b"
                                           :class="['col-md-6', 'col-form-label', validationErrors['spread_following_b'] ? 'text-danger' : '']"
                                    >Following B (spread):</label>
                                    <input type="number"
                                           class="form-control col-md-6"
                                           id="spread_following_b"
                                           v-model="population.spread_following_b">
                                    <span v-for="val_error in validationErrors['spread_following_b']" class="text-danger">{{ val_error}}</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" v-on:click.prevent="clearAndClose()">Cancel</button>
                    <button type="button" class="btn btn-primary" v-on:click.prevent="addNewPopulation()">
                        Save
                    </button>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "population-create",
        mounted() {
        },
        data() {
            return {
                validationErrors: [],
                errorFeedback: null,
                population: {
                    name: null,
                    size_a: 0,
                    init_expertise_a: 50,
                    spread_expertise_a: 0,
                    init_confidence_a: 50,
                    spread_confidence_a: 0,
                    init_following_a: 50,
                    spread_following_a: 0,
                    size_b: 0,
                    init_expertise_b: 50,
                    spread_expertise_b: 0,
                    init_confidence_b: 50,
                    spread_confidence_b: 0,
                    init_following_b: 50,
                    spread_following_b: 0
                }
            }
        },
        methods: {
            resetErrorFeedback() {
                this.errorFeedback = null;
            },
            addNewPopulation() {
                console.log('add...');
                console.log(this.population);
                axios.post(route('internal.api.population.post'), this.population).then((response) => {
                    console.log(response.data);
                    Bus.$emit('PopulationCreated', true, response.data.meta);
                    this.clearAndClose();
                }).catch((err) => {
                    console.log(err.response.data);
                    this.errorFeedback = err.response.data.message;
                    if (err.response.data.val_errors) {
                        this.validationErrors = err.response.data.val_errors;
                    } else {
                        this.validationErrors = [];
                    }
                });
            },
            clearAndClose() {
                this.validationErrors = [];
                this.errorFeedback = null;
                $('#create-population-modal').modal('hide');
            }
        }
    }
</script>

<style scoped>

</style>
