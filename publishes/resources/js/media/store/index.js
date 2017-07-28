import Vue from 'vue';
import Vuex from 'vuex';
import Selection from './Selection';
import Response from './Response';
import Storage from './Storage';

Vue.use(Vuex);

const store = new Vuex.Store({
    debug: true,
    modules: {
        selection: Selection,
        response: Response,
        storage: Storage,
    },
});

export default store;