<template>
    <!-- EXAMPLE::PROGETTO lista studenti front end -->
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Studenti
            </h2>
        </template>

        <div class="py-12 mx-10">
            <div class="flex items-center justify-end w-full mb-4">
                <v-btn
                    @click="createStudentRedirect"
                    tile
                    color="blue">
                    <v-icon left>
                        mdi-account-plus
                    </v-icon>
                    Aggiungi Studente
                </v-btn>
            </div>
            <v-card>
                <v-card-title>
                    <v-text-field
                        v-model="form.search"
                        append-icon="mdi-magnify"
                        label="Cerca..."
                        single-line
                        hide-details
                    ></v-text-field>
                </v-card-title>
                <v-data-table
                    class="cursor-pointer"
                    :options.sync="options"
                    :custom-sort="getStudents"
                    :multi-sort="false"
                    :headers="headers"
                    :items="students.data"
                    :items-per-page="form.items_per_page"
                    @click:row="handleClick"
                    hide-default-footer
                ></v-data-table>
                <v-pagination
                    class="mb-5 mt-5"
                    :disabled="students.last_page <= 1"
                    v-model="form.current_page"
                    :length="students.last_page"
                ></v-pagination>
            </v-card>
        </div>
        <div>
        </div>

    </app-layout>
</template>

<script>
import AppLayout from "../../Layouts/AppLayout";
import JetButton from "../../Jetstream/Button";
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'
import { mapValues } from "lodash";

export default {
    components: {
        AppLayout,
        JetButton,
    },

    data() {
        return {
            options: {},
            form: {
                search: '',
                sort_order: null,
                sort_field: null,
                items_per_page: this.students.per_page,
                current_page: this.students.current_page
            },
            headers: [
                {
                    text: 'Nome',
                    align: 'start',
                    sortable: true,
                    value: 'first_name',
                },
                { text: 'Cognome', value: 'last_name', sortable: true },
                { text: 'Email', value: 'user.email', sortable: true },
                { text: 'Età', value: 'age', sortable: true },
            ],
        }
    },

    watch: {
        options: {
            handler () {
                this.form.sort_field = this.options.sortBy.length > 0 ? this.options.sortBy[0] : null;
                this.form.sort_order = this.options.sortDesc.length > 0 ? (this.options.sortDesc[0] ? 'desc' : 'asc') : null;
            },
            deep: true,
        },

        form: {
            handler: throttle(function() {
                this.loadData();
            }, 150),
            deep: true,
        },
    },

    props: {
        students: Object,
    },

    metaInfo: { title: 'Studenti' },

    methods: {
        getStudents() {
            return this.students.data;
        },

        handleClick(value) {
            this.$inertia.get(this.route('students.edit', value.id));
        },

        loadData() {
            let query = pickBy(this.form);
            let newQuery = {};
            Object.keys(query).forEach(function (key) {
                newQuery[key] = query[key];
            });
            this.$inertia.put(window.location.pathname, newQuery, { preserveScroll: true, preserveState: true });
        },

        createStudentRedirect() {
            this.$inertia.post(this.route('students.create'));
        },

        reset() {
            this.form = mapValues(this.form, () => null)
        },
    },
}
</script>
