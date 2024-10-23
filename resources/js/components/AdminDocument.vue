<template>
  <div class="admin-documents">
    <h1>Admin - Document Management</h1>

    <div class="document-actions">
      <!-- Search -->
      <input v-model="searchQuery" type="text" placeholder="Search documents..." @input="searchDocuments" />

      <!-- Sort by -->
      <select v-model="sortOption" @change="sortDocuments">
        <option value="title">Sort by Title</option>
        <option value="category">Sort by Category</option>
      </select>

      <button @click="openDocumentModal()">Add New Document</button>
    </div>

    <!-- Document List -->
    <div v-if="filteredDocuments.length">
      <h2>Document List</h2>
      <ul>
        <li v-for="doc in filteredDocuments" :key="doc.id">
          <strong>{{ doc.title }}</strong> - {{ doc.content }}
          <br />
          <em>Category: {{ getCategoryName(doc.category_id) }}</em>
          <br />
          <button @click="openDocumentModal(doc)">Edit</button>
          <button @click="handleDeleteDocument(doc.id)">Delete</button>
          <button @click="downloadDocument(doc)">Download</button>
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
      searchQuery: '', // For search functionality
      sortOption: 'title', // For sorting functionality
      showDocumentModal: false,
      documentForm: {
        title: '',
        content: '',
        category_id: '',
        file: null,
      },
      editingDocument: null,
      selectedFile: null,
    };
  },
  computed: {
    filteredDocuments() {
      // Filter documents based on search query
      let filtered = this.documents.filter(doc =>
        doc.title.toLowerCase().includes(this.searchQuery.toLowerCase())
      );

      // Sort documents based on the selected option
      if (this.sortOption === 'title') {
        return filtered.sort((a, b) => a.title.localeCompare(b.title));
      } else if (this.sortOption === 'category') {
        return filtered.sort((a, b) => {
          const categoryA = this.getCategoryName(a.category_id);
          const categoryB = this.getCategoryName(b.category_id);
          return categoryA.localeCompare(categoryB);
        });
      }
      return filtered;
    },
  },
  methods: {
    fetchDocuments() {
      axios
        .get('/api/Admin-documents', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then(response => {
          this.documents = response.data;
        })
        .catch(error => {
          console.error('Error fetching documents:', error);
        });
    },
    fetchCategories() {
      axios
        .get('/api/categories')
        .then(response => {
          this.categories = response.data;
        })
        .catch(error => {
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
        axios
          .post(`/api/Admin-documents/${this.editingDocument.id}`, formData)
          .then(() => {
            this.fetchDocuments();
            this.closeDocumentModal();
          })
          .catch(error => {
            console.error('Error updating document:', error);
          });
      } else {
        axios
          .post('/api/Admin-documents', formData)
          .then(() => {
            this.fetchDocuments();
            this.closeDocumentModal();
          })
          .catch(error => {
            console.error('Error adding document:', error);
          });
      }
    },
    handleDeleteDocument(id) {
      if (confirm('Are you sure you want to delete this document?')) {
        axios
          .delete(`/api/Admin-documents/${id}`)
          .then(() => {
            this.fetchDocuments();
          })
          .catch(error => {
            console.error('Error deleting document:', error);
          });
      }
    },
    searchDocuments() {
      this.filteredDocuments;
    },
    sortDocuments() {
      this.filteredDocuments;
    },
    downloadDocument(doc) {
      axios
        .get(`/api/Admin-documents/download/${doc.id}`, {
          responseType: 'blob',
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`,
          },
        })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement('a');
          link.href = url;
          link.setAttribute('download', `${doc.title}.pdf`); // Assuming PDF download
          document.body.appendChild(link);
          link.click();
        })
        .catch(error => {
          console.error('Error downloading document:', error);
        });
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

input,
textarea,
select {
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

ul li strong {
  flex-grow: 1;
  color: #34495e;
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
  .admin-documents {
    padding: 10px;
  }

  button {
    width: 100%;
  }
}
</style>
