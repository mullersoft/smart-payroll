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
          <svg
            class="w-4 h-4 mr-1"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
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

      <!-- Profile Form -->
      <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <form @submit.prevent="handleUpdate" class="space-y-4">
          <!-- Name -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Name</label
            >
            <input
              v-model="profileForm.name"
              type="text"
              required
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring focus:ring-blue-400 dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Email (readonly) -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Email</label
            >
            <input
              v-model="profileForm.email"
              type="email"
              disabled
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-500 dark:text-gray-400"
            />
          </div>

          <!-- Password -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >New Password</label
            >
            <input
              v-model="profileForm.password"
              type="password"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring focus:ring-blue-400 dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Confirm Password -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Confirm Password</label
            >
            <input
              v-model="profileForm.password_confirmation"
              type="password"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring focus:ring-blue-400 dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Bank Details -->
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Bank Account Number</label
            >
            <input
              v-model="profileForm.bank_account_number"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring focus:ring-blue-400 dark:bg-gray-700 dark:text-white"
            />
          </div>
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Bank Name</label
            >
            <input
              v-model="profileForm.bank_name"
              type="text"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring focus:ring-blue-400 dark:bg-gray-700 dark:text-white"
            />
          </div>

          <!-- Error -->
          <div v-if="profileError" class="text-red-500 text-sm">
            {{ profileError }}
          </div>

          <!-- Success -->
          <div v-if="profileSuccess" class="text-green-500 text-sm">
            {{ profileSuccess }}
          </div>

          <!-- Submit -->
          <div class="flex space-x-4">
            <button
              type="submit"
              :disabled="profileLoading"
              class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-md font-semibold transition duration-200"
            >
              {{ profileLoading ? "Updating..." : "Update Profile" }}
            </button>
            <button
              type="button"
              @click="$router.back()"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md font-semibold transition duration-200 dark:bg-gray-600 dark:hover:bg-gray-500 dark:text-white"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";
import { useAuthStore } from "@/store/auth";
import { ref, onMounted } from "vue";
import { useToast } from "vue-toastification";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const toast = useToast();
const router = useRouter();

const employeeData = ref({});
const profileForm = ref({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  bank_account_number: "",
  bank_name: "",
});
const profileLoading = ref(false);
const profileError = ref("");
const profileSuccess = ref("");

// Methods
const handleUpdate = async () => {
  profileError.value = "";
  profileSuccess.value = "";
  profileLoading.value = true;

  try {
    const formData = new FormData();

    // Append form fields
    Object.keys(profileForm.value).forEach((key) => {
      if (profileForm.value[key]) formData.append(key, profileForm.value[key]);
    });

    const response = await api.post("/profile/update", formData, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    // Update auth store with new user data
    auth.setUser(response.data.user);

    profileSuccess.value = "Profile updated successfully!";
    toast.success("Profile updated successfully!");

    // Refresh employee data
    await fetchEmployeeData();
  } catch (err) {
    profileError.value = err.response?.data?.message || "Update failed";
    toast.error(profileError.value);
  } finally {
    profileLoading.value = false;
  }
};

const fetchEmployeeData = async () => {
  try {
    const response = await api.get("/my/profile");
    employeeData.value = response.data;

    // Initialize form with current data
    profileForm.value = {
      name: auth.user?.name || "",
      email: auth.user?.email || "",
      password: "",
      password_confirmation: "",
      bank_account_number:
        employeeData.value.bank_account?.account_number || "",
      bank_name: employeeData.value.bank_account?.bank_name || "",
    };
  } catch (error) {
    console.error("Error fetching employee data:", error);
    toast.error("Failed to load employee data");
  }
};

onMounted(() => {
  fetchEmployeeData();
});
</script>
