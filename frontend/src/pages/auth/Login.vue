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
          <!-- type="password" -->

        <div class="relative">
  <input
    v-model="password"
    :type="showPassword ? 'text' : 'password'"
    id="password"
    required
    class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none
           focus:ring-blue-400 dark:bg-gray-700 dark:text-white dark:border-gray-600 pr-10"
  />

  <!-- Toggle button -->
  <button
    type="button"
    @click="showPassword = !showPassword"
    class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500"
  >
    <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    </svg>

    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97
               9.97 0 012.223-3.592M6.1 6.1A9.969 9.969 0 0112 5c4.477 0 8.268
               2.943 9.542 7a9.97 9.97 0 01-4.442 5.411M15 12a3 3 0
               01-3 3m0-6a3 3 0 013 3m-9 9l12-12" />
    </svg>
  </button>
</div>

      </div>

      <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>

      <button
        type="submit"


          :disabled="loading"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200"
      >

<svg
    v-if="loading"
    class="animate-spin h-5 w-5 mr-2 text-white"
    xmlns="http://www.w3.org/2000/svg"
    fill="none"
    viewBox="0 0 24 24"
  >
  <circle
      class="opacity-25"
      cx="12"
      cy="12"
      r="10"
      stroke="currentColor"
      stroke-width="4"
    ></circle>
    <path
      class="opacity-75"
      fill="currentColor"
      d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
    ></path>
  </svg>

    <span>{{ loading ? "Logging in..." : "Login" }}</span>


        <!-- Login -->
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
import AuthLayout from "@/components/layout/AuthLayout.vue";
import { useAuthStore } from "@/store/auth";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

const toast = useToast();
const email = ref("");
const password = ref("");
const error = ref(null);
const loading = ref(false);
const showPassword = ref(false);



const authStore = useAuthStore();
const router = useRouter();

const handleLogin = async () => {
  error.value = null;
    loading.value = true; // start spinner

  try {
    await authStore.login(email.value, password.value);
    const role = authStore.user?.role;
    const status = authStore.user?.status;

    if (status === "deactivated") {
      toast.error("Your account is deactivated. Contact admin.");
      authStore.logout(); // log out immediately
    }
    else if (status === "pending") {
      router.push("/PendingProfile"); // pending users go to profile
    }
     else {
      // active users with assigned roles
      if (role === "admin") router.push("/admin");
      else if (role === "preparer") router.push("/preparer");
      else if (role === "approver") router.push("/approver");
      else router.push("/employee-dashboard");
    }
  } catch (err) {
    error.value = err.response?.data?.error || "Login failed";
    toast.error(error.value);
    console.error("Login error:", err);
  }
finally {
    loading.value = false; // stop spinner
  }
};

const loginWithGoogle = () => {

  // window.location.href = `${api.defaults.baseURL}/auth/google/redirect`;
  window.location.href = "https://smart-payroll-api.onrender.com/api/auth/google";

};
</script>
