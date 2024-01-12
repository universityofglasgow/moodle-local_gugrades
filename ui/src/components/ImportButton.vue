<template>
    <button type="button" class="btn btn-outline-dark  mr-1" @click="import_button_click()">{{ mstrings.importgrades }}</button>

    <Teleport to="body">
        <ModalForm :show="showimportmodal" @close="showimportmodal = false">
            <template #header>
                <h4>{{ mstrings.importgrades }}</h4>
            </template>
            <template #body>
                <div class="alert alert-info">
                    {{  mstrings.importinfo }}
                </div>
                <div v-if="is_importgrades" class="alert alert-warning">
                    {{ mstrings.gradesimported }}
                </div>
                <p><button class="btn btn-primary" @click="importgrades">{{ mstrings.import }}</button></p>
            </template>
        </ModalForm>
    </Teleport>
</template>

<script setup>
    import {ref, defineProps, defineEmits, inject} from '@vue/runtime-core';
    import ModalForm from '@/components/ModalForm.vue';
    import { useToast } from "vue-toastification";

    const props = defineProps({
        userids: Array,
        itemid: Number
    });

    const toast = useToast();

    const emit = defineEmits(['imported']);

    const showimportmodal = ref(false);
    const is_importgrades = ref(false);
    const mstrings = inject('mstrings');

    /**
     * Import grades button clicked
     */
     function importgrades() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_import_grades_users',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
                userlist: props.userids,
            }
        }])[0]
        .then((result) => {
            const importcount = result['importcount'];
            emit('imported');
            if (importcount) {
                toast.success(mstrings.gradesimportedsuccess + ' (' + importcount + ')');
            } else {
                toast.warning(mstrings.nogradestoimport);
            }
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        showimportmodal.value = false;
    }

    /**
     * When button clicked
     * Check for existing grades
     */
    function import_button_click() {
        showimportmodal.value = true;

        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_is_grades_imported',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
            }
        }])[0]
        .then((result) => {
            is_importgrades.value = result.imported;
        })
        .catch((error) => {
            window.console.log(error);
            toast.error('Error communicating with server (see console)');
        });
    }
</script>