<template>
    <button type="button" class="btn btn-outline-info  mr-1" @click="showcsvmodal = true">{{ mstrings.csvimport }}</button>

    <VueModal v-model="showcsvmodal" modalClass="col-11 col-lg-5 rounded" :title="mstrings.csvimport">

        <p>{{  mstrings.csvdownloadhelp }}</p>

        <button class="btn btn-primary" type="button" @click="csv_download()">{{  mstrings.csvdownload }}</button>

        <FormKit class="border rounded" type="form" @submit="submit_csv_form">
            <FormKit
                type="file"
                name="csvupload"
                label="CSV Upload"
                accept=".csv"
                help="Upload CSV blah blah."
                multiple="false"
                />
        </FormKit>

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
    const csvcontent = ref('');
    const mstrings = inject('mstrings');

    const toast = useToast();

    const props = defineProps({
        itemid: Number,
        groupid: Number,
        itemname: String,
    });

    /**
     * Download the pro-forma csv file
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

    /**
     * Handle the submitted upload form
     * Got working more by luck....
     */
    function submit_csv_form(data) {
        window.console.log(data.csvupload[0]);
        const reader = new FileReader();
        reader.addEventListener('load', (event) => {
            csvcontent.value = event.target.result;
        });
        reader.readAsText(data.csvupload[0].file);
    }


</script>
