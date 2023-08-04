<template>
    <button type="button" class="btn btn-outline-primary btn-sm" @click="read_history()">{{ strings.history }}</button>

    <Teleport to="body">
        <ModalForm :show="showhistorymodal" @close="showhistorymodal = false">
            <template #header>
                <h4>{{ $strings.gradehistory }}</h4>
                
            </template>
            <template #body>
                <div>
                    <ul class="list-unstyled">
                        <li><b>{{ $strings.name }}:</b> {{ name }}</li>
                        <li><b>{{ $strings.itemname }}:</b> {{ itemname }}</li>
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
    import {ref, defineProps, onMounted, getCurrentInstance} from '@vue/runtime-core';
    import ModalForm from '@/components/ModalForm.vue';
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
        const { proxy } = getCurrentInstance();
        const $strings = proxy.$strings;

        headers.value = [
               {text: $strings.time, value: 'time'},
               {text: $strings.by, value: 'auditbyname'},
               {text: $strings.grade, value: 'displaygrade'},
               {text: $strings.gradetype, value: 'description'},
               {text: $strings.current, value: 'current'},
               {text: $strings.comment, value: 'auditcomment'},
            ];
    })
</script>