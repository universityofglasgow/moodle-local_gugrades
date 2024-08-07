<template>
    <button type="button" class="btn btn-outline-primary  mr-1" @click="conversion_clicked()">
        {{ mstrings.convertgrades }}
    </button>

    <VueModal v-model="showselectmodal" modalClass="col-11 col-lg-6 rounded" :title="mstrings.conversionselect">

        <!--  If no map is currently selected, show the selection dialogue -->
        <div v-if="!selection">
            <div v-if="nomaps && loaded" class="alert alert-warning">
                {{ mstrings.nomaps }}
            </div>

            <div v-else class="alert alert-warning">
                {{ mstrings.noimportafterconversion }}
            </div>

            <EasyDataTable v-if="!nomaps && loaded" :items="maps" :headers="headers" :hide-footer="true">
                <template #item-select="item">
                    <input type="radio" :value="item.id" v-model="mapid"/>
                </template>
            </EasyDataTable>

            <div>
                <button class="btn btn-primary mr-1" @click="save_clicked" :disabled="mapid == 0">{{ mstrings.save }}</button>
                <button class="btn btn-warning" @click="showselectmodal = false">{{ mstrings.cancel }}</button>
            </div>
        </div>

        <!-- if a map is selected then show warning message and option to remove -->
        <div v-if="selection">
            <div class="alert alert-danger">
                {{ mstrings.conversionremovewarning }}
            </div>
            <div class="mt-1 mb-4">
                <button class="btn btn-danger rounded" @click="remove_clicked">{{ mstrings.remove }}</button>
            </div>
        </div>
    </VueModal>
</template>

<script setup>
    import {ref, inject, defineProps, defineEmits} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    const mstrings = inject('mstrings');
    const maps = ref([]);
    const nomaps = ref(true);
    const loaded = ref(false);
    const selection = ref(0);
    const mapid = ref(0);
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

    const emits = defineEmits(['converted']);

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
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }

    /**
     * Get currently selected map (if any)
     */
    function get_selected() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_selected_conversion',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
            }
        }])[0]
        .then((result) => {

            // id==0 if no selection (which is fine).
            selection.value = result.id;
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
        get_selected();
        showselectmodal.value = true;
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
                mapid: mapid.value,
            }
        }])[0]
        .then(() => {
            toast.success('Map selection saved');
            emits('converted');
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        showselectmodal.value = false;
    }

    /**
     * Remove button has been clicked
     *
     */
    function remove_clicked() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_select_conversion',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
                mapid: 0,
            }
        }])[0]
        .then(() => {
            toast.success('Map selection removed');
            emits('converted');
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        showselectmodal.value = false;
    }
</script>