<style>
    /*@import '~@/abstracts/_variables.scss';*/
    @import "../../sass/abstracts/_variables.scss";

    div#app-layout {

    div.show-filters {
        height: 90px;
        width: 23px;
        position: absolute;
        left: 0px;
        background-color: white;
        border-top-right-radius: 3px;
        border-bottom-right-radius: 3px;
        line-height: 90px;
        top: 50%;
        cursor: pointer;
        margin-top: -45px;
        z-index: 9;
        text-align: center;
    }

    }
</style>

<template>
    <div id="app-layout">
        <div class="show-filters" v-show="!showFilters" v-on:click="toggleShowFilters()">
            <img src="/img/grey-right.svg"/>a
        </div>
        <success-notification></success-notification>
        <navigation></navigation>
        <router-view></router-view>
        <!--children components of layouts as stated in the routes will be injected here-->
        <login-modal></login-modal>
        <filters></filters>
        <pop-out></pop-out>
        <error-notification></error-notification>
    </div>
</template>

<script>

    import {EventBus} from '../event-bus.js';
    import Navigation from '../components/global/Navigation.vue';
    import LoginModal from '../components/global/LoginModal.vue';
    import Filters from '../components/global/Filters.vue';
    import PopOut from '../components/global/PopOut.vue';
    import SuccessNotification from '../components/global/SuccessNotification.vue';
    import ErrorNotification from '../components/global/ErrorNotification.vue';

    export default {
        components: {
            Navigation,
            LoginModal,
            Filters,
            PopOut,
            SuccessNotification,
            ErrorNotification
        },

        created(){
            this.$store.dispatch('loadCafes');
            this.$store.dispatch('loadUser');
            this.$store.dispatch('loadBrewMethods');

            /*
             If the admin module is set, unregister it. We don't need
             it here.
             */
            if( this.$store._modules.get(['admin'] ) ){
                this.$store.unregisterModule( 'admin', {} );
            }
        },

        computed: {
            showFilters(){
                return this.$store.getters.getShowFilters;
            },
            addedCafe(){
                return this.$store.getters.getAddedCafe;
            },
            addCafeStatus(){
                return this.$store.getters.getCafeAddStatus;
            }
        },
        methods: {
            toggleShowFilters(){
                this.$store.dispatch('toggleShowFilters', {showFilters: !this.showFilters});
            }
        },

        watch: {
            'addCafeStatus': function () {
                if (this.addCafeStatus == 2) {
                    EventBus.$emit('show-success', {
                        notification: this.addedCafe.name + ' has been added!'
                    });
                }
            }
        }
    }
</script>