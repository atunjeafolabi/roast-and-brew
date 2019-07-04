/**
 * Created by Atunje on 24/05/2019.
 */

import { ROAST_CONFIG } from '../config.js';

export default {

    /*
     GET     /api/v1/cafes
     */
    getCafes: function(){
        return axios.get( ROAST_CONFIG.API_URL + '/cafes' );
    },

    /*
     GET   /api/v1/cafes/{cafeID}
     */
    getCafe: function( cafeID ){
        return axios.get( ROAST_CONFIG.API_URL + '/cafes/' + cafeID );
    },

    /*
     GET 	/api/v1/cafes/{cafeID}/edit
     */
    getCafeEdit: function( cafeID ){
        return axios.get( ROAST_CONFIG.API_URL + '/cafes/' + cafeID + '/edit' );
    },

    /*
     PUT 	/api/v1/cafes/{id}
     */
    putEditCafe: function( id, companyName, companyID, companyType, website, locationName, address, city, state, zip, lat, lng, brewMethods, matcha, tea ){

        let formData = new FormData();

        formData.append('_method', 'PUT');
        formData.append('company_name', companyName);
        formData.append('company_id', companyID);
        formData.append('company_type', companyType);
        formData.append('website', website);
        formData.append('location_name', locationName);
        formData.append('address', address);
        formData.append('city', city);
        formData.append('state', state);
        formData.append('zip', zip);
        formData.append('lat', lat);
        formData.append('lng', lng);
        formData.append('brew_methods', JSON.stringify( brewMethods ) );
        formData.append('matcha', matcha);
        formData.append('tea', tea);

        return axios.post( ROAST_CONFIG.API_URL + '/cafes/'+id,
            formData
        );
    },

    /*
     POST  /api/v1/cafes
     */
    postAddNewCafe: function( companyName, companyID, companyType, website, locationName, address, city, state, zip, lat, lng, brewMethods, matcha, tea ){

        let formData = new FormData();

        formData.append('company_name', companyName);
        formData.append('company_id', companyID);
        formData.append('company_type', companyType);
        formData.append('website', website);
        formData.append('location_name', locationName);
        formData.append('address', address);
        formData.append('city', city);
        formData.append('state', state);
        formData.append('zip', zip);
        formData.append('lat', lat);
        formData.append('lng', lng);
        formData.append('brew_methods', JSON.stringify( brewMethods ) );
        formData.append('matcha', matcha);
        formData.append('tea', tea);

        return axios.post( ROAST_CONFIG.API_URL + '/cafes',
            formData,
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }
        );
    },
    /*
     POST  /api/v1/cafes/{cafeID}/like
     */
    postLikeCafe: function( cafeID ){
        return axios.post( ROAST_CONFIG.API_URL + '/cafes/' + cafeID + '/like' );
    },
    /*
     DELETE /api/v1/cafes/{cafeID}/like
     */
    deleteLikeCafe: function( cafeID ){
        return axios.delete( ROAST_CONFIG.API_URL + '/cafes/' + cafeID + '/like' );
    },

    deleteCafe: function( cafeID ){
        return axios.delete( ROAST_CONFIG.API_URL + '/cafes/' + cafeID );
    }
}