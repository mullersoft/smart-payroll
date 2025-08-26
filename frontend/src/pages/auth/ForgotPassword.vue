<template>
  <AuthLayout>
   <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-100 mb-6">
      Forgot Password
    </h2>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input
          v-model="email"
          type="email"
          id="email"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none focus:ring-blue-400"
        />
      </div>

      <div v-if="message" class="text-green-600 text-sm">{{ message }}</div>
      <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>

      <button
        type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200"
      >
        Send Reset Link
      </button>
    </form>

    <p class="mt-4 text-sm text-center">
      Remember your password?
      <router-link to="/login" class="text-blue-600 hover:underline">Login</router-link>
    </p>
  </AuthLayout>
</template>

<script setup>
import { ref } from "vue";
import axios from "@/services/api";
import AuthLayout from "@/components/layout/AuthLayout.vue";
import { useToast } from "vue-toastification";
const toast = useToast();

const email = ref("");
const message = ref(null);
const error = ref(null);

const handleSubmit = async () => {
  message.value = null;
  error.value = null;

  try {
    const res = await axios.post("/forgot-password", { email: email.value });
    message.value = res.data.message || "Password reset link sent!";
    toast.success(`✅ ${message.value}`);
  } catch (err) {
    error.value = err.response?.data?.message || "Failed to send reset link.";
    toast.error(`❌ ${error.value}`);
  }
};
</script>
