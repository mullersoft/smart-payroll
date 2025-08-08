<template>
  <div class="flex min-h-screen">
    <Sidebar />
    <div class="flex-1 ml-64 flex flex-col">
      <Header />
      <main class="p-6 bg-gray-50 flex-grow">
        <slot />
      </main>
      <Footer />
    </div>
  </div>
</template>

<script setup>
import Header from "./Header.vue";
import Sidebar from "./Sidebar.vue";
import Footer from "./Footer.vue";
</script>
