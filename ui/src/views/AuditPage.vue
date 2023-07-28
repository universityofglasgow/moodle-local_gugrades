<template>
    <div>
        <div v-if="!showdata" class="alert alert-primary">
            <MString name="noaudit"></MString>
        </div>
        <div v-if="showdata">
            <EasyDataTable
                alternating
                :headers="headers"
                :items="items"
            >
                <template #item-type="item">
                    <span class="badge" :class="'badge-' + item.bgcolor">{{ item.type }}</span>
                </template>
            </EasyDataTable>
        </div>
    </div>
</template>

<script setup>
    import {ref, onMounted} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";
    import MString from '@/components/MString.vue';
    import { getstrings } from '@/js/getstrings.js';

    const items = ref([]);
    const headers = ref([]);
    const strings = ref([]);
    const totalrows = ref(0);
    const showdata = ref(false);

    const toast = useToast();

    onMounted(() => {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        // Get the moodle strings for this page
        const stringslist = [
            'date',
            'type',
            'gradeitem',
            'description',
            'by',
        ];
        getstrings(stringslist)
        .then(results => {
            Object.keys(results).forEach((name) => {strings.value[name] = results[name]});

            // Headers
            headers.value = [
                {text: strings.value.date, value: 'time'},
                {text: strings.value.by, value: 'fullname'},
                {text: strings.value.type, value: 'type'},
                {text: strings.value.gradeitem, value: 'gradeitem'},
                {text: strings.value.description, value: 'message'}
            ];
        })
        .catch((error) => {
            window.console.log(error);
            toast.error('Error communicating with server (see console)');
        });

        // Get audit trail
        fetchMany([{
            methodname: 'local_gugrades_get_audit',
            args: {
                courseid: courseid,
                userid: 0
            }
        }])[0]
        .then((result) => {
            items.value = result;
            totalrows.value = items.value.length;
            showdata.value = totalrows.value > 0;
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        })
    });
</script>