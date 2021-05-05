<template>
    <!-- EXAMPLE::PROGETTO form creazione studente front end -->
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Aggiungi Studente
            </h2>
        </template>

        <div class="py-12 mx-10">
            <v-card>
                <form @submit.prevent="" class="mx-5 my-5">
                    <v-text-field
                        class="mt-6"
                        v-model="form.first_name"
                        :error-messages="firstNameErrors"
                        :counter="20"
                        label="Nome"
                        required
                        @input="$v.form.first_name.$touch()"
                        @blur="$v.form.first_name.$touch()" />
                    <v-text-field
                        class="mt-6"
                        v-model="form.last_name"
                        :error-messages="lastNameErrors"
                        :counter="20"
                        label="Cognome"
                        required
                        @input="$v.form.last_name.$touch()"
                        @blur="$v.form.last_name.$touch()" />
                    <v-text-field
                        class="mt-6"
                        v-model="form.email"
                        :error-messages="emailErrors"
                        :counter="50"
                        label="E-mail"
                        required
                        @input="$v.form.email.$touch()"
                        @blur="$v.form.email.$touch()" />
                    <v-select
                        class="mt-6"
                        v-model="form.age"
                        :items="ages"
                        label="Età"
                        :error-messages="ageErrors"
                        @change="$v.form.age.$touch()"
                        @blur="$v.form.age.$touch()" />
                    <v-text-field
                        class="mt-6"
                        v-model="form.password"
                        :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                        :error-messages="passwordErrors"
                        :type="showPassword ? 'text' : 'password'"
                        name="password"
                        label="Password"
                        hint="Almeno 6 caratteri, massimo 20"
                        counter
                        @click:append="showPassword = !showPassword"
                        required
                        @input="$v.form.password.$touch()"
                        @blur="$v.form.password.$touch()" />
                    <div class="v-text-field__details">
                        <div class="v-messages theme--light error--text" role="alert">
                                <div class="v-messages__message" v-for="error in otherErrors">
                                    <span class="red--text">{{ error }}</span>
                                </div>
                        </div>
                    </div>
                    <div class=" mt-6">
                        <v-btn
                            :disabled="loading"
                            class="mr-4"
                            @click="submit">
                            Salva
                        </v-btn>
                        <v-btn @click="clear" :disabled="loading">
                            Reset
                        </v-btn>
                    </div>
                </form>
            </v-card>
        </div>
        <v-snackbar
            v-model="this.reset"
            absolute
            bottom
            right
            color="blue"
            :vertical="true">
            Campi resettati
        </v-snackbar>
        <v-snackbar
            v-model="this.serverErrors"
            absolute
            bottom
            right
            color="red"
            :vertical="true">
            Errore durante il salvataggio
        </v-snackbar>
        <v-snackbar
            v-model="this.form.recentlySuccessful"
            absolute
            bottom
            right
            color="green"
            :vertical="true">
            Salvataggio avvenuto con successo
        </v-snackbar>
    </app-layout>
</template>

<script>
import AppLayout from "../../Layouts/AppLayout";
import JetButton from "../../Jetstream/Button";
import { validationMixin } from 'vuelidate'
import { required, maxLength, minLength, email, numeric, between } from 'vuelidate/lib/validators'

export default {
    components: {
        AppLayout,
        JetButton,
    },

    mixins: [validationMixin],

    validations: {
        form: {
            first_name: { required, maxLength: maxLength(20) },
            last_name: { required, maxLength: maxLength(20) },
            //in questo caso la password non è required perché si tratta di un update
            //ergo se viene messa sostituisce la vecchia, altrimenti lascia quella vecchia
            password: { maxLength: maxLength(20), minLength: minLength(4) },
            age: { required, between: between(10, 18), numeric },
            email: { required, email },
        }
    },

    props: {
        student: Object
    },

    data() {
        return {
            showPassword: false,
            loading: false,
            reset: false,
            serverErrors: false,
            //non è il modo corretto di farlo
            ages: ['-- Seleziona --', 10, 11, 12, 13, 14, 15, 16, 17, 18],
            //il modo corretto è creare un metodo che returna un array creato dinamicamente
            form: this.$inertia.form({
                id: this.student.id,
                first_name: this.student.first_name,
                last_name: this.student.last_name,
                user_id: this.student.user_id,
                email: this.student.email,
                password: this.student.password,
                age: this.student.age
            }, {
                bag: 'validation',
                errors: Object,
                resetOnSuccess: false,
            }),
        }
    },

    computed: {
        firstNameErrors () {
            const errors = []
            //errori frontend
            if (!this.$v.form.first_name.$dirty) return errors
            !this.$v.form.first_name.required && errors.push('Il nome è obbligatorio')
            !this.$v.form.first_name.maxLength && errors.push('Il nome deve essere massimo di 20 caratteri!')
            //errori provenienti dal backend
            if (this.form.errors.validation) {
                if (this.form.errors.validation.first_name) {
                    errors.push(this.form.errors.validation['first_name'])
                }
            }
            return errors
        },

        lastNameErrors () {
            const errors = []
            if (!this.$v.form.last_name.$dirty) return errors
            !this.$v.form.last_name.required && errors.push('Il cognome è obbligatorio')
            !this.$v.form.last_name.maxLength && errors.push('Il cognome deve essere massimo di 20 caratteri!')
            if (this.form.errors.validation) {
                if (this.form.errors.validation.last_name) {
                    errors.push(this.form.errors.validation['last_name'])
                }
            }
            return errors
        },

        emailErrors () {
            const errors = []
            if (!this.$v.form.email.$dirty) return errors
            !this.$v.form.email.required && errors.push('L\'email è obbligatoria')
            !this.$v.form.email.email && errors.push('Inserire una email valida')
            if (this.form.errors.validation) {
                if (this.form.errors.validation.email) {
                    errors.push(this.form.errors.validation['email'])
                }
            }
            return errors
        },

        ageErrors () {
            const errors = []
            if (!this.$v.form.age.$dirty) return errors
            !this.$v.form.age.numeric && errors.push('Seleziona l\'età')
            !this.$v.form.age.required && errors.push('L\'età è obbligatoria')
            !this.$v.form.age.between && errors.push('L\'età deve essere compresa tra 10 e 18')
            if (this.form.errors.validation) {
                if (this.form.errors.validation.age) {
                    errors.push(this.form.errors.validation['age'])
                }
            }
            return errors
        },

        passwordErrors () {
            const errors = []
            if (!this.$v.form.password.$dirty) return errors
            !this.$v.form.password.minLength && errors.push('La password deve contenere minimo 4 caratteri')
            !this.$v.form.password.maxLength && errors.push('La password deve contenere massimo 20 caratteri')
            if (this.form.errors.validation) {
                if (this.form.errors.validation.password) {
                    errors.push(this.form.errors.validation['password'])
                }
            }
            return errors
        },

        otherErrors() {
            const errors = []
            if (this.form.errors.validation) {
                if (this.form.errors.validation.user_id) {
                    errors.push(this.form.errors.validation['user_id'])
                }
            }
            return errors;
        },
    },

    watch: {
        'form.processing': {
            deep: true,
            immediate: true,
            handler(newValue, oldValue) {
                if (typeof(oldValue) === 'undefined') {
                    return;
                }
                if (newValue) {
                    this.loading = true;
                    return;
                }
                this.loading = false;
                if (this.form.recentlySuccessful) {
                    //salvataggio riuscito
                } else {
                    this.serverErrors = true;
                    setTimeout(() => this.serverErrors = false, 5000);
                }
            }
        }
    },

    methods: {
        submit () {
            this.$v.$touch()

            if (!this.$v.$error) {
                this.form
                    .transform(data => ({
                        ... data
                    }))
                    .put('/studenti/' + this.student.id, {
                        preserveScroll: true
                    })
            }
        },

        clear () {
            //resetto gli errori
            this.$v.$reset()
            //resetto i valori originali del form
            this.form.first_name = this.student.first_name
            this.form.last_name = this.student.last_name
            this.form.email = this.student.email
            this.form.password = ''
            this.form.age = this.student.age
            this.reset = true
            //dopo 2 secondi setto la variabile reset nuovamente a false
            setTimeout(() => this.reset = false, 2000);
        },
    },
}
</script>
