<template>
    <div class="border rounded p-2 mt-2">
        <div class="col-12 col-lg-6">
            <LevelOneSelect  @levelchange="levelOneChange"></LevelOneSelect>
            <GroupSelect v-if="level1category" @groupselected="groupselected"></GroupSelect>
        </div>
    </div>

    <div v-if="level1category" class="mt-2">
        <NameFilter @selected="filter_selected" ref="namefilterref"></NameFilter>
        <EasyDataTable
            buttons-pagination
            alternating
            :items="users"
            :headers="headers"
        >

            <!-- User picture column -->
            <template #item-slotuserpicture="item">
                <img :src="item.pictureurl" :alt="item.displayname" class="userpicture defaultuserpic" width="35" height="35"/>
            </template>

        </EasyDataTable>
    </div>
</template>

<script setup>
    import {ref, defineEmits, computed, inject} from '@vue/runtime-core';
    import LevelOneSelect from '@/components/LevelOneSelect.vue';
    import NameFilter from '@/components/NameFilter.vue';
    import GroupSelect from '@/components/GroupSelect.vue';
    import { useToast } from "vue-toastification";

    const toast = useToast();

    const mstrings = inject('mstrings');

    const level1category = ref(0);
    const groupid = ref(0);
    const items = ref([]);
    const users = ref([]);
    const columns = ref([]);
    const categories = ref([]);

    let firstname = '';
    let lastname = '';

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
     * Add columns to user array
     */
    function process_users(users) {
        users.forEach(user => {
            user.fields.forEach(field => {
                user[field.fieldname] = field.display;
            })
        });

        return users;
    }

    /**
     * Create list of headers for EasyDataTable
     *
     */
    const headers = computed(() => {
        let heads = [];
        heads.push({text: mstrings.userpicture, value: "slotuserpicture"});
        heads.push({text: mstrings.firstnamelastname, value: "displayname", sortable: true})
        heads.push({text: mstrings.idnumber, value: "idnumber", sortable: true});

        columns.value.forEach(column => {
            heads.push({
                text: column.shortname,
                value: column.fieldname,
            });
        })

        return heads;
    });

    /**
     * Firstname/lastname filter selected
     * @param {*} first
     * @param {*} last
     */
     function filter_selected(first, last) {
        if (first == 'all') {
            first = '';
        }
        if (last == 'all') {
            last = '';
        }
        firstname = first;
        lastname = last;

        // Reset page
        //currentpage.value = 1;
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
                firstname: firstname,
                lastname: lastname,
                groupid: groupid.value,
                viewfullnames: false,
            }
        }])[0]
        .then((result) => {
            items.value = result.items;
            categories.value = result.categories;
            users.value = result.users;
            columns.value = result.columns;

            users.value = process_users(users.value);
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }
</script>