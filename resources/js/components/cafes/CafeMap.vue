<style lang="scss">

    /*@import '~@/abstracts/_variables.scss';*/
    @import "../../../sass/abstracts/_variables.scss";

    /*div#cafe-map{*/
        /*width: 100%;*/
        /*height: 400px;*/
    /*}*/

    div#cafe-map-container{

        position: absolute;
        top: 75px;
        left: 0px;
        right: 0px;
        bottom: 0px;

        div#cafe-map{
            position: absolute;
            top: 0px;
            left: 0px;
            right: 0px;
            bottom: 0px;
        }

        div.cafe-info-window {

            div.cafe-name {
                display: block;
                text-align: center;
                color: $dark-color;
                font-family: 'Josefin Sans', sans-serif;
            }
            div.cafe-address {
                display: block;
                text-align: center;
                margin-top: 5px;
                color: $grey;
                font-family: 'Lato', sans-serif;
                span.street {
                    font-size: 14px;
                    display: block;
                }
                span.city {
                    font-size: 12px;
                }
                span.state {
                    font-size: 12px;
                }
                span.zip {
                    font-size: 12px;
                    display: block;
                }
                a {
                    color: $secondary-color;
                    font-weight: bold;
                }
            }
        }
    }
</style>

<template>
    <div id="cafe-map-container">
        <div id="cafe-map"></div>
        <!--<cafe-map-filter></cafe-map-filter>-->
    </div>
</template>

<script>

    import { CafeTypeFilter } from '../../mixins/filters/CafeTypeFilter.js';
    import { EventBus } from '../../event-bus.js';
    import { CafeBrewMethodsFilter } from '../../mixins/filters/CafesBrewMethodsFilter.js';
    import { CafeTagsFilter } from '../../mixins/filters/CafeTagsFilter.js';
    import { CafeTextFilter } from '../../mixins/filters/CafeTextFilter.js';
    import { CafeUserLikeFilter } from '../../mixins/filters/CafeUserLikeFilter.js';
    import { CafeHasMatchaFilter } from '../../mixins/filters/CafeHasMatchaFilter.js';
    import { CafeHasTeaFilter } from '../../mixins/filters/CafeHasTeaFilter.js';

    export default {

        components: {},

        mixins: [
            CafeTypeFilter,
            CafeBrewMethodsFilter,
            CafeTagsFilter,
            CafeTextFilter,
            CafeUserLikeFilter,
            CafeHasMatchaFilter,
            CafeHasTeaFilter
        ],

        props: {
            'latitude': {
                type: Number,
                default: function () {
                    return 39.50
                }
            },
            'longitude': {
                type: Number,
                default: function () {
                    return -98.35
                }
            },
            'zoom': {
                type: Number,
                default: function () {
                    return 5
                }
            }
        },
        data(){
            return {}
        },
        computed: {
            cafes(){
                return this.$store.getters.getCafes;
            }
        },
        watch: {
            /*
             Watches the cafes. When they are updated, clear the markers
             and re build them
             */
            cafes(){
                this.clearMarkers();
                this.buildMarkers();
            }
        },
        mounted(){

            this.$markers = [];

            this.$map = new google.maps.Map(document.getElementById('cafe-map'), {
                center: {lat: this.latitude, lng: this.longitude},
                zoom: this.zoom,
                fullscreenControl: false,
                mapTypeControl: false
            });

            this.clearMarkers();
            this.buildMarkers();

            EventBus.$on('map-filters-updated', function (filters) {
                this.processFilters(filters);
            }.bind(this));

            EventBus.$on('location-selected', function (cafe) {
                var latLng = new google.maps.LatLng(cafe.lat, cafe.lng);
                this.$map.setZoom(17);
                this.$map.panTo(latLng);
            }.bind(this));
        },
        methods: {
            /*
             Builds all of the markers for the cafes
             */
            buildMarkers(){

                this.$markers = [];

                for (var i = 0; i < this.cafes.length; i++) {

                    /*
                     Create the marker for each of the cafes and set the
                     latitude and longitude to the latitude and longitude
                     of the cafe. Also set the map to be the local map.
                     */

                    if (this.cafes[i].company.roaster == 1) {
                        var image = '/img/roaster-marker.svg';
                    } else {
                        var image = '/img/cafe-marker.svg';
                    }

                    /*
                     If the cafe has a lat and lng, create a marker object and
                     show it on the map.
                     */
                    if (this.cafes[i].latitude != null) {
                        /*
                         Create a new marker object.
                         */
                        var marker = new google.maps.Marker({
                            position: {
                                lat: parseFloat(this.cafes[i].latitude),
                                lng: parseFloat(this.cafes[i].longitude)
                            },
                            map: this.$map,
                            icon: image,
                            cafe: this.cafes[i]
                        });

                        /*
                         Localize the global router variable so when clicked, we go
                         to the cafe.
                         */
                        let router = this.$router;

                        marker.addListener('click', function () {
                            router.push({name: 'cafe', params: {slug: this.cafe.slug}});
                        });

                        this.$markers.push( marker);
                    }
                }

            },
//        },

            clearMarkers(){
                /*
                 Iterate over all of the markers and set the map
                 to null so they disappear.
                 */
                for( var i = 0; i < this.$markers.length; i++ ){
                    this.$markers[i].setMap( null);
                }
            },

            /*
             Process filters on the map selected by the user.
             */
            processFilters(filters ){

                for (var i = 0; i < this.$markers.length; i++) {

                    if (filters.text == ''
                        && filters.type == 'all'
                        && filters.brewMethods.length == 0
                        && !filters.liked
                        && !filters.matcha
                        && !filters.tea)
                    {
                        this.$markers[i].setMap( this.$map );

                    } else {
                        /*
                         Initialize flags for the filtering
                         */
                        var textPassed = false;
                        var brewMethodsPassed = false;
                        var typePassed = false;
                        var likedPassed = false;
                        var matchaPassed = false;
                        var teaPassed = false;

                        /*
                         Check if the roaster passes
                         */
                        if(this.processCafeTypeFilter( this.$markers[i].cafe, filters.type) ){
                            typePassed = true;
                        } else if (!filters.roaster) {
                            roasterPassed = true;
                        }

                        /*
                         Check if text passes
                         */
                        if (filters.text != '' && this.processCafeTextFilter(this.$markers[i].cafe, filters.text)) {
                            textPassed = true;
                        } else if (filters.text == '') {
                            textPassed = true;
                        }

                        /*
                         Check if brew methods passes
                         */
                        if (filters.brewMethods.length != 0 && this.processCafeBrewMethodsFilter(this.$markers[i].cafe, filters.brewMethods)) {
                            brewMethodsPassed = true;
                        } else if (filters.brewMethods.length == 0) {
                            brewMethodsPassed = true;
                        }

                        /*
                         Check if liked passes
                         */
                        if(filters.liked && this.processCafeUserLikeFilter( this.$markers[i].cafe ) ){
                            likedPassed = true;
                        } else if(!filters.liked ){
                            likedPassed = true;
                        }

                        /*
                         Checks if the cafe passes matcha filter
                         */
                        if( filters.matcha && this.processCafeHasMatchaFilter( this.$markers[i].cafe ) ){
                            matchaPassed = true;
                        }else if( !filters.matcha ){
                            matchaPassed = true;
                        }
                        /*
                         Checks if the cafe passes the tea filter
                         */
                        if( filters.tea && this.processCafeHasTeaFilter( this.$markers[i].cafe ) ){
                            teaPassed = true;
                        }else if( !filters.tea ){
                            teaPassed = true;
                        }

                        /*
                         If everything passes, then we show the Cafe Marker
                         */
                        if( typePassed && textPassed && brewMethodsPassed && likedPassed && matchaPassed && teaPassed ){
                            this.$markers[i].setMap(this.$map);
                        } else {
                            this.$markers[i].setMap(null);
                        }
                    }
                }
            },
        }
    }
</script>