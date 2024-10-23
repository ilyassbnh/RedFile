<template>
  <div class="admin-documents">
    <h1>Admin - Document Management</h1>

    <!-- Button to add a new document -->
    <button @click="openDocumentModal()">Add New Document</button>

    <!-- Document List -->
    <div v-if="documents.length">
      <h2>Document List</h2>
      <ul>
        <li v-for="document in documents" :key="document.id">
          <strong>{{ document.title }}</strong> - {{ document.content }}
          <br>
          <em>Category: {{ getCategoryName(document.category_id) }}</em>
          <button @click="openDocumentModal(document)">Edit</button>
          <button @click="handleDeleteDocument(document.id)">Delete</button>
        </li>
      </ul>
    </div>

    <!-- Document Modal -->
    <div v-if="showDocumentModal" class="modal">
      <div class="modal-content">
        <h2>{{ editingDocument ? 'Edit Document' : 'Add New Document' }}</h2>
        <form @submit.prevent="handleSaveDocument">
          <div class="form-group">
            <label for="title">Title:</label>
            <input v-model="documentForm.title" type="text" id="title" required />
          </div>
          <div class="form-group">
            <label for="content">Contents:</label>
            <textarea v-model="documentForm.content" id="content" required></textarea>
          </div>
          <div class="form-group">
            <label for="category">Category:</label>
            <select v-model="documentForm.category_id" required>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="form-group">
            <label for="file">Document File:</label>
            <input type="file" @change="handleFileUpload" />
          </div>
          <button type="submit">{{ editingDocument ? 'Update' : 'Add' }} Document</button>
          <button type="button" @click="closeDocumentModal">Cancel</button>
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
      documents: [],
      categories: [],
      showDocumentModal: false,
      showCategoryModal: false,
      documentForm: {
        title: '',
        content: '',
        category_id: '',
        file: null,
      },
      categoryForm: {
        name: '',
      },
      editingDocument: null,
      editingCategory: null,
      selectedFile: null,
    };
  },
  methods: {
    fetchDocuments() {
      axios.get('/api/Admin-documents', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
        },
      }).then(response => {
        this.documents = response.data;
      }).catch(error => {
        console.error('Error fetching documents:', error);
      });
    },
    fetchCategories() {
      axios.get('/api/categories').then(response => {
        this.categories = response.data;
      }).catch(error => {
        console.error('Error fetching categories:', error);
      });
    },
    getCategoryName(id) {
      const category = this.categories.find(cat => cat.id === id);
      return category ? category.name : 'Unknown';
    },
    handleFileUpload(event) {
      this.selectedFile = event.target.files[0];
    },
    openDocumentModal(document = null) {
      if (document) {
        this.editingDocument = { ...document };
        this.documentForm = { ...document };
      } else {
        this.editingDocument = null;
        this.documentForm = { title: '', content: '', category_id: '', file: null };
      }
      this.showDocumentModal = true;
    },
    closeDocumentModal() {
      this.showDocumentModal = false;
      this.editingDocument = null;
      this.documentForm = { title: '', content: '', category_id: '', file: null };
      this.selectedFile = null;
    },
    handleSaveDocument() {
      const formData = new FormData();
      formData.append('title', this.documentForm.title);
      formData.append('content', this.documentForm.content);
      formData.append('category_id', this.documentForm.category_id);
      if (this.selectedFile) {
        formData.append('file', this.selectedFile);
      }

      if (this.editingDocument) {
        axios.post(`/api/Admin-documents/${this.editingDocument.id}`, formData).then(() => {
          this.fetchDocuments();
          this.closeDocumentModal();
        }).catch(error => {
          console.error('Error updating document:', error);
        });
      } else {
        axios.post('/api/Admin-documents', formData).then(() => {
          this.fetchDocuments();
          this.closeDocumentModal();
        }).catch(error => {
          console.error('Error adding document:', error);
        });
      }
    },
    handleDeleteDocument(id) {
      if (confirm('Are you sure you want to delete this document?')) {
        axios.delete(`/api/Admin-documents/${id}`).then(() => {
          this.fetchDocuments();
        }).catch(error => {
          console.error('Error deleting document:', error);
        });
      }
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
  },
  mounted() {
    this.fetchDocuments();
    this.fetchCategories();
  },
};
</script>

<style scoped>
.admin-documents {
  font-family: 'Roboto', sans-serif;
  padding: 20px;
  max-width: 800px; /* Set a max width for better layout */
  margin: 0 auto; /* Center the container */
  background-color: #f8f9fa; /* Light background for the page */
  border-radius: 8px; /* Rounded corners */
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* Shadow for depth */
}

h1,
h2 {
  color: #34495e; /* Darker color for headings */
  margin-bottom: 15px;
  text-align: center; /* Center align headings */
}

button {
  background-color: #007bff; /* Bootstrap primary color */
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin: 5px 0; /* Add margin for vertical spacing */
}

button:hover {
  background-color: #0056b3; /* Darker shade for hover effect */
}

.form-group {
  margin-bottom: 15px;
}

label {
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
  color: #2c3e50; /* Darker text for labels */
}

input,
textarea,
select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ced4da; /* Light border color */
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
  border: 1px solid #ced4da; /* Light border for list items */
  border-radius: 4px; /* Rounded corners */
  display: flex; /* Use flex for item layout */
  justify-content: space-between; /* Space out title and buttons */
  align-items: center; /* Center align vertically */
}

ul li strong {
  flex-grow: 1; /* Allow title to grow and fill space */
  color: #34495e; /* Darker color for the document title */
}

ul li button {
  margin-left: 10px; /* Add a gap between the buttons */
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
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); /* Shadow for modal */
}

@media (max-width: 768px) {
  .admin-documents {
    padding: 10px;
  }

  button {
    width: 100%; /* Full width buttons on small screens */
  }
}
</style>


