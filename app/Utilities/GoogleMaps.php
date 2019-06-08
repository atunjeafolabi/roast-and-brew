<?php
/**
 * Created by PhpStorm.
 * User: Atunje
 * Date: 08/06/2019
 * Time: 14:20
 */

namespace App\Utilities;

class GoogleMaps{

    /*
      Geocodes an address so we can get the latitude and longitude
    */
    public static function geocodeAddress( $address, $city, $state, $zip ){

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode( $address.' '.$city.', '.$state.' '.$zip ).'&key='.env( 'GOOGLE_MAPS_KEY' );

        $client = new \GuzzleHttp\Client();

        $geocodeResponse = $client->get( $url )->getBody();

        $geocodeData = json_decode( $geocodeResponse );

        /*
          Initializes the response for the GeoCode Location
        */
        $coordinates['lat'] = null;
        $coordinates['lng'] = null;

        /*
          If the response is not empty (something returned),
          we extract the latitude and longitude from the
          data.
        */
        if( !empty( $geocodeData )
            && $geocodeData->status != 'ZERO_RESULTS'
            && isset( $geocodeData->results )
            && isset( $geocodeData->results[0] ) ){
            $coordinates['lat'] = $geocodeData->results[0]->geometry->location->lat;
            $coordinates['lng'] = $geocodeData->results[0]->geometry->location->lng;
        }

        return $coordinates;

    }
}