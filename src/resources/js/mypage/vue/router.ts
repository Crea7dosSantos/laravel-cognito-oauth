import { createRouter, createWebHistory } from 'vue-router'

import Hello from './components/pages/Hello.vue'
import Login from './components/pages/Login.vue'
import Redirect from './components/pages/Redirect.vue'
import NotFound from './components/pages/NotFound.vue'

const routes = [
    {
        path: '/',
        name: 'hello',
        component: Hello,
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
    },
    {
        path: '/auth/callback',
        name: 'redirect',
        component: Redirect,
    },
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: NotFound
    }
]

export default createRouter({
    history: createWebHistory(),
    routes,
})