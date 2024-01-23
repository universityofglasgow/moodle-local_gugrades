<template>
    <div>
        <TabsNav @tabchange="tabChange"></TabsNav>

        <div v-if="currenttab == 'capture'">
            <CaptureTable></CaptureTable>
        </div>

        <div v-if="currenttab == 'settings'">
            <SettingsPage></SettingsPage>
        </div>

        <div v-if="currenttab == 'audit'">
            <AuditPage></AuditPage>
        </div>
    </div>
</template>

<script setup>
    import {ref} from '@vue/runtime-core';
    import TabsNav from '@/components/TabsNav.vue';
    import CaptureTable from '@/components/CaptureTable.vue';
    import SettingsPage from '@/views/SettingsPage.vue';
    import AuditPage from '@/views/AuditPage.vue';

    const currenttab = ref('capture');
    const level1category = ref(0);
    const showactivityselect = ref(false);
    const itemid = ref(0);

    /**
     * Capture change to capture/aggregate tab
     * @param {*} tab
     */
    function tabChange(tab) {
        currenttab.value = tab;
        level1category.value = 0;
        showactivityselect.value = false;
        itemid.value = 0;
    }
</script>