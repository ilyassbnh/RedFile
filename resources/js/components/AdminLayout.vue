<template>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>Admin Dashboard</h2>
      <nav>
        <ul>
          <li>
            <router-link to="/admin-document">Documents</router-link>
          </li>
          <li>
            <router-link to="/admin-categories">Categories</router-link>
          </li>
          <li>
            <router-link to="/users">Users</router-link>
          </li>
          <li>
            <a href="#" @click.prevent="logout" class="logout-link">Logout</a> <!-- Logout Link -->
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main content area -->
    <div class="content">
      <header>
        <h1>{{ pageTitle }}</h1>
      </header>
      <main>
        <router-view />
      </main>
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    pageTitle() {
      const routeName = this.$route.name;
      switch (routeName) {
        case 'AdminDocument':
          return 'Document Management';
        case 'AdminCategories':
          return 'Category Management';
        case 'users':
          return 'Users Management';
        default:
          return 'Admin Dashboard';
      }
    }
  },
  methods: {
    logout() {
      localStorage.removeItem('token'); // Remove the token from local storage
      this.$router.push('/login'); // Redirect to login page
    }
  }
};
</script>

<style scoped>
.admin-layout {
  display: flex;
  height: 100vh;
}

.sidebar {
  width: 250px;
  background-color: #2c3e50;
  color: white;
  padding: 20px;
}

.sidebar h2 {
  font-size: 1.5em;
  margin-bottom: 20px;
}

.sidebar nav ul {
  list-style: none;
  padding: 0;
}

.sidebar nav ul li {
  margin-bottom: 10px;
}

.sidebar nav ul li a {
  color: white;
  text-decoration: none;
}

.logout-link {
  color: #e74c3c; /* Optional: Change color for logout link */
  font-weight: bold;
}

.content {
  flex-grow: 1;
  padding: 20px;
  background-color: #ecf0f1;
}

header {
  margin-bottom: 20px;
}

header h1 {
  font-size: 2em;
  color: #34495e;
}
</style>
