<template>
  <header
    class="bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-200 px-4 sm:px-6 py-4 sm:py-6 flex justify-between items-center shadow-lg transition-colors duration-300"
  >
    <button
      @click="toggleSidebar"
      class="text-gray-600 dark:text-gray-300 p-2 rounded-md lg:hidden hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 12h16M4 18h16"
        />
      </svg>
    </button>

    <h1 class="text-lg sm:text-xl font-semibold">Smart Payroll</h1>
    <div class="flex items-center space-x-4 sm:space-x-6 relative">
      <div v-if="auth.user" class="relative">
        <button
          @click="toggleProfileDropdown"
          class="flex items-center space-x-2 text-gray-700 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors duration-200"
        >
          <span class="text-sm sm:text-base font-medium">{{
            auth.user?.name
          }}</span>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="w-5 h-5"
          >
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
          </svg>
        </button>

        <transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <div
            v-if="isProfileDropdownOpen"
            class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-xl z-50 py-1"
          >
            <router-link
              to="/userProfile"
              @click="closeProfileDropdown"
              class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
            >
              Profile
            </router-link>
            <button
              @click="logoutAndCloseDropdown"
              class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200"
            >
              Logout
            </button>
          </div>
        </transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { useAuthStore } from "@/store/auth";
import { toggleSidebar } from "@/store/sidebar"; // Import toggleSidebar
import { onMounted, onUnmounted, ref } from "vue";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

// State for the profile dropdown
const isProfileDropdownOpen = ref(false);

const toggleProfileDropdown = () => {
  isProfileDropdownOpen.value = !isProfileDropdownOpen.value;
};

const closeProfileDropdown = () => {
  isProfileDropdownOpen.value = false;
};

const logoutAndCloseDropdown = async () => {
  await logout();
  closeProfileDropdown();
};

const logout = async () => {
  await auth.logout();
  router.push("/login");
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (isProfileDropdownOpen.value && !event.target.closest(".relative")) {
    closeProfileDropdown();
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>
