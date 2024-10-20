import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Home.vue';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import UploadDocument from '../components/UploadDocument.vue';
import users from '../components/Admin.vue';
import AdminDocument from '../components/AdminDocument.vue';
import AdminCategories from '../components/AdminCategories.vue';

// Mock authentication function (replace with your actual authentication check)
const isAuthenticated = () => {
  return !!localStorage.getItem('token'); // Assuming you store your JWT in local storage
};

const routes = [
  { path: '/home', name: 'home', component: Home, meta: { requiresAuth: true } },
  { path: '/users', name: 'users', component: users },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', component: Register },
  { path: '/upload-document', name: 'UploadDocument', component: UploadDocument },
  { path: '/admin-document', name: 'AdminDocument', component: AdminDocument },
  {path: '/admin-categories',component: AdminCategories,},
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard to protect routes
router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  // Use the isAuthenticated function from outside the guard
  if (requiresAuth && !isAuthenticated()) {
    next({ name: 'login' }); // Redirect to login if not authenticated
  } else {
    next(); // Proceed to the route
  }
});

export default router;
