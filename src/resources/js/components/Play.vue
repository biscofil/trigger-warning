<template>

    <div>

        <Round v-if="round_id" :round_id="round_id" @end="round_id = null"></Round>

        <div class="col-sm-12" align="center" v-else>

            <PlayerProfile :player="me"></PlayerProfile>

            <hr>

            <h1>Nuova partita</h1>

            Utenti con cui giocare:

            <div class="row">

                <div class="col-sm-12" v-for="user in users">

                    <input type="checkbox" :checked="user.active" :id="user.id" @change="setUserActive(user)"/>
                    <label :for="user.id">{{user.name}}</label>

                </div>

                <div class="col-sm-12" align="center">

                    <button class="btn btn-lg btn-success" @click="newRound">
                        Nuovo round
                    </button>

                </div>

            </div>

        </div>

        <hr>


        <div class="col-sm-12" v-if="cards">

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
    import PlayerProfile from "./PlayerProfile";

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

            axios.get('api/game')
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

            },

            setUserActive(user) {

                let self = this;

                let newActive = user.active ? 0 : 1;

                axios.post('api/users/' + user.id + '/active/' + newActive)
                    .then(response => {

                        self.users = response.data.users;
                        self.$toastr.s("Ok!");

                    })
                    .catch(e => {

                        self.$toastr.e(e.response.data.error, "Cazzo...");

                    });
            }

        }

    }

</script>
