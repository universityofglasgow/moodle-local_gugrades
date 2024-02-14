<template>
    <div class="row">
        <div class="col-xl-6 col-md-8 col-12">
            <h2>{{ mstrings.conversionmaps }}</h2>

            <!-- show available maps -->
            <div v-if="!editmap">
                <div v-if="!maps.length" class="alert alert-warning">
                    {{ mstrings.noconversionmaps }}
                </div>

                <div v-else class="border rounded p-4">
                    <div class="row">
                        <div class="col"><h4>{{ mstrings.name }}</h4></div>
                        <div class="col"><h4>{{ mstrings.scale }}</h4></div>
                        <div class="col"><h4>{{ mstrings.maxgrade }}</h4></div>
                        <div class="col">&nbsp;</div>
                    </div>
                    <div v-for="map in maps" :key="map.id" class="row lead">
                        <div class="col"><b>{{ map.name }}</b></div>
                        <div class="col">{{ map.scale }}</div>
                        <div class="col">{{ map.maxgrade }}</div>
                        <div class="col">
                            <button class="btn btn-success btn-sm mr-1" @click="edit_clicked(map.id)">{{ mstrings.edit }}</button>
                            <button class="btn btn-danger btn-sm mr-1" :class="{ disabled: map.inuse }">{{ mstrings.delete }}</button>
                            <button class="btn btn-info btn-sm mr-1">{{ mstrings.export }}</button>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-primary" @click="add_map">{{ mstrings.addconversionmap }}</button>
                </div>
            </div>

            <!-- Map creation/editing -->
            <div v-if="editmap">
                <EditMap :mapid="editmapid" @close="editmap_closed"></EditMap>
            </div>
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
    const mstrings = inject('mstrings');

    const toast = useToast();

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