<style lang="scss">
    /*@import '~@/abstracts/_variables.scss';*/
    @import "../../../sass/abstracts/_variables.scss";

    div.cafe-card{
        border-radius: 5px;
        box-shadow: 0 2px 2px 0 rgba(0,0,0,0.16), 0 0 0 1px rgba(0,0,0,0.08);
        padding: 15px 5px;
        padding: 15px 5px 5px 5px;
        margin-top: 20px;
        cursor: pointer;
        -webkit-transform: scaleX(1) scaleY(1);
        transform: scaleX(1) scaleY(1);
        transition: .2s;
        span.title{
            display: block;
            text-align: center;
            color: black;
            font-size: 18px;
            font-weight: bold;
            font-family: 'Lato', sans-serif;
            color: black;
            font-size: 18px;
            font-weight: bold;
            font-family: 'Lato', sans-serif;
        }
        span.address{
            display: block;
            text-align: center;
            margin-top: 5px;
            color: $grey;
            font-family: 'Lato', sans-serif;
            span.street{
                font-size: 14px;
                display: block;
            }
            span.city{
                font-size: 14px;
            }
            span.state{
                font-size: 14px;
            }
            span.zip{
                font-size: 14px;
                display: block;
            }
        }
        span.liked-meta{
            color: $grey;
            font-size: 10px;
            margin-left: 5px;
            margin-right: 3px;
            img{
                width: 10px;
            }
        }
        &:hover{
            -webkit-transform: scaleX(1.041) scaleY(1.041);
            transform: scaleX(1.041) scaleY(1.041);
            transition: .2s;
        }
    }
</style>

<template>
    <div class="large-6 medium-6 small-6 cell cafe-card-container" v-show="show">
        <router-link :to="{ name: 'cafe', params: { id: cafe.id} }" v-on:click.native="panToLocation( cafe )">
            <div class="cafe-card">
                <span class="title">{{ cafe.companyName }}</span>
                <span class="address">
                  <span class="street">{{ cafe.address }}</span>
                  <span class="city">{{ cafe.city }}</span> <span class="state">{{ cafe.state }}</span>
                  <span class="zip">{{ cafe.zip }}</span>
                </span>
                <div class="meta-data">
                    <span class="liked-meta">
                        <img v-bind:src="cafe.user_like_count > 0 ? '/img/liked.svg' : '/img/unliked.svg'"/> {{ cafe.likes_count }}
                    </span>
                </div>
            </div>
        </router-link>
    </div>
</template>

<script>

    import { CafeBrewMethodsFilter } from '../../mixins/filters/CafesBrewMethodsFilter.js';
    import { CafeTagsFilter } from '../../mixins/filters/CafeTagsFilter.js';
    import { CafeTextFilter } from '../../mixins/filters/CafeTextFilter.js';
    import { CafeUserLikeFilter } from '../../mixins/filters/CafeUserLikeFilter.js';
    import { EventBus } from '../../event-bus.js';
    import { CafeTypeFilter } from '../../mixins/filters/CafeTypeFilter.js';
    import { CafeHasMatchaFilter } from '../../mixins/filters/CafeHasMatchaFilter.js';
    import { CafeHasTeaFilter } from '../../mixins/filters/CafeHasTeaFilter.js';

    export default {

         // This component accepts one cafe as a property
        props: ['cafe'],

        mixins: [
            CafeTypeFilter,
            CafeBrewMethodsFilter,
            CafeTagsFilter,
            CafeTextFilter,
            CafeUserLikeFilter,
            CafeHasMatchaFilter,
            CafeHasTeaFilter
        ],

        mounted(){
            EventBus.$on('filters-updated', function( filters ){
                this.processFilters( filters );
            }.bind(this));
        },

        data(){
            return {
                show: true
            }
        },

        methods: {
            processFilters( filters ){
                /*
                 If no filters are selected, show the card
                 */
                if( filters.text == ''
                    && filters.type == 'all'
                    && filters.brewMethods.length == 0
                    && !filters.liked
                    && !filters.matcha
                    && !filters.tea){

                    this.show = true;
                }else{
                    /*
                     Initialize flags for the filtering
                     */
                    var typePassed = false;
                    var likedPassed = false;
                    var brewMethodsPassed = false;
                    var textPassed = false;
                    var matchaPassed = false;
                    var teaPassed = false;

                    /*
                     Check if the roaster passes
                     */
                    if( this.processCafeTypeFilter( this.cafe, filters.type) ){
                        typePassed = true;
                    }

                    /*
                     Check if text passes
                     */
                    if( filters.text != '' && this.processCafeTextFilter( this.cafe, filters.text ) ){
                        textPassed = true;
                    }else if( filters.text == '' ){
                        textPassed = true;
                    }

                    /*
                     Check if brew methods passes
                     */
                    if( filters.brewMethods.length != 0 && this.processCafeBrewMethodsFilter( this.cafe, filters.brewMethods ) ){
                        brewMethodsPassed = true;
                    }else if( filters.brewMethods.length == 0 ){
                        brewMethodsPassed = true;
                    }

                    /*
                        Check if liked passes
                     */
                     if( filters.liked && this.processCafeUserLikeFilter( this.cafe ) ){
                         likedPassed = true;
                     }else if( !filters.liked ) {
                         likedPassed = true;
                     }

                    /*
                     Checks if the cafe passes matcha filter
                     */
                    if( filters.matcha && this.processCafeHasMatchaFilter( this.cafe ) ){
                        matchaPassed = true;
                    }else if( !filters.matcha ){
                        matchaPassed = true;
                    }
                    /*
                     Checks if the cafe passes the tea filter
                     */
                    if( filters.tea && this.processCafeHasTeaFilter( this.cafe ) ){
                        teaPassed = true;
                    }else if( !filters.tea ){
                        teaPassed = true;
                    }

                    /*
                     If everything passes, then we show the Cafe Card
                     */
                    if( typePassed && textPassed && brewMethodsPassed && likedPassed && matchaPassed && teaPassed ){
                        this.show = true;
                    }else{
                        this.show = false;
                    }
                }
            },

            panToLocation( cafe ){
                EventBus.$emit('location-selected', { lat: parseFloat( cafe.latitude ), lng: parseFloat( cafe.longitude ) });
                }
            }
    }
</script>