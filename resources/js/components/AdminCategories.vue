<template>
  <div class="admin-categories">
    <h1>Admin - Category Management</h1>

    <button @click="openAddCategoryModal">Add New Category</button>

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
.admin-categories {
  font-family: 'Roboto', sans-serif;
  padding: 20px;
  max-width: 800px; 
  margin: 0 auto; 
  background-color: #f8f9fa; 
  border-radius: 8px; 
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

h1,
h2 {
  color: #34495e; 
  margin-bottom: 15px;
  text-align: center; 
}

button {
  background-color: #007bff; 
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin: 5px 0; 
}

button:hover {
  background-color: #0056b3; 
}

.form-group {
  margin-bottom: 15px;
}

label {
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
  color: #2c3e50; 
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ced4da; 
  border-radius: 4px;
  margin-bottom: 10px;
}

ul {
  list-style-type: none;
  padding: 0;
}

ul li {
  margin-bottom: 15px;
  padding: 10px;
  border: 1px solid #ced4da; 
  border-radius: 4px; 
  display: flex; 
  justify-content: space-between; 
  align-items: center;
}

ul li button {
  margin-left: 10px; 
}

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
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); 
}

@media (max-width: 768px) {
  .admin-categories {
    padding: 10px;
  }

  button {
    width: 100%;
  }
}
</style>
