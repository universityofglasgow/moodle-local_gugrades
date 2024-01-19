<template>
    <a class="dropdown-item" href="#" @click="add_grade()">{{ mstrings.addgrade }}</a>

    <VueModal v-model="showaddgrademodal" modalClass="col-11 col-lg-5" :title="mstrings.addgrade">
        <template #content>
            <ul class="list-unstyled">
                    <li><b>{{ mstrings.itemname }}:</b> {{ itemname }}</li>
                    <li><b>{{ mstrings.username }}:</b> {{ name }}</li>
                    <li><b>{{ mstrings.idnumber }}:</b> {{ idnumber }}</li>
                    <li>{{ reason }}</li>
                </ul>
                <FormKit type="form" @submit="submit_form">
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
                        type="select"
                        :label="mstrings.admingrades"
                        name="admingrades"
                        v-model="admingrade"
                        :options="adminmenu"
                    ></FormKit>
                    <FormKit
                        v-if="usescale"
                        type="select"
                        :label="mstrings.grade"
                        :placeholder="mstrings.specifyscale"
                        :disabled="admingrade != 'GRADE'"
                        name="scale"
                        v-model="scale"
                        :options="scalemenu"
                    ></FormKit>
                    <FormKit
                        v-if="!usescale"
                        type="text"
                        :label="mstrings.grade"
                        :placeholder="mstrings.specifygrade"
                        :validation="gradevalidation"
                        :disabled="admingrade != 'GRADE'"
                        number="float"
                        validation-visibility="live"
                        name="grade"
                        v-model="grade"
                    ></FormKit>
                    <FormKit
                        type="textarea"
                        label="Notes"
                        :placeholder="mstrings.reasonforammendment"
                        name="notes"
                        v-model="notes"
                    />
                </FormKit>
        </template>
    </VueModal>
</template>

<script setup>
    import {ref, defineProps, defineEmits, inject} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    const showaddgrademodal = ref(false);
    const mstrings = inject('mstrings');
    const gradetypes = ref({});
    const idnumber = ref('');
    const reason = ref('');
    const admingrade = ref('GRADE'); // GRADE == not an admin grade (a real grade)
    const scale = ref('');
    const grade = ref(0);
    const notes = ref('');
    const other = ref('');
    const usescale = ref(false);
    const grademax = ref(0);
    const scalemenu = ref([]);
    const adminmenu = ref([]);
    const gradevalidation = ref([]);

    const emit = defineEmits([
        'gradeadded'
    ]);

    const toast = useToast();

    const props = defineProps({
        userid: Number,
        itemid: Number,
        itemname: String,
        name: String,
    });

    /**
     * Get data for form
     */
    function add_grade() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_add_grade_form',
            args: {
                courseid: courseid,
                gradeitemid: props.itemid,
                userid: props.userid,
            }
        }])[0]
        .then((result) => {
            gradetypes.value = result['gradetypes'];
            idnumber.value = result['idnumber'];
            usescale.value = result['usescale'];
            grademax.value = result['grademax'];
            scalemenu.value = result['scalemenu'];
            adminmenu.value = result['adminmenu'];

            // Add 'use grade' option onto front of adminmenu
            adminmenu.value.unshift({
                value: 'GRADE',
                label: mstrings.selectnormalgrade,
            });

            gradevalidation.value = [
                ['required'],
                ['number'],
                ['between', 0, result['grademax']],
            ];
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        showaddgrademodal.value = true;
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
        showaddgrademodal.value = false;
    }
</script>