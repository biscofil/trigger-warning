<template>



</template>

<script>
    import draggable from 'vuedraggable'

    import Card from "./Card";

    export default {
        name: "CardDeck",

        components: {
            Card,
            draggable
        },

        props: {
            cards: {
                required: true,
                type: Array
            }
        }
    }
</script>

<style scoped>

</style>
