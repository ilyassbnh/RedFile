<template>
  <head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  </head>
  <div class="upload-container p-6 max-w-2xl mx-auto bg-white rounded-lg shadow-lg">
    <div class="header flex justify-between items-center mb-4">
      <h2 class="text-2xl font-semibold text-gray-800">Upload Document</h2>
      <button @click="logout" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500 transition duration-200">Logout</button>
    </div>

    <form @submit.prevent="uploadDocument" class="upload-form mb-6">
      <div class="form-group mb-4">
        <label for="title" class="block text-gray-700 font-semibold mb-1">Title:</label>
        <input v-model="form.title" type="text" required class="border border-gray-300 rounded-md p-2 w-full" />
      </div>

      <div class="form-group mb-4">
        <label for="contents" class="block text-gray-700 font-semibold mb-1">Contents:</label>
        <textarea v-model="form.contents" required class="border border-gray-300 rounded-md p-2 w-full"></textarea>
      </div>

      <div class="form-group mb-4">
        <label for="category" class="block text-gray-700 font-semibold mb-1">Category:</label>
        <select v-model="form.category_id" required class="border border-gray-300 rounded-md p-2 w-full">
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
      </div>

      <div class="form-group mb-4">
        <label for="file" class="block text-gray-700 font-semibold mb-1">Document File:</label>
        <input type="file" @change="handleFileUpload" class="border border-gray-300 rounded-md p-2 w-full" />
      </div>

      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-500 transition duration-200">Upload</button>
    </form>

    <!-- List of User Documents -->
    <div class="documents-list">
      <h3 class="text-xl font-semibold mb-4">Your Documents</h3>
      <ul>
        <li v-for="document in documents" :key="document.id" class="document-item mb-4">
          <div class="document-details p-4 bg-gray-100 rounded-md">
            <h4 class="font-semibold">{{ document.title }}</h4>
            <p>{{ document.content }}</p>
            <button @click="deleteDocument(document.id)" class="text-red-600 mt-2">Delete</button>
          </div>
        </li>
      </ul>
    </div>

    <!-- Success message display -->
    <div v-if="successMessage" class="success-message mt-4 text-green-600 font-bold">
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
      documents: [], // To store fetched user documents
      successMessage: '', // For success messages
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
        this.successMessage = 'Document uploaded successfully!';
        this.fetchUserDocuments(); // Fetch documents again after uploading
        this.clearForm();

        setTimeout(() => {
          this.successMessage = '';
        }, 3000);
      })
      .catch(error => {
        console.error('Error uploading document:', error);
      });
    },
    fetchCategories() {
      axios.get('/api/categorie', {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      })
      .then(response => {
        this.categories = response.data;
      })
      .catch(error => {
        console.error('Error fetching categories:', error);
      });
    },
    fetchUserDocuments() {
      axios.get('/api/user/documents', {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      })
      .then(response => {
        this.documents = response.data;
      })
      .catch(error => {
        console.error('Error fetching documents:', error);
      });
    },
    deleteDocument(id) {
      axios.delete(`/api/document/${id}`, {
        headers: { 'Authorization': `Bearer ${localStorage.getItem('token')}` }
      })
      .then(response => {
        this.successMessage = 'Document deleted successfully!';
        this.fetchUserDocuments(); // Fetch documents again after deletion

        setTimeout(() => {
          this.successMessage = '';
        }, 3000);
      })
      .catch(error => {
        console.error('Error deleting document:', error);
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
    }
  },
  mounted() {
    this.fetchCategories();
    this.fetchUserDocuments();
  }
};
</script>

<style scoped>
.upload-container {
  font-family: 'Inter', sans-serif;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
</style>