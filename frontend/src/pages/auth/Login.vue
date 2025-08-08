<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
      Smart Payroll Login
    </h2>

    <form @submit.prevent="handleLogin" class="space-y-4">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700"
          >Email</label
        >
        <input
          v-model="email"
          type="email"
          id="email"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none focus:ring-blue-400"
        />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700"
          >Password</label
        >
        <input
          v-model="password"
          type="password"
          id="password"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none focus:ring-blue-400"
        />
      </div>

      <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>

      <button
        type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200"
      >
        Login
      </button>
    </form>

    <div class="mt-4 text-sm text-center">
      <router-link to="/forgot-password" class="text-blue-500 hover:underline"
        >Forgot Password?</router-link
      >
      <br />
      <span class="text-gray-600">Don't have an account?</span>
      <router-link to="/register" class="text-blue-600 hover:underline ml-1"
        >Register</router-link
      >
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/store/auth";
import AuthLayout from "@/components/layout/AuthLayout.vue";

const email = ref("");
const password = ref("");
const error = ref(null);

const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
  error.value = null;
  try {
    await authStore.login(email.value, password.value);

    const role = authStore.user?.role;
    if (role === "admin") router.push("/admin");
    else if (role === "preparer") router.push("/preparer");
    else if (role === "approver") router.push("/approver");
    else router.push("/");
  } catch (err) {
    error.value = err.response?.data?.error || "Login failed";
  }
};
</script>
