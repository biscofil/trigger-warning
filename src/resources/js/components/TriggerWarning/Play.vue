<template>

    <div>

        <Round v-if="round_id" :round_id="round_id" @end="round_id = null"></Round>

        <div class="col-sm-12" align="center" v-else>

            <div class="col-sm-12" align="center">

                <button class="btn btn-lg btn-success" @click="newRound">
                    Nuovo round
                </button>

            </div>

        </div>

        <hr>


        <div class="col-sm-12" v-if="cards" align="center">

            <p>
                Carte da riempire: {{cards.to_fill}}
                <br>
                Carte riempitive: {{cards.filling}}
            </p>

            <button class="btn btn-lg btn-info" @click="newCard">
                Nuova carta
            </button>

        </div>

        <modals-container/>

    </div>

</template>

<script>
    import VModal from 'vue-js-modal'
    import NewCardModal from "./NewCardModal";
    import Round from "./Round";
    import PlayerProfile from "../PlayerProfile";

    Vue.use(VModal, {dynamic: true, dynamicDefaults: {clickToClose: false}})

    export default {

        name: 'Play',

        components: {
            PlayerProfile,
            Round
        },

        data() {
            return {
                me: null,
                cards: null,
                users: null,
                round_id: null
            }
        },

        mounted() {

            let self = this;

            axios.get('/api/games/trigger_warning/game')
                .then(response => {

                    self.me = response.data.me;
                    self.users = response.data.users;
                    self.cards = response.data.cards;
                    self.round_id = response.data.round_id;

                })
                .catch(e => {

                    self.$toastr.e("Merda...");

                });

        },

        methods: {

            newRound() {

                let self = this;

                axios.post('/api/games/trigger_warning/rounds')
                    .then(response => {

                        self.round_id = response.data.round.id;
                        self.$toastr.s("Alleluia!");

                    })
                    .catch(e => {

                        self.$toastr.e(e.response.data.error, "Cazzo...");

                    });
            },

            newCard() {

                this.$modal.show(NewCardModal, {}, {
                    height: 'auto'
                });

            }

        }

    }

</script>
