require('./bootstrap');
import Vue from 'vue';
import App from './App.vue';

import router from './router';

window.Vue = require('vue').default;


Vue.component('example-component', require('./components/ExampleComponent.vue').default);


const app = new Vue({
    el: '#app',
    router: router,
    render: (h) => h(App),
}).$mount('#app');
