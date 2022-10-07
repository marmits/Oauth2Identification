// ...
const $ = require('jquery');
global.$ = global.jquery = $;
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

import '../css/marmitsgoogle.scss';


import Main from './main.js';
console.log("==============marmitsgoogle.js===================");
export default Main;