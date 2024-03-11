
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything


import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import '../css/marmitsgoogle.scss';

import { createApp } from 'vue';
import App from '../App.vue';

createApp(App).mount('#vue-app');

console.log("==============marmitsgoogle.js===================");
