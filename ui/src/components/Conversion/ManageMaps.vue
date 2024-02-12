<template>
    <div class="row">
        <div class="col-xl-6 col-md-8 col-12">
            <h2>{{ mstrings.conversionmaps }}</h2>

            <!-- show available maps -->
            <div v-if="!editmap">
                <div v-if="!maps.length" class="alert alert-warning">
                    {{ mstrings.noconversionmaps }}
                </div>

                <div class="mt-2">
                    <button class="btn btn-primary" @click="add_map">{{ mstrings.addconversionmap }}</button>
                </div>
            </div>

            <!-- Map creation/editing -->
            <div v-if="editmap">
                <EditMap :mapid="editmapid"></EditMap>
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
     * Get all the maps for this course
     */
    onMounted(() => {
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
    });

    /**
     * Add map button has been pressed
     */
    function add_map() {
        editmap.value = true;
        editmapid.value = 0;
    }
</script>