<template>
    <div class="row">
        <FormKit
            type="select"
            name="admingrades"
            outer-class="col pr-1"
            v-model="admingrade"
            :options="adminmenu"
            @input="input_updated"
        ></FormKit>
        <FormKit
            v-if="!props.usescale"
            outer-class="col pl-0"
            type="text"
            validation-visibility="live"
            maxlength="8"
            name="grade"
            v-model="grade"
            :disabled="admingrade != 'GRADE'"
            @input="input_updated"
        ></FormKit>
        <FormKit
            v-if="props.usescale"
            type="select"
            outer-class="col pl-0"
            :disabled="admingrade != 'GRADE'"
            name="scale"
            v-model="scale"
            :options="scalemenu"
            @input="input_updated"
        ></FormKit>
    </div>
</template>

<script setup>
    import {ref, defineProps, onMounted, onBeforeUnmount, defineEmits} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    // (item.id is current userid)
    // (item.reason is the reason/gradetype)
    // (item.other is the other text)
    // (item.gradeitemid)
    const props = defineProps({
        item: Object,
        gradeitemid: Number,
        column: String,
        gradetype: String,
        usescale: Boolean,
        scalemenu: Array,
        adminmenu: Array,
    });

    const grade = ref(0);
    const scale = ref(0);
    const admingrade = ref('GRADE');
    const edited = ref(false);
    const toast = useToast();

    const emits = defineEmits(['gradewritten']);

    onMounted(() => {

        // Extract the correct current grade from the item
        const value = props.item[props.column];
        grade.value = value;

        // Could is be an admingrade?
        props.adminmenu.forEach((adminitem) => {
            if (adminitem.value == value) {
                admingrade.value = value;
                grade.value = '';
            }
        })
    });

    /**
     * Change made to edit box
     *
     */
    function input_updated() {

        // If anything has changed, flag that we will need
        // to save it at some point.
        edited.value = true;
    }

    /**
     * When this component closes, save the data
     */
    onBeforeUnmount(() => {

        // if this cell hasn't been edited then nothing to do!
        if (!edited.value) {
            return
        }

        const userid = props.item.id;
        const reason = props.gradetype;
        const other = props.item.other;
        const gradeitemid = props.gradeitemid;
        const saveadmingrade = admingrade.value == 'GRADE' ? '' : admingrade.value;
        const savescale = (admingrade.value == 'GRADE') && props.usescale ? scale.value : 0;
        const savegrade = (admingrade.value == 'GRADE') && !props.usescale ? grade.value : 0;
        const notes = '';

        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_write_additional_grade',
            args: {
                courseid: courseid,
                gradeitemid: gradeitemid,
                userid: userid,
                admingrade: saveadmingrade,
                reason: reason,
                other: other,
                scale: savescale,
                grade: savegrade,
                notes: notes,
            }
        }])[0]
        .then(() => {
            //emits('gradewritten');
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        emits('gradewritten');
    });

</script>