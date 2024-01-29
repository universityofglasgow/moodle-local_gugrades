<template>
    <button type="button" class="btn btn-outline-danger  mr-1" @click="add_multiple_button_click()">
        {{ mstrings.addmultiple }}
    </button>

    <VueModal v-model="showaddmultiplemodal" modalClass="col-11 col-lg-5 rounded" :title="mstrings.addmultiple">
        <FormKit class="border rounded" type="form" @submit="submit_form">
            <FormKit
                type="select"
                :label="mstrings.reasonforadditionalgrade"
                name="reason"
                v-model="reason"
                :options="gradetypes"
                :placeholder="mstrings.selectareason"
                validation="required"
            />
            <FormKit
                v-if = 'reason == "OTHER"'
                :label="mstrings.pleasespecify"
                type="text"
                :placeholder="mstrings.pleasespecify"
                name="other"
                v-model="other"
            />
            <FormKit
                type="textarea"
                label="Notes"
                :placeholder="mstrings.reasonforammendment"
                name="notes"
                v-model="notes"
            />
        </FormKit>

        <div class="row mt-2">
            <div class="col-sm-12">
                <div class="float-right">
                    <button class="btn btn-warning" type="button" @click="showaddmultiplemodal = false">{{  mstrings.cancel }}</button>
                </div>
            </div>
        </div>
    </VueModal>
</template>

<script setup>
    import {ref, defineProps, defineEmits, inject} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    const showaddmultiplemodal = ref(false);
    const mstrings = inject('mstrings');
    const gradetypes = ref({});
    const reason = ref('');
    const admingrade = ref('GRADE'); // GRADE == not an admin grade (a real grade)
    const scale = ref('');
    const grade = ref(0);
    const notes = ref('');
    const other = ref('');

    const emit = defineEmits([
        'gradeadded'
    ]);

    const toast = useToast();

    const props = defineProps({
        userid: Number,
        itemid: Number,
        groupid: Number,
        itemname: String,
        name: String,
    });

    /**
     * Button clicked
     */
    function add_multiple_button_click() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_gradetypes',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
            }
        }])[0]
        .then((result) => {
            gradetypes.value = result;

        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        showaddmultiplemodal.value = true;
    }

    /**
     * Process form submission
     */
    function submit_form() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_write_additional_grade',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
                userid: props.userid,
                admingrade: admingrade.value == 'GRADE' ? '' : admingrade.value,
                reason: reason.value,
                other: other.value,
                scale: scale.value ? scale.value : 0, // WS expecting int
                grade: grade.value,
                notes: notes.value,
            }
        }])[0]
        .then(() => {
            emit('gradeadded');
            toast.success(mstrings.gradeadded);
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        // close the modal
        showaddmultiplemodal.value = false;
    }
</script>