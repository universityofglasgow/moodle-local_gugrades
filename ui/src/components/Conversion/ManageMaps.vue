<template>
    <div id="managemaps">
        <h2>{{ mstrings.conversionmaps }}</h2>

        <!-- show available maps -->
        <div v-if="!editmap && loaded">
            <div v-if="!maps.length" class="alert alert-warning">
                {{ mstrings.noconversionmaps }}
            </div>

            <EasyDataTable v-if="loaded" :headers="headers" :items="maps">
                <template #item-inuse="map">
                    <span v-if="map.inuse">{{ mstrings.yes }}</span>
                    <span v-else>{{ mstrings.no }}</span>
                </template>
                <template #item-actions="map">
                    <div class="btn-group" role="group" aria-label="Actions">
                        <button class="btn btn-primary btn-sm mr-1" @click="edit_clicked(map.id)">{{ mstrings.edit }}</button>
                        <button class="btn btn-primary btn-sm mr-1" :class="{ disabled: map.inuse }" :disabled="map.inuse" @click="delete_clicked(map.id)">{{ mstrings.delete }}</button>
                        <button class="btn btn-primary btn-sm mr-1" @click="export_clicked(map.id)">{{ mstrings.export }}</button>
                    </div>
                </template>
            </EasyDataTable>

            <div class="mt-4">
                <button class="btn btn-primary mr-1" @click="add_map">{{ mstrings.addconversionmap }}</button>
                <button class="btn btn-info" @click="import_clicked">{{ mstrings.importconversionmap }}</button>
            </div>
        </div>

        <!-- Map creation/editing -->
        <div v-if="editmap">
            <EditMap :mapid="editmapid" @close="editmap_closed"></EditMap>
        </div>
    </div>

    <!-- Modal for delete confirm -->
    <ConfirmModal :show="showconfirm" :message="mstrings.deletemapconfirm" @confirm="confirmdelete"></ConfirmModal>

    <!-- Model for map upload -->
    <VueModal v-model="showimportmodal" modalClass="col-11 col-lg-6 rounded" :title="mstrings.importconversionmap">
        <div class="p-4 mb-3 border rounded">
            <button class="btn btn-primary mr-1" type="button" @click="open()">
                Choose files
            </button>
            <button class="btn btn-warning" type="button" :disabled="!files" @click="reset()">
                Reset
            </button>
            <div class="mt-2" v-if="files">
                <p>You have selected: <b>{{ `${files.length} ${files.length === 1 ? 'file' : 'files'}` }}</b></p>
                <li v-for="file of files" :key="file.name">
                    {{ file.name }}
                </li>
            </div>
        </div>
        <button class="btn btn-info mr-1" @click="process_import">{{ mstrings.import }}</button>
        <button class="btn btn-warning" @click="showimportmodal = false">{{ mstrings.cancel }}</button>
    </VueModal>
</template>

<script setup>
    import {ref, inject, onMounted} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";
    import EditMap from '@/components/Conversion/EditMap.vue';
    import ConfirmModal from '@/components/ConfirmModal.vue';
    import { saveAs } from 'file-saver';
    import { useFileDialog } from '@vueuse/core';

    const maps = ref([]);
    const editmap = ref(false);
    const editmapid = ref(0);
    const loaded = ref(false);
    const showconfirm = ref(false);
    const deletemapid = ref(0);
    const showimportmodal = ref(false);
    const mstrings = inject('mstrings');

    const toast = useToast();

    const headers = ref([]);

    const { files, open, reset } = useFileDialog({
        accept: 'text/json', // Set to accept only json files
        multiple: false,
        directory: false, // Select directories instead of files if set true
    })

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
     * Import button clicked
     */
    function import_clicked() {

        // Clear any list of import files
        reset();

        showimportmodal.value = true;
    }

    /**
     * Import json map
     */
    function process_json(jsonmap) {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_import_conversion_map',
            args: {
                courseid: courseid,
                jsonmap: jsonmap
            }
        }])[0]
        .then(() => {
            get_maps();
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }

    /**
     * Import button on modal clicked
     * Proces selected file.
     */
    function process_import() {
        if (!files.value) {
            toast.warning('No file to import');
            return;
        }

        const file = files.value[0];
        const reader = new FileReader();
        reader.addEventListener('load', (event) => {
            const jsondata = event.target.result;

            process_json(jsondata);
            showimportmodal.value = false;
        });
        reader.readAsText(file);
    }

    /**
     * EditMap was closed
     */
    function editmap_closed() {
        editmap.value = false;
        get_maps();
    }

    /**
     * Export button was clicked
     */
    function export_clicked(mapid) {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_conversion_map',
            args: {
                courseid: courseid,
                mapid: mapid,
                schedule: '',
            }
        }])[0]
        .then((result) => {
            const json = JSON.stringify(result, null, 4);
            let filename = result.name + '.json';
            filename = filename.replace(/[/\\?%*:|"<>]/g, '-');
            const blob = new Blob([json], {type: 'text/json;charset=utf-8'});
            saveAs(blob, filename);
            toast.success('Map exported');
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }

    /**
     * Get all the maps for this course
     */
    onMounted(() => {
        headers.value = [
            {text: mstrings.name, value: 'name'},
            {text: mstrings.scalehead, value: 'scale'},
            {text: mstrings.maxgrade, value: 'maxgrade'},
            {text: mstrings.createdby, value: 'createdby'},
            {text: mstrings.createdat, value: 'createdat'},
            {text: mstrings.inuse, value: 'inuse'},
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

    /**
     * Delete map button has been clicked
     */
    function delete_clicked(mapid) {
        showconfirm.value = true;
        deletemapid.value = mapid;
    }

    /**
     * Confirm modal for deletion clicked
     */
    function confirmdelete(confirm) {

        if (confirm) {
            const GU = window.GU;
            const courseid = GU.courseid;
            const fetchMany = GU.fetchMany;

            fetchMany([{
                methodname: 'local_gugrades_delete_conversion_map',
                args: {
                    courseid: courseid,
                    mapid: deletemapid.value,
                }
            }])[0]
            .then((result) => {
                const success = result.success;
                if (success) {
                    toast.success('Deleted');
                } else {
                    toast.error('Map could not be deleted');
                }
                get_maps();
            })
            .catch((error) => {
                window.console.error(error);
                toast.error('Error communicating with server (see console)');
            });
        }

        showconfirm.value = false;
    }
</script>