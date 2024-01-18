<template>
    <div class="col-12 mt-2">
        <ImportButton :itemid="props.itemid" :groupid="groupid" :userids="props.userids" @imported="emit('refreshtable')"></ImportButton>
        <ReleaseButton v-if="props.gradesimported" :gradeitemid="props.itemid" :groupid="groupid" @released="emit('refreshtable')"></ReleaseButton>
        <ExportWorksheetButton v-if="itemtype=='assign'" :users="props.users" :itemtype="props.itemtype" :itemname="props.itemname"></ExportWorksheetButton>
        <ViewFullNamesButton v-if="props.usershidden" @viewfullnames="viewfullnames"></ViewFullNamesButton>
    </div>
</template>

<script setup>
    import {defineProps, defineEmits} from '@vue/runtime-core';
    import ImportButton from '@/components/ImportButton.vue';
    import ReleaseButton from '@/components/ReleaseButton.vue';
    import ExportWorksheetButton from '@/components/ExportWorksheetButton.vue';
    import ViewFullNamesButton from './ViewFullNamesButton.vue';

    const props = defineProps({
        itemid: Number,
        groupid: Number,
        userids: Array,
        users: Array,
        itemtype: String,
        itemname: String,
        usershidden: Boolean,
        gradesimported: Boolean,
    });

    const emit = defineEmits(['viewfullnames', 'refreshtable']);

    /**
     * Handle viewfullnames
     * @param bool toggleview
     */
     function viewfullnames(toggleview) {
        emit('viewfullnames', toggleview);
    }

</script>