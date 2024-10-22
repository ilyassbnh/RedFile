<template>
  <div class="admin-categories">
    <h1>Admin - Category Management</h1>

    <!-- Button to add a new category -->
    <button @click="openAddCategoryModal">Add New Category</button>

    <!-- Category List -->
    <div v-if="categories.length">
      <h2>Category List</h2>
      <ul>
        <li v-for="category in categories" :key="category.id">
          {{ category.name }}
          <button @click="openEditCategoryModal(category)">Edit</button>
          <button @click="handleDeleteCategory(category.id)">Delete</button>
        </li>
      </ul>
    </div>

    <!-- Add Category Modal -->
    <div v-if="showAddCategoryModal" class="modal">
      <div class="modal-content">
        <h2>Add New Category</h2>
        <form @submit.prevent="handleAddCategory">
          <div class="form-group">
            <label for="addCategoryName">Category Name:</label>
            <input v-model="addCategoryForm.name" type="text" id="addCategoryName" required />
          </div>
          <button type="submit">Add Category</button>
          <button type="button" @click="closeAddCategoryModal">Cancel</button>
        </form>
      </div>
    </div>

    <!-- Edit Category Modal -->
    <div v-if="showEditCategoryModal" class="modal">
      <div class="modal-content">
        <h2>Edit Category</h2>
        <form @submit.prevent="handleEditCategory">
          <div class="form-group">
            <label for="editCategoryName">Category Name:</label>
            <input v-model="editCategoryForm.name" type="text" id="editCategoryName" required />
          </div>
          <button type="submit">Update Category</button>
          <button type="button" @click="closeEditCategoryModal">Cancel</button>
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
        id: null, // Store the ID for editing
      },
      addCategoryForm: {
        name: '',
      },
    };
  },
  methods: {
    // Fetch all categories
    fetchCategories() {
      axios.get('/api/categories')
        .then(response => {
          this.categories = response.data;
        })
        .catch(error => {
          console.error('Error fetching categories:', error);
        });
    },

    // Open the modal to add a new category
    openAddCategoryModal() {
      this.showAddCategoryModal = true;
      this.addCategoryForm = { name: '' }; // Reset form
    },

    // Open the modal to edit an existing category
    openEditCategoryModal(category) {
      this.showEditCategoryModal = true;
      this.editCategoryForm = { ...category }; // Pre-fill the form with the selected category
    },

    // Close the add category modal
    closeAddCategoryModal() {
      this.showAddCategoryModal = false;
      this.addCategoryForm = { name: '' }; // Reset form on close
    },

    // Close the edit category modal
    closeEditCategoryModal() {
      this.showEditCategoryModal = false;
      this.editCategoryForm = { name: '', id: null }; // Reset form on close
    },

    // Handle adding a new category
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

    // Handle editing an existing category
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

    // Delete a category
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

input {
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
