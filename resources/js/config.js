/**
 * Created by Atunje on 24/05/2019.
 */

/*
 Defines the API route we are using.
 */
var api_url = '';

var google_maps_js_api = 'AIzaSyD5t7G0K8ghJDAsiMUkts1mDC3h5XlNtr8';

switch( process.env.NODE_ENV ){
    case 'development':
        api_url = 'https://roast-and-brew.app/api/v1';
        // api_url = 'https://f7309f8c.ngrok.io/api/v1';
        break;
    case 'production':
        api_url = 'https://roastandbrew.coffee/api/v1';
        break;
}

export const ROAST_CONFIG = {
    API_URL: api_url,
    GOOGLE_MAPS_JS_API: google_maps_js_api
}