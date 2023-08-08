<template>
    <div>
        <div v-if="!gradesupported">
            <div class="alert alert-danger mt-2">
                <span v-html="mstrings.gradenotsupported"></span>
            </div>
        </div>

        <div v-else>
            <div class="border rounded p-2 py-4 mt-2">
                <ImportButton :itemid="parseInt(itemid)" :userids="userids" @imported="gradesimported"></ImportButton>
                <ExportWorksheetButton :users="users" :itemtype="itemtype" :itemname="itemname"></ExportWorksheetButton>
            </div>

            <NameFilter v-if="!usershidden" @selected="filter_selected" ref="namefilterref"></NameFilter>

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
                        <AddGradeButton :itemid="itemid" :userid="parseInt(item.id)" :name="item.displayname" :itemname="itemname"></AddGradeButton>&nbsp;
                        <HistoryButton :userid="parseInt(item.id)" :itemid="itemid" :name="item.displayname" :itemname="itemname"></HistoryButton>
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
    import HistoryButton from '@/components/HistoryButton.vue';
    import ImportButton from '@/components/ImportButton.vue';
    import AddGradeButton from '@/components/AddGradeButton.vue';
    import ExportWorksheetButton from '@/components/ExportWorksheetButton.vue';
    import { useToast } from "vue-toastification";

    const props = defineProps({
        itemid: Number,
    });

    const users = ref([]);
    const userids = ref([]);
    const strings = ref({});
    const mstrings = inject('mstrings');
    const totalrows = ref(0);
    const currentpage = ref(1);
    const usershidden = ref(false);
    const namefilterref = ref(null);
    const itemtype = ref('');
    const itemname = ref('');
    const gradesupported = ref(true);
    const columns = ref([]);

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
        heads.push({text: mstrings.moodlegrade, value: "grade"});
        columns.value.forEach(column => {
            heads.push({text: column.description, value: column.gradetype});
        });
        heads.push({text: "", value: "actions"});

        return heads;
    });

    /**
     * Add grade columns into 'users' data so the table component can display them
     * @param users
     * @param columns
     * @return array
     */
    function add_grades(users, columns) {
        let grade = {};

        users.forEach(user => {
            columns.forEach(column => {
                grade = user.grades.find((element) => {
                    return (element.gradetype == column.gradetype);
                });
                if (grade) {
                    user[column.gradetype] = grade.displaygrade;
                } else {
                    user[column.gradetype] = strings.value.awaitingcapture;
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
        
        fetchMany([{
            methodname: 'local_gugrades_get_capture_page',
            args: {
                courseid: courseid,
                gradeitemid: itemid,
                firstname: first,
                lastname: last,
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