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
                    :multi-sort="false"
                    :sort-by.sync="sortBy"
                    :sort-desc.sync="sortDesc"
                    :headers="headers"
                    :items="students.data"
                ></v-data-table>
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
import {mapValues} from "lodash";

export default {
    components: {
        AppLayout,
        JetButton,
    },

    data() {
        return {
            sortBy: '',
            sortDesc: false,
            form: {
                search: '',
                sort_order: null,
                sort_field: null,
            },
            headers: [
                {
                    text: 'Nome',
                    align: 'start',
                    sortable: true,
                    value: 'first_name',
                },
                { text: 'Cognome', value: 'last_name', sortable: true },
                { text: 'Email', value: 'email', sortable: true },
                { text: 'Age', value: 'age', sortable: true },
            ],
        }
    },

    watch: {
        sortBy: function () {
            this.form.sort_field = this.getSortField()
            this.form.sort_order = this.getSortOrder()
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
        getSortField() {
            return Array.isArray(this.sortBy) && this.sortBy.length > 0 ? this.sortBy[0] : (this.sortBy ?? '');
        },

        getSortOrder() {
            let sortDesc = Array.isArray(this.sortDesc) && this.sortDesc.length > 0 ? this.sortDesc[0] : this.sortDesc;

            return sortDesc ? 'desc' : 'asc';
        },

        loadData() {
            let query = pickBy(this.form);
            let newQuery = {};
            Object.keys(query).forEach(function (key) {
                newQuery[key] = query[key];
            });
            this.$inertia.put(window.location.pathname, newQuery, { preserveScroll: true, preserveState: true });
            // this.deleteOldSelected();
            // this.$scrollTo($(this.uniqueId), 10, {force:true, offset: 0});
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
