<template>
  <div class="admin-panel">
    <h1>Admin Panel - User Management</h1>

    <form @submit.prevent="handleSubmit">
      <div>
        <label>Name:</label>
        <input v-model="form.name" type="text" required />
      </div>
      <div>
        <label>Email:</label>
        <input v-model="form.email" type="email" required />
      </div>
      <div v-if="!isEditing"> 
        <label>Password:</label>
        <input v-model="form.password" type="password" required />
      </div>
      <div v-if="!isEditing">
        <label>Confirm Password:</label>
        <input v-model="form.password_confirmation" type="password" required />
      </div>
      <div>
        <label>Role:</label>
        <select v-model="form.role" required>
          <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
        </select>
      </div>
      <button type="submit">{{ isEditing ? 'Update User' : 'Add User' }}</button>
    </form>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>{{ user.roles[0].name }}</td>
          <td>
            <button @click="editUser(user)">Edit</button>
            <button @click="deleteUser(user.id)">Delete</button>
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
  font-family: 'Roboto', sans-serif; 
  background-color: #f8f9fa; 
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  color: #34495e; 
}

form {
  margin-bottom: 20px;
  padding: 20px;
  background-color: #ffffff; 
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

form div {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
  color: #2c3e50; 
}

input[type="text"],
input[type="email"],
input[type="password"],
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ced4da; 
  border-radius: 4px;
  font-size: 16px; 
}

button {
  background-color: #007bff; 
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px; 
}

button:hover {
  background-color: #0056b3; 
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

table th,
table td {
  border: 1px solid #dee2e6; 
  padding: 10px;
  text-align: left;
}

table th {
  background-color: #f1f1f1; 
  font-weight: bold;
}

table tbody tr:hover {
  background-color: #f8f9fa; 
}

table td button {
  margin-right: 10px; 
}

@media (max-width: 768px) {
  .admin-panel {
    padding: 10px;
  }

  button {
    width: 100%; 
  }

  table {
    font-size: 14px; 
  }
}
</style>
