<template>

    <div>

        <Round v-if="round_id" :round_id="round_id" @end="round_id = null"></Round>

        <button v-else @click="newRound">
            Nuovo round
        </button>

    </div>

</template>


<script>

    import Round from "./Round";

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
            }

        }

    }

</script>
