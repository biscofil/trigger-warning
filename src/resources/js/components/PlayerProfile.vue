<template>

    <div class="card bg-secondary text-white player-card">

        <img class="card-img-top" :src="player.avatar" :alt="player.name">

        <div class="card-header">
            {{ player.name }} - {{ player.score }}
        </div>

    </div>

</template>

<script>
    export default {
        name: "PlayerProfile",

        props: {
            player: {
                required: true
            }
        }
    }
</script>

<style scoped>

    .player-card {
        margin: 10px;
        max-width: 200px;
    }

</style>
