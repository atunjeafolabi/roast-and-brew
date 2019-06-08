<style lang="scss">
    div#cafe-map{
        width: 100%;
        height: 400px;
    }
</style>

<template>
    <div id="cafe-map">

    </div>
</template>

<script>
    export default {

        props: {
            'latitude': {
                type: Number,
                default: function(){
                    return 39.50
                }
            },
            'longitude': {
                type: Number,
                default: function(){
                    return -98.35
                }
            },
            'zoom': {
                type: Number,
                default: function(){
                    return 4
                }
            }
        },
        data(){
            return {
                markers: []
            }
        },
        computed: {
            cafes(){
                return this.$store.getters.getCafes;
            }
        },
        watch: {
            /*
             Watches the cafes. When they are updated, clear the markers
             and re build them.
             */
            cafes(){
                this.clearMarkers();
                this.buildMarkers();
            }
        },
        mounted(){
            /*
             We don't want the map to be reactive, so we initialize it locally,
             but don't store it in our data array.
             */
            this.map = new google.maps.Map(document.getElementById('cafe-map'), {
                center: {lat: this.latitude, lng: this.longitude},
                zoom: this.zoom
            });

            this.clearMarkers();
            this.buildMarkers();
        },
        methods:{
            /*
             Builds all of the markers for the cafes
             */
            buildMarkers(){

                this.markers = [];

                for( var i = 0; i < this.cafes.length; i++ ){

                    /*
                     Create the marker for each of the cafes and set the
                     latitude and longitude to the latitude and longitude
                     of the cafe. Also set the map to be the local map.
                     */
                    var marker = new google.maps.Marker({
                        position: { lat: parseFloat( this.cafes[i].latitude ), lng: parseFloat( this.cafes[i].longitude ) },
                        map: this.map
                    });

                    this.markers.push( marker );
                }
            },
            clearMarkers(){
                /*
                 Iterate over all of the markers and set the map
                 to null so they disappear.
                 */
                for( var i = 0; i < this.markers.length; i++ ){
                    this.markers[i].setMap( null );
                }
            },
        },
    }
</script>