<template>

    <div class="card">

        <div class="card-header text-white bg-dark">
            Nuova parola
        </div>

        <div class="card-body text-dark bg-white">

            <div class="row">

                <div class="col-sm-12">

                    <div class="form-group">

                        <label for="word">Nuova parola</label>
                        <input type="text" class="form-control" id="word" v-model="newWord.word">

                    </div>

                    <div class="form-group">

                        <label for="forbidden_words">Parole vietate</label>
                        <textarea class="form-control" id="forbidden_words" v-model="newWord.forbidden_words">
                        </textarea>

                    </div>

                    <button class="btn btn-danger" @click="close">Chiudi</button>
                    <button class="btn btn-success" @click="save">Salva</button>

                </div>

            </div>

        </div>
    </div>

</template>

<script>

    export default {
        name: "newWordModal",

        data() {
            return {
                newWord: {
                    word: '',
                    forbidden_words: ''
                }
            }
        },

        methods: {
            save() {
                let self = this;
                axios.post('/api/games/one_word_each/words', this.newWord)
                    .then(response => {
                        self.newWord.word = "";
                        self.newWord.forbidden_words = "";
                        self.$toastr.s("OOOOH LA");
                    })
                    .catch(e => {
                        let error = e.response ? e.response.data.error : e;
                        self.$toastr.e(error, "Upsie...");
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
