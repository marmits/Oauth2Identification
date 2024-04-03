
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything

require('bootstrap');
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

import '../css/marmitsgoogle.scss';

import { createApp } from 'vue';
import App from '../App.vue';

export default createApp(App).mount('#Oauth');

console.log("==============marmitsgoogle.js===================");
