<template>
  <head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

  </head>
  <div class="container mx-auto p-8">
    <!-- Page Title -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">Admin - Document Management</h1>
      <button @click="openDocumentModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-500 focus:ring-2 focus:ring-indigo-600">Add New Document</button>
    </div>

    <!-- Search and Sort -->
    <div class="flex justify-between items-center mb-6">
      <input v-model="searchQuery" type="text" placeholder="Search documents..." @input="searchDocuments" class="border border-gray-300 rounded-md py-2 px-4 w-1/2 focus:ring-2 focus:ring-indigo-600" />
      
      <select v-model="sortOption" @change="sortDocuments" class="border border-gray-300 rounded-md py-2 px-4 focus:ring-2 focus:ring-indigo-600">
        <option value="title">Sort by Title</option>
        <option value="category">Sort by Category</option>
      </select>
    </div>

    <!-- Document List -->
    <div v-if="filteredDocuments.length">
      <h2 class="text-xl font-semibold text-gray-700 mb-4">Document List</h2>
      <ul class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <li v-for="doc in filteredDocuments" :key="doc.id" class="bg-white shadow-md rounded-lg p-4">
          <strong class="block text-lg font-medium text-gray-900">{{ doc.title }}</strong>
          <p class="text-gray-700">{{ doc.content }}</p>
          <em class="text-sm text-gray-500 block mt-2">Category: {{ getCategoryName(doc.category_id) }}</em>

          <div class="mt-4 flex space-x-2">
            <button @click="openDocumentModal(doc)" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-400">Edit</button>
            <button @click="handleDeleteDocument(doc.id)" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-400">Delete</button>
            <button @click="downloadDocument(doc)" class="bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-400">Download</button>
          </div>
        </li>
      </ul>
    </div>
    <!-- No documents message -->
    <div v-else class="text-gray-600">No documents found.</div>

    <!-- Document Modal -->
    <div v-if="showDocumentModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
        <h2 class="text-xl font-semibold mb-4">{{ editingDocument ? 'Edit Document' : 'Add New Document' }}</h2>
        
        <form @submit.prevent="handleSaveDocument" class="space-y-6">
          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title:</label>
            <input v-model="documentForm.title" type="text" id="title" required class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-indigo-600 focus:border-indigo-600" />
          </div>
          <!-- Content -->
          <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Contents:</label>
            <textarea v-model="documentForm.content" id="content" rows="4" required class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-indigo-600 focus:border-indigo-600"></textarea>
          </div>
          <!-- Category -->
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Category:</label>
            <select v-model="documentForm.category_id" id="category" required class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-indigo-600 focus:border-indigo-600">
              <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>
          </div>
          <!-- File Upload -->
          <div>
            <label for="file" class="block text-sm font-medium text-gray-700">Document File:</label>
            <input type="file" id="file" @change="handleFileUpload" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:ring-indigo-600 focus:border-indigo-600" />
          </div>
          
          <!-- Action Buttons -->
          <div class="flex justify-end space-x-4">
            <button type="button" @click="closeDocumentModal" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-400">Cancel</button>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-500">{{ editingDocument ? 'Update' : 'Add' }} Document</button>
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
      documents: [],
      categories: [],
      searchQuery: '',
      sortOption: 'title',
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
      let filtered = this.documents.filter(doc =>
        doc.title.toLowerCase().includes(this.searchQuery.toLowerCase())
      );

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
            console.error('Error saving document:', error);
          });
      } else {
        axios
          .post('/api/Admin-documents', formData)
          .then(() => {
            this.fetchDocuments();
            this.closeDocumentModal();
          })
          .catch(error => {
            console.error('Error saving document:', error);
          });
      }
    },
    handleDeleteDocument(id) {
      axios
        .delete(`/api/Admin-documents/${id}`)
        .then(() => {
          this.fetchDocuments();
        })
        .catch(error => {
          console.error('Error deleting document:', error);
        });
    },
    downloadDocument(document) {
      axios({
        url: `/api/Admin-documents/${document.id}/download`,
        method: 'GET',
        responseType: 'blob',
      }).then(response => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `${document.title}.pdf`);
        document.body.appendChild(link);
        link.click();
      });
    },
    searchDocuments() {
      this.filteredDocuments;
    },
    sortDocuments() {
      this.filteredDocuments;
    },
  },
  mounted() {
    this.fetchDocuments();
    this.fetchCategories();
  },
};
</script>

<style scoped>
.container {
  max-width: 1200px;
}
</style>
