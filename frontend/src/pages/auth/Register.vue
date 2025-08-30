<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-100 mb-6">
      Create Your Account
    </h2>

    <!-- Register Form -->
    <form @submit.prevent="handleRegister" class="space-y-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Full Name
        </label>
        <input
          v-model="name"
          type="text"
          id="name"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none
                 focus:ring-blue-400 dark:bg-gray-700 dark:text-white dark:border-gray-600"
        />
      </div>

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

      <div>
        <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Role
        </label>
        <select
          v-model="role"
          id="role"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none
                 focus:ring-blue-400 dark:bg-gray-700 dark:text-white dark:border-gray-600"
        >
          <option disabled value="">Select role</option>
          <option value="preparer">Preparer</option>
          <option value="approver">Approver</option>
          <option value="admin">Admin</option>
          <option value="pending">Pending</option>
        </select>
      </div>



      <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>

      <button
        type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200"
      >
        Register
      </button>
    </form>

    <!-- Divider -->
    <div class="flex items-center my-6">
      <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
      <span class="mx-2 text-gray-500 text-sm">OR</span>
      <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
    </div>

    <!-- Google Register Button -->
    <button
      @click="registerWithGoogle"
      class="w-full flex items-center justify-center gap-2 border py-2 rounded-lg font-semibold transition duration-200
             hover:bg-gray-100 dark:hover:bg-gray-700 dark:bg-gray-800 dark:border-gray-600 text-gray-700 dark:text-gray-200"
    >
      <img src="https://www.svgrepo.com/show/355037/google.svg" alt="Google" class="w-5 h-5" />
      Sign Up with Google
    </button>

    <p class="mt-4 text-sm text-center text-gray-600 dark:text-gray-400">
      Already have an account?
      <router-link to="/login" class="text-blue-600 hover:underline">Login</router-link>
    </p>
  </AuthLayout>
</template>

<script setup>
import AuthLayout from "@/components/layout/AuthLayout.vue";
import api from "@/services/api";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";
const toast = useToast();

const name = ref("");
const email = ref("");
const password = ref("");
const role = ref("");
const employee_id = ref("");
const error = ref(null);
const router = useRouter();

const handleRegister = async () => {
  error.value = null;
  try {
    await api.post("/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      role: role.value,
    });
    router.push("/login");
  } catch (err) {
    error.value = err.response?.data?.message || "Registration failed";
    toast.error(`${error.value}`);
  }
};

const registerWithGoogle = () => {
  window.location.href = `${api.defaults.baseURL}/auth/google/redirect`;

};
</script>
