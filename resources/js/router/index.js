import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import UploadDocument from '../components/UploadDocument.vue';
import users from '../components/Admin.vue';
import AdminDocument from '../components/AdminDocument.vue';
import AdminCategories from '../components/AdminCategories.vue';
import AdminLayout from '../components/AdminLayout.vue';

// Mock authentication and role check (replace with actual backend logic)
const isAuthenticated = () => {
  return !!localStorage.getItem('token'); // Assuming you store JWT in local storage
};

const getUserRole = () => {
  return localStorage.getItem('role'); // Store the user role in local storage after login
};

const routes = [
  // Admin Layout with child routes
  {
    path: '/admin',
    component: AdminLayout,
    meta: { requiresAuth: true, role: 'admin' }, // Only admins can access
    children: [
      { path: '/users', name: 'users', component: users },
      { path: '/admin-document', name: 'AdminDocument', component: AdminDocument },
      { path: '/admin-categories', name: 'AdminCategories', component: AdminCategories }
    ],
  },

  // User route for uploading documents
  { path: '/upload-document', name: 'UploadDocument', component: UploadDocument, meta: { requiresAuth: true, role: 'user' } },

  // Authentication routes
  { path: '/login', name: 'login', component: Login },
  { path: '/register', component: Register },


  // Redirect all unknown routes to login
  { path: '/:pathMatch(.*)*', redirect: '/login' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// Navigation guard to handle role-based redirects
router.beforeEach((to, from, next) => {
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
  const userRole = getUserRole();
  const isLoggedIn = isAuthenticated();

  // Check if the user is authenticated
  if (requiresAuth && !isLoggedIn) {
    next({ name: 'login' }); // Redirect to login if not authenticated
  } else if (requiresAuth && isLoggedIn) {
    // If user is authenticated, check their role and route accordingly
    if (to.meta.role && to.meta.role !== userRole) {
      if (userRole === 'admin') {
        next({ path: '/admin' }); // Redirect admin to the admin layout
      } else if (userRole === 'user') {
        next({ path: '/upload-document' }); // Redirect user to upload document page
      }
    } else {
      next(); // Proceed if role matches or no role restriction is set
    }
  } else {
    next(); // Proceed if no authentication is required
  }
});


export default router;
