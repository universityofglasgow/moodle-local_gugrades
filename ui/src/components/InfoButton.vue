<template>
    <!-- info button -->
    <a href="#" class="ml-2" @click="info_clicked">
        <i class="fa fa-info-circle align-middle" :class="[fasizeclass]" aria-hidden="true"></i>
    </a>

    <!-- modal to show info-->
    <VueModal v-model="showinfomodal" modalClass="col-11 col-lg-5 rounded" :title="itemname">

        <table class="table">
            <tbody>
                <tr>
                    <th>{{ mstrings.name }}</th>
                    <td>{{ itemname }}</td>
                </tr>
                <tr>
                    <th>{{ mstrings.type }}</th>
                    <td>{{ itemtype }}</td>
                </tr>
                <tr>
                    <th>{{ mstrings.module }}</th>
                    <td>{{ itemmodule }}</td>
                </tr>
                <tr v-if="isscale">
                    <th>{{  mstrings.scale }}</th>
                    <td>{{ scalename }}</td>
                </tr>
                <tr v-if="!isscale">
                    <th>{{ mstrings.maxgrade }}</th>
                    <td>{{ grademax }}</td>
                </tr>
                <tr>
                    <th>{{ mstrings.weight }}</th>
                    <td>{{  weight }}</td>
                </tr>
            </tbody>
        </table>

        <button
            class="btn btn-warning"
            @click="showinfomodal = false"
            >{{ mstrings.close }}
        </button>
    </VueModal>
</template>

<script setup>
    import {ref, inject, defineProps, defineEmits, computed} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    const showinfomodal = ref(false);
    const itemname = ref('');
    const itemtype = ref('');
    const itemmodule = ref('');
    const isscale = ref(false);
    const scalename = ref('');
    const grademax = ref(0);
    const weight = ref(0);
    const mstrings = inject('mstrings');

    const props = defineProps({
        itemid: Number,
        size: Number,
    });

    const toast = useToast();

    const fasizeclass = computed(() => {
        if (props.size == 0) {
            return 'fa-sm';
        } else {
            return 'fa-' + props.size + 'x';
        }
    })

    /**
     * Info button clicked
     */
    function info_clicked() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_grade_item',
            args: {
                itemid: props.itemid,
            }
        }])[0]
        .then((result) => {
            itemname.value = result.itemname;
            itemtype.value = result.itemtype;
            itemmodule.value = result.itemmodule;
            isscale.value = result.isscale;
            scalename.value = result.scalename;
            grademax.value = result.grademax;
            weight.value = result.weight;
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        showinfomodal.value = true;
    }
</script>