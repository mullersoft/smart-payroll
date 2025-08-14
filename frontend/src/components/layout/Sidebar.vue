<script setup>
import { isSidebarOpen, toggleSidebar } from "@/store/sidebar";
import { useAuthStore } from "@/store/auth";
import { theme, toggleTheme } from "@/store/theme";

const auth = useAuthStore();

// Updated allLinks array to include an 'icon' for each link
const allLinks = [
  {
    path: "/admin",
    label: "Admin Dashboard",
    roles: ["admin"],
    icon: `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
  },
  {
    path: "/admin/users",
    label: "Manage Users",
    roles: ["admin"],
    icon: `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
  },
  {
    path: "/preparer",
    label: "Preparer Dashboard",
    roles: ["preparer"],
    icon: `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
  },
  {
    path: "/approver",
    label: "Approver Dashboard",
    roles: ["approver"],
    icon: `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>`,
  },
  {
    path: "/employees",
    label: "Manage Employees",
    roles: ["preparer"],
    icon: `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>`,
  },
  {
    path: "/payrolls",
    label: "Manage Payrolls",
    roles: ["preparer"],
    icon: `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M2 16h20"/><path d="M2 12h20"/><path d="M2 8h20"/><path d="M2 4h20"/><path d="M2 20h20"/><path d="M10 2v20"/><path d="M14 2v20"/></svg>`,
  },
  {
    path: "/bank-accounts",
    label: "Bank Accounts",
    roles: ["preparer"],
    icon: `
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><rect width="20" height="12" x="2" y="6" rx="2"/><path d="M6 12h4m2 0h-2m2 0v4m0-4h-2"/></svg>`,
  },
  // {
  //   path: "/reports/monthly",
  //   label: "Monthly Reports",
  //   roles: ["preparer", "admin"],
  //   icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>`,
  // },
  {
    path: "/transactions",
    label: "Transactions",
    roles: ["preparer", "approver", "admin"],
    icon: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M21 12H3"/><path d="M3 6h18"/><path d="M3 18h18"/><path d="M9 12l-6-6"/><path d="M9 12l-6 6"/></svg>`,
  },
];

const filteredLinks = allLinks.filter((link) =>
  link.roles.includes(auth.user?.role)
);
</script>

<template>
  <div>
    <!-- Sidebar Container -->
    <aside
      class="bg-gray-800 dark:bg-gray-950 text-white dark:text-gray-100 h-full fixed top-0 left-0 shadow-2xl flex flex-col transform transition-all duration-300 ease-in-out z-40"
      :class="isSidebarOpen ? 'w-64' : 'w-20'"
    >
      <div
        class="flex items-center justify-between h-20 mb-6 pl-6 pr-4 bg-gray-900 dark:bg-gray-900 border-b border-gray-700 dark:border-gray-800"
      >
        <span
          v-if="isSidebarOpen"
          class="text-3xl font-bold text-indigo-500 dark:text-indigo-400 select-none tracking-wide"
          >Smart Payroll</span
        >
        <!-- Toggle button inside the sidebar -->
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
          @click="isSidebarOpen = false"
        >
          <span v-html="link.icon" class="text-white"></span>
          <span v-if="isSidebarOpen">{{ link.label }}</span>
          <span
            v-if="!isSidebarOpen"
            class="absolute left-full ml-4 w-auto p-2 bg-gray-900 text-yellow-400 text-sm rounded-md shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap z-50"
          >
            {{ link.label }}
          </span>
        </router-link>
      </nav>

      <!-- Theme Toggle at the bottom of the sidebar -->
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
          <!-- Theme Icon -->
          <span class="text-white">
            <span v-if="theme === 'light'">🌙</span>
            <span v-else>☀️</span>
          </span>
          <!-- Theme Label -->
          <span v-if="isSidebarOpen">Theme</span>
          <!-- Tooltip for collapsed sidebar -->
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
