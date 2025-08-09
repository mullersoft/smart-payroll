<!-- frontend/src/components/layout/Header.vue -->
<template>
  <header
    class="bg-blue-600 dark:bg-gray-900 text-white dark:text-gray-200 px-6 py-4 flex justify-between items-center shadow"
  >
    <h1 class="text-xl font-semibold">Smart Payroll</h1>
    <div class="flex items-center space-x-4">
      <span class="text-sm">Hello, {{ user?.email }}</span>

      <!-- Theme toggle -->
      <button
        @click="toggleTheme"
        class="p-2 rounded-md bg-white dark:bg-gray-700 text-blue-600 dark:text-gray-200 hover:opacity-80 transition"
      >
        <span v-if="theme === 'light'">🌙</span>
        <span v-else>☀️</span>
      </button>

      <button
        @click="logout"
        class="bg-white text-blue-600 px-3 py-1 rounded hover:bg-gray-100 transition text-sm"
      >
        Logout
      </button>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from "@/store/auth";
import { useRouter } from "vue-router";
import { theme, toggleTheme } from "@/store/theme";

const auth = useAuthStore();
const user = auth.user;
const router = useRouter();

const logout = async () => {
  await auth.logout();
  router.push("/login");
};
</script>
