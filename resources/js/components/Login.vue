<template>
  <div class="auth-container">
    <h1>Login</h1>
    <form @submit.prevent="handleLogin" class="auth-form">
      <div class="form-group">
        <label for="email" class="label">Email:</label>
        <input type="email" v-model="email" required class="input-field">
      </div>
      <div class="form-group">
        <label for="password" class="label">Password:</label>
        <input type="password" v-model="password" required class="input-field">
      </div>
      <button type="submit" class="auth-button">Login</button>
    </form>
    <div class="register-container">
      <router-link to="/register" class="register-button">Don't have an account? Register</router-link>
    </div>
    <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
    };
  },
  methods: {
    async handleLogin() {
      try {
        this.errorMessage = ''; // Clear previous errors
        const response = await axios.post('/api/login', {
          email: this.email,
          password: this.password,
        });

        // Store the token and role in localStorage
        localStorage.setItem('token', response.data.token);

        // Assuming there's only one role for simplicity, you can use response.data.roles[0]
        localStorage.setItem('role', response.data.roles[0]); // Store the first role if multiple exist

        // Redirect based on the user role
        const userRole = response.data.roles[0]; // Check the first role
        if (userRole === 'admin') {
          this.$router.push('/admin'); // Redirect admin to the admin layout
        } else {
          this.$router.push('/upload-document'); // Redirect users to the document upload page
        }
      } catch (error) {
        this.errorMessage = error.response?.data?.message || 'Login failed. Please try again.';
      }
    }


  },
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

.auth-container {
  font-family: 'Roboto', sans-serif;
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.auth-form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 15px;
}

.label {
  margin-bottom: 5px;
  font-weight: 500;
}

.input-field {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  width: 100%;
}

.auth-button {
  background-color: #007bff;
  color: #fff;
  padding: 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.auth-button:hover {
  background-color: #0056b3;
}

.register-container {
  margin-top: 10px;
  text-align: center;
}

.register-button {
  color: #007bff;
  text-decoration: none;
}

.error {
  color: red;
  margin-top: 10px;
}
</style>