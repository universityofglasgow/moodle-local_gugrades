<template>
    <button type="button" class="btn btn-outline-info  mr-1" @click="showcsvmodal = true">{{ mstrings.csvimport }}</button>

    <VueModal v-model="showcsvmodal" modalClass="col-11 col-lg-5 rounded" :title="mstrings.csvimport">

        <p>{{  mstrings.csvdownloadhelp }}</p>

        <button class="btn btn-primary" type="button" @click="csv_download()">{{  mstrings.csvdownload }}</button>

        <div class="row mt-2">
            <div class="col-sm-12">
                <div class="float-right">
                    <button class="btn btn-warning" type="button" @click="showcsvmodal = false">{{  mstrings.close }}</button>
                </div>
            </div>
        </div>
    </VueModal>
</template>

<script setup>
    import {ref, defineProps, inject} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";
    import { saveAs } from 'file-saver';

    const showcsvmodal = ref(false);
    const mstrings = inject('mstrings');

    const toast = useToast();

    const props = defineProps({
        itemid: Number,
        groupid: Number,
        itemname: String,
    });

    /**
     * Download the empty csv file
     */
    function csv_download() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_csv_download',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
                groupid: props.groupid,
            }
        }])[0]
        .then((result) => {
            const csv = result['csv'];
            const d = new Date();
            const filename = props.itemname + d.toLocaleString() + '.csv';
            const blob = new Blob([csv], {type: 'text/csv;charset=utf-8'});
            saveAs(blob, filename);
        })
        .catch((error) => {
            window.console.log(error);
            toast.error('Error communicating with server (see console)');
        });
    }


</script>
