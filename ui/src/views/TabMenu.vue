<template>
    <div id="tabmenu">
        <TabsNav @tabchange="tabChange" :viewaggregation="viewaggregation"></TabsNav>

        <div v-if="currenttab == 'capture'">
            <CaptureTable></CaptureTable>
        </div>

        <div v-if="currenttab == 'conversion'">
            <ConversionPage></ConversionPage>
        </div>

        <div v-if="(currenttab == 'aggregation') && viewaggregation">
            <AggregationTable></AggregationTable>
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
    import {ref, onMounted} from '@vue/runtime-core';
    import TabsNav from '@/components/TabsNav.vue';
    import CaptureTable from '@/views/CaptureTable.vue';
    import AggregationTable from '@/views/AggregationTable.vue';
    import ConversionPage from '@/views/ConversionPage.vue';
    import SettingsPage from '@/views/SettingsPage.vue';
    import AuditPage from '@/views/AuditPage.vue';
    import { useToast } from "vue-toastification";

    const currenttab = ref('capture');
    const level1category = ref(0);
    const showactivityselect = ref(false);
    const itemid = ref(0);
    const enabledashboard = ref(false);
    const viewaggregation = ref(true);

    const toast = useToast();

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

    /**
     * Get current state of dashboard enabled/disabled
     */
     function get_dashboard_enabled() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_dashboard_enabled',
            args: {
                courseid: courseid,
            }
        }])[0]
        .then((result) => {
            const enabled = result.enabled;

            // Bodge to get jQuery needed for Bootstrap JS.
            const $ = window.jQuery;

            if (enabled) {
                $('#mygradeslogo').css('filter', 'grayscale(0)');
            } else {
                $('#mygradeslogo').css('filter', 'grayscale(1)');
            }
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }

    /**
     * Check for aggregation tab permission
     */
     onMounted(() => {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        get_dashboard_enabled();


        fetchMany([{
            methodname: 'local_gugrades_has_capability',
            args: {
                courseid: courseid,
                capability: 'local/gugrades:viewaggregation'
            }
        }])[0]
        .then((result) => {
            viewaggregation.value = result.hascapability;
            window.console.log(viewaggregation.value);
        })
        .catch((error) => {
            window.console.log(error);
        });
    })
</script>