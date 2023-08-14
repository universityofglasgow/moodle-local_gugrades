<template>
    <button type="button" class="btn btn-outline-primary btn-sm" @click="add_grade()">Add grade</button>

    <Teleport to="body">
        <ModalForm :show="showaddgrademodal" @close="showaddgrademodal = false">
            <template #header>
                <h4>{{ mstrings.addgrade }}</h4>
                
            </template>
            <template #body>
                <ul class="list-unstyled">
                    <li><b>{{ mstrings.itemname }}:</b> {{ itemname }}</li>
                    <li><b>{{ mstrings.username }}:</b> {{ name }}</li>
                    <li><b>{{ mstrings.idnumber }}:</b> {{ idnumber }}</li>
                </ul>
                <FormKit type="form">
                    <FormKit
                        type="select"
                        :label="mstrings.reasonforadditionalgrade"
                        name="reason"
                        v-model="reason"
                        :options="gradetypes"
                    />
                    <FormKit
                        v-if = 'reason == "OTHER"'
                        :label="mstrings.pleasespecify"
                        type="text"
                        :placeholder="mstrings.pleasespecify"
                        name="other"
                    />
                    <FormKit
                        v-if='usescale'
                        type="select"
                        :label="mstrings.grade"
                        name="scale"
                        :options="scalemenu"
                    ></FormKit>
                    <FormKit
                        v-if="!usescale"
                        type="text"
                        :label="mstrings.grade"
                        :validation="gradevalidation"
                        validation-visibility="live"
                        name="grade"
                    ></FormKit>
                    <FormKit
                        type="textarea"
                        label="Notes"
                        :placeholder="mstrings.reasonforammendment"
                        name="notes"
                    />
                </FormKit>
            </template>
        </ModalForm>
    </Teleport>
</template>

<script setup>
    import {ref, defineProps, inject} from '@vue/runtime-core';
    import ModalForm from '@/components/ModalForm.vue';
    import { useToast } from "vue-toastification";

    const showaddgrademodal = ref(false);
    const mstrings = inject('mstrings');
    const gradetypes = ref({});
    const idnumber = ref('');
    const reason = ref('');
    const usescale = ref(false);
    const grademax = ref(0);
    const scalemenu = ref([]);
    const gradevalidation = ref([]);

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

            gradevalidation.value = [
                ['required'],
                ['between', 0, result['grademax']],
            ];

            window.console.log(result);
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        })

        showaddgrademodal.value = true;
    }
</script>