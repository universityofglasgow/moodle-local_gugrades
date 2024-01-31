<template>
    <div class="row">
        <FormKit
            type="select"
            name="admingrades"
            outer-class="col"
            v-model="admingrade"
            :options="adminmenu"
        ></FormKit>
        <FormKit
            v-if="!props.usescale"
            outer-class="col"
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
            outer-class="col"
            :disabled="admingrade != 'GRADE'"
            name="scale"
            v-model="grade"
            :options="scalemenu"
        ></FormKit>
    </div>
</template>

<script setup>
    import {ref, defineProps, onMounted, defineEmits} from '@vue/runtime-core';

    const props = defineProps({
        item: Object,
        column: String,
        usescale: Boolean,
        scalemenu: Array,
        adminmenu: Array,
    });

    const grade = ref('');
    const admingrade = ref('GRADE');
    const emits = defineEmits(['editcolumn']);

    // const mstrings = inject('mstrings');

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
        emits('editcolumn', {
            grade: grade.value,
            id: props.item.id,
        });
    }

</script>