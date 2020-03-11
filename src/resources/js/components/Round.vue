<template>

    <div class="row" v-if="round">

        <div class="col-sm-12">

            <div class="row">

                <div class="col-sm-3" align="center">

                    <h3>Host</h3>

                    <PlayerProfile
                        :player="round.host">

                    </PlayerProfile>

                </div>

                <div class="col-sm-3" align="center">

                    <h3>Carta pescata:</h3>

                    <Card :card="round.card_to_fill"></Card>

                </div>

                <div class="col-sm-6" align="center" v-if="me.id !== round.host_user_id">

                    <h3>Le tue carte scelte:</h3>

                    <draggable class="row draggable-holder"
                               v-model="picked_cards"
                               v-bind="dragOptions"
                               @end="onDrop"
                               id="picked_card_list">

                        <div class="col" v-for="picked_card in picked_cards" :key="picked_card.id"
                             :card_id="picked_card.id">
                            <Card :card="picked_card"></Card>
                        </div>

                    </draggable>

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
                                <h4>Carte pescate</h4>
                            </span>

                            <span v-else class="badge badge-warning">
                                <h4>Non ancora pronto!</h4>
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

        <!-- IF NOT HOST, SHOW DECK -->
        <div class="col-sm-12" v-if="me.id !== round.host_user_id">

            <h3>Il tuo mazzo</h3>

            <draggable class="draggable-holder row"
                       v-model="my_cards"
                       v-bind="dragOptions"
                       @end="onDrop"
                       :move="onMoveCallback"
                       id="deck">

                <div class="col-sm-2" v-for="card in my_cards" :key="card.id" :card_id="card.id">
                    <Card :card="card"></Card>
                </div>

            </draggable>

        </div>

    </div>

</template>

<script>

    import PlayerProfile from "./PlayerProfile";
    import Card from "./Card";
    import draggable from 'vuedraggable';

    export default {

        name: "Round",

        components: {
            draggable,
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
                    self.me = response.data.me;
                    self.round = response.data.round;
                    self.my_cards = response.data.my_cards;
                    self.picked_cards = response.data.picked_cards;
                })
                .catch(e => {

                    self.$toastr.e("Merda...");

                });

        },

        data() {
            return {
                me: null,
                round: null,
                my_cards: null,
                picked_cards: null
            }
        },
        computed: {
            dragOptions() {
                return {
                    animation: 0,
                    group: "user_cards",
                    disabled: false,
                };
            }
        },

        methods: {

            onDrop(evt) {

                let self = this;

                if (evt.to.id === 'picked_card_list') {

                    axios.put('api/rounds/' + this.round.id + '/cards/' + evt.item.attributes.card_id.value)
                        .then(response => {
                            self.$toastr.s("OK");
                        })
                        .catch(e => {
                            self.$toastr.e("Errore");
                        });

                }
            },

            onMoveCallback: function (evt, originalEvent) {
                if (evt.to.id === 'picked_card_list') {
                    return this.picked_cards.length < this.round.card_to_fill.spaces_count;
                }
                return true;
            }

        }

    }

</script>

<style scoped>

    hr {
        border-top: 2px solid rgba(255, 255, 255, 0.28);
    }

    .draggable-holder {
        min-height: 200px;
        border-radius: 20px;
        border: 2px dashed white;
    }

</style>
