<template>
  <head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
  </head>
  <div class="admin-documents p-6 max-w-2xl mx-auto bg-white rounded-lg shadow-lg">
    <h1 class="text-2xl font-semibold text-gray-800 text-center mb-4">Admin - Document Management</h1>

    <div class="flex items-center justify-between mb-4">
      <!-- Search -->
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Search documents..."
        @input="searchDocuments"
        class="border border-gray-300 rounded-md p-2 w-1/2"
      />

      <!-- Sort by -->
      <select v-model="sortOption" @change="sortDocuments" class="border border-gray-300 rounded-md p-2 mx-2">
        <option value="title">Sort by Title</option>
        <option value="category">Sort by Category</option>
      </select>

      <button
        @click="openDocumentModal()"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-500 transition duration-200"
      >
        Add New Document
      </button>
    </div>

    <!-- Document List -->
    <div v-if="filteredDocuments.length" class="mb-4">
      <h2 class="text-xl font-semibold text-gray-700 mb-2">Document List</h2>
      <ul>
        <li
          v-for="doc in filteredDocuments"
          :key="doc.id"
          class="flex justify-between items-center bg-gray-100 p-4 mb-2 rounded-md shadow-sm"
        >
          <div>
            <strong class="text-gray-800">{{ doc.title }}</strong> - {{ doc.content }}
            <br />
            <em class="text-gray-600">Category: {{ getCategoryName(doc.category_id) }}</em>
          </div>
          <div>
            <button @click="openDocumentModal(doc)" class="text-blue-600 hover:underline mx-1">Edit</button>
            <button @click="handleDeleteDocument(doc.id)" class="text-red-600 hover:underline mx-1">Delete</button>
            <button @click="downloadDocument(doc)" class="text-green-600 hover:underline mx-1">Download</button>
          </div>
        </li>
      </ul>
    </div>

    <!-- Document Modal -->
    <div v-if="showDocumentModal" class="modal">
      <div class="modal-content bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-semibold mb-4">{{ editingDocument ? 'Edit Document' : 'Add New Document' }}</h2>
        <form @submit.prevent="handleSaveDocument">
          <div class="form-group mb-4">
            <label for="title" class="block text-gray-700 font-semibold mb-1">Title:</label>
            <input
              v-model="documentForm.title"
              type="text"
              id="title"
              required
              class="border border-gray-300 rounded-md p-2 w-full"
            />
          </div>
          <div class="form-group mb-4">
            <label for="content" class="block text-gray-700 font-semibold mb-1">Contents:</label>
            <textarea
              v-model="documentForm.content"
              id="content"
              required
              class="border border-gray-300 rounded-md p-2 w-full"
            ></textarea>
          </div>
          <div class="form-group mb-4">
            <label for="category" class="block text-gray-700 font-semibold mb-1">Category:</label>
            <select v-model="documentForm.category_id" required class="border border-gray-300 rounded-md p-2 w-full">
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="form-group mb-4">
            <label for="file" class="block text-gray-700 font-semibold mb-1">Document File:</label>
            <input type="file" @change="handleFileUpload" class="border border-gray-300 rounded-md p-2 w-full" />
          </div>
          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-500 transition duration-200"
          >
            {{ editingDocument ? 'Update' : 'Add' }} Document
          </button>
          <button
            type="button"
            @click="closeDocumentModal"
            class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-200 ml-2"
          >
            Cancel
          </button>
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
      // Trigger reactivity
      this.filteredDocuments;
    },
    sortDocuments() {
      // Trigger reactivity
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
  width: 400px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}
</style>
