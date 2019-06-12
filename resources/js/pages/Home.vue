<style>

</style>

<template>
  <div id="home">
    <loader v-show="cafesLoadStatus == 1" :width="100" :height="100"></loader>
    <span v-show="cafesLoadStatus == 1">Loading</span>
    <span v-show="cafesLoadStatus == 2">Cafes loaded successfully!</span>
    <span v-show="cafesLoadStatus == 3">Cafes cannot be loaded!</span>

    <ul>
      <li v-for="cafe in cafes">
        <router-link :to="{ name: 'cafe', params: { id: cafe.id }}">{{ cafe.name }}</router-link>
      </li>
    </ul>
  </div>
</template>

<script>

    import Loader from '../components/global/Loader.vue';

    export default {
        components:{
            Loader
        },
        created(){
            this.$store.dispatch( 'loadCafes' );
        },

        computed: {
          /*
           Gets the cafes load status
           */
            cafesLoadStatus(){
                return this.$store.getters.getCafesLoadStatus;
            },

          /*
           Gets the cafes
           */
            cafes(){
                return this.$store.getters.getCafes;
            }
        }
    }
</script>