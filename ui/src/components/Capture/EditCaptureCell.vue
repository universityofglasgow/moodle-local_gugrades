<template>
    <div class="row">
        <div class="col">
            <button class="btn btn-outline-primary btn-sm text-primary">
                ADM
            </button>
        </div>
        <FormKit
            outer-class="col"
            type="text"
            validation-visibility="live"
            maxlength="8"
            name="grade"
            v-model="grade"
            @input="input_updated"
        ></FormKit>
    </div>
</template>

<script setup>
    import {ref, defineProps, onMounted, defineEmits} from '@vue/runtime-core';

    const props = defineProps({
        item: Object,
        column: String,
    });

    const grade = ref('');
    const emits = defineEmits(['editcolumn']);

    // const mstrings = inject('mstrings');

    onMounted(() => {

        // Extract the correct current grade from the item
         grade.value = props.item[props.column];
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