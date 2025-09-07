<!-- D:\qelem meda\smart-payroll\frontend\src\pages\profile\EmployeeProfile.vue -->
<template>
  <MainLayout>
    <div class="max-w-3xl mx-auto space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
          My Profile
        </h1>
        <button
          @click="$router.back()"
          class="flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            ></path>
          </svg>
          Back to Dashboard
        </button>
      </div>

      <!-- USER PROFILE (always visible) -->
      <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Account Information</h2>
        <form @submit.prevent="handleUserUpdate" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium">Name</label>
            <input
              v-model="userForm.name"
              type="text"
              required
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium">Email</label>
            <input
              v-model="userForm.email"
              type="email"
              disabled
              class="w-full px-4 py-2 border rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500"
            />
          </div>

          <!-- Password -->
          <div>
            <label class="block text-sm font-medium">New Password</label>
            <input
              v-model="userForm.password"
              type="password"
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Confirm Password -->
          <div>
            <label class="block text-sm font-medium">Confirm Password</label>
            <input
              v-model="userForm.password_confirmation"
              type="password"
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-md font-semibold"
          >
            {{ loading ? "Updating..." : "Update Account" }}
          </button>
        </form>
      </div>

      <!-- EMPLOYEE INFO (only for employees) -->
      <div
        v-if="auth.user?.role === 'employee'"
        class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6"
      >
        <h2 class="text-lg font-semibold mb-4">Employee Information</h2>
        <form @submit.prevent="handleEmployeeUpdate" class="space-y-4">
          <!-- Bank Account Number -->
          <div>
            <label class="block text-sm font-medium">Bank Account Number</label>
            <input
              v-model="employeeForm.bank_account_number"
              type="text"
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Bank Name -->
          <div>
            <label class="block text-sm font-medium">Bank Name</label>
            <input
              v-model="employeeForm.bank_name"
              type="text"
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white"
            />
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white px-4 py-2 rounded-md font-semibold"
          >
            {{ loading ? "Updating..." : "Update Employee Info" }}
          </button>
        </form>
      </div>

      <!-- Messages -->
      <div v-if="errorMessage" class="text-red-500 text-sm">{{ errorMessage }}</div>
      <div v-if="successMessage" class="text-green-500 text-sm">{{ successMessage }}</div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";
import { useAuthStore } from "@/store/auth";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

const auth = useAuthStore();
const toast = useToast();

const userForm = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
});

const employeeForm = ref({
  bank_account_number: "",
  bank_name: "",
});

const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

// ✅ Fetch initial data
const fetchProfile = async () => {
  try {
    // Fill user data
    userForm.value = {
      name: auth.user?.name || "",
      email: auth.user?.email || "",
      password: "",
      password_confirmation: "",
    };

    // Fetch employee info if applicable
    if (auth.user?.role === "employee") {
      const { data } = await api.get("/my/profile");
      employeeForm.value = {
        bank_account_number: data.bank_account?.account_number || "",
        bank_name: data.bank_account?.bank_name || "",
      };
    }
  } catch (err) {
    console.error("Error fetching profile:", err);
    toast.error("Failed to load profile data");
  }
};

// ✅ Update User Info
const handleUserUpdate = async () => {
  loading.value = true;
  errorMessage.value = "";
  successMessage.value = "";
  try {
    await api.put("/profile/update-user", userForm.value);
    toast.success("Account updated successfully");
    successMessage.value = "Account updated successfully";
  } catch (err) {
    errorMessage.value = err.response?.data?.message || "Failed to update account";
    toast.error(errorMessage.value);
  } finally {
    loading.value = false;
  }
};

// ✅ Update Employee Info
const handleEmployeeUpdate = async () => {
  loading.value = true;
  errorMessage.value = "";
  successMessage.value = "";
  try {
    await api.put("/profile/update-employee", employeeForm.value);
    toast.success("Employee info updated successfully");
    successMessage.value = "Employee info updated successfully";
  } catch (err) {
    errorMessage.value = err.response?.data?.message || "Failed to update employee info";
    toast.error(errorMessage.value);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchProfile);
</script>
