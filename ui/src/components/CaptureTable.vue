<template>
    <div>
        <div v-if="!gradesupported">
            <div class="alert alert-danger mt-2">
                <span v-html="mstrings.gradenotsupported"></span>
            </div>
        </div>

        <div v-else>
            <div class="border rounded p-2 py-4 mt-2">
                <ImportButton :itemid="parseInt(itemid)" :userids="userids" @imported="gradesimported()"></ImportButton>
                <ReleaseButton :gradeitemid="parseInt(itemid)" @released="gradesreleased()"></ReleaseButton>
                <ExportWorksheetButton :users="users" :itemtype="itemtype" :itemname="itemname"></ExportWorksheetButton>
                <ViewFullNamesButton v-if="usershidden" @viewfullnames="viewfullnames"></ViewFullNamesButton>
            </div>

            <NameFilter v-if="!usershidden" @selected="filter_selected" ref="namefilterref"></NameFilter>

            <PreLoader v-if="!loaded"></PreLoader>

            <div v-if="showtable">
                <EasyDataTable
                    buttons-pagination
                    alternating
                    :items="users"
                    :headers="headers"
                    >
                    <template #item-slotuserpicture="item">
                        <img :src="item.pictureurl" :alt="item.displayname" class="userpicture defaultuserpic" width="35" height="35"/>
                    </template>
                    <template #item-grade="item">
                        <CaptureGrades :grades="item.grades"></CaptureGrades>
                    </template>
                    <template #item-actions="item">
                        <CaptureMenu :itemid="itemid" :userid="parseInt(item.id)" :name="item.displayname" :itemname="itemname" @gradeadded = "get_page_data(props.itemid, firstname, lastname)"></CaptureMenu>
                    </template>
                    <template #item-alert="item">
                        <span v-if="item.alert" class="badge badge-danger">{{ mstrings.discrepancy }}</span>
                    </template>
                </EasyDataTable>
            </div>

            <h2 v-if="!showtable">{{ mstrings.nothingtodisplay }}</h2>
        </div>
    </div>
</template>

<script setup>
    import {ref, defineProps, computed, watch, onMounted, inject} from '@vue/runtime-core';
    import NameFilter from '@/components/NameFilter.vue';
    import CaptureGrades from '@/components/CaptureGrades.vue';
    import CaptureMenu from '@/components/CaptureMenu.vue';
    import ImportButton from '@/components/ImportButton.vue';
    import ReleaseButton from '@/components/ReleaseButton.vue';
    import ExportWorksheetButton from '@/components/ExportWorksheetButton.vue';
    import PreLoader from '@/components/PreLoader.vue';
    import { useToast } from "vue-toastification";
    import ViewFullNamesButton from './ViewFullNamesButton.vue';

    const props = defineProps({
        itemid: Number,
    });

    const users = ref([]);
    const userids = ref([]);
    const mstrings = inject('mstrings');
    const totalrows = ref(0);
    const currentpage = ref(1);
    const usershidden = ref(false);
    const namefilterref = ref(null);
    const itemtype = ref('');
    const itemname = ref('');
    const gradesupported = ref(true);
    const columns = ref([]);
    const loaded = ref(false);
    const showalert = ref(false);
    const revealnames = ref(false);

    const toast = useToast();

    let firstname = '';
    let lastname = '';

    /**
     * Get headers
     */
    const headers = computed(() => {
        let heads = [];
        if (!usershidden.value) {
            heads.push({text: mstrings.userpicture, value: "slotuserpicture"});
            heads.push({text: mstrings.firstnamelastname, value: "displayname", sortable: true})
        } else {
            heads.push({text: mstrings.participant, value: "displayname", sortable: true});
        }
        heads.push({text: mstrings.idnumber, value: "idnumber", sortable: true});
        if (showalert.value) {
            heads.push({text: mstrings.discrepancy, value: "alert"});
        }

        // Add the grades columns
        columns.value.forEach(column => {
            // Make sure that the value is a string
            heads.push({text: column.description, value: 'GRADE' + column.id});
        });

        // Space for the buttons
        heads.push({text: "", value: "actions"});

        return heads;
    });

    /**
     * Handle viewfullnames
     * @param bool toggleview
     */
    function viewfullnames(toggleview) {
        revealnames.value = toggleview;
        get_page_data(props.itemid, firstname, lastname);
    }

    /**
     * Add grade columns into 'users' data so the table component can display them
     * @param users
     * @param columns
     * @return array
     */
    function add_grades(users, columns) {
        let grade = {};

        showalert.value = false;
        users.forEach(user => {

            // Only show alert/discrepancy column if there are any
            if (user.alert) {
                showalert.value = true;
            }
            columns.forEach(column => {
                grade = user.grades.find((element) => {
                    return (element.columnid == column.id);
                });
                if (grade) {
                    user['GRADE' + column.id] = grade.displaygrade;
                } else if (column.gradetype == 'FIRST') {
                    user['GRADE' + column.id] = mstrings.awaitingcapture;
                } else {
                    user['GRADE' + column.id] = ' ';
                }
            });
        });

        return users;
    }

    /**
     * Get filtered/paged data
     * @param int itemid
     * @param char first
     * @param char last
     */
     function get_page_data(itemid, first, last) {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        loaded.value = false;

        fetchMany([{
            methodname: 'local_gugrades_get_capture_page',
            args: {
                courseid: courseid,
                gradeitemid: itemid,
                firstname: first,
                lastname: last,
                viewfullnames: revealnames.value,
            }
        }])[0]
        .then((result) => {
            usershidden.value = result['hidden'];
            users.value = result['users'];
            itemtype.value = result['itemtype'];
            itemname.value = result['itemname'];
            gradesupported.value = result['gradesupported'];
            columns.value = result['columns'];
            userids.value = users.value.map(u => u.id);
            totalrows.value = users.value.length;

            users.value = add_grades(users.value, columns.value);

            loaded.value = true;
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }

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
        currentpage.value = 1;
        get_page_data(props.itemid, first, last);
    }

    /**
     * Import grades function is complete
     */
    function gradesimported() {

        // Get the data for the table
        get_page_data(props.itemid, firstname, lastname);

        // Done it
        toast.success("Import complete", {
        });
    }

    /**
     * Import grades function is complete
     */
     function gradesreleased() {

        // Get the data for the table
        get_page_data(props.itemid, firstname, lastname);

        // Done it
        toast.success("Release complete", {
        });
    }

    /**
     * Show table if there's anything to show
     */
    const showtable = computed(() => {
        return users.value.length != 0;
    });

    /**
     * Watch for displayed grade-item changing
     */
    watch(() => props.itemid, (itemid) => {
        get_page_data(itemid, firstname, lastname);
    })

    /**
     * Get initial data for table.
     */
    onMounted(() => {

        // Get the data for the table
        get_page_data(props.itemid, firstname, lastname);
    })
</script>