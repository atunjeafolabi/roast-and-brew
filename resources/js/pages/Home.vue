<style lang="scss">
  /*@import '~@/abstracts/_variables.scss';*/
  @import "../../sass/abstracts/_variables.scss";

  div#home{
    a.add-cafe-button{
      float: right;
      display: block;
      margin-top: 10px;
      margin-bottom: 10px;
      background-color: $dark-color;
      color: white;
      padding-top: 5px;
      padding-bottom: 5px;
      padding-left: 10px;
      padding-right: 10px;
    }
  }
</style>

<template>
  <div id="home">
    <div class="grid-container">
      <div class="grid-x">
        <div class="large-12 medium-12 small-12 columns">
          <router-link :to="{ name: 'newcafe' }" class="add-cafe-button">+ Add Cafe</router-link>
        </div>
      </div>
    </div>

    <cafe-filter></cafe-filter>

    <loader v-show="cafesLoadStatus == 1" :width="100" :height="100"></loader>

    <div class="grid-container">
      <div class="grid-x">
        <cafe-card v-for="cafe in cafes" :key="cafe.id" :cafe="cafe"></cafe-card>
      </div>
    </div>
  </div> <!-- end of #home -->
</template>

<script>

    import Loader from '../components/global/Loader.vue';
    import CafeCard from '../components/cafes/CafeCard.vue';
    import CafeFilter from '../components/cafes/CafeFilter.vue';

    export default {
        components:{
            CafeCard,
            Loader,
            CafeFilter
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