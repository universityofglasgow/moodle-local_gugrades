<template>
    <button type="button" class="btn btn-outline-success mr-1" @click="release_grades()">{{ mstrings.releasegrades }}</button>

    <Teleport to="body">
        <ModalForm :show="showreleasemodal" @close="showreleasemodal = false">
            <template #header>
                <h4>{{ mstrings.releasegrades }}</h4>
            </template>
            <template #body>
                <div class="alert alert-warning">{{ mstrings.releaseconfirm }}</div>
            </template>
            <template #footer>
                <button
                    class="modal-default-button btn btn-primary"
                    @click="release_grades()"
                    >{{ mstrings.yesrelease }}
                </button>
                <button
                    class="modal-default-button btn btn-warning"
                    @click="showreleasemodal = false"
                    >{{ mstrings.cancel }}
                </button>
            </template>
        </ModalForm>
    </Teleport>
</template>

<script setup>
    import {ref, inject} from '@vue/runtime-core';
    import ModalForm from '@/components/ModalForm.vue';
    //import { useToast } from "vue-toastification";

    const showreleasemodal = ref(false);
    const mstrings = inject('mstrings');

    //const toast = useToast();

    const props = defineProps({
        courseid: Number,
        gradeitemid: Number,
    });

    /**
     * Release grades on button click
     */
    function release_grades() {
        //const GU = window.GU;
        //const courseid = GU.courseid;
        //const fetchMany = GU.fetchMany;



        showreleasemodal.value = true;
    }
</script>