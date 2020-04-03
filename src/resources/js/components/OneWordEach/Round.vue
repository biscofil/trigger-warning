<template>

    <div class="row" v-if="round">

        <div class="col-sm-4" align="center">

            <b>Suggeritore 1</b>

            <PlayerProfile
                :player="round.first_suggesting_user"/>

        </div>

        <div class="col-sm-4" align="center">

            <PlayerProfile
                :player="round.guessing_user"/>

        </div>

        <div class="col-sm-4" align="center">

            <b>Suggeritore 2</b>

            <PlayerProfile
                :player="round.second_suggesting_user"/>

        </div>

        <div class="col-sm-12" align="center" v-if="me.id !== round.guessing_user.id">

            <h1>{{round.word.word}}</h1>

            <p>Parole vietate: {{round.word.forbidden_words}}</p>

            <button class="btn btn-success" @click="closeRound">
                Fine round
            </button>

        </div>

    </div>

</template>

<script>
    import PlayerProfile from "../PlayerProfile";

    export default {

        name: "Round",

        components: {
            PlayerProfile
        },

        props: {
            round_id: {
                required: true
            }
        },

        data() {
            return {
                me: null,
                round: null
            }
        },


        mounted() {

            let self = this;
            self.fetch();

        },

        methods: {

            fetch() {

                let self = this;

                axios.get('/api/games/one_word_each/rounds/' + this.round_id)
                    .then(response => {

                        self.me = response.data.me;
                        self.round = response.data.round;

                    })
                    .catch(e => {

                        self.$toastr.e("Ops...");

                    });
            },

            closeRound() {
                let self = this;

                axios.post('/api/games/one_word_each/rounds/' + this.round_id + '/close')
                    .then(response => {
                        self.$toastr.s("Ok");
                        self.$emit('end');
                    })
                    .catch(e => {
                        self.$toastr.e("Ops...");
                    });

            }

        }

    }
</script>

<style scoped>

</style>
