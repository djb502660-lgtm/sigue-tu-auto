import './bootstrap';
import '../css/app.css';
import Alpine from 'alpinejs';
import { createApp } from 'vue';
import App from './App.vue';

window.Alpine = Alpine;
Alpine.start();

const appElement = document.getElementById('app');
if (appElement) {
    createApp(App).mount(appElement);
}
