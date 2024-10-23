<template>
      <head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  </head>
  <div class="admin-panel">
    <h1 class="text-2xl font-semibold text-center text-gray-900">Admin Panel - User Management</h1>

    <form @submit.prevent="handleSubmit" class="mt-8 space-y-6 bg-white p-6 rounded-lg shadow-md">
      <div>
        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
        <input v-model="form.name" type="text" required
          class="mt-2 block w-full rounded-md border border-gray-300 py-1.5 px-3 text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring focus:ring-indigo-600"
        />
      </div>
      
      <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
        <input v-model="form.email" type="email" required
          class="mt-2 block w-full rounded-md border border-gray-300 py-1.5 px-3 text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring focus:ring-indigo-600"
        />
      </div>

      <div v-if="!isEditing">
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
        <input v-model="form.password" type="password" required
          class="mt-2 block w-full rounded-md border border-gray-300 py-1.5 px-3 text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring focus:ring-indigo-600"
        />
      </div>

      <div v-if="!isEditing">
        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password</label>
        <input v-model="form.password_confirmation" type="password" required
          class="mt-2 block w-full rounded-md border border-gray-300 py-1.5 px-3 text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring focus:ring-indigo-600"
        />
      </div>

      <div>
        <label for="role" class="block text-sm font-medium leading-6 text-gray-900">Role</label>
        <select v-model="form.role" required
          class="mt-2 block w-full rounded-md border border-gray-300 py-1.5 px-3 text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring focus:ring-indigo-600"
        >
          <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
        </select>
      </div>

      <div class="flex justify-end">
        <button type="submit" class="mt-4 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
          {{ isEditing ? 'Update User' : 'Add User' }}
        </button>
      </div>
    </form>

    <table class="mt-8 min-w-full divide-y divide-gray-200">
      <thead>
        <tr>
          <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">Name</th>
          <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">Email</th>
          <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">Role</th>
          <th class="px-6 py-3 text-left text-sm font-medium text-gray-900">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <tr v-for="user in users" :key="user.id">
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.name }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.email }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ user.roles[0].name }}</td>
          <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
            <button @click="editUser(user)" class="text-indigo-600 hover:text-indigo-900">Edit</button>
            <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-900 ml-4">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      users: [],
      roles: [],
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: ''
      },
      isEditing: false,
      editingUserId: null,
    };
  },
  methods: {
    fetchUsers() {
      axios.get('/api/users')
        .then(response => {
          this.users = response.data;
        });
    },

    fetchRoles() {
      axios.get('/api/roles')
        .then(response => {
          this.roles = response.data;
        });
    },

    handleSubmit() {
      if (this.isEditing) {
        this.updateUser();
      } else {
        this.addUser();
      }
    },

    addUser() {
      axios.post('/api/users', this.form)
        .then(response => {
          this.users.push(response.data);
          this.resetForm();
          this.fetchUsers(); 
          this.fetchRoles();
        })
        .catch(error => {
          console.error('Error adding user:', error);
        });
    },

    editUser(user) {
      this.isEditing = true;
      this.editingUserId = user.id;
      this.form.name = user.name;
      this.form.email = user.email;
      this.form.role = user.roles[0].name;
      this.form.password = '';
      this.form.password_confirmation = '';
    },

    updateUser() {
      axios.put(`/api/users/${this.editingUserId}`, this.form)
        .then(response => {
          this.fetchUsers();
          this.resetForm();
        })
        .catch(error => {
          console.error('Error updating user:', error);
        });
    },

    deleteUser(id) {
      axios.delete(`/api/users/${id}`)
        .then(response => {
          this.fetchUsers();
        })
        .catch(error => {
          console.error('Error deleting user:', error);
        });
    },

    resetForm() {
      this.form = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role: ''
      };
      this.isEditing = false;
      this.editingUserId = null;
    }
  },
  mounted() {
    this.fetchUsers(); 
    this.fetchRoles(); 
  }
};
</script>

<style scoped>
.admin-panel {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}
</style>
