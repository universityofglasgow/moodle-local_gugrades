<template>
    <a v-if="props.gradehidden" class="dropdown-item" href="#" @click="showhide('show')">{{ mstrings.show }}</a>
    <a v-if="!props.gradehidden" class="dropdown-item" href="#" @click="showhide('hide')">{{ mstrings.hide }}</a>
</template>

<script setup>
    import {ref, defineProps, onMounted, inject} from '@vue/runtime-core';

    const mstrings = inject('mstrings');

    const props = defineProps({
        gradehidden: Boolean,
    });

    /**
     * Hide/show button clicked
     */
    function showhide(action) {
        window.console.log(action);
    }
</script>