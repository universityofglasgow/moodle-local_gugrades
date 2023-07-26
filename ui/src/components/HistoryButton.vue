<template>
    <button type="button" class="btn btn-outline-primary btn-sm" @click="read_history()">{{ strings.history }}</button>

    <Teleport to="body">
        <ModalForm :show="showhistorymodal" @close="showhistorymodal = false">
            <template #header>
                <h4>{{ strings.gradehistory }}</h4>
                
            </template>
            <template #body>
                <div>
                    <ul class="list-unstyled">
                        <li><b>Name:</b> {{ name }}</li>
                        <li><b>Item name:</b> {{ itemname }}</li>
                    </ul>
                </div>
                <div v-if="grades.length == 0" class="alert alert-warning">{{ strings.nohistory }}</div>

                <EasyDataTable v-else :headers="headers" :items="grades">
                </EasyDataTable>
            </template>
        </ModalForm>
    </Teleport>
</template>

<script setup>
    import {ref, defineProps, onMounted} from '@vue/runtime-core';
    import ModalForm from '@/components/ModalForm.vue';
    import { getstrings } from '@/js/getstrings.js';
    import { useToast } from "vue-toastification";

    const showhistorymodal = ref(false);
    const grades = ref([]);
    const strings = ref({});
    const headers = ref([]);

    const toast = useToast();



    const props = defineProps({
        userid: Number,
        itemid: Number,
        name: String,
        itemname: String,
    });

    /**
     * Read history on button click
     */
    function read_history() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_history',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
                userid: props.userid,
            }
        }])[0]
        .then((result) => {
            grades.value = result;
        })
        .catch((error) => {
            window.console.log(error);
            toast.error('Error communicating with server (see console)');
        });

        showhistorymodal.value = true;
    }

    /**
     * Load strings (mostly for table) and get initial data for table.
     */
    onMounted(() => {

        // Get the moodle strings for this page
        const stringslist = [
            'history',
            'nohistory',
            'gradehistory',
            'time',
            'grade',
            'current',
            'gradetype',
            'name',
            'activityname',
            'by',
            'comment',
        ];
        getstrings(stringslist)
        .then(results => {
            Object.keys(results).forEach((name) => {strings.value[name] = results[name]});

            headers.value = [
               {text: strings.value.time, value: 'time'},
               {text: strings.value.by, value: 'auditbyname'},
               {text: strings.value.grade, value: 'displaygrade'},
               {text: strings.value.gradetype, value: 'description'},
               {text: strings.value.current, value: 'current'},
               {text: strings.value.comment, value: 'auditcomment'},
            ];
        })
        .catch((error) => {
            window.console.log(error);
            toast.error('Error communicating with server (see console)');
        });
    })
</script>