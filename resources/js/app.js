import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import '../css/app.css';

const app = createApp(App);

app.use(createPinia());
app.use(router);

// Global error handlers - show errors visibly so users can report them
window.onerror = function(msg, url, line) {
  console.error('JS Error:', msg, 'at', url + ':' + line);
  return false;
};
window.onunhandledrejection = function(e) {
  console.error('Unhandled Promise Rejection:', e.reason);
};

app.mount('#app');
