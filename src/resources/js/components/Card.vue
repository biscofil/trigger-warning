<template>

    <div class="card game-card" :class="cardClasses">

        <div class="card-body" v-html="cardContent"/>

    </div>

</template>

<script>
    export default {
        name: "Card",

        props: {
            card: {
                require: true
            }
        },

        computed: {
            cardContent() {
                return this.card.content.replace('@', '____');
            },

            cardClasses() {

                if (this.card.type == 1) { // to fill
                    return ['text-white', 'bg-black'];
                } else { // filling
                    return ['text-dark', 'bg-white'];
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
        border: 2px solid black;
        border-radius: 15px;
        font-size: 40px;
        margin: 10px;
        max-width: 400px;
    }

</style>
