/*
 |-------------------------------------------------------------------------------
 | VUEX modules/brewMethods.js
 |-------------------------------------------------------------------------------
 | The Vuex data store for the brewMethods
 */

import BrewMethodAPI from '../api/brewMethod.js';

export const brewMethods = {
    /*
     Defines the state being monitored for the module.
     */
    state: {
        brewMethods: [],
        brewMethodsLoadStatus: 0,
    },

    /*
     Defines the actions used to retrieve the data.
     */
    actions: {
        /*
         Loads the brewMethods from the API
         */
        loadBrewMethods( { commit } ){
            commit( 'setBrewMethodsLoadStatus', 1 );

            BrewMethodAPI.getBrewMethods()
                .then( function( response ){
                    commit( 'setBrewMethods', response.data );
                    commit( 'setBrewMethodsLoadStatus', 2 );
                })
                .catch( function(){
                    commit( 'setBrewMethods', [] );
                    commit( 'setBrewMethodsLoadStatus', 3 );
                });
        }
    },

    mutations: {

        setBrewMethodsLoadStatus( state, status ){
            state.brewMethodsLoadStatus = status;
        },

        setBrewMethods( state, brewMethods ){
            state.brewMethods = brewMethods;
        },

        setBrewMethodLoadStatus( state, status ){
            state.cafeLoadStatus = status;
        }
    },

    getters: {

        getBrewMethodsLoadStatus( state ){
            return state.brewMethodsLoadStatus;
        },

        getBrewMethods( state ){
            return state.brewMethods;
        }
    }
}