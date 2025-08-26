<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-100 mb-6">
      Reset Your Password
    </h2>

    <form @submit.prevent="handleReset" class="space-y-4">
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

      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
        <input
          v-model="password"
          type="password"
          id="password"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none focus:ring-blue-400"
        />
      </div>

      <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input
          v-model="password_confirmation"
          type="password"
          id="password_confirmation"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none focus:ring-blue-400"
        />
      </div>

      <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>
      <div v-if="message" class="text-green-600 text-sm">{{ message }}</div>

      <button
        type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200"
      >
        Reset Password
      </button>
    </form>
  </AuthLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "@/services/api";
import AuthLayout from "@/components/layout/AuthLayout.vue";
import { useToast } from "vue-toastification";
const toast = useToast();

const email = ref("");
const password = ref("");
const password_confirmation = ref("");
const token = ref("");
const message = ref(null);
const error = ref(null);

const route = useRoute();
const router = useRouter();

onMounted(() => {
  token.value = route.query.token || "";
  email.value = route.query.email || "";
});

const handleReset = async () => {
  message.value = null;
  error.value = null;

  try {
    await axios.post("/reset-password", {
      email: email.value,
      token: token.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
    });

    message.value = "Password reset successful. Redirecting to login...";
    setTimeout(() => router.push("/login"), 3000);
  } catch (err) {
    error.value = err.response?.data?.message || "Password reset failed.";
    toast.error(`❌ ${error.value}`);
  }
};
</script>
