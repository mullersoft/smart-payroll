<!-- src/components/layout/MainLayout.vue -->
<script setup>
import Header from "./Header.vue";
import Sidebar from "./Sidebar.vue";
import Footer from "./Footer.vue";
import { isSidebarOpen } from "@/store/sidebar";
</script>

<template>
  <div
    class="flex min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300"
  >
    <Sidebar />
    <div
      class="flex-1 flex flex-col transition-all duration-300"
      :class="isSidebarOpen ? 'ml-64' : 'ml-20'"
    >
      <Header />
      <main class="p-6 flex-grow">
        <slot />
      </main>
      <Footer />
    </div>
  </div>
</template>
