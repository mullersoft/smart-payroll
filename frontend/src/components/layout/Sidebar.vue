<!-- src/components/layout/Sidebar.vue -->
<template>
  <aside
    class="w-64 bg-gray-800 text-white h-full fixed top-0 left-0 pt-4 shadow-lg flex flex-col"
  >
    <!-- Logo area -->
    <div
      class="flex items-center justify-center h-24 mb-6 border-b border-gray-700 px-4"
    >
      <!-- Bold large colored text logo -->
      <span class="text-5xl font-extrabold text-indigo-400 select-none"
        >QMT</span
      >

      <!-- Image logo option (uncomment if you want to use image instead) -->
      <!-- <img src="/logo.png" alt="QMT Logo" class="h-16 object-contain" /> -->
    </div>

    <!-- Navigation links -->
    <nav class="flex flex-col space-y-2 px-4 flex-grow">
      <router-link
        v-for="link in filteredLinks"
        :key="link.path"
        :to="link.path"
        class="py-2 px-3 rounded hover:bg-gray-700 transition"
        active-class="bg-gray-700"
      >
        {{ link.label }}
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
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
