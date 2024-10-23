<template>
  <div class="admin-layout">
    <div v-if="isSidebarOpen" class="sidebar-backdrop" @click="toggleSidebar"></div>

    <aside :class="{ 'sidebar': true, 'open': isSidebarOpen }">
      <h2>Admin Dashboard</h2>
      <nav>
        <ul>
          <li>
            <router-link to="/admin-document" @click="toggleSidebar">Documents</router-link>
          </li>
          <li>
            <router-link to="/admin-categories" @click="toggleSidebar">Categories</router-link>
          </li>
          <li>
            <router-link to="/users" @click="toggleSidebar">Users</router-link>
          </li>
          <li>
            <a href="#" @click.prevent="logout" class="logout-link">Logout</a>
          </li>
        </ul>
      </nav>
    </aside>

    <button class="toggle-button" @click="toggleSidebar">
      <span class="toggle-icon"></span>
      <span class="toggle-icon"></span>
      <span class="toggle-icon"></span>
    </button>

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
  data() {
    return {
      isSidebarOpen: false,
    };
  },
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
    },
  },
  methods: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },
    logout() {
      localStorage.removeItem('token'); 
      this.$router.push('/login'); 
    },
  },
};
</script>

<style scoped>
.admin-layout {
  display: flex;
  height: 100vh;
  position: relative;
}

.sidebar {
  width: 250px;
  background-color: #2c3e50; 
  color: white;
  padding: 10px;
  position: fixed;
  top: 0;
  left: -250px;
  transition: left 0.3s ease; 
  z-index: 1000;
  height: 100%; 
  font-family: 'Roboto', sans-serif; 
}

.sidebar.open {
  left: 0; 
}

.sidebar-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5); 
  z-index: 900; 
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
  color: #e74c3c;
  font-weight: bold;
}

.content {
  flex-grow: 1;
  padding: 20px;
  transition: margin-left 0.3s ease; 
}

header {
  margin-bottom: 20px;
}

header h1 {
  font-size: 2em;
  color: #34495e; 
  font-family: 'Roboto', sans-serif; 
}

.toggle-button {
  position: absolute;
  top: 20px;
  right: 20px; 
  background-color: #2c3e50; 
  color: white;
  border: none;
  border-radius: 4px;
  padding: 10px;
  cursor: pointer;
  z-index: 1001; 
}

.toggle-icon {
  display: block;
  width: 20px;
  height: 2px;
  background-color: white;
  margin: 4px 0;
}
</style>
