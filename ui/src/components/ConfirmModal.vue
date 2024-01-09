<template>
    <ModalForm>
        <template #header>
            <h4>{{ mstrings.confirm }}</h4>
        </template>
        <template #body>
            <div class="alert alert-danger">
                <p><strong>{{  props.message }}</strong></p>
                <p>{{ mstrings.areyousure }}</p>
            </div>
        </template>
        <template #footer>
            <button class="btn btn-primary" @click="emit('confirm', true)">{{ mstrings.yes }}</button>
            <button class="btn btn-warning" @click="emit('confirm', false)">{{ mstrings.no }}</button>
        </template>
    </ModalForm>
</template>

<script setup>
    import {defineProps, defineEmits, inject} from '@vue/runtime-core';
    import ModalForm from '@/components/ModalForm.vue';

    const props = defineProps({
        message: String,
    });

    const emit = defineEmits(['confirm']);
    const mstrings = inject('mstrings');
</script>