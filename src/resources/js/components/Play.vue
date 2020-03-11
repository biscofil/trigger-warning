<template>

    <div>

        <Round v-if="round_id" :round_id="round_id" @end="round_id = null"></Round>

        <button class="btn btn-lg btn-success" v-else @click="newRound">
            Nuovo round
        </button>

        <button class="btn btn-lg btn-info" @click="newCard">
            Nuova carta
        </button>

        <modals-container/>

    </div>

</template>

<script>
    import VModal from 'vue-js-modal'
    import NewCardModal from "./NewCardModal";
    import Round from "./Round";

    Vue.use(VModal, {dynamic: true, dynamicDefaults: {clickToClose: false}})

    export default {

        name: 'Play',

        components: {
            Round
        },

        data() {
            return {
                round_id: null
            }
        },

        mounted() {

            let self = this;

            axios.get('api/game')
                .then(response => {

                    self.round_id = response.data.round_id;

                })
                .catch(e => {

                    self.$toastr.e("Merda...");

                });

        },

        methods: {

            newRound() {

                let self = this;

                axios.post('api/rounds')
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
