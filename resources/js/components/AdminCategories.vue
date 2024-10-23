<template>
      <head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  </head>
  <div class="max-w-2xl mx-auto p-6 bg-gray-100 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Admin - Category Management</h1>

    <button 
      @click="openAddCategoryModal" 
      class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-500 transition duration-300"
    >
      Add New Category
    </button>

    <div v-if="categories.length" class="mt-6">
      <h2 class="text-xl font-semibold text-gray-700 mb-2">Category List</h2>
      <ul class="divide-y divide-gray-300">
        <li 
          v-for="category in categories" 
          :key="category.id" 
          class="flex justify-between items-center py-2 px-4 hover:bg-gray-200 transition duration-200"
        >
          <span class="text-gray-800">{{ category.name }}</span>
          <div>
            <button 
              @click="openEditCategoryModal(category)" 
              class="bg-yellow-500 text-white py-1 px-3 rounded hover:bg-yellow-400 transition duration-300"
            >
              Edit
            </button>
            <button 
              @click="handleDeleteCategory(category.id)" 
              class="bg-red-500 text-white py-1 px-3 rounded hover:bg-red-400 transition duration-300 ml-2"
            >
              Delete
            </button>
          </div>
        </li>
      </ul>
    </div>

    <!-- Add Category Modal -->
    <div v-if="showAddCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-xl font-semibold mb-4">Add New Category</h2>
        <form @submit.prevent="handleAddCategory">
          <div class="mb-4">
            <label for="addCategoryName" class="block text-gray-700 font-medium mb-2">Category Name:</label>
            <input 
              v-model="addCategoryForm.name" 
              type="text" 
              id="addCategoryName" 
              required 
              class="border border-gray-300 rounded w-full px-3 py-2"
            />
          </div>
          <div class="flex justify-between">
            <button 
              type="submit" 
              class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-500 transition duration-300"
            >
              Add Category
            </button>
            <button 
              type="button" 
              @click="closeAddCategoryModal" 
              class="text-gray-700 py-2 px-4 rounded hover:bg-gray-200 transition duration-300"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Category Modal -->
    <div v-if="showEditCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
      <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-xl font-semibold mb-4">Edit Category</h2>
        <form @submit.prevent="handleEditCategory">
          <div class="mb-4">
            <label for="editCategoryName" class="block text-gray-700 font-medium mb-2">Category Name:</label>
            <input 
              v-model="editCategoryForm.name" 
              type="text" 
              id="editCategoryName" 
              required 
              class="border border-gray-300 rounded w-full px-3 py-2"
            />
          </div>
          <div class="flex justify-between">
            <button 
              type="submit" 
              class="bg-green-600 text-white py-2 px-4 rounded hover:bg-green-500 transition duration-300"
            >
              Update Category
            </button>
            <button 
              type="button" 
              @click="closeEditCategoryModal" 
              class="text-gray-700 py-2 px-4 rounded hover:bg-gray-200 transition duration-300"
            >
              Cancel
            </button>
          </div>
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
      showAddCategoryModal: false,
      showEditCategoryModal: false,
      editCategoryForm: {
        name: '',
        id: null, 
      },
      addCategoryForm: {
        name: '',
      },
    };
  },
  methods: {
    fetchCategories() {
      axios.get('/api/categories')
        .then(response => {
          this.categories = response.data;
        })
        .catch(error => {
          console.error('Error fetching categories:', error);
        });
    },

    openAddCategoryModal() {
      this.showAddCategoryModal = true;
      this.addCategoryForm = { name: '' }; 
    },

    openEditCategoryModal(category) {
      this.showEditCategoryModal = true;
      this.editCategoryForm = { ...category }; 
    },

    closeAddCategoryModal() {
      this.showAddCategoryModal = false;
      this.addCategoryForm = { name: '' }; 
    },

    closeEditCategoryModal() {
      this.showEditCategoryModal = false;
      this.editCategoryForm = { name: '', id: null }; 
    },

    handleAddCategory() {
      axios.post('/api/categories', this.addCategoryForm)
        .then(() => {
          this.fetchCategories();
          this.closeAddCategoryModal();
        })
        .catch(error => {
          console.error('Error adding category:', error);
        });
    },

    handleEditCategory() {
      axios.put(`/api/categories/${this.editCategoryForm.id}`, { name: this.editCategoryForm.name })
        .then(() => {
          this.fetchCategories();
          this.closeEditCategoryModal();
        })
        .catch(error => {
          console.error('Error updating category:', error);
        });
    },

    handleDeleteCategory(id) {
      if (confirm('Are you sure you want to delete this category?')) {
        axios.delete(`/api/categories/${id}`)
          .then(() => {
            this.fetchCategories();
          })
          .catch(error => {
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
/* You can add any additional styles here if needed */
</style>
