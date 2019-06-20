
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
        userLoadStatus: 0,
        userUpdateStatus: 0
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
                .catch( function(error){
                    commit( 'setUser', {} );
                    commit( 'setUserLoadStatus', 3 );
                    console.log(error.response);
                });
        },

        /*
         Edits a user
         */
        editUser( { commit, state, dispatch }, data ){
            commit( 'setUserUpdateStatus', 1 );

            UserAPI.putUpdateUser( data.public_visibility, data.favorite_coffee, data.flavor_notes, data.city, data.state )
                .then( function( response ){
                    commit( 'setUserUpdateStatus', 2 );
                    dispatch( 'loadUser' );
                })
                .catch( function(){
                    commit( 'setUserUpdateStatus', 3 );
                });
        },

        /*
         Logs out a user and clears the status and user pieces of
         state.
         */
        logoutUser( { commit } ){
            commit( 'setUserLoadStatus', 0 );
            commit( 'setUser', {} );
        }
    },

    mutations: {
        setUserLoadStatus( state, status ){
            state.userLoadStatus = status;
        },

        setUser( state, userData ){
            state.user = userData;
        },

        setUserUpdateStatus( state, status ){
            state.userUpdateStatus = status;
        }
    },

    getters: {
        getUserLoadStatus( state ){
            return function(){
                return state.userLoadStatus;
            }
        },

        getUserUpdateStatus( state, status ){
            return state.userUpdateStatus;
        },

        getUser( state ){
            return state.user;
        }
    }
}