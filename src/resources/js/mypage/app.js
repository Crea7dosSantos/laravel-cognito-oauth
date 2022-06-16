import { createApp } from 'vue'
import VueAxios from 'vue-axios'
import router from './vue/router'
import App from './vue/App.vue'
import axios from 'axios'

const app = createApp(App)
app.use(router)
app.use(VueAxios, axios)
app.mount('#app')