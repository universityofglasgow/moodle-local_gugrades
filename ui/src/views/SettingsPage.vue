<template>
    <div>
        <h1>Settings</h1>

        <FormKit type="form" @submit="submit_form">
            <FormKit 
                type="checkbox"
                :label="mstrings.enabledashboard"
                v-model="enabledashboard"
                >
            </FormKit>

        </FormKit>
    </div>
</template>

<script setup>
    import {ref, inject, onMounted} from '@vue/runtime-core';
    import { useToast } from "vue-toastification";

    const mstrings = inject('mstrings');
    const enabledashboard = ref(false);

    const toast = useToast();

    /**
     * Submit button clicked
     */
    function submit_form() {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_save_settings',
            args: {
                courseid: courseid,
                gradeitemid: 0,
                settings: [
                    {
                        name: 'enabledashboard',
                        value: enabledashboard.value,
                    },
                ]
            }
        }])[0]
        .then(() => {
            toast.success(mstrings.settingssaved);
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    }

    /**
     * Load initial page
     */
    onMounted(() => {
        const GU = window.GU;
        const courseid = GU.courseid;
        const fetchMany = GU.fetchMany;

        fetchMany([{
            methodname: 'local_gugrades_get_settings',
            args: {
                courseid: courseid,
                gradeitemid: 0,
            }
        }])[0]
        .then((settings) => {
            settings.forEach((setting) => {

                // TODO: Something a bit cleverer than this
                if (setting.name == 'enabledashboard') {
                    enabledashboard.value = setting.value ? true : false;
                }
            })
        })
        .catch((error) => {
            window.console.error(error);
            toast.error('Error communicating with server (see console)');
        });
    })

</script>