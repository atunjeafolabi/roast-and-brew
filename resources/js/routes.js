/**
 * Created by Atunje on 24/05/2019.
 */

/*
 |-------------------------------------------------------------------------------
 | routes.js
 |-------------------------------------------------------------------------------
 | Contains all of the routes for the application
 */

/*
 Imports Vue and VueRouter to extend with the routes.
 */
import Vue from 'vue'
import VueRouter from 'vue-router'
import store from './store.js';

Vue.use( VueRouter );

/*
 This will check to see if the user is authenticated or not.
 */
function requireAuth (to, from, next) {
    /*
     Determines where we should send the user.
     */
    function proceed () {
        /*
         If the user has been loaded determine where we should
         send the user.
         */
        if ( store.getters.getUserLoadStatus() == 2 ) {
            /*
             If the user is not empty, that means there's a user
             authenticated we allow them to continue. Otherwise, we
             send the user back to the home page.
             */
            if( store.getters.getUser != '' ){
                switch( to.meta.permission ){
                    /*
                     If the route that requires authentication is a user, then we continue.
                     All users can access these routes
                     */
                    case 'user':
                        next();
                        break;
                    /*
                     If the route that requires authentication is an owner and the permission
                     the user has is greater than or equal to 1 (an owner or higher), we allow
                     access. Otherwise we redirect back to the cafes.
                     */
                    case 'owner':
                        if( store.getters.getUser.permission >= 1 ){
                            next();
                        }else{
                            next('/cafes');
                        }
                        break;
                    /*
                     If the route that requires authentication is an admin and the permission
                     the user has is greater than or equal to 2 (an owner or higher), we allow
                     access. Otherwise we redirect back to the cafes.
                     */
                    case 'admin':
                        if( store.getters.getUser.permission >= 2 ){
                            next();
                        }else{
                            next('/cafes');
                        }
                        break;
                    /*
                     If the route that requires authentication is a super admin and the permission
                     the user has is equal to 3 (a super admin), we allow
                     access. Otherwise we redirect back to the cafes.
                     */
                    case 'super-admin':
                        if( store.getters.getUser.permission == 3 ){
                            next();
                        }else{
                            next('/cafes');
                        }
                        break;
                }
            }else{
                next('/cafes');
            }
        }
    }

    /*
     Confirms the user has been loaded
     */
    if ( store.getters.getUserLoadStatus != 2 ) {
        /*
         If not, load the user
         */
        store.dispatch( 'loadUser' );

        /*
         Watch for the user to be loaded. When it's finished, then
         we proceed.
         */
        store.watch( store.getters.getUserLoadStatus, function(){
            if( store.getters.getUserLoadStatus() == 2 ){
                proceed();
            }
        });
    } else {
        /*
         User call completed, so we proceed
         */
        proceed()
    }
}

export default new VueRouter({

    mode: 'history',

    routes: [
        {
            path: '/',
            // redirect: { name: 'cafes' },
            name: 'layout',
            component: Vue.component( 'Layout', require( './pages/Layout.vue' ).default ),
            children: [
                {
                    path: 'cafes',
                    name: 'cafes',
                    component: Vue.component( 'Home', require( './pages/Home.vue' ).default ),
                    children: [
                        {
                            path: 'new',
                            name: 'newcafe',
                            component: Vue.component( 'NewCafe', require( './pages/NewCafe.vue' ).default ),
                            beforeEnter: requireAuth,
                            meta: {
                                permission: 'user'
                            }
                        },
                        {
                            path: ':slug',
                            name: 'cafe',
                            component: Vue.component( 'Cafe', require( './pages/Cafe.vue' ).default )
                        },
                    ]
                },
                {
                    path: 'cafes/:slug/edit',
                    name: 'editcafe',
                    component: Vue.component( 'EditCafe', require( './pages/EditCafe.vue' ).default ),
                    beforeEnter: requireAuth,
                    meta: {
                        permission: 'user'
                    }
                },
                {
                    path: 'profile',
                    name: 'profile',
                    component: Vue.component( 'Profile', require( './pages/Profile.vue' ).default ),
                    beforeEnter: requireAuth,
                    meta: {
                        permission: 'user'
                    }
                },
                /*
                 Catch Alls
                 */
                { path: '_=_', redirect: '/' }
            ]
        },
        {
            path: '/admin',
            name: 'admin',
            redirect: { name: 'admin-actions' },
            component: Vue.component( 'Admin', require('./layouts/Admin.vue' ).default ),
            beforeEnter: requireAuth,
            meta: {
                permission: 'owner'
            },
            children: [
                {
                    path: 'actions',
                    name: 'admin-actions',
                    component: Vue.component( 'AdminActions', require( './pages/admin/Actions.vue' ).default ),
                    meta: {
                        permission: 'owner'
                    }
                },
                {
                    path: 'companies',
                    name: 'admin-companies',
                    component: Vue.component( 'AdminCompanies', require( './pages/admin/Companies.vue' ).default ),
                    meta: {
                        permission: 'owner'
                    }
                },
                /*
                 Catch Alls
                 */
                { path: '_=_', redirect: '/' }
            ]
        }
    ]
});