<template>
  <div
    class="min-h-screen flex flex-col justify-center items-center bg-gray-100 dark:bg-gray-900"
  >
    <!-- Theme Toggle Button (top right corner) -->
    <button
      @click="toggleTheme"
      class="absolute top-4 right-4 p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200"
      :title="theme === 'light' ? 'Switch to dark mode' : 'Switch to light mode'"
    >
      <span v-if="theme === 'light'">🌙</span>
      <span v-else>☀️</span>
    </button>

    <main class="w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
      <slot />
    </main>

    <!-- Optional Footer -->
    <footer class="mt-4 text-gray-500 dark:text-gray-400 text-sm text-center">
      &copy; {{ new Date().getFullYear() }} Smart Payroll System
    </footer>
  </div>
</template>

<script setup>
import { theme, toggleTheme } from "@/store/theme";
</script>