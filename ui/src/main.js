import { createApp, reactive } from 'vue'
import App from './App.vue'
import router from './router'
import Toast  from "vue-toastification";
import "vue-toastification/dist/index.css";
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';

// This stuff makes sure that the window.GU variable
// exists.
// This can take some time as Moodle runs this once the page
// has loaded
var timeout = 1000000;

function ensureGUIsSet(timeout) {
    var start = Date.now();
    return new Promise(waitForGU);


    function waitForGU(resolve, reject) {
        if (window.GU) {
            resolve(window.GU)
        } else if (timeout && (Date.now() - start) >= timeout) {
            reject(new Error("timeout"));
        } else {
            setTimeout(waitForGU.bind(this, resolve, reject), 30);
        }
    }
}

// Toast defaults
const options = {
    position: 'top-center',
    timeout: 5000,
};

ensureGUIsSet(timeout)
.then(() => {
    const app = createApp(App);
    app.config.globalProperties.$strings = reactive({
        test: "Test string",
    });
    app.use(router);
    app.use(Toast, options);
    app.component('EasyDataTable', Vue3EasyDataTable);
    app.mount('#app');

    // Read strings
    const GU = window.GU;
    const fetchMany = GU.fetchMany;

    fetchMany([{
        methodname: 'local_gugrades_get_all_strings',
        args: {
        }
    }])[0]
    .then((result) => {
        const strings = result;
        strings.forEach((string) => {
            app.config.globalProperties.$strings[string.tag] = string.stringvalue;
        });
    })
    .catch((error) => {
        window.console.error(error);
    })
});


