<template>
    <div class="admin-categories">
      <h1>Admin - Category Management</h1>
  
      <!-- Button to add a new category -->
      <button @click="openCategoryModal">Add New Category</button>
  
      <!-- Category List -->
      <div v-if="categories.length">
        <h2>Category List</h2>
        <ul>
          <li v-for="category in categories" :key="category.id">
            {{ category.name }}
            <button @click="openCategoryModal(category)">Edit</button>
            <button @click="handleDeleteCategory(category.id)">Delete</button>
          </li>
        </ul>
      </div>
  
      <!-- Category Modal -->
      <div v-if="showCategoryModal" class="modal">
        <div class="modal-content">
          <h2>{{ editingCategory ? 'Edit Category' : 'Add New Category' }}</h2>
          <form @submit.prevent="handleSaveCategory">
            <div class="form-group">
              <label for="categoryName">Category Name:</label>
              <input v-model="categoryForm.name" type="text" id="categoryName" required />
            </div>
            <button type="submit">{{ editingCategory ? 'Update' : 'Add' }} Category</button>
            <button type="button" @click="closeCategoryModal">Cancel</button>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        categories: [],
        showCategoryModal: false,
        editingCategory: null,
        categoryForm: {
          name: '',
        },
      };
    },
    methods: {
      fetchCategories() {
        axios.get('/api/categories').then(response => {
          this.categories = response.data;
        }).catch(error => {
          console.error('Error fetching categories:', error);
        });
      },
      openCategoryModal(category = null) {
        if (category) {
          this.editingCategory = { ...category };
          this.categoryForm = { ...category };
        } else {
          this.editingCategory = null;
          this.categoryForm = { name: '' };
        }
        this.showCategoryModal = true;
      },
      closeCategoryModal() {
        this.showCategoryModal = false;
        this.editingCategory = null;
        this.categoryForm = { name: '' };
      },
      handleSaveCategory() {
        if (this.editingCategory) {
          axios.put(`/api/categories/${this.editingCategory.id}`, this.categoryForm).then(() => {
            this.fetchCategories();
            this.closeCategoryModal();
          }).catch(error => {
            console.error('Error updating category:', error);
          });
        } else {
          axios.post('/api/categories', this.categoryForm).then(() => {
            this.fetchCategories();
            this.closeCategoryModal();
          }).catch(error => {
            console.error('Error adding category:', error);
          });
        }
      },
      handleDeleteCategory(id) {
        if (confirm('Are you sure you want to delete this category?')) {
          axios.delete(`/api/categories/${id}`).then(() => {
            this.fetchCategories();
          }).catch(error => {
            console.error('Error deleting category:', error);
          });
        }
      },
    },
    mounted() {
      this.fetchCategories();
    },
  };
  </script>
  
  <style scoped>
  .admin-categories {
    font-family: 'Roboto', sans-serif;
    padding: 20px;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
  }
  
  input, textarea, select {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  
  button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #45a049;
  }
  
  h1, h2 {
    font-size: 24px;
    margin-bottom: 15px;
  }
  
  ul {
    list-style-type: none;
    padding: 0;
  }
  
  ul li {
    margin-bottom: 10px;
  }
  
  /* Modal styles */
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    width: 400px;
  }
  </style>
  