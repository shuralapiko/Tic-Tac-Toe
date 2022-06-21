import Vue from 'vue';
import VueRouter from 'vue-router';

const Test = () => import('../views/Test.vue');


const routes = [
    { path: '/', component: Test },
];

Vue.use(VueRouter);

const router = new VueRouter({
    mode: 'history',
    routes,
});

export default router;
