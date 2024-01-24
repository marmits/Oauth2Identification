// ...
const $ = require('jquery');
global.$ = global.jquery = $;
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');

import '../css/marmitsgoogle.scss';



console.log("==============marmitsgoogle.js===================");
