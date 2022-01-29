import Login from './components/auth/login.vue';
import Home from './components/layout/home.vue';
import companies from './components/pages/companies.vue';
import categories from './components/pages/categories.vue';
import products from './components/pages/products.vue';




export const routes = [
    {
        path: '/login',
        name : "login",
        component: Login,

    },
    {
        path: '/home',
        component: Home,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/companies',
        component: companies,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/categories',
        component: categories,
        meta: {
            requireAuth: true
        }
    },
    {
        path: '/products',
        component: products,
        meta: {
            requireAuth: true
        }
    }
]
