<template>
  <div class="admin-panel">
    <h1>Admin Panel - User Management</h1>

    <!-- Add User Form -->
    <form @submit.prevent="handleSubmit">
      <div>
        <label>Name:</label>
        <input v-model="form.name" type="text" required />
      </div>
      <div>
        <label>Email:</label>
        <input v-model="form.email" type="email" required />
      </div>
      <div v-if="!isEditing"> <!-- Only show password fields when adding a new user -->
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

    <!-- User List -->
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
      roles: [], // Dynamically fetched roles
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
    // Fetch all users
    fetchUsers() {
      axios.get('/api/users')
        .then(response => {
          this.users = response.data;
        });
    },

    // Fetch all roles
    fetchRoles() {
      axios.get('/api/roles')
        .then(response => {
          this.roles = response.data;
        });
    },

    // Handle form submission for both adding and updating
    handleSubmit() {
      if (this.isEditing) {
        this.updateUser();
      } else {
        this.addUser();
      }
    },

    // Add a new user
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
    
    // Edit user (populate the form)
    editUser(user) {
      this.isEditing = true;
      this.editingUserId = user.id;
      this.form.name = user.name;
      this.form.email = user.email;
      this.form.role = user.roles[0].name;
      // Clear password fields when editing
      this.form.password = '';
      this.form.password_confirmation = '';
    },

    // Update user
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

    // Delete user
    deleteUser(id) {
      axios.delete(`/api/users/${id}`)
        .then(response => {
          this.fetchUsers();
        })
        .catch(error => {
          console.error('Error deleting user:', error);
        });
    },

    // Reset form fields
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
    this.fetchUsers(); // Fetch users when the component is mounted
    this.fetchRoles(); // Fetch roles when the component is mounted
  }
};
</script>

<style scoped>
.admin-panel {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  font-family: 'Roboto', sans-serif; /* Consistent font */
  background-color: #f8f9fa; /* Light background */
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  color: #34495e; /* Darker color for the title */
}

form {
  margin-bottom: 20px;
  padding: 20px;
  background-color: #ffffff; /* White background for the form */
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
  color: #2c3e50; /* Darker text for labels */
}

input[type="text"],
input[type="email"],
input[type="password"],
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ced4da; /* Border color */
  border-radius: 4px;
  font-size: 16px; /* Consistent font size */
}

button {
  background-color: #007bff; /* Bootstrap primary color */
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px; /* Consistent font size */
}

button:hover {
  background-color: #0056b3; /* Darker shade for hover effect */
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

table th,
table td {
  border: 1px solid #dee2e6; /* Border color for table */
  padding: 10px;
  text-align: left;
}

table th {
  background-color: #f1f1f1; /* Light gray background for header */
  font-weight: bold;
}

table tbody tr:hover {
  background-color: #f8f9fa; /* Hover effect for table rows */
}

/* Add margin between the edit and delete buttons */
table td button {
  margin-right: 10px; /* Adjust the margin as needed for spacing */
}

@media (max-width: 768px) {
  .admin-panel {
    padding: 10px;
  }

  button {
    width: 100%; /* Full width buttons on small screens */
  }

  table {
    font-size: 14px; /* Smaller font size for table on small screens */
  }
}
</style>
