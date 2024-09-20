// import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

// main.js o App.vue
import '@fortawesome/fontawesome-free/css/all.css';

const app = createApp(App)
const vuetify = createVuetify({
    components,

    directives,
    icons: {
        defaultSet: 'mdi',
    },

})

app.use(createPinia())
app.use(router)
app.use(vuetify)
app.use(VueSweetalert2)
app.mount('#app')
