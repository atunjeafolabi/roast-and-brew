import { ROAST_CONFIG } from '../config.js';

export default {
    /*
     GET     /api/v1/brew-methods
     */
    getBrewMethods: function(){
        return axios.get( ROAST_CONFIG.API_URL + '/brew-methods' );
    },

    /*
     GET     /api/v1/cafes/{cafeID}
     */
    // getBrewMethod: function( cafeID ){
    //     return axios.get( ROAST_CONFIG.API_URL + '/cafes/' + cafeID );
    // },

    /*
     POST    /api/v1/cafes
     */
    // postAddNewBrewMethod: function( name, address, city, state, zip ){
    //     return axios.post( ROAST_CONFIG.API_URL + '/cafes',
    //         {
    //             name: name,
    //             address: address,
    //             city: city,
    //             state: state,
    //             zip: zip
    //         }
    //     );
    // }
}