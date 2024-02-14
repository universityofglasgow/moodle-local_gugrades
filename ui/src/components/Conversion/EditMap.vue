<template>
    <FormKit type="form" @submit="submit_form">
        <FormKit
            type="text"
            outer-class="mb-3"
            :label="mstrings.conversionmapname"
            validation="required"
            validation-visibility="live"
            name="mapname"
            v-model="mapname"
        ></FormKit>
        <FormKit
            type="text"
            outer-class="mb-3"
            :label="mstrings.maxgrade"
            number="float"
            validation="required|between:0,100"
            validation-visibility="live"
            name="maxgrade"
            v-model="maxgrade"
        ></FormKit>
        <FormKit
            type="select"
            :label="mstrings.scaletype"
            name="scaletype"
            v-model="scaletype"
            value="schedulea"
            :options="scaletypeoptions"
        ></FormKit>
        <p class="mb-1 mt-3">{{ mstrings.entrytype }}</p>
        <FormKit
            v-model="entrytype"
            type="radio"
            :options="entrytypeoptions"
        ></FormKit>
        <div class="row mt-3">
            <div class="col"><h3>{{ mstrings.percentage}}</h3></div>
            <div class="col"><h3>{{ mstrings.points }}</h3></div>
        </div>

        <div  class="row" v-for="item in items" :key="item.band">
            <div class="col">
                <FormKit
                    type="text"
                    outer-class="mb-3"
                    :disabled="entrytype != 'percentage'"
                    :label="item.band"
                    validation="required|between:0,100"
                    validation-visibility="live"
                    v-model="item.boundpc"
                ></FormKit>
            </div>    
            <div class="col">
                <FormKit
                    type="text"
                    number="float"
                    outer-class="mb-3"
                    :disabled="entrytype != 'points'"
                    :label="item.band"
                    :validation-rules="{ validate_points }"
                    validation="required|validate_points"
                    validation-visibility="live"
                    :validation-messages="{
                        validate_points: 'Number must be between 0 and the maximum grade set ' + maxgrade,
                    }"
                    v-model="item.boundpoints"
                ></FormKit>
            </div>   
        </div>
    </FormKit>
</template>

<script setup>
    import {ref, inject, defineProps, onMounted, watch} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";
    import { watchDebounced } from '@vueuse/core';
    //import {schedulea} from '@/js/schedulea.js';
    //import {scheduleb} from '@/js/scheduleb.js';

    const mstrings = inject('mstrings');
    const mapname = ref('');
    const maxgrade = ref(100);
    const rawmap = ref([]);
    const items = ref([]);
    const scaletype = ref('schedulea');
    const entrytype = ref('percentage');
    const scaletypeoptions = [
        {value: 'schedulea', label: 'Schedule A'},
        {value: 'scheduleb', label: 'Schedule B'},
    ];
    const entrytypeoptions = [
        {value: 'percentage', label: 'Percentage'},
        {value: 'points', label: 'Points'},
    ];

    const toast = useToast();

    const props = defineProps({
        mapid: Number,
    });

    /**
     * Build items array
     * (depending on scale type)
     */
    function build_items() {
        items.value = [];
        rawmap.value.forEach((item) => {
            items.value.push({
                band: item.band,
                grade: item.grade,
                boundpc: item.bound,
                boundpoints: item.bound * maxgrade.value / 100,
            });
        });
    }

    /**
     * If maxgrade changes then we need to recalculate the map
     */
     watchDebounced(
        maxgrade,
        () => {
            build_items();
        },
        { debounce: 500, maxWait: 1000 },
    );

    /**
     * If the schedule changes then the map can be reloaded
     * only if mapid==0. If it's an existing map, then it would
     * need to be deleted and recreated
     */
    watch(
        scaletype,
        () => {
            if (props.mapid == 0) {
                update_map();
            }
        }
    );

    /**
     * Custom rule for points values
     */
    function validate_points(node) {
        return (node.value >= 0) && (node.value <= maxgrade.value);
    }

    /**
     * Form submitted
     */
    function submit_form() {

    }

    /**
     * Update the conversion map
     */
    function update_map() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_conversion_map',
            args: {
                courseid: courseid,
                mapid: props.mapid,
                schedule: scaletype.value,
            }
        }])[0]
        .then((result) => {
            window.console.log(result);
            mapname.value = result.name;
            //scaletype.value = result.scaletype;
            maxgrade.value = result.maxgrade;
            rawmap.value = result.map;

            build_items();
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });   
    }

    /**
     * Is this a new map (id=0) or an existing one
     */
    onMounted(() => {
        update_map();
    })
</script>