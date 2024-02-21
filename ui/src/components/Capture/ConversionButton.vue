<template>
    <button type="button" class="btn btn-outline-warning  mr-1" @click="conversion_clicked()">
        {{ mstrings.conversion }}
    </button>

    <VueModal v-model="showselectmodal" modalClass="col-11 col-lg-6 rounded" :title="mstrings.conversionselect">

        <div v-if="nomaps && loaded" class="alert alert-warning">
            {{ mstrings.nomaps }}
        </div>

        <EasyDataTable v-if="!nomaps && loaded" class="mb-3" :items="maps" :headers="headers" :hide-footer="true">
            <template #item-select="item">
                <input type="radio" :value="item.id" v-model="selection"/>
            </template>
        </EasyDataTable>

        <button class="btn btn-primary mr-1" @click="save_clicked" :disabled="selection == 0">{{ mstrings.save }}</button>
        <button class="btn btn-warning" @click="showselectmodal = false">{{ mstrings.cancel }}</button>
    </VueModal>
</template>

<script setup>
    import {ref, inject, defineProps} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    const mstrings = inject('mstrings');
    const maps = ref([]);
    const nomaps = ref(true);
    const loaded = ref(false);
    const selection = ref(0);
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
            nomaps.value = maps.value.length == 0;
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

    /**
     * Save button has been clicked
     */
    function save_clicked() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_select_conversion',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
                mapid: selection.value,
            }
        }])[0]
        .then(() => {
            toast.success('Map selection saved');
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        showselectmodal.value = false;
    }
</script>