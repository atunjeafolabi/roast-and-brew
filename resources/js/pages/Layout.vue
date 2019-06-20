<style>

</style>

<template>
    <div id="app-layout">
        <navigation></navigation>
        <router-view></router-view>     <!--children components of layouts as stated in the routes will be injected here-->
        <login-modal></login-modal>
    </div>
</template>

<script>

    import Navigation from '../components/global/Navigation.vue';
    import LoginModal from '../components/global/LoginModal.vue';

    export default {

        components: {
            Navigation,
            LoginModal
        },

        created(){
            this.$store.dispatch( 'loadCafes' );
            this.$store.dispatch( 'loadUser' );
            this.$store.dispatch( 'loadBrewMethods' );
        }
    }
</script>