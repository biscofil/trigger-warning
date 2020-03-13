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

                    <div class="draggable-wrapper">

                        <draggable class="row draggable-holder"
                                   v-model="picked_cards"
                                   v-bind="dragOptions"
                                   @end="onDrop"
                                   :move="onMoveCallback"
                                   id="picked_card_list">

                            <div class="col" v-for="picked_card in orderedPickedCards"
                                 :key="picked_card.id"
                                 :card_id="picked_card.id">
                                <Card :card="picked_card"></Card>
                            </div>

                        </draggable>

                    </div>

                </div>

            </div>

            <hr>

        </div>

        <div class="col-sm-12" v-if="me.id === round.host_user_id" align="center">

            <span v-else class="badge badge-warning" v-if="!round.ready_to_pick">
                <h4>Manca qualcuno!</h4>
            </span>

            <div class="row">

                <div class="col" v-for="player in rand_players" v-if="player.ready" align="center">

                    <div class="row">

                        <div class="col-sm-12" align="center"
                             v-for="picked_card in player.picked_cards">
                            <Card :card="picked_card" :covered="true" :uncoverable="round.ready_to_pick"></Card>
                        </div>

                    </div>

                    <button class="btn btn-lg btn-success" v-if="round.ready_to_pick" @click="electWinner(player)">
                        Eleggi vincitore!
                    </button>

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

                            <span v-if="player.ready" class="badge badge-success">
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

            <div class="draggable-wrapper">
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

    </div>

</template>

<script>

    import PlayerProfile from "./PlayerProfile";
    import Card from "./Card";
    import draggable from 'vuedraggable';
    import shuffle from 'lodash/shuffle'

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
            self.fetch();

            this.$nextTick(function () {

                window.setInterval(() => {

                    if (self.round) {
                        if (self.round.ready_to_pick) {
                            return;
                        }
                    }

                    self.fetch();

                }, 5000);

            })

        },

        data() {
            return {
                me: null,
                round: null,
                my_cards: null,
                picked_cards: null,
                rand_players: []
            }
        },

        computed: {

            dragOptions() {
                return {
                    animation: 0,
                    group: "user_cards",
                    disabled: false,
                };
            },

            orderedPickedCards() {
                return this.sortCards(this.picked_cards);
            }

        },

        methods: {

            sortCards(cards) {
                return cards.sort((one, two) => {
                    return one.order - two.order;
                });
            },

            fetch() {
                let self = this;

                axios.get('api/rounds/' + this.round_id)
                    .then(response => {
                        self.me = response.data.me;

                        self.round = response.data.round;

                        self.round.players = self.round.players.map(function (player) {
                            player.picked_cards = self.sortCards(player.picked_cards);
                            return player;
                        });

                        self.rand_players = shuffle(self.round.players);

                        self.my_cards = response.data.my_cards;
                        self.picked_cards = response.data.picked_cards;
                    })
                    .catch(e => {

                        self.$toastr.e("Merda...");

                    });
            },

            onDrop(evt) {

                let self = this;

                let cardId = evt.item.attributes.card_id.value;

                if (evt.from.id === 'picked_card_list') {

                    // from picked_card_list

                    if (evt.to.id === 'deck') {

                        // to deck
                        return;

                    } else {

                        // picked_card_list reorder

                        if (evt.oldIndex === evt.newIndex) {
                            // no call required
                            return;
                        }

                        axios.put('api/rounds/' + this.round.id + '/cards/' + cardId + '/picked',
                            {
                                'from_pos': evt.oldIndex,
                                'to_pos': evt.newIndex
                            }
                        )
                            .then(response => {
                                self.picked_cards = response.data.picked_cards;
                                self.$toastr.s("OK");
                            })
                            .catch(e => {
                                let error = e.response ? e.response.data.error : e;
                                self.$toastr.e(error, "Errore");
                            });

                    }

                } else {

                    // from deck

                    if (evt.to.id === 'picked_card_list') {

                        axios.put('api/rounds/' + this.round.id + '/cards/' + cardId + '/picked',
                            {
                                'to_pos': evt.newIndex
                            }
                        )
                            .then(response => {
                                self.$toastr.s("OK");
                            })
                            .catch(e => {
                                let error = e.response ? e.response.data.error : e;
                                self.$toastr.e(error, "Errore");
                            });

                    } else {

                        // deck reorder
                        return;

                    }

                }

            },

            onMoveCallback: function (evt, originalEvent) {

                if (evt.from.id === 'picked_card_list') {

                    // from picked_card_list

                    if (evt.to.id === 'deck') {

                        // to deck
                        return false;

                    } else {

                        // picked_card_list reorder
                        return true;

                    }

                } else {

                    // from deck

                    if (evt.to.id === 'picked_card_list') {

                        // from deck to picked_card_list
                        return this.picked_cards.length < this.round.card_to_fill.spaces_count;

                    } else {

                        // deck reorder
                        return true;

                    }

                }

            },

            electWinner(player) {
                let self = this;

                axios.post('api/rounds/' + this.round_id + '/close/' + player.id)
                    .then(response => {
                        self.$toastr.s("Ok");
                        self.$emit('end');
                    })
                    .catch(e => {
                        self.$toastr.e("Merda...");
                    });

            }

        }

    }

</script>

<style scoped>

    hr {
        border-top: 2px solid rgba(255, 255, 255, 0.28);
    }

    .draggable-wrapper {
        padding: 20px;
    }

    .draggable-holder {
        min-height: 200px;
        border-radius: 20px;
        border: 2px dashed white;
    }

</style>
