<template>
  <header
    class="bg-blue-600 text-white px-6 py-4 flex justify-between items-center shadow"
  >
    <h1 class="text-xl font-semibold">Smart Payroll</h1>
    <div class="flex items-center space-x-4">
      <span class="text-sm">Hello, {{ user?.email }}</span>
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

const auth = useAuthStore();
const user = auth.user;
const router = useRouter();

const logout = async () => {
  await auth.logout();
  router.push("/login");
};
</script>
