<template>
    <div>
        <h2>{{ mstrings.conversionmaps }}</h2>

        <div v-if="!maps.length" class="alert alert-warning">
            {{ mstrings.noconversionmaps }}
        </div>

        <div class="mt-2">
            <button class="btn btn-primary">{{ mstrings.addconversionmap }}</button>
        </div>
    </div>
</template>

<script setup>
    import {ref, inject, onMounted} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    const maps = ref([]);
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
</script>