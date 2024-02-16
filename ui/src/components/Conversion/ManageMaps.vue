<template>
    <div id="managemaps">
        <h2>{{ mstrings.conversionmaps }}</h2>

        <!-- show available maps -->
        <div v-if="!editmap && loaded">
            <div v-if="!maps.length" class="alert alert-warning">
                {{ mstrings.noconversionmaps }}
            </div>

            <EasyDataTable v-if="loaded" :headers="headers" :items="maps">
                <template #item-actions="map">
                    <button class="btn btn-success btn-sm mr-1" @click="edit_clicked(map.id)">{{ mstrings.edit }}</button>
                    <button class="btn btn-danger btn-sm mr-1" :class="{ disabled: map.inuse }">{{ mstrings.delete }}</button>
                    <button class="btn btn-info btn-sm mr-1">{{ mstrings.export }}</button>
                </template>
            </EasyDataTable>

            <div class="mt-4">
                <button class="btn btn-primary" @click="add_map">{{ mstrings.addconversionmap }}</button>
            </div>
        </div>

        <!-- Map creation/editing -->
        <div v-if="editmap">
            <EditMap :mapid="editmapid" @close="editmap_closed"></EditMap>
        </div>
    </div>
</template>

<script setup>
    import {ref, inject, onMounted} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";
    import EditMap from '@/components/Conversion/EditMap.vue';

    const maps = ref([]);
    const editmap = ref(false);
    const editmapid = ref(0);
    const loaded = ref(false);
    const mstrings = inject('mstrings');

    const toast = useToast();

    const headers = ref([]);

    /**
     * Get/update the maps
     */
    function get_maps() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_conversion_maps',
            args: {
                courseid: courseid,
            }
        }])[0]
        .then((result) => {
            maps.value = result;
            loaded.value = true;
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }

    /**
     * Edit button was clicked
     */
    function edit_clicked(mapid) {
        editmapid.value = mapid;
        editmap.value = true;
    }

    /**
     * EditMap was closed
     */
    function editmap_closed() {
        editmap.value = false;
        get_maps();
    }

    /**
     * Get all the maps for this course
     */
    onMounted(() => {
        headers.value = [
            {text: mstrings.name, value: 'name'},
            {text: mstrings.scale, value: 'scale'},
            {text: mstrings.maxgrade, value: 'maxgrade'},
            {text: mstrings.createdby, value: 'createdby'},
            {text: mstrings.createdat, value: 'createdat'},
            {text: '', value: 'actions'},
        ];
        get_maps();
    });

    /**
     * Add map button has been pressed
     */
    function add_map() {
        editmap.value = true;
        editmapid.value = 0;
    }
</script>