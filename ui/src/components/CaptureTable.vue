<template>
    <div>
        <div v-if="!ineditcellmode" class="border rounded p-2 mt-2">
            <div class="col-12 mb-2">
                <button class="badge badge-primary" @click="selectcollapse">
                    <span v-if="collapsed"><i class="fa fa-arrow-right"></i> {{ mstrings.show }}</span>
                    <span v-else><i class="fa fa-arrow-down"></i> {{ mstrings.hide }}</span>
                </button>
            </div>

            <div id="captureselect" class="collapse show">
                <CaptureSelect @selecteditemid="selecteditemid"></CaptureSelect>

                <div v-if="itemid">
                    <CaptureAlerts
                        :gradesupported="gradesupported"
                        :gradehidden="gradehidden"
                        :gradelocked="gradelocked"
                        >
                    </CaptureAlerts>

                    <CaptureButtons
                        v-if="gradesupported"
                        :itemid="itemid"
                        :groupid="groupid"
                        :userids="userids"
                        :users="users"
                        :itemtype="itemtype"
                        :itemname="itemname"
                        :usershidden="usershidden"
                        :gradesimported="gradesimported"
                        @refreshtable="refresh"
                        @viewfullnames="viewfullnames"
                        >
                    </CaptureButtons>
                </div>
            </div>
        </div>

        <div v-if="itemid && gradesupported" class="mt-2">
            <NameFilter v-if="!usershidden" @selected="filter_selected" ref="namefilterref"></NameFilter>

            <PreLoader v-if="!loaded"></PreLoader>

            <div v-if="showtable">

                <!-- button for saving cell edits -->
                <div class="pb-1 clearfix" v-if="ineditcellmode">
                    <button class="btn btn-primary float-right" @click="edit_cell_saved">{{ mstrings.save }}</button>
                </div>

                <!-- Note. The array 'users' contains he lines of data. One record for each user -->
                <EasyDataTable
                    buttons-pagination
                    alternating
                    :items="users"
                    :headers="headers"
                    >

                    <!-- add header text and edit cog next to cell if required -->
                    <!-- component needs to return info about which column (which reason/gradetype has been selected)-->
                    <template #header="header">
                        {{ header.text }}
                        <CaptureColumnEditCog v-if="header.editable  && !ineditcellmode" :header="header" :itemid="itemid" @editcolumn="editcog_clicked"></CaptureColumnEditCog>
                    </template>

                    <!-- User picture column -->
                    <template #item-slotuserpicture="item">
                        <img :src="item.pictureurl" :alt="item.displayname" class="userpicture defaultuserpic" width="35" height="35"/>
                    </template>

                    <!--
                    <template #item-grade="item">
                        <CaptureGrades :grades="item.grades"></CaptureGrades>
                    </template>
                    -->

                    <!-- switch to input for bulk editing (if selected) -->
                    <template v-slot:[editcolumnslot]="item">
                        <EditCaptureCell
                            :item="item"
                            :column="editcolumn"
                            :gradeitemid="itemid"
                            :gradetype="editgradetype"
                            :usescale="editusescale"
                            :scalemenu="editscalemenu"
                            :adminmenu="editadminmenu"
                            @gradewritten = "edit_grade_written()"
                             >
                        </EditCaptureCell>
                    </template>

                    <!-- dropdown in the final column -->
                    <template #item-actions="item">
                        <CaptureMenu
                            v-if="!ineditcellmode"
                            :gradeitemid="itemid"
                            :userid="parseInt(item.id)"
                            :name="item.displayname"
                            :itemname="itemname"
                            :gradesimported="gradesimported"
                            :awaitingcapture="item.awaitingcapture"
                            @gradeadded = "get_page_data(itemid, firstname, lastname, groupid)"
                            >
                        </CaptureMenu>
                    </template>

                    <!-- show warning if grade sdo not agree -->
                    <template #item-alert="item">
                        <span v-if="item.alert" class="badge badge-danger">{{ mstrings.discrepancy }}</span>
                    </template>
                </EasyDataTable>

                <!-- button for saving cell edits -->
                <div class="pt-1 clearfix" v-if="ineditcellmode">
                    <button class="btn btn-primary float-right">{{ mstrings.save }}</button>
                </div>
            </div>

            <h2 v-if="!showtable">{{ mstrings.nothingtodisplay }}</h2>
        </div>
    </div>
</template>

<script setup>
    import {ref, computed, inject} from '@vue/runtime-core';
    import NameFilter from '@/components/NameFilter.vue';
    import CaptureSelect from '@/components/CaptureSelect.vue';
    //import CaptureGrades from '@/components/CaptureGrades.vue';
    import CaptureMenu from '@/components/CaptureMenu.vue';
    import PreLoader from '@/components/PreLoader.vue';
    import { useToast } from "vue-toastification";
    import CaptureButtons from '@/components/Capture/CaptureButtons.vue';
    import CaptureAlerts from '@/components/CaptureAlerts.vue';
    import CaptureColumnEditCog from '@/components/CaptureColumnEditCog.vue';
    import EditCaptureCell from '@/components/Capture/EditCaptureCell.vue';

    const users = ref([]);
    const userids = ref([]);
    const itemid = ref(0);
    const groupid = ref(0);
    const mstrings = inject('mstrings');
    const totalrows = ref(0);
    const currentpage = ref(1);
    const usershidden = ref(false);
    const namefilterref = ref(null);
    const itemtype = ref('');
    const itemname = ref('');
    const gradesupported = ref(true);
    const gradesimported = ref(false);
    const gradehidden = ref(false);
    const gradelocked = ref(false);
    const columns = ref([]);
    const loaded = ref(false);
    const showalert = ref(false);
    const revealnames = ref(false);
    const collapsed = ref(false);
    const editcolumn = ref('');
    const editcolumnslot = ref('');
    const editusescale = ref(false);
    const editscalemenu = ref([]);
    const editadminmenu = ref([]);
    const editgradetype = ref('');

    const toast = useToast();

    let firstname = '';
    let lastname = '';

    /**
     * Collapse selection area
     */
    function selectcollapse() {

        // Bodge to get jQuery needed for Bootstrap JS.
        const $ = window.jQuery;

        if (collapsed.value) {
            $('#captureselect').collapse('show');
        } else {
            $('#captureselect').collapse('hide');
        }
        collapsed.value = !collapsed.value;
    }

    /**
     * New itemid and/or groupid has been selected
     */
    function selecteditemid(itemgroup) {
        itemid.value = itemgroup.itemid;
        groupid.value = itemgroup.groupid;

        get_page_data(itemid.value, firstname, lastname, groupid.value);
    }

    /**
     * Column editcog has been clicked
     */
     function editcog_clicked(cellform) {

        // Unpack data
        const columnname = cellform.columnname;

        // Note: this is the EasyDataTable slot name for the column.
        editcolumnslot.value = 'item-' + columnname;
        editcolumn.value = columnname;
        editusescale.value = cellform.usescale;
        editscalemenu.value = cellform.scalemenu;
        editadminmenu.value = cellform.adminmenu;
        editgradetype.value = cellform.gradetype;
        get_page_data(itemid.value, firstname, lastname, groupid.value);
    }

    /**
     * In edit mode, the save button is clicked
     */
    function edit_cell_saved() {
        editcolumn.value = '';
        editcolumnslot.value = '';
        get_page_data(itemid.value, firstname, lastname, groupid.value);
    }

    /**
     * A cell has declared that it has been written
     * (We're probably getting lots of these)
     */
    function edit_grade_written() {
        window.console.log('GRADE WRITTEN');
    }

    /**
     * Are we in "edit a cell" mode?
     * Stuff doesn't appear, if so, and 'Save' button appears.
     */
    const ineditcellmode = computed(() => {
        return editcolumn.value != '';
    });

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
            heads.push({
                text: column.description,
                value: 'GRADE' + column.id,
                gradetype: column.gradetype,
                editable: column.editable,
            });
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
        get_page_data(itemid.value, firstname, lastname, groupid.value);
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

            // Allow import if there are no grades for this user.
            user.awaitingcapture = true;
            columns.forEach(column => {
                const columnname = 'GRADE' + column.id;
                grade = user.grades.find((element) => {
                    return (element.columnid == column.id);
                });
                if (grade) {
                    user.awaitingcapture = false;
                    user[columnname] = grade.displaygrade;
                } else if (column.gradetype == 'FIRST') {
                    user[columnname] = mstrings.awaitingcapture;
                } else {
                    user[columnname] = ' ';
                }

                // Is this column in 'editing mode'?
                // If so, we add the 'editcolumn' ta (true) to each cell in that column
                // The table slot can then pick it up and display an edit box
                // Similarly the reason/gradetype stuff
                user.editcolumn = (columnname == editcolumn.value);
                user.reason = column.gradetype;
                user.other = column.other;
                user.gradeitemid = column.gradeitemid;
            });
        });
        //window.console.log(users);

        return users;
    }

    /**
     * Get filtered/paged data
     * @param int itemid
     * @param char first
     * @param char last
     * @param int gid (group id)
     */
     function get_page_data(itemid, first, last, gid) {
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
                groupid: gid,
                viewfullnames: revealnames.value,
            }
        }])[0]
        .then((result) => {
            usershidden.value = result['hidden'];
            users.value = result['users'];
            itemtype.value = result['itemtype'];
            itemname.value = result['itemname'];
            gradesupported.value = result['gradesupported'];
            gradesimported.value = result['gradesimported'];
            gradehidden.value = result['gradehidden'];
            gradelocked.value = result['gradelocked'];
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
        get_page_data(itemid.value, first, last, groupid.value);
    }

    /**
     * Refresh the data table
     */
    function refresh() {
        get_page_data(itemid.value, firstname, lastname, groupid.value);
    }

    /**
     * Show table if there's anything to show
     */
    const showtable = computed(() => {
        return users.value.length != 0;
    });

</script>