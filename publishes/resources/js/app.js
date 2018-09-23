require('./bootstrap');

import store from './media/store/index';
import lang from 'element-ui/lib/locale/lang/en';
import locale from 'element-ui/lib/locale';

locale.use(lang);

new Vue({
    el: '#app',
    store
});