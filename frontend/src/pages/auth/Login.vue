<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-100 mb-6">
      Smart Payroll Login
    </h2>

    <!-- Email/Password Login -->
    <form @submit.prevent="handleLogin" class="space-y-4">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Email
        </label>
        <input
          v-model="email"
          type="email"
          id="email"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none
                 focus:ring-blue-400 dark:bg-gray-700 dark:text-white dark:border-gray-600"
        />
      </div>

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Password
        </label>
        <input
          v-model="password"
          type="password"
          id="password"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none
                 focus:ring-blue-400 dark:bg-gray-700 dark:text-white dark:border-gray-600"
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

    <!-- Divider -->
    <div class="flex items-center my-6">
      <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
      <span class="mx-2 text-gray-500 text-sm">OR</span>
      <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
    </div>

    <!-- Google Login Button -->
    <button
      @click="loginWithGoogle"
      class="w-full flex items-center justify-center gap-2 border py-2 rounded-lg font-semibold transition duration-200
             hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 dark:border-gray-600 text-gray-700 dark:text-gray-200"
    >
      <img src="https://www.svgrepo.com/show/355037/google.svg" alt="Google" class="w-5 h-5" />
      Continue with Google
    </button>

    <!-- Links -->
    <div class="mt-4 text-sm text-center">
      <router-link to="/forgot-password" class="text-blue-500 hover:underline">Forgot Password?</router-link>
      <br />
      <span class="text-gray-600 dark:text-gray-400">Don't have an account?</span>
      <router-link to="/register" class="text-blue-600 hover:underline ml-1">Register</router-link>
    </div>
  </AuthLayout>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/store/auth";
import AuthLayout from "@/components/layout/AuthLayout.vue";
import api from "@/services/api";

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

const loginWithGoogle = () => {
  window.location.href = `${api.defaults.baseURL}/auth/google/redirect`;
};
</script>
