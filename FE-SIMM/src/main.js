import '@/styles/styles.css';
import 'primeicons/primeicons.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { useAuthStore } from './stores/AuthStore';

import App from './App.vue';
import router from './router';
import ToastPlugin from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-bootstrap.css';
import Swal from "sweetalert2";

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(ToastPlugin);
app.config.globalProperties.$swal = Swal;

app.mount('#app');

const AuthStore = useAuthStore();
const token = localStorage.getItem('token');
const user = localStorage.getItem('user');

if (token) {
    AuthStore.setToken(JSON.parse(token));
}

if (user) {
    AuthStore.setUser(JSON.parse(user));
}