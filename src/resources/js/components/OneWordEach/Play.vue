<template>

    <div class="row">

        <div class="col-sm-12" v-if="round_id">
            <Round :round_id="round_id"></Round>
        </div>

        <div class="col-sm-12" v-else>

            <button class="btn btn-lg btn-success" @click="newRound">
                Nuovo round
            </button>

        </div>

        <div class="col-sm-12">
            <button class="btn btn-lg btn-info" @click="newWord">
                Nuova parola
            </button>
        </div>

        <modals-container/>

    </div>

</template>

<script>
    import VModal from 'vue-js-modal'
    import NewWordModal from "./NewWordModal";
    import Round from "./Round";

    Vue.use(VModal, {dynamic: true, dynamicDefaults: {clickToClose: false}})

    export default {

        name: 'Play',

        data() {
            return {
                me: null,
                round_id: null,
                users: null,
            }
        },

        components: {
            Round
        },

        mounted() {

            let self = this;

            axios.get('/api/games/one_word_each/game')
                .then(response => {

                    self.me = response.data.me;
                    self.round_id = response.data.round_id;
                    self.users = response.data.users;

                })
                .catch(e => {

                    self.$toastr.e("Ops...");

                });

        },

        methods: {

            newRound() {

                let self = this;

                axios.post('/api/games/one_word_each/rounds')
                    .then(response => {

                        self.round_id = response.data.round.id;
                        self.$toastr.s("Alleluia!");

                    })
                    .catch(e => {

                        self.$toastr.e(e.response.data.error, "Cazzo...");

                    });
            },


            newWord() {

                this.$modal.show(NewWordModal, {}, {
                    height: 'auto'
                });


            }

        }
    }

</script>
