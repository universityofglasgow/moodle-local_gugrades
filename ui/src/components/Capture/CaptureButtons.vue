<template>
    <div class="col-12 mt-2">
        <ImportButton :itemid="props.itemid" :groupid="props.groupid" :userids="props.userids" @imported="emit('refreshtable')"></ImportButton>
        <CSVImportButton :itemid="props.itemid" :groupid="props.groupid" :itemname="props.itemname" @uploaded="emit('refreshtable')"></CSVImportButton>
        <AddMultipleButton :itemid="props.itemid" :groupid="props.groupid" :itemname="props.itemname"></AddMultipleButton>
        <ReleaseButton v-if="props.gradesimported" :gradeitemid="props.itemid" :groupid="props.groupid" @released="emit('refreshtable')"></ReleaseButton>
        <ExportWorksheetButton v-if="itemtype=='assign'" :users="props.users" :itemtype="props.itemtype" :itemname="props.itemname"></ExportWorksheetButton>
        <ViewFullNamesButton v-if="props.usershidden" @viewfullnames="viewfullnames"></ViewFullNamesButton>
    </div>
</template>

<script setup>
    import {defineProps, defineEmits} from '@vue/runtime-core';
    import ImportButton from '@/components/Capture/ImportButton.vue';
    import CSVImportButton from '@/components/Capture/CSVImportButton.vue';
    import ReleaseButton from '@/components/Capture/ReleaseButton.vue';
    import ExportWorksheetButton from '@/components/Capture/ExportWorksheetButton.vue';
    import ViewFullNamesButton from '@/components/Capture/ViewFullNamesButton.vue';
    import AddMultipleButton from '@/components/Capture/AddMultipleButton.vue';

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