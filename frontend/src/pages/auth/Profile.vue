<template>
  <div
    class="flex flex-col min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300"
  >
    <!-- Header with dropdown -->
    <header
      class="flex items-center justify-between px-6 py-4 bg-white dark:bg-gray-800 shadow"
    >
      <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
        My Profile
      </h1>

      <div class="relative" ref="dropdownRef">
        <!-- Avatar / User menu button -->
        <button
          @click="dropdownOpen = !dropdownOpen"
          class="flex items-center gap-2 px-3 py-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 focus:outline-none hover:bg-gray-300 dark:hover:bg-gray-600"
        >
          <span class="hidden sm:inline font-medium">{{ user.name }}</span>
          <img
            src="https://www.svgrepo.com/show/452030/account.svg"
            alt="Profile"
            class="w-6 h-6"
          />
        </button>

        <!-- Dropdown menu -->
        <div
          v-if="dropdownOpen"
          class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
        >
          <button
            @click="toggleTheme"
            class="w-full px-4 py-2 text-left text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            <span v-if="theme === 'light'">🌙 Dark Mode</span>
            <span v-else>☀️ Light Mode</span>
          </button>
          <button
            @click="openSection('update')"
            class="w-full px-4 py-2 text-left text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            ✏️ Update Profile
          </button>
          <button
            @click="openSection('password')"
            class="w-full px-4 py-2 text-left text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            🔒 Change Password
          </button>
          <button
            @click="openSection('contact')"
            class="w-full px-4 py-2 text-left text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            📩 Contact Us
          </button>
          <button
            @click="logout"
            class="w-full px-4 py-2 text-left text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
          >
            🚪 Logout
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col items-center px-4 py-6">
      <div
        class="w-full max-w-2xl bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6"
      >
        <!-- Account Information -->
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
          Account Information
        </h2>
        <div class="space-y-2">
          <p class="text-gray-700 dark:text-gray-300">
            <strong>Name:</strong> {{ user.name }}
          </p>
          <p class="text-gray-700 dark:text-gray-300">
            <strong>Email:</strong> {{ user.email }}
          </p>
          <p class="text-gray-700 dark:text-gray-300">
            <strong>Role:</strong> {{ user.role }}
          </p>
        </div>

        <div
          v-if="user.role === 'pending'"
          class="mt-4 p-3 bg-yellow-100 dark:bg-yellow-900 rounded text-sm"
        >
          ⚠️ Your account is pending approval by an Admin. Once approved, you’ll
          gain access to the appropriate dashboard.
        </div>

        <!-- Update Profile -->
        <div
          v-if="activeSection === 'update'"
          class="mt-6 border-t border-gray-300 dark:border-gray-700 pt-4"
        >
          <h3
            class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2"
          >
            Update Profile
          </h3>
          <form @submit.prevent="updateProfile" class="space-y-3">
            <input
              v-model="form.name"
              type="text"
              placeholder="Full Name"
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            <input
              v-model="form.email"
              type="email"
              placeholder="Email"
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            <button
              type="submit"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg transition"
            >
              Save Changes
            </button>
          </form>
        </div>

        <!-- Change Password -->
        <div
          v-if="activeSection === 'password'"
          class="mt-6 border-t border-gray-300 dark:border-gray-700 pt-4"
        >
          <h3
            class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2"
          >
            Change Password
          </h3>
          <form @submit.prevent="changePassword" class="space-y-3">
            <input
              v-model="passwordForm.current_password"
              type="password"
              placeholder="Current Password"
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            <input
              v-model="passwordForm.new_password"
              type="password"
              placeholder="New Password"
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            <input
              v-model="passwordForm.new_password_confirmation"
              type="password"
              placeholder="Confirm New Password"
              class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
            <button
              type="submit"
              class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition"
            >
              Update Password
            </button>
          </form>
        </div>

        <!-- Contact Us -->
        <div
          v-if="activeSection === 'contact'"
          class="mt-6 border-t border-gray-300 dark:border-gray-700 pt-4"
        >
          <h3
            class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-2"
          >
            Contact Us
          </h3>
          <p class="text-gray-600 dark:text-gray-400 text-sm mb-2">
            If you need help with your account or have payroll-related
            questions, our support team is ready to assist you.
          </p>
          <a
            href="mailto:support@smartpayroll.com"
            class="text-blue-600 dark:text-blue-400 hover:underline text-sm"
          >
            📧 support@smartpayroll.com
          </a>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer
      class="text-center py-4 text-gray-600 dark:text-gray-400 text-sm border-t border-gray-200 dark:border-gray-700"
    >
      &copy; {{ new Date().getFullYear() }} Smart Payroll System. Designed for
      efficient payroll management.
    </footer>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/store/auth";
import { theme, toggleTheme } from "@/store/theme";
import api from "@/services/api";
import { useToast } from "vue-toastification";
const toast = useToast();
const authStore = useAuthStore();
const router = useRouter();
const user = authStore.user;

const dropdownOpen = ref(false);
const activeSection = ref(null);
const dropdownRef = ref(null);

// close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    dropdownOpen.value = false;

  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

// Form state
const form = reactive({
  name: user.name,
  email: user.email,
});

const passwordForm = reactive({
  current_password: "",
  new_password: "",
  new_password_confirmation: "",
});

// Update Profile
const updateProfile = async () => {
  try {
    const { data } = await api.put("/profile", form);
    authStore.setAuth(authStore.token, data.user);
    alert("Profile updated successfully!");
    activeSection.value = null;
  } catch (err) {
    console.error("Profile update failed:", err);
    // alert("Failed to update profile.");
    toast.error("❌ Failed to update profile.");
  }
};

// Change Password
const changePassword = async () => {
  try {
    await api.post("/change-password", passwordForm);
    alert("Password updated successfully!");
    passwordForm.current_password = "";
    passwordForm.new_password = "";
    passwordForm.new_password_confirmation = "";
    activeSection.value = null;
  } catch (err) {
    console.error("Password change failed:", err);
    // alert("Failed to update password.");
    toast.error("❌ Failed to update password.");
  }
};

// Logout
const logout = () => {
  authStore.logout();
  router.push("/login");
};

// Open dropdown sections
const openSection = (section) => {
  activeSection.value = section;
  dropdownOpen.value = false;
};
</script>
