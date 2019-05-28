
/*
 |-------------------------------------------------------------------------------
 | VUEX modules/users.js
 |-------------------------------------------------------------------------------
 | The Vuex data store for the users
 */

import UserAPI from '../api/user.js';

export const users = {
    state: {
        user: {},
        userLoadStatus: 0
    },

    /*
     Defines the actions used to retrieve the data.
     */
    actions: {
        /*
         Loads an individual user from the API
         */
        loadUser( { commit }, data ){
            commit( 'setUserLoadStatus', 1 );

            UserAPI.getUser()
                .then( function( response ){
                    commit( 'setUser', response.data );
                    commit( 'setUserLoadStatus', 2 );
                })
                .catch( function(){
                    commit( 'setUser', {} );
                    commit( 'setUserLoadStatus', 3 );
                });

        }
    },

    mutations: {
        setUserLoadStatus( state, status ){
            state.userLoadStatus = status;
        },

        setUser( state, userData ){
            state.user = userData;
        }
    },

    getters: {
        getUserLoadStatus( state ){
            return state.userLoadStatus;
        },

        getUser( state ){
            return state.user;
        }
    }
}