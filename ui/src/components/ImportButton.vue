<template>
    <button type="button" class="btn btn-outline-dark  mr-1" @click="import_button_click()">
        <span v-if="groupimport">{{ mstrings.importgradesgroup }}</span>
        <span v-else>{{ mstrings.importgrades }}</span>
    </button>

    <VueModal v-model="showimportmodal" modalClass="col-11 col-lg-5" :title="mstrings.importgrades">
        <div v-if="is_importgrades" class="alert alert-warning">
            {{ mstrings.gradesimported }}
            <p v-if="groupimport" class="mt-1"><b>{{ mstrings.importinfogroup }}</b></p>
        </div>
        <div v-else class="alert alert-info">
            {{ mstrings.importinfo }}
            <p v-if="groupimport" class="mt-1"><b>{{ mstrings.importinfogroup }}</b></p>
        </div>
        <div class="mt-2 pt-2 border-top">
            <button
                    class="btn btn-primary mr-1"
                    @click="importgrades()"
                    >{{ mstrings.yesimport }}
            </button>
            <button
                class="btn btn-warning"
                @click="showimportmodal = false"
                >{{ mstrings.cancel }}
            </button>
        </div>
    </VueModal>
</template>

<script setup>
    import {ref, defineProps, defineEmits, inject, computed} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    const props = defineProps({
        userids: Array,
        itemid: Number,
        groupid: Number,
    });

    const toast = useToast();
    const groupimport = computed(() => {
        return props.groupid > 0;
    });

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
                groupid: props.groupid,
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