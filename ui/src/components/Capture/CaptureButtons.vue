<template>
    <div class="col-12 mt-2">
        <ImportButton v-if="!converted" :itemid="props.itemid" :groupid="props.groupid" :userids="props.userids" @imported="emit('refreshtable')"></ImportButton>
        <CSVImportButton :itemid="props.itemid" :groupid="props.groupid" :itemname="props.itemname" @uploaded="emit('refreshtable')"></CSVImportButton>
        <AddMultipleButton :itemid="props.itemid"  @editcolumn="multipleclicked"></AddMultipleButton>
        <ReleaseButton v-if="props.gradesimported" :gradeitemid="props.itemid" :groupid="props.groupid" :released="props.released" @released="emit('refreshtable')"></ReleaseButton>
        <ExportWorksheetButton v-if="itemtype=='assign'" :users="props.users" :itemtype="props.itemtype" :itemname="props.itemname"></ExportWorksheetButton>
        <ViewFullNamesButton v-if="props.usershidden"  @viewfullnames="viewfullnames"></ViewFullNamesButton>
        <ConversionButton v-if="showconversion" :itemid="props.itemid" @converted="emit('refreshtable')"></ConversionButton>
        <InfoButton :itemid="props.itemid" size="2"></InfoButton>
        <ReloadButton size="3" @refreshtable="refresh_clicked"></ReloadButton>
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
    import ConversionButton from '@/components/Capture/ConversionButton.vue';
    import InfoButton from '@/components/InfoButton.vue';
    import ReloadButton from '@/components/ReloadButton.vue';

    const props = defineProps({
        itemid: Number,
        groupid: Number,
        userids: Array,
        users: Array,
        itemtype: String,
        itemname: String,
        usershidden: Boolean,
        gradesimported: Boolean,
        showconversion: Boolean,
        converted: Boolean,
        released: Boolean,
    });

    const emit = defineEmits(['viewfullnames', 'refreshtable', 'editcolumn']);

    /**
     * Handle viewfullnames
     * @param bool toggleview
     */
     function viewfullnames(toggleview) {
        emit('viewfullnames', toggleview);
    }

    /**
     * Multiple button has added another column
     * We need to know what it was
     */
    function multipleclicked(cellform) {
        emit('editcolumn', cellform);
    }

    /**
     * Refresh icon was clicked
     */
    function refresh_clicked() {
        emit('refreshtable');
    }

</script>