<template>
    <button type="button" class="btn btn-outline-dark  mr-1" @click="open_modal()">{{ mstrings.exportcapture }}</button>

    <VueModal v-model="showexportmodal" modalClass="col-11 col-lg-6 rounded" :title="mstrings.exportcapture">

        <div class="alert alert-info">
            {{  mstrings.exportcapturehelp }}
        </div>

        <FormKit
            type="form"
            :submit-label="mstrings.export"
            @submit="submit_export_form"
        >
            <FormKit
                v-model="allnone"
                type="checkbox"
                :label="mstrings.allnone"
            />
            <div class="mb-1">&nbsp;</div>
            <FormKit
                v-for="option in options"
                type="checkbox"
                v-model="option.selected"
                :label="option.description"
            />
        </FormKit>

        <div class="row mt-2">
            <div class="col-sm-12">
                <div class="float-right">
                    <button class="btn btn-warning" type="button" @click="close_modal()">{{  mstrings.cancel }}</button>
                </div>
            </div>
        </div>
    </VueModal>
</template>

<script setup>
    import {ref, defineProps, inject, watch} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";
    import { saveAs } from 'file-saver';

    const showexportmodal = ref(false);
    const allnone = ref(false);
    const options = ref([]);
    const mstrings = inject('mstrings');

    const toast = useToast();

    const props = defineProps({
        itemid: Number,
        groupid: Number,
        viewfullnames: Boolean,
    });

    /**
     * Load initial options
     */
    function open_modal() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_capture_export_options',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
                groupid: props.groupid,
            }
        }])[0]
        .then((result) => {
            options.value = result;
        })
        .catch((error) => {
            window.console.error(error);
        });

        showexportmodal.value = true;
    }

    /**
     * Watch for all/none changing
     */
    watch(allnone, (newallnone) => {
        options.value.forEach((option) => {
            option.selected = newallnone;
        });
    });

    /**
     * Download the pro-forma csv file
     */
    function submit_export_form() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_capture_export_data',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
                groupid: props.groupid,
                viewfullnames: props.viewfullnames,
                options: options.value,
            }
        }])[0]
        .then((result) => {
            const csv = result['csv'];
            const d = new Date();
            const filename = props.itemname + '_' + d.toLocaleString() + '.csv';
            const blob = new Blob([csv], {type: 'text/csv;charset=utf-8'});
            saveAs(blob, filename);
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }



    /**
     * Close the modal
     */
    function close_modal() {
        showexportmodal.value = false;
    }
</script>
