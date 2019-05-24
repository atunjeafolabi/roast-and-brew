/**
 * Created by Atunje on 24/05/2019.
 *
 * This file allows for any communication through messaging between components
 */

//This just adds a Vue instance that will be used for message passing.
import Vue from 'vue';
export const EventBus = new Vue();