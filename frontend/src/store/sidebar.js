// src/store/sidebar.js
import { ref } from "vue";

export const isSidebarOpen = ref(false); // Changed to false for default icon view

export const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

export const closeSidebar = () => {
  isSidebarOpen.value = false;
};
