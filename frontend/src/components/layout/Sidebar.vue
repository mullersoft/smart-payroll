<!-- D:\qelem meda\smart-payroll\frontend\src\components\layout\Sidebar.vue -->
<script setup>
import { useAuthStore } from "@/store/auth";
import { closeSidebar, isSidebarOpen, toggleSidebar } from "@/store/sidebar";
import { theme, toggleTheme } from "@/store/theme";
import { onMounted, onUnmounted } from "vue";

const auth = useAuthStore();

const allLinks = [
  // Admin Dashboard
  {
    path: "/admin",
    label: "Admin Dashboard",
    roles: ["admin"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
  },
  {
    path: "/admin/users",
    label: "Manage Users",
    roles: ["admin"],
    icon: `<svg xmlns="http://www.w3.org2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
  },
  {
    path: "/admin/employees",
    label: "Manage Employees",
    roles: ["admin"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
  },
  {
    path: "/admin/accounts",
    label: "Manage Accounts",
    roles: ["admin"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect width="20" height="12" x="2" y="6" rx="2"/><path d="M6 12h4m2 0h-2m2 0v4m0-4h-2"/></svg>`,
  },

  // Preparer Dashboard
  {
    path: "/preparer",
    label: "Preparer Dashboard",
    roles: ["preparer"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
  },
  {
    path: "/preparer/payrolls",
    label: "Manage Payrolls",
    roles: ["preparer"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M2 16h20"/><path d="M2 12h20"/><path d="M2 8h20"/><path d="M2 4h20"/><path d="M2 20h20"/><path d="M10 2v20"/><path d="M14 2v20"/></svg>`,
  },

  // Approver Dashboard
  {
    path: "/approver",
    label: "Approver Dashboard",
    roles: ["approver"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
  },
 // Shared Pages
  {
    path: "/transactions",
    label: "All Transactions",
    roles: ["preparer", "approver"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M21 12H3"/><path d="M3 6h18"/><path d="M3 18h18"/><path d="M9 12l-6-6"/><path d="M9 12l-6 6"/></svg>`,
  },
  // Employee Dashboard and Pages
  {
    path: "/employee-dashboard",
    label: "Dashboard",
    roles: [ "employee"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
  },
  {
    path: "/payroll-history",
    label: "Payroll History",
    roles: [ "employee"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M2 16h20"/><path d="M2 12h20"/><path d="M2 8h20"/><path d="M2 4h20"/><path d="M2 20h20"/><path d="M10 2v20"/><path d="M14 2v20"/></svg>`,
  },
  {
    path: "/payroll-transactions",
    label: "Payroll Transactions",
    roles: [  "employee"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M21 12H3"/><path d="M3 6h18"/><path d="M3 18h18"/><path d="M9 12l-6-6"/><path d="M9 12l-6 6"/></svg>`,
  },
  {
    path: "/EmployeeProfile",
    label: "My Profile",
    roles: [ "admin","preparer","approver","employee"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`,
  },


];

// Determine user role for filtering
const getUserRole = () => {
  if (auth.user?.role) return auth.user.role;
  // If user has no role but is authenticated, treat as employee
  if (auth.user) return "employee";
  return null;
};

const userRole = getUserRole();
const filteredLinks = allLinks.filter((link) =>
  link.roles.includes(userRole)
);

const handleLinkClick = () => {
  closeSidebar();
};

const checkScreenSize = () => {
  if (window.innerWidth >= 1024) {
    isSidebarOpen.value = false;
  } else {
    isSidebarOpen.value = false;
  }
};

onMounted(() => {
  checkScreenSize();
  window.addEventListener("resize", checkScreenSize);
});

onUnmounted(() => {
  window.removeEventListener("resize", checkScreenSize);
});
</script>

<template>
  <div>
    <div
      v-if="isSidebarOpen"
      @click="closeSidebar"
      class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
    ></div>

    <aside
      class="bg-gray-800 dark:bg-gray-950 text-white dark:text-gray-100 h-full fixed top-0 left-0 shadow-2xl flex flex-col transform transition-all duration-300 ease-in-out z-40"
      :class="{
        'w-64 translate-x-0': isSidebarOpen,
        '-translate-x-full lg:translate-x-0': !isSidebarOpen,
        'lg:w-64': isSidebarOpen,
        'lg:w-20': !isSidebarOpen,
      }"
    >
      <div
        class="flex items-center justify-between h-20 mb-6 pl-6 pr-4 bg-gray-900 dark:bg-gray-900 border-b border-gray-700 dark:border-gray-800"
      >
        <span
          v-if="isSidebarOpen"
          class="text-xl sm:text-2xl font-bold text-indigo-500 dark:text-indigo-400 select-none tracking-wide"
          >Smart Payroll</span
        >
        <button
          @click="toggleSidebar"
          :title="isSidebarOpen ? 'Close Sidebar' : 'Open Sidebar'"
          class="p-2 text-gray-400 dark:text-gray-400 rounded-full hover:bg-gray-700 dark:hover:bg-gray-700 focus:outline-none transition-colors duration-200"
        >
          <span v-if="isSidebarOpen" class="font-bold">✖</span>
          <span v-else class="font-bold">☰</span>
        </button>
      </div>

      <nav class="flex flex-col space-y-2 px-6 flex-grow">
        <router-link
          v-for="link in filteredLinks"
          :key="link.path"
          :to="link.path"
          class="py-3 rounded-lg text-gray-300 hover:bg-gray-700 dark:text-gray-300 dark:hover:bg-indigo-800 transition-colors duration-200 flex items-center relative group"
          :class="{
            'active-link': $route.path === link.path,
            'justify-start gap-3 px-4': isSidebarOpen,
            'justify-center': !isSidebarOpen,
          }"
          active-class="bg-indigo-600 font-semibold text-white dark:bg-indigo-700 dark:text-white"
          @click="handleLinkClick"
        >
          <span v-html="link.icon" class="text-white"></span>
          <span v-if="isSidebarOpen">{{ link.label }}</span>
         <span
  v-if="!isSidebarOpen"
  class="absolute left-full ml-4 w-auto p-2 bg-gray-900 text-yellow-400 text-sm rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap z-50 pointer-events-none"
>
  {{ link.label }}
</span>

        </router-link>
      </nav>

      <div
        class="mt-auto px-6 pb-6 pt-4 border-t border-gray-700 dark:border-gray-800"
      >
        <button
          @click="toggleTheme"
          :title="!isSidebarOpen ? 'Toggle Theme' : ''"
          class="py-3 rounded-lg text-gray-300 hover:bg-gray-700 dark:hover:bg-indigo-800 transition-colors duration-200 flex items-center relative group w-full"
          :class="{
            'justify-start gap-3 px-4': isSidebarOpen,
            'justify-center': !isSidebarOpen,
          }"
        >
          <span class="text-white">
            <span v-if="theme === 'light'">🌙</span>
            <span v-else>☀️</span>
          </span>
          <span v-if="isSidebarOpen">Theme</span>
          <span
            v-if="!isSidebarOpen"
            class="absolute left-full ml-4 w-auto p-2 bg-gray-900 text-yellow-400 text-sm rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap z-50"
          >
            Toggle Theme
          </span>
        </button>
      </div>
    </aside>
  </div>
</template>
