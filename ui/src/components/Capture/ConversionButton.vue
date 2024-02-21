<template>
    <button type="button" class="btn btn-outline-warning  mr-1" @click="conversion_clicked()">
        {{ mstrings.conversion }}
    </button>

    <VueModal v-model="showselectmodal" modalClass="col-11 col-lg-6 rounded" :title="mstrings.conversionselect">

        <EasyDataTable class="mb-3" :items="maps" :headers="headers" :hide-footer="true">
            <template #item-select="item">
                <input type="checkbox" :value="item.id"/>
            </template>
        </EasyDataTable>

        <button class="btn btn-warning" @click="showselectmodal = false">{{ mstrings.cancel }}</button>
    </VueModal>
</template>

<script setup>
    import {ref, inject, defineProps} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    const mstrings = inject('mstrings');
    const maps = ref([]);
    const loaded = ref(false);
    const showselectmodal = ref(false);

    const toast = useToast();

    const headers = ref([
        {text: mstrings.select, value: 'select'},
        {text: mstrings.name, value: 'name'},
        {text: mstrings.scale, value: 'scale'},
    ]);

    const props = defineProps({
        itemid: Number,
    });

    /**
     * Get maps
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
            window.console.log(maps.value);
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }

    /**
     * Conversion button has been clicked
     */
    function conversion_clicked() {
        get_maps();
        showselectmodal.value = true;
        window.console.log(props.itemid);
    }
</script>