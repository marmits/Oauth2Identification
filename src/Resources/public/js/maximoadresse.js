/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
// any CSS you import will output into a single css file (app.css in this case)


import '../css/maximoadresse.scss';


import $ from 'jquery';
global.$ = global.jquery = $;

const PROJECT_DIR = process.env.PROJECT_DIR;
import MainAdresse from './main.js';

console.log("==============maximoadresse.js===================");
export default MainAdresse;




