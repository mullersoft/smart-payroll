<template>
  <AuthLayout>
    <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-100 mb-6">
      Create Your Account
    </h2>

    <!-- Register Form -->
    <form @submit.prevent="handleRegister" class="space-y-4">
      <!-- Full Name -->
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Full Name
        </label>
        <input
          v-model="name"
          maxlength="20"
          minlength="3"
          type="text"
          id="name"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none
                 focus:ring-blue-400 dark:bg-gray-700 dark:text-white dark:border-gray-600"
        />
      </div>

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Email
        </label>
        <input
          v-model="email"
          maxlength="30"
          type="email"
          id="email"
          required
          class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none
                 focus:ring-blue-400 dark:bg-gray-700 dark:text-white dark:border-gray-600"
        />
        <!-- Show validation error -->
        <p v-if="emailError" class="text-red-500 text-sm mt-1">{{ emailError }}</p>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Password
        </label>
        <div class="relative">
          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            id="password"
            required
            class="w-full px-4 py-2 border rounded-lg focus:ring focus:outline-none
                   focus:ring-blue-400 dark:bg-gray-700 dark:text-white dark:border-gray-600 pr-10"
          />

          <!-- Toggle show/hide password -->
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500"
          >
            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0
                       8.268 2.943 9.542 7-1.274 4.057-5.065
                       7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>

            <svg v-else xmlns="http://www.w3.org/2000/svg"
                 class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13.875 18.825A10.05 10.05 0
                       0112 19c-4.477 0-8.268-2.943-9.542-7a9.97
                       9.97 0 012.223-3.592M6.1 6.1A9.969
                       9.969 0 0112 5c4.477 0 8.268
                       2.943 9.542 7a9.97 9.97 0
                       01-4.442 5.411M15 12a3 3 0
                       01-3 3m0-6a3 3 0 013 3m-9
                       9l12-12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- General error -->
      <div v-if="error" class="text-red-500 text-sm">{{ error }}</div>

      <!-- Submit Button -->
      <button
        type="submit"
        :disabled="loading"
        class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700
               text-white py-2 rounded-lg font-semibold transition duration-200"
      >
        <svg
          v-if="loading"
          class="animate-spin h-5 w-5 mr-2 text-white"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle class="opacity-25" cx="12" cy="12" r="10"
                  stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0
                   00-4 4H4z"></path>
        </svg>
        <span>{{ loading ? "Registering..." : "Register" }}</span>
      </button>
    </form>

    <!-- Divider -->
    <div class="flex items-center my-6">
      <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
      <span class="mx-2 text-gray-500 text-sm">OR</span>
      <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
    </div>

    <!-- Google Register -->
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
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

const toast = useToast();
const name = ref("");
const email = ref("");
const password = ref("");
const error = ref(null);
const router = useRouter();
const loading = ref(false);
const showPassword = ref(false);
const emailError = ref(null);

// ✅ Register function
const handleRegister = async () => {
  error.value = null;

  // client-side validation
  if (!name.value.trim()) {
    toast.error("Full name is required.");
    return;
  }
  if (emailError.value) {
    toast.error("Please enter a valid email address.");
    return;
  }
  if (!password.value.trim()) {
    toast.error("Password is required.");
    return;
  }

  loading.value = true;
  try {
    await api.post("/register", {
      name: name.value,
      email: email.value,
      password: password.value,
      role: null,          // ✅ don't send "pending" if column is enum
      status: "pending",   // only status is pending
    });

    toast.success("Registration successful! Your account is pending approval.");
    router.push("/login");

  } catch (err) {
    // check if backend returned a validation error
    const backendMessage = err.response?.data?.message;

    if (backendMessage?.includes("email")) {
      error.value = "This email is already registered or invalid.";
      toast.error(error.value);
    } else {
      error.value = "Registration failed. Please try again.";
      toast.error(error.value);
    }

    // log full error only for developer debugging
    console.error("Developer Error:", err.response?.data || err.message);

  } finally {
    loading.value = false;
  }
};

// ✅ Email validation
const validateEmail = (value) => {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(value);
};

watch(email, (newVal) => {
  if (!newVal) {
    emailError.value = "Email is required.";
  } else if (!validateEmail(newVal)) {
    emailError.value = "Please enter a valid email address.";
  } else {
    emailError.value = null;
  }
});

// ✅ Google OAuth
const registerWithGoogle = () => {
  window.location.href = `${api.defaults.baseURL}/auth/google/redirect`;
};
</script>
