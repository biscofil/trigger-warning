<template>

    <div class="row">

        <div class="col-sm-12">

            <PlayerProfile :player="me"></PlayerProfile>

            <hr>

        </div>

        <div class="col-sm-12">
            Utenti con cui giocare:
        </div>

        <div class="col-sm-12" v-for="user in users">

            <input type="checkbox" :checked="user.active" :id="user.id" @change="setUserActive(user)"/>
            <label :for="user.id">{{user.name}}</label>

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

        },

        methods: {

            setUserActive(user) {

                let self = this;

                let newActive = user.active ? 0 : 1;

                axios.post('/api/users/' + user.id + '/active/' + newActive)
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

<style scoped>


</style>
