<template>
    <div class="border rounded p-2 mt-2">
        <div class="col-12 col-lg-6">
            <LevelOneSelect  @levelchange="levelOneChange"></LevelOneSelect>
            <GroupSelect v-if="level1category" @groupselected="groupselected"></GroupSelect>
        </div>
    </div>



    <div v-if="level1category" class="mt-2">

        <!-- Filter on initials -->
        <NameFilter @selected="filter_selected" ref="namefilterref"></NameFilter>

        <!-- Breadcrumb trail -->
        <div v-if="breadcrumb.length > 1" class="gug_breadcrumb border rounded my-3 p-2 text-white">
            <ul class="list-inline mb-0">
                <li v-for="(item, index) in breadcrumb" :key="item.id" class="list-inline-item">
                    <span v-if="index != 0"> > </span>
                    <a class="text-white" href="#" @click="expand_clicked(item.id)">{{ item.shortname }}</a>
                </li>
            </ul>
        </div>

        <EasyDataTable
            buttons-pagination
            alternating
            table-class-name="aggregation-table"
            :items="users"
            :headers="headers"
        >

            <!-- additional information in header cells -->
            <template #header="header">
                <div class="aggregation-header">
                    <div data-toggle="tooltip" :title="header.fullname" :data-original-title="header.fullname">
                        <div>{{ header.text }}</div>
                        <div v-if="header.weight">{{ header.weight }}%</div>
                        <div v-if="header.gradetype">{{ header.gradetype }} <span v-if="!header.isscale">({{ header.grademax }})</span></div>
                    </div>
                    <div v-if="header.categoryid">
                        <a href="#" @click="expand_clicked(header.categoryid)">
                            <span class="badge badge-light mt-2" >
                                <i class="fa fa-caret-left" aria-hidden="true"></i>
                                {{ mstrings.expand }}
                                <i class="fa fa-caret-right" aria-hidden="true"></i>
                            </span>
                        </a>
                    </div>
                </div>
            </template>

            <!-- User picture column -->
            <template #item-slotuserpicture="item">
                <img :src="item.pictureurl" :alt="item.displayname" class="userpicture defaultuserpic" width="35" height="35"/>
            </template>

            <!-- Resit required -->
            <template #item-resitrequired="item">
                <a href="#" @click="resit_clicked(item.id, !item.resitrequired)">
                    <span v-if="item.resitrequired" class="gug_pill badge badge-pill badge-success">{{ mstrings.yes }}</span>
                    <span v-else class="gug_pill badge badge-pill badge-secondary">{{ mstrings.no }}</span>
                </a>
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
    const categoryid = ref(0);
    const groupid = ref(0);
    const items = ref([]);
    const users = ref([]);
    const columns = ref([]);
    const categories = ref([]);
    const breadcrumb = ref([]);

    let firstname = '';
    let lastname = '';

    /**
     * Capture change to top level category dropdown
     * @param {*} level
     */
    function levelOneChange(level) {
        level1category.value = parseInt(level);
        categoryid.value = level1category.value;
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
     * Resit required 'pill' clicked
     */
    function resit_clicked(userid, required) {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_resit_required',
            args: {
                courseid: courseid,
                userid: userid,
                required: required,
            }
        }])[0]
        .then(() => {
            table_update();
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
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
                weight: column.weight,
                fullname: column.fullname,
                categoryid: column.categoryid,
                gradetype: column.gradetype,
                grademax: column.grademax,
                isscale: column.isscale,
            });
        });

        heads.push({text: mstrings.resitrequired, value: "resitrequired"});

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
                gradecategoryid: categoryid.value,
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
            breadcrumb.value = result.breadcrumb;

            users.value = process_users(users.value);
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }

    /**
     * Expand button was clicked in header
     */
    function expand_clicked(id) {
        categoryid.value = id;
        table_update();
    }
</script>

<style>
    .aggregation-table {
        --easy-table-header-font-size: 14px;
        --easy-table-header-height: 50px;
        --easy-table-header-font-color: #c1cad4;
        --easy-table-header-background-color: #005c8a;

        --easy-table-header-item-padding: 10px 15px;
    }

    .aggregation-header {
        display: flex;
        flex-direction: column;
        text-align: center;
    }

    .gug_breadcrumb {
        background-color: #005c8a;
    }

    .gug_pill {
        font-size: 125%;
    }
</style>