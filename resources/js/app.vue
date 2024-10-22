<template>
    <router-view></router-view>
  </template>
  
  <script>
  import { createRouter, createWebHistory } from 'vue-router';
  import Login from './components/Login.vue';
  
  const routes = [
    { path: '/login', component: Login },
  ];
  
  const router = createRouter({
    history: createWebHistory(),
    routes,
  });
  
  export default {
    setup() {
      return { router };
    },
  };
  </script>
  