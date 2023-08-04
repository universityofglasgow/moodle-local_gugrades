<template>
    <ul class="nav nav-pills mb-4 border-bottom">
        <li class="nav-item">
            <a class="nav-link" :class="{active: activetab == 'capture'}" @click="clickTab('capture')">
                <i class="fa fa-download" aria-hidden="true"></i>&nbsp;
                {{ mstrings.assessmentgradecapture }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" :class="{active: activetab == 'aggregate'}" @click="clickTab('aggregate')">
                <i class="fa fa-compress" aria-hidden="true"></i>&nbsp;
                {{ mstrings.coursegradeaggregation }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" :class="{active: activetab == 'audit'}" @click="clickTab('audit')">
                <i class="fa fa-history" aria-hidden="true"></i>&nbsp;
                {{ mstrings.auditlog }}
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" :class="{active: activetab == 'settings'}" @click="clickTab('settings')">
                <i class="fa fa-cog" aria-hidden="true"></i>&nbsp;
                {{ mstrings.settings }}
            </a>
        </li>        
    </ul> 
</template>

<script setup>
    import {ref, defineEmits, inject} from '@vue/runtime-core';

    const activetab = ref('capture');
    const mstrings = inject('mstrings');

    const emit = defineEmits(['tabchange']);

    /**
     * Detect change of tab and emit result to parent
     * @param {} item 
     */
    function clickTab(item) {
        activetab.value = item;
        emit('tabchange', item);
    }
</script>