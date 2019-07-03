/**
 * Created by Atunje on 24/05/2019.
 */

/*
 |-------------------------------------------------------------------------------
 | VUEX store.js
 |-------------------------------------------------------------------------------
 | Builds the data store from all of the modules for the Roast app.
 */
/*
 Adds the promise polyfill for IE 11
 */
require('es6-promise').polyfill();

import Vue from 'vue'
import Vuex from 'vuex'
import { cafes } from './modules/cafes.js'
import {users} from './modules/users.js'
import {brewMethods} from './modules/brewMethods'
import { display } from './modules/display.js';

/*
 Initializes Vuex on Vue.
 */
Vue.use( Vuex )

/*
 Exports our data store.
 */
export default new Vuex.Store({
    modules: {
        cafes,
        users,
        brewMethods,
        display
    }
});