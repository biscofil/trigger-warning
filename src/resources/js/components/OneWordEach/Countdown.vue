<template>

    <VueCountdown v-if="time>0" :time="time" @end="handleCountdownEnd">
        <template slot-scope="props">
            <h2>- {{ props.minutes }}m {{ props.seconds }}s</h2>
        </template>
    </VueCountdown>

</template>

<script>

    import VueCountdown from '@chenfengyuan/vue-countdown';

    export default {

        name: "Countdown",

        components: {
            VueCountdown
        },

        props: {
            deadline: {
                required: true
            }
        },

        mounted() {
            let now = new Date();
            let target = this.deadline.toDate();
            this.time = target - now;
        },

        data() {
            return {
                time: null
            }
        },

        methods: {
            handleCountdownEnd() {
                this.$emit('end');
            }
        }

    }

</script>

<style scoped>

</style>
