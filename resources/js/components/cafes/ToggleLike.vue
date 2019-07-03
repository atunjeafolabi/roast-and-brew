<style lang="scss">
    /*@import '~@/abstracts/_variables.scss';*/
    @import '../../../sass/abstracts/_variables.scss';

    span.toggle-like{
        display: block;
        text-align: center;
        margin-top: 30px;

        span.like-toggle{
            display: inline-block;
            font-weight: bold;
            text-decoration: underline;
            font-size: 20px;
            cursor: pointer;
            color: #8E8E8E;
            font-size: 18px;
            margin-bottom: 5px;

            span.image-container{
              width: 35px;
              text-align: center;
              display: inline-block;
            }

            span.like-count{
              font-family: "Lato", sans-serif;
              font-size: 12px;
              margin-left: 10px;
              color: #8E8E8E;
            }
        }
    }
</style>

<template>
  <span class="toggle-like" v-show="userLoadStatus == 2 && user != ''">
    <span class="like" v-on:click="likeCafe( cafe.id )" v-if="!liked && cafeLoadStatus == 2 && cafeLikeActionStatus != 1 && cafeUnlikeActionStatus != 1">
      <span class="image-container">
        <img src="/img/unliked.svg"/>
      </span> Like?
    </span>
    <span class="un-like" v-on:click="unlikeCafe( cafe.id )" v-if="liked && cafeLoadStatus == 2 && cafeLikeActionStatus != 1 && cafeUnlikeActionStatus != 1">
      <span class="image-container">
        <img src="/img/liked.svg"/>
      </span> Liked
    </span>
    <loader v-show="cafeLikeActionStatus == 1 || cafeUnlikeActionStatus == 1 || cafeLoadStatus != 2"
            :width="23"
            :height="23"
            :display="'inline-block'"
    />
    <span class="like-count">
      {{ cafe.likes_count }} likes
    </span>
  </span>
</template>

<script>

    import Loader from '../global/Loader.vue';

    export default {
        components: {
            Loader
        },

        computed: {

            userLoadStatus(){
                return this.$store.getters.getUserLoadStatus();
            },

            user(){
                return this.$store.getters.getUser;
            },

            cafeLoadStatus(){
                return this.$store.getters.getCafeLoadStatus;
            },

            cafe(){
                return this.$store.getters.getCafe;
            },

            liked(){
                return this.$store.getters.getCafeLikedStatus;
            },

            cafeLikeActionStatus(){
                return this.$store.getters.getCafeLikeActionStatus;
            },

            cafeUnlikeActionStatus(){
                return this.$store.getters.getCafeUnlikeActionStatus;
            }
        },

        methods: {
            likeCafe( cafeID ){
                this.$store.dispatch( 'likeCafe', {
                    id: this.cafe.id
                });
            },
            unlikeCafe( cafeID ){
                this.$store.dispatch( 'unlikeCafe', {
                    id: this.cafe.id
                });
            }
        }
    }
</script>