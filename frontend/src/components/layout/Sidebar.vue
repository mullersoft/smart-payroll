<script setup>
import { isSidebarOpen, toggleSidebar } from "@/store/sidebar";
import { useAuthStore } from "@/store/auth";

const auth = useAuthStore();

const allLinks = [
  { path: "/admin", label: "Admin Dashboard", roles: ["admin"] },
  { path: "/admin/users", label: "Manage Users", roles: ["admin"] },
  { path: "/preparer", label: "Preparer Dashboard", roles: ["preparer"] },
  { path: "/approver", label: "Approver Dashboard", roles: ["approver"] },
  { path: "/employees", label: "Manage Employees", roles: ["preparer"] },
  { path: "/payrolls", label: "Manage Payrolls", roles: ["preparer"] },
  { path: "/bank-accounts", label: "Bank Accounts", roles: ["preparer"] },
  {
    path: "/reports/monthly",
    label: "Monthly Reports",
    roles: ["preparer", "admin"],
  },
  {
    path: "/transactions",
    label: "Transactions",
    roles: ["preparer", "approver", "admin"],
  },
  {
    path: "/approver/pending-payrolls",
    label: "Pending Payrolls",
    roles: ["approver"],
  },
];

const filteredLinks = allLinks.filter((link) =>
  link.roles.includes(auth.user?.role)
);
</script>

<template>
  <div>
    <button
      @click="toggleSidebar"
      class="fixed top-4 left-4 z-50 p-2 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 focus:outline-none"
    >
      <span v-if="isSidebarOpen">✖</span>
      <span v-else>☰</span>
    </button>
    <aside
      class="bg-gray-800 dark:bg-gray-900 text-white dark:text-gray-100 h-full fixed top-0 left-0 shadow-lg flex flex-col transform transition-transform duration-300 ease-in-out"
      :class="isSidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64'"
    >
      <div
        class="flex items-center justify-center h-24 mb-6 border-b border-gray-700 dark:border-gray-600 px-4"
      >
        <span class="text-5xl font-extrabold text-indigo-400 select-none"
          >QMT</span
        >
      </div>

      <nav class="flex flex-col space-y-2 px-4 flex-grow">
        <router-link
          v-for="link in filteredLinks"
          :key="link.path"
          :to="link.path"
          class="py-2 px-3 rounded hover:bg-gray-700 dark:hover:bg-gray-800 transition"
          active-class="bg-gray-700 dark:bg-gray-800"
          @click="isSidebarOpen = false"
        >
          {{ link.label }}
        </router-link>
      </nav>
    </aside>

    <!-- <aside
      class="bg-gray-800 text-white h-full fixed top-0 left-0 shadow-lg flex flex-col transform transition-transform duration-300 ease-in-out"
      :class="isSidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full w-64'"
    >
      <div
        class="flex items-center justify-center h-24 mb-6 border-b border-gray-700 px-4"
      >
        <span class="text-5xl font-extrabold text-indigo-400 select-none"
          >QMT</span
        >
      </div>

      <nav class="flex flex-col space-y-2 px-4 flex-grow">
        <router-link
          v-for="link in filteredLinks"
          :key="link.path"
          :to="link.path"
          class="py-2 px-3 rounded hover:bg-gray-700 transition"
          active-class="bg-gray-700"
          @click="isSidebarOpen = false"
        >
          {{ link.label }}
        </router-link>
      </nav>
    </aside> -->
  </div>
</template>
