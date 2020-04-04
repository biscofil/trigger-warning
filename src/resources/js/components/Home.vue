<template>

    <div class="row">

        <div class="col-sm-12">

            <PlayerProfile :player="me"></PlayerProfile>

            <hr>

        </div>

        <div class="col-sm-12">
            Utenti online:
        </div>

        <div class="col-sm-2" v-for="user in users">
            <PlayerProfile :player="user"></PlayerProfile>
        </div>

    </div>

</template>

<script>
    import PlayerProfile from "./PlayerProfile";

    export default {

        name: "Home",

        components: {
            PlayerProfile
        },

        data() {
            return {
                me: null,
                users: null,
            }
        },

        mounted() {

            let self = this;

            axios.get('/api/users')
                .then(response => {
                    self.me = response.data.me;
                    self.users = response.data.users;
                })
                .catch(e => {
                    self.$toastr.e("Ops...");
                });

        }

    }
</script>

<style scoped>


</style>
