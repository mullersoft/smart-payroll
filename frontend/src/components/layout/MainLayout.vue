<script setup>
import Header from "./Header.vue";
import Sidebar from "./Sidebar.vue";
import Footer from "./Footer.vue";
import { isSidebarOpen } from "@/store/sidebar";
import { provide } from "vue";

const scrollControl = {
  disableWindowScroll: () => {
    document.body.style.overflow = "hidden";
    document.documentElement.style.overflow = "hidden";
  },
  enableWindowScroll: () => {
    document.body.style.overflow = "";
    document.documentElement.style.overflow = "";
  },
};

provide("scrollControl", scrollControl);
</script>

<template>
  <div class="flex min-h-screen bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <Sidebar />
    
    <div class="flex-1 flex flex-col transition-all duration-300 relative z-20"
      :class="{
        'ml-0 lg:ml-64': isSidebarOpen,
        'ml-0 lg:ml-20': !isSidebarOpen,
      }"
    >
      <Header />
      
      <main class="p-4 sm:p-6 flex-grow overflow-hidden relative z-20">
        <div class="h-full overflow-y-auto">
          <slot />
        </div>
      </main>
      
      <Footer />
    </div>
  </div>
</template>

<style scoped>
/* Ensure main content has proper stacking context */
.flex-1 {
  position: relative;
  z-index: 20;
}

main {
  position: relative;
  z-index: 20;
}

/* Add some padding to prevent content from being too close to sidebar */
@media (min-width: 1024px) {
  .flex-1 {
    padding-left: 0.5rem;
  }
}
</style>