<template>
  <div
    class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 transition-colors duration-300"
  >
    <!-- Header -->
    <header
      class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <div class="flex items-center">
            <h1 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
              Smart Payroll
            </h1>
          </div>
          <div class="flex items-center space-x-4">
            <span
              class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full text-sm font-medium"
            >
              Account Pending
            </span>
            <button
              @click="logout"
              class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Account Status Card -->
        <div class="lg:col-span-2">
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center mb-6">
              <div
                class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center mr-4"
              >
                <span class="text-2xl text-indigo-600 dark:text-indigo-400"
                  >👤</span
                >
              </div>
              <div>
                <h2
                  class="text-xl font-semibold text-gray-800 dark:text-gray-100"
                >
                  {{ user.name }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">{{ user.email }}</p>
              </div>
            </div>

            <div
              class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6"
            >
              <div class="flex">
                <div class="flex-shrink-0">
                  <span class="text-yellow-500 text-xl">⏳</span>
                </div>
                <div class="ml-3">
                  <h3
                    class="text-sm font-medium text-yellow-800 dark:text-yellow-200"
                  >
                    Account Pending Approval
                  </h3>
                  <div
                    class="mt-2 text-sm text-yellow-700 dark:text-yellow-300"
                  >
                    <p>
                      Your account is currently being reviewed by our
                      administration team. This process usually takes 1-2
                      business days.
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class="progression-steps">
              <h3
                class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4"
              >
                Account Activation Process
              </h3>
              <div class="space-y-4">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <div
                      class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center"
                    >
                      <span class="text-green-600 dark:text-green-400 text-sm"
                        >1</span
                      >
                    </div>
                  </div>
                  <div class="ml-4">
                    <p
                      class="text-sm font-medium text-gray-800 dark:text-gray-200"
                    >
                      Account Created
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                      Your account has been successfully created in our system.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <div
                      class="w-8 h-8 rounded-full bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center"
                    >
                      <span class="text-yellow-600 dark:text-yellow-400 text-sm"
                        >2</span
                      >
                    </div>
                  </div>
                  <div class="ml-4">
                    <p
                      class="text-sm font-medium text-gray-800 dark:text-gray-200"
                    >
                      Admin Review
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                      Our team is verifying your account details and role
                      permissions.
                    </p>
                  </div>
                </div>

                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <div
                      class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center"
                    >
                      <span class="text-gray-400 dark:text-gray-500 text-sm"
                        >3</span
                      >
                    </div>
                  </div>
                  <div class="ml-4">
                    <p
                      class="text-sm font-medium text-gray-500 dark:text-gray-400"
                    >
                      Account Activation
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-500">
                      You'll receive access to the appropriate dashboard based
                      on your role.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- System Information Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3
              class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4"
            >
              About Smart Payroll System
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4
                  class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mb-2"
                >
                  Ethiopian Payroll Processing
                </h4>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                  <li>• Standard 30 working days per month</li>
                  <li>
                    • Income tax calculation based on Ethiopian tax brackets
                  </li>
                  <li>• Pension contributions (7% employee, 11% employer)</li>
                  <li>• Allowances: Transport, hardship, position-based</li>
                  <li>• Overtime calculated at 1.5x regular rate</li>
                </ul>
              </div>

              <div>
                <h4
                  class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 mb-2"
                >
                  Technology Stack
                </h4>
                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                  <li>• Vue.js 3 - Modern frontend framework</li>
                  <li>• Laravel - Robust backend API</li>
                  <li>• Tailwind CSS - Responsive styling</li>
                  <li>• MySQL - Secure data storage</li>
                  <li>• RESTful API - Seamless communication</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Info Sidebar -->
        <div class="space-y-6">
          <!-- Default Login Credentials (for testing only) -->
<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl shadow-lg p-6">
  <h3 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-4">
    Default Login Credentials (Testing Only)
  </h3>
  <h1><strong>log out and then login again with the following Credentials:</strong></h1>
  <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
    <li>👑 Admin: Email: <b>admin@gmail.com</b>, Password: <b>123456</b></li>
    <li>🛠 Preparer: Email: <b>preparer@gmail.com</b>, Password: <b>123456</b></li>
    <li>✅ Approver: Email: <b>approver@gmail.com</b>, Password: <b>123456</b></li>
    <li>👤 Employee: Email: <b>mulugeta.linger@wu.edu.et</b>, Password: <b>MwUMBrkSVy</b></li>

  </ul>
</div>

          <!-- Support Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3
              class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4"
            >
              Need Help?
            </h3>
            <div class="space-y-3">
              <div class="flex items-center">
                <span class="text-blue-500 mr-3">📧</span>
                <div>
                  <p
                    class="text-sm font-medium text-gray-800 dark:text-gray-200"
                  >
                    Email Support
                  </p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    mulerselinger@gmail.com
                  </p>
                </div>
              </div>
              <div class="flex items-center">
                <span class="text-blue-500 mr-3">📞</span>
                <div>
                  <p
                    class="text-sm font-medium text-gray-800 dark:text-gray-200"
                  >
                    Phone Support
                  </p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    +251947300026
                  </p>
                </div>
              </div>
              <div class="flex items-center">
                <span class="text-blue-500 mr-3">🕒</span>
                <div>
                  <p
                    class="text-sm font-medium text-gray-800 dark:text-gray-200"
                  >
                    Support Hours
                  </p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    Mon-Fri, 8:00 AM - 5:00 PM
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Features Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3
              class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4"
            >
              Once Approved, You Can:
            </h3>
            <div class="space-y-3">
              <div class="flex items-start">
                <span class="text-green-500 mr-3 mt-0.5">✓</span>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Access your personalized dashboard
                </p>
              </div>
              <div class="flex items-start">
                <span class="text-green-500 mr-3 mt-0.5">✓</span>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  View your payroll history and details
                </p>
              </div>
              <div class="flex items-start">
                <span class="text-green-500 mr-3 mt-0.5">✓</span>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Update your personal information
                </p>
              </div>
              <div class="flex items-start">
                <span class="text-green-500 mr-3 mt-0.5">✓</span>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Access transaction records
                </p>
              </div>
            </div>
          </div>

          <!-- Status Update Card -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h3
              class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4"
            >
              Check Approval Status
            </h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
              We'll notify you via email once your account is approved. You can
              also check back here for updates.
            </p>
            <button
              @click="checkStatus"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-md transition-colors flex items-center justify-center"
            >
              <span>Refresh Status</span>
              <svg
                v-if="refreshing"
                class="animate-spin ml-2 h-4 w-4 text-white"
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
                  d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                ></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer
      class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            &copy; {{ new Date().getFullYear() }} Smart Payroll System. All
            rights reserved.
          </p>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 md:mt-0">
            Designed for efficient payroll management in Ethiopia
          </p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { useAuthStore } from "@/store/auth";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { useToast } from "vue-toastification";

const authStore = useAuthStore();
const router = useRouter();
const toast = useToast();
const user = authStore.user;
const refreshing = ref(false);

const checkStatus = async () => {
  refreshing.value = true;
  try {
    // Simulate checking status with the server
    await new Promise((resolve) => setTimeout(resolve, 1000));
    toast.info("Your account status is still pending approval");
  } catch (error) {
    console.error("Error checking status:", error);
  } finally {
    refreshing.value = false;
  }
};

const logout = () => {
  authStore.logout();
  router.push("/login");
};
</script>

<style scoped>
.progression-steps {
  position: relative;
}

.progression-steps::before {
  content: "";
  position: absolute;
  top: 1rem;
  bottom: 1rem;
  left: 0.75rem;
  width: 2px;
  background: linear-gradient(
    to bottom,
    #10b981 0%,
    #10b981 33%,
    #f59e0b 33%,
    #f59e0b 66%,
    #e5e7eb 66%,
    #e5e7eb 100%
  );
  z-index: 0;
}

@media (prefers-color-scheme: dark) {
  .progression-steps::before {
    background: linear-gradient(
      to bottom,
      #059669 0%,
      #059669 33%,
      #d97706 33%,
      #d97706 66%,
      #4b5563 66%,
      #4b5563 100%
    );
  }
}
</style>
