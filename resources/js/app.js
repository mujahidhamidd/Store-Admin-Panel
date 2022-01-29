require('./bootstrap');
import Vuetify from "vuetify";

import VueRouter from 'vue-router';
import { routes } from './routes';
import { getLoggedInUser, setAutorizationHeaders } from './helpers/helpers';



window.Vue = require("vue").default;



const router = new VueRouter({
    routes,
    mode: 'history'
})

Vue.use(VueRouter);
Vue.use(Vuetify);


router.beforeEach((to, from, next) => {
    const requireAuth = to.matched.some(record => record.meta.requireAuth)
    const currentUser = getLoggedInUser();

    if (requireAuth && !currentUser) {
        next('/login');
    } else if (to.path == '/login' && currentUser) {
        next('/companies')
    }
    else if (to.path == '/' && currentUser) {
        next('/companies')
    }
    else if (to.path == '/' && !currentUser) {
        next('/login')
    }
    else {
        next()
    }
});


if (getLoggedInUser()) {
    let token = getLoggedInUser().token;
    setAutorizationHeaders(token)
}


Vue.component("login", require("./components/auth/login.vue").default);
Vue.component("home", require("./components/layout/home.vue").default);


const app = new Vue({
    el: "#app",
    router,
    vuetify: new Vuetify(),
});
