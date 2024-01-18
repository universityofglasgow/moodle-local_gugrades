<template>
    <button type="button" class="btn btn-outline-success mr-1" @click="showreleasemodal=true">
        <span v-if="grouprelease">{{ mstrings.releasegradesgroup }}</span>
        <span v-else>{{ mstrings.releasegrades }}</span>
    </button>

    <Teleport to="body">
        <ModalForm :show="showreleasemodal" @close="showreleasemodal = false">
            <template #header>
                <h4>{{ mstrings.releasegrades }}</h4>
            </template>
            <template #body>
                <div class="alert alert-warning">
                    {{ mstrings.releaseconfirm }}
                    <p v-if="grouprelease" class="mt-1"><b>{{ mstrings.releaseconfirmgroup }}</b></p>
                </div>
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
    import {ref, inject, defineProps, defineEmits, computed} from '@vue/runtime-core';
    import ModalForm from '@/components/ModalForm.vue';
    import { useToast } from "vue-toastification";

    const showreleasemodal = ref(false);
    const mstrings = inject('mstrings');

    const emit = defineEmits(['released']);

    const toast = useToast();

    const props = defineProps({
        gradeitemid: Number,
        groupid: Number,
    });

    const grouprelease = computed(() => {
        return props.groupid > 0;
    });

    /**
     * Release grades on button click
     */
    function release_grades() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_release_grades',
            args: {
                courseid: courseid,
                gradeitemid: props.gradeitemid,
                groupid: props.groupid,
            }
        }])[0]
        .then(() => {
            emit('released');
            showreleasemodal.value = false;
            toast.success(mstrings.gradesreleased);
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });

        showreleasemodal.value = true;
    }
</script>