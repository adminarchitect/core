import Vue from 'vue';
import store from './media/store/index';
import MediaManager from './media/components/MediaManager.vue';
import VueClip from 'vue-clip'

Vue.use(VueClip);

Vue.filter('truncate', (value, length) => {
    const l = value.length;

    return value.substr(0, length) + ((l > length) ? '...' : '');
});

new Vue({
    el: '#media',
    components: {
        mediaManager: MediaManager,
    },
    store,
});