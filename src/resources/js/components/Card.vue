<template>

    <div class="card game-card" :class="cardClasses" @click="uncover">

        <div v-if="!covered" class="card-body" v-html="cardContent"/>

    </div>

</template>

<script>
    export default {
        name: "Card",

        props: {
            card: {
                require: true
            },
            covered: {
                require: false,
                default: false,
                type: Boolean
            },
            uncoverable: {
                require: false,
                default: false,
                type: Boolean
            }
        },

        computed: {
            cardContent() {
                if (this.card.type == 1) { // to fill
                    return this.card.content.replace(new RegExp(/@/, 'g'), '____');
                }
                return this.card.content;
            },

            cardClasses() {

                if (this.card.type == 1) { // to fill
                    return ['text-white', 'bg-black'];
                } else { // filling
                    return ['text-dark', 'bg-white'];
                }

            }
        },
        methods: {
            uncover() {
                if (this.covered && this.uncoverable) {
                    this.covered = false;
                }
            }
        }
    }
</script>

<style scoped>

    .bg-black {
        background-color: #000 !important;
    }

    .game-card {
        width: 300px;
        height: 400px;
        border: 2px solid black;
        border-radius: 15px;
        font-size: 40px;
        margin: 10px;
    }

</style>
