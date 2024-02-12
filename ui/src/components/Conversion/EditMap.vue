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
                        validate_points: 'Number must be between 0 and the maximum grade set'
                    }"
                    v-model="item.boundpoints"
                ></FormKit>
            </div>   
        </div>
    </FormKit>
</template>

<script setup>
    import {ref, inject, defineProps, onMounted, computed} from '@vue/runtime-core';
    import {schedulea} from '@/js/schedulea.js';
    import {scheduleb} from '@/js/scheduleb.js';

    const mstrings = inject('mstrings');
    const mapname = ref('');
    const maxgrade = ref(100);
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

    const props = defineProps({
        mapid: Number,
    });

    /**
     * Array to build map
     * (depending on scale type)
     */
    const items = computed(() => {
        const scaleitems = scaletype.value == 'schedulea' ? schedulea : scheduleb;
        const finalitems = [];
        scaleitems.forEach((item) => {
            finalitems.push({
                band: item.band,
                grade: item.grade,
                boundpc: 0,
                boundpoints: 0,
            })
        });

        return finalitems;
    });

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
     * Is this a new map (id=0) or an existing one
     */
    onMounted(() => {
        if (props.mapid) {
            return;
        }
    })
</script>