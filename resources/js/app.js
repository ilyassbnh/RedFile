import { createApp } from 'vue';
import router from './router'; // Import the router
import App from './App.vue';
import axios from 'axios'; // Import axios

// Set up Axios interceptor
axios.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`;
  }
  
  return config;
}, (error) => {
  return Promise.reject(error);
});

// Create the Vue app and use the router
const app = createApp(App);
app.use(router); // Add the router to the Vue app
app.mount('#app');
