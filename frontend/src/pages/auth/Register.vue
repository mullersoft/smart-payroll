<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">
      Create Your Account
    </h2>

    <form @submit.prevent="handleRegister" class="space-y-4">
      <!-- Full Name Field -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
        <input
          v-model="name"
          type="text"
          id="name"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none focus:ring-blue-400"
        />
      </div>
      
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

      <div>
        <label for="role" class="block text-sm font-medium text-gray-700"
          >Role</label
        >
        <select
          v-model="role"
          id="role"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none focus:ring-blue-400"
        >
          <option disabled value="">Select role</option>
          <option value="admin">Admin</option>
          <option value="preparer">Preparer</option>
          <option value="approver">Approver</option>
        </select>
      </div>

      <div>
        <label for="employee_id" class="block text-sm font-medium text-gray-700"
          >Employee ID (Optional)</label
        >
        <input
          v-model="employee_id"
          type="number"
          id="employee_id"
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none focus:ring-blue-400"
        />
      </div>

      <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>

      <button
        type="submit"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition duration-200"
      >
        Register
      </button>
    </form>

    <p class="mt-4 text-sm text-center">
      Already have an account?
      <router-link to="/login" class="text-blue-600 hover:underline"
        >Login</router-link
      >
    </p>
  </AuthLayout>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import AuthLayout from "@/components/layout/AuthLayout.vue";
import axios from "@/services/api";

const name = ref(""); // Added name ref
const email = ref("");
const password = ref("");
const role = ref("");
const employee_id = ref("");
const error = ref(null);
const router = useRouter();

const handleRegister = async () => {
  error.value = null;
  try {
    const payload = {
      name: name.value, // Added name to the payload
      email: email.value,
      password: password.value,
      role: role.value,
      employee_id: employee_id.value || null,
    };

    await axios.post("/register", payload);
    router.push("/login");
  } catch (err) {
    error.value = err.response?.data?.message || "Registration failed";
  }
};
</script>
