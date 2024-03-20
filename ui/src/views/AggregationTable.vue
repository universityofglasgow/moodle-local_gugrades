<template>
    <div class="col-12 col-lg-6">
        <LevelOneSelect  @levelchange="levelOneChange"></LevelOneSelect>
        <GroupSelect v-if="itemid" @groupselected="groupselected"></GroupSelect>
    </div>
</template>

<script setup>
    import {ref, defineEmits} from '@vue/runtime-core';
    import LevelOneSelect from '@/components/LevelOneSelect.vue';
    import { useToast } from "vue-toastification";

    const toast = useToast();

    const level1category = ref(0);
    const groupid = ref(0);
    const items = ref([]);
    const categories = ref([]);

    /**
     * Capture change to top level category dropdown
     * @param {*} level
     */
    function levelOneChange(level) {
        level1category.value = parseInt(level);
        table_update();
    }

    /**
     * Capture change to group
     */
     function groupselected(gid) {
        groupid.value = Number(gid);
        table_update();
    }

    /**
     * Update table (when something changes)
     */
    function table_update() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_aggregation_page',
            args: {
                courseid: courseid,
                gradecategoryid: level1category.value,
                firstname: '',
                lastname: '',
                groupid: groupid.value,
                viewfullnames: false,
            }
        }])[0]
        .then((result) => {
            items.value = result.items;
            categories.value = result.categories;
            window.console.log(items.value);
            window.console.log(categories.value);
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }
</script>