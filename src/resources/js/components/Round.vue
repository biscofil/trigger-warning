<template>

    <div class="row" v-if="round">

        <div class="col-sm-12">

            <div class="row">

                <div class="col-sm-6" align="center">

                    <h3>Host</h3>

                    <PlayerProfile
                        :player="round.host">

                    </PlayerProfile>

                </div>

                <div class="col-sm-6" align="center">

                    <h3>Carta pescata:</h3>

                    <Card :card="round.card_to_fill"></Card>

                </div>

            </div>

            <hr>

        </div>

        <div class="col-sm-12">

            <h3>Riempitori</h3>

            <div class="row">

                <div class="col" v-for="player in round.players">

                    <div class="row">

                        <div class="col-sm-12" align="center">

                            <!--

                            <Card :card="round.card_to_fill"></Card>

                            -->

                            <span v-if="player.id%2==0" class="badge badge-success">
                                <h4>Carta pescata</h4>
                            </span>

                            <span v-else class="badge badge-warning">
                                <h4>Carta non pescata</h4>
                            </span>

                        </div>

                        <div class="col-sm-12" align="center">

                            <PlayerProfile
                                :player="player">
                            </PlayerProfile>

                        </div>

                    </div>

                </div>

            </div>

            <hr>

        </div>

        <div class="col-sm-12">

            <h3>Il tuo mazzo</h3>

            <div class="row">

                <div class="col-sm-2" v-for="card in my_cards" :key="card.id">
                    <Card :card="card"></Card>
                </div>

            </div>

        </div>

    </div>

</template>

<script>

    import PlayerProfile from "./PlayerProfile";
    import Card from "./Card";

    export default {

        name: "Round",

        components: {
            Card,
            PlayerProfile
        },

        props: {
            round_id: {
                required: true
            }
        },

        mounted() {

            let self = this;

            axios.get('api/rounds/' + this.round_id)
                .then(response => {

                    self.my_cards = response.data.my_cards;
                    self.round = response.data.round;

                })
                .catch(e => {

                    self.$toastr.e("Merda...");

                });

        },

        data() {
            return {
                round: null,
                my_cards: null
            }
        }

    }

</script>

<style scoped>

    hr {
        border-top: 2px solid rgba(255, 255, 255, 0.28);
    }

</style>
