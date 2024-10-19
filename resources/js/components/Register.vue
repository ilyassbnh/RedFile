<template>
    <div class="auth-container">
      <h1>Register</h1>
      <form @submit.prevent="handleRegister" class="auth-form">
        <div class="form-group">
          <label for="name" class="label">Name:</label>
          <input type="text" v-model="name" required class="input-field">
        </div>
        <div class="form-group">
          <label for="email" class="label">Email:</label>
          <input type="email" v-model="email" required class="input-field">
        </div>
        <div class="form-group">
          <label for="password" class="label">Password:</label>
          <input type="password" v-model="password" required class="input-field">
        </div>
        <div class="form-group">
          <label for="password_confirmation" class="label">Confirm Password:</label>
          <input type="password" v-model="password_confirmation" required class="input-field">
        </div>
        <button type="submit" class="auth-button">Register</button>
      </form>
      <div class="register-container">
        <router-link to="/login" class="register-button">Already have an account? Login</router-link>
      </div>
      <div v-if="errorMessage" class="error">{{ errorMessage }}</div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        errorMessage: '',
      };
    },
    methods: {
      async handleRegister() {
        try {
          this.errorMessage = ''; // Clear previous errors
          const response = await axios.post('/api/register', {
            name: this.name,
            email: this.email,
            password: this.password,
            password_confirmation: this.password_confirmation,
          });
  
          this.$router.push('/login'); // Redirect to login page after registration
        } catch (error) {
          if (error.response?.data?.errors) {
            this.errorMessage = Object.values(error.response.data.errors).flat().join(', ');
          } else {
            this.errorMessage = 'Registration failed. Please try again.';
          }
        }
      },
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
  