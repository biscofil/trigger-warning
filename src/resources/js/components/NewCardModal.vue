<template>

    <div class="card">

        <div class="card-header text-white bg-dark">
            Nuova carta
        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-sm-12">

                    <div class="form-check">

                        <input class="form-check-input"
                               type="radio"
                               name="exampleRadios"
                               id="exampleRadios1"
                               value="1"
                               v-model="newCard.type"
                               checked>

                        <label class="form-check-label" for="exampleRadios1">
                            Carta da riempire
                        </label>

                    </div>

                    <div class="form-check">

                        <input class="form-check-input"
                               type="radio"
                               name="exampleRadios"
                               id="exampleRadios2"
                               value="2"
                               v-model="newCard.type">

                        <label class="form-check-label" for="exampleRadios2">
                            Carta riempitiva
                        </label>

                    </div>

                    <div class="form-group">

                        <div class="alert alert-info" v-if="newCard.type==1">
                            Utilizza il carattere <b>@</b> per lo spazio da riempire
                        </div>

                        <label for="content">Testo della carta</label>
                        <textarea class="form-control" id="content" v-model="newCard.content"></textarea>

                    </div>

                    <button class="btn btn-success" @click="save">Salva</button>

                </div>

                <div class="col-sm-12" align="center">
                    <p><b>Anterpima:</b></p>
                    <Card :card="newCard"></Card>
                </div>

            </div>

        </div>
    </div>

</template>

<script>
    import Card from "./Card";

    export default {
        name: "NewCardModal",

        components: {
            Card
        },

        data() {
            return {
                newCard: {
                    type: 1,
                    content: ''
                }
            }
        },

        methods: {
            save() {
                let self = this;
                axios.post('api/cards', this.newCard)
                    .then(response => {
                        self.newCard.content = "";
                        self.$toastr.s("OOOOH LA");
                    })
                    .catch(e => {
                        self.$toastr.e("FUCK.. ppobblemi");
                    })
            },

            close() {
                this.$emit('close');
            }

        }
    }
</script>

<style scoped>

</style>
