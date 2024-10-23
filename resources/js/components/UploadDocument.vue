<template>
  <div class="upload-container">
    <div class="header">
      <h2>Upload Document</h2>
      <button @click="logout" class="logout-button">Logout</button> <!-- Added logout button -->
    </div>

    <form @submit.prevent="uploadDocument" class="upload-form">
      <div class="form-group">
        <label for="title" class="label">Title:</label>
        <input v-model="form.title" type="text" required class="input-field" />
      </div>

      <div class="form-group">
        <label for="contents" class="label">Contents:</label>
        <textarea v-model="form.contents" required class="input-field"></textarea>
      </div>

      <div class="form-group">
        <label for="category" class="label">Category:</label>
        <select v-model="form.category_id" required class="input-field">
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label for="file" class="label">Document File:</label>
        <input type="file" @change="handleFileUpload" class="input-field" ref="fileInput" />
      </div>

      <button type="submit" class="upload-button">Upload</button>
    </form>

    <!-- Success message display -->
    <div v-if="successMessage" class="success-message">
      {{ successMessage }}
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        title: '',
        contents: '',
        category_id: '',
        file: null,
      },
      categories: [],
      successMessage: '', // Added state for success message
    };
  },
  methods: {
    handleFileUpload(event) {
      this.form.file = event.target.files[0]; // Capture the file
    },
    uploadDocument() {
      const formData = new FormData();
      formData.append('title', this.form.title);
      formData.append('contents', this.form.contents);
      formData.append('category_id', this.form.category_id);

      if (this.form.file) {
        formData.append('file', this.form.file);
      }

      axios.post('/api/document', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
      })
      .then(response => {
        console.log(response.data);
        this.successMessage = 'Document uploaded successfully!'; // Set success message
        this.clearForm(); // Clear the form after successful upload

        // Optionally, you could hide the success message after a few seconds
        setTimeout(() => {
          this.successMessage = '';
        }, 3000);
      })
      .catch(error => {
        console.error('Error uploading document:', error);
      });
    },
    fetchCategories() {
      axios.get('/api/categorie')
        .then(response => {
          this.categories = response.data;
        })
        .catch(error => {
          console.error('Error fetching categories:', error);
        });
    },
    logout() {
      localStorage.removeItem('token'); // Remove the token from local storage
      this.$router.push('/login'); // Redirect to login page
    },
    clearForm() {
      this.form = {
        title: '',
        contents: '',
        category_id: '',
        file: null,
      };
      this.$refs.fileInput.value = ''; // Clear the file input field
    }
  },
  mounted() {
    this.fetchCategories();
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap');

.upload-container {
  font-family: 'Roboto', sans-serif;
  max-width: 500px;
  margin: 0 auto;
  padding: 20px;
  border-radius: 8px;
  background-color: #f9f9f9;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.logout-button {
  background-color: #dc3545;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.logout-button:hover {
  background-color: #c82333;
}

.upload-form {
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

.upload-button {
  background-color: #007bff;
  color: #fff;
  padding: 10px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 500;
}

.upload-button:hover {
  background-color: #0056b3;
}

.success-message {
  margin-top: 15px;
  color: green; /* Style for success message */
  font-weight: bold;
}

.error {
  color: red;
  margin-top: 10px;
}
</style>
