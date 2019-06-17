/**
 * Created by Atunje on 24/05/2019.
 */

/*
 |-------------------------------------------------------------------------------
 | VUEX modules/cafes.js
 |-------------------------------------------------------------------------------
 | The Vuex data store for the cafes
 */

import CafeAPI from '../api/cafe.js';

export const cafes = {
    /*
     Defines the state being monitored for the module.
     */
    state: {
        cafes: [],
        cafesLoadStatus: 0,

        cafe: {},
        cafeLoadStatus: 0,
        cafeEdit: {},
        cafeEditLoadStatus: 0,
        cafeEditStatus: 0,

        cafeAddStatus: 0,

        cafeLikeActionStatus: 0,
        cafeUnlikeActionStatus: 0,
        cafeLiked: false,
    },

    /*
     Defines the actions used to retrieve the data.
     */
    actions: {
        /*
         Loads the cafes from the API
         */
        loadCafes( { commit } ){
            commit( 'setCafesLoadStatus', 1 );

            CafeAPI.getCafes()
                .then( function( response ){
                    commit( 'setCafes', response.data );
                    commit( 'setCafesLoadStatus', 2 );
                })
                .catch( function(){
                    commit( 'setCafes', [] );
                    commit( 'setCafesLoadStatus', 3 );
                });
        },

        loadCafe( { commit }, data ){
            commit( 'setCafeLikedStatus', false );
            commit( 'setCafeLoadStatus', 1 );

            CafeAPI.getCafe( data.id )
                .then( function( response ){
                    commit( 'setCafe', response.data );
                    if( response.data.user_like.length > 0 ){
                        commit('setCafeLikedStatus', true);
                    }
                    commit( 'setCafeLoadStatus', 2 );
                })
                .catch( function(){
                    commit( 'setCafe', {} );
                    commit( 'setCafeLoadStatus', 3 );
                });
        },

        /*
         Loads a cafe to edit from the API
         */
        loadCafeEdit( { commit }, data ){
            commit( 'setCafeEditLoadStatus', 1 );

            CafeAPI.getCafeEdit( data.id )
                .then( function( response ){
                    commit( 'setCafeEdit', response.data );
                    commit( 'setCafeEditLoadStatus', 2 );
                })
                .catch( function(){
                    commit( 'setCafeEdit', {} );
                    commit( 'setCafeEditLoadStatus', 3 );
                });
        },

        /*
         Edits a cafe
         */
        editCafe( { commit, state, dispatch }, data ){
            commit( 'setCafeEditStatus', 1 );

            CafeAPI.putEditCafe( data.id, data.name, data.locations, data.website, data.description, data.roaster )
                .then( function( response ){
                    commit( 'setCafeEditStatus', 2 );
                    dispatch( 'loadCafes' );
                })
                .catch( function(error){
                    commit( 'setCafeEditStatus', 3 );
                    console.log(error.response);
                });
        },

        addCafe( { commit, state, dispatch }, data ){

            commit( 'setCafeAddedStatus', 1 );

            CafeAPI.postAddNewCafe( data.name, data.locations, data.website, data.description, data.roaster )
                .then( function( response ){
                    console.log(response)
                    commit( 'setCafeAddedStatus', 2 );
                    dispatch( 'loadCafes' );
                })
                .catch( function(error){
                    console.log(error.response)
                    commit( 'setCafeAddedStatus', 3 );
                });
        },

        likeCafe( { commit, state }, data ){

            commit( 'setCafeLikeActionStatus', 1 );

            CafeAPI.postLikeCafe( data.id )
                .then( function( response ){
                    commit( 'setCafeLikedStatus', true );
                    commit( 'setCafeLikeActionStatus', 2 );
                })
                .catch( function(){
                    commit( 'setCafeLikeActionStatus', 3 );
                });
        },

        unlikeCafe( { commit, state }, data ){

            commit( 'setCafeUnlikeActionStatus', 1 );

            CafeAPI.deleteLikeCafe( data.id )
                .then( function( response ){
                    commit( 'setCafeLikedStatus', false );
                    commit( 'setCafeUnlikeActionStatus', 2 );
                })
                .catch( function(){
                    commit( 'setCafeUnlikeActionStatus', 3 );
                });
        }
    },

    mutations: {

        setCafesLoadStatus( state, status ){
            state.cafesLoadStatus = status;
        },

        setCafes( state, cafes ){
            state.cafes = cafes;
        },

        setCafeLoadStatus( state, status ){
            state.cafeLoadStatus = status;
        },

        setCafe( state, cafe ){
            state.cafe = cafe;
        },

        /*
         Sets the cafe to be edited
         */
        setCafeEdit( state, cafe ){
            state.cafeEdit = cafe;
        },

        /*
         Sets the cafe edit status
         */
        setCafeEditStatus( state, status ){
            state.cafeEditStatus = status;
        },

        /*
         Sets the cafe edit load status
         */
        setCafeEditLoadStatus( state, status ){
            state.cafeEditLoadStatus = status;
        },

        setCafeAddedStatus( state, status ){
            state.cafeAddStatus = status;
        },

        setCafeLikedStatus( state, status ){
            state.cafeLiked = status;
        },

        setCafeLikeActionStatus( state, status ){
            state.cafeLikeActionStatus = status;
        },

        setCafeUnlikeActionStatus( state, status ){
            state.cafeUnlikeActionStatus = status;
        }
    },

    getters: {

        getCafesLoadStatus( state ){
            return state.cafesLoadStatus;
        },

        getCafes( state ){
            return state.cafes;
        },

        getCafeLoadStatus( state ){
            return state.cafeLoadStatus;
        },

        getCafe( state ){
            return state.cafe;
        },

        /*
         Gets the cafe we are editing
         */
        getCafeEdit( state ){
            return state.cafeEdit;
        },

        /*
         Gets the cafe edit status
         */
        getCafeEditStatus( state ){
            return state.cafeEditStatus;
        },

        /*
         Gets the cafe edit load status
         */
        getCafeEditLoadStatus( state ){
            return state.cafeEditLoadStatus;
        },

        getCafeAddStatus(state){
            return state.cafeAddStatus;
        },

        getCafeLikedStatus( state ){
            return state.cafeLiked;
        },

        getCafeLikeActionStatus( state ){
            return state.cafeLikeActionStatus;
        },

        getCafeUnlikeActionStatus( state ){
            return state.cafeUnlikeActionStatus;
        }
    }
}