<template>
    <div class="dropdown">
        <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <AddGradeButton :itemid="props.itemid" :userid="props.userid" :name="props.name" :itemname="props.itemname" @gradeadded = "grade_added()"></AddGradeButton>
            <HistoryButton :userid="props.userid" :itemid="props.itemid" :name="props.name" :itemname="props.itemname"></HistoryButton>
        </div>
    </div>
</template>

<script setup>
    import {defineProps, defineEmits} from '@vue/runtime-core';
    import HistoryButton from '@/components/HistoryButton.vue';
    import AddGradeButton from '@/components/AddGradeButton.vue';

    //const mstrings = inject('mstrings');

    const props = defineProps({
            userid: Number,
            itemid: Number,
            itemname: String,
            name: String,
        });

    const emit = defineEmits([
        'gradeadded'
    ]);

    function grade_added() {
        emit('gradeadded');
    }
</script>