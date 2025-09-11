<!-- D:\qelem meda\smart-payroll\frontend\src\pages\transactions\PayrollTransactions.vue -->
<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
          Payroll Transactions
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

      <!-- Filter Section -->
      <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
          Filter Transactions
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Month</label
            >
            <select
              v-model="filters.month"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            >
              <option value="">All Months</option>
              <option
                v-for="(month, index) in months"
                :key="index"
                :value="index + 1"
              >
                {{ month }}
              </option>
            </select>
          </div>
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Year</label
            >
            <select
              v-model="filters.year"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            >
              <option v-for="year in availableYears" :key="year" :value="year">
                {{ year }}
              </option>
            </select>
          </div>
          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
              >Type</label
            >
            <select
              v-model="filters.type"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            >
              <option value="">All Types</option>
              <option value="salary">Salary</option>
              <option value="bonus">Bonus</option>
              <option value="deduction">Deduction</option>
              <option value="tax">Tax</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-8">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"
        ></div>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
          Loading transactions...
        </p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="filteredTransactions.length === 0"
        class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8 text-center"
      >
        <svg
          class="w-16 h-16 text-gray-400 mx-auto mb-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          ></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
          No transactions found
        </h3>
        <p class="text-gray-500 dark:text-gray-400">
          No transactions match your current filters.
        </p>
      </div>

      <!-- Transactions List -->
      <div v-else class="space-y-4">
        <div
          v-for="transaction in filteredTransactions"
          :key="transaction.id"
          class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden"
        >
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-gray-100 capitalize"
                >
                  {{ transaction.transaction_type }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  {{ formatDate(transaction.transaction_date) }}
                </p>
              </div>
              <span
                :class="{
                  'px-3 py-1 rounded-full text-xs font-medium': true,
                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200':
                    transaction.status === 'completed',
                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200':
                    transaction.status === 'pending',
                  'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200':
                    transaction.status === 'failed',
                }"
              >
                {{ transaction.status?.toUpperCase() }}
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Amount</p>
                <p
                  class="text-lg font-semibold"
                  :class="{
                    'text-green-600 dark:text-green-400':
                      transaction.transaction_type === 'payment',
                    'text-red-600 dark:text-red-400':
                      transaction.transaction_type === 'deduction',
                  }"
                >
                  Birr {{ formatCurrency(transaction.amount) }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Reference
                </p>
                <p class="text-lg font-mono text-gray-800 dark:text-gray-100">
                  {{ transaction.id }}
                </p>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
              <div>
                <p class="text-gray-600 dark:text-gray-400">
                  From: {{ transaction.from_account }}
                </p>
                <p class="text-gray-600 dark:text-gray-400">
                  To: {{ transaction.to_account }}
                </p>
              </div>
              <div>
                <p class="text-gray-600 dark:text-gray-400">
                  Processed by: {{ transaction.processed_by || "System" }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";
import { formatCurrency, formatDate } from "@/utils/formatters";
import { computed, onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();

const transactions = ref([]);
const loading = ref(true);

const filters = ref({
  month: "",
  year: new Date().getFullYear(),
  type: "",
});

const months = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];

// Computed properties
const availableYears = computed(() => {
  const years = new Set();
  transactions.value.forEach((transaction) => {
    if (transaction.transaction_date) {
      const year = new Date(transaction.transaction_date).getFullYear();
      years.add(year);
    }
  });
  return Array.from(years).sort((a, b) => b - a);
});

const filteredTransactions = computed(() => {
  let filtered = transactions.value;

  // Filter by year
  if (filters.value.year) {
    filtered = filtered.filter((transaction) => {
      if (!transaction.transaction_date) return false;
      const year = new Date(transaction.transaction_date).getFullYear();
      return year === parseInt(filters.value.year);
    });
  }

  // Filter by month
  if (filters.value.month) {
    filtered = filtered.filter((transaction) => {
      if (!transaction.transaction_date) return false;
      const month = new Date(transaction.transaction_date).getMonth() + 1;
      return month === parseInt(filters.value.month);
    });
  }

  // Filter by type
  if (filters.value.type) {
    filtered = filtered.filter(
      (transaction) => transaction.transaction_type === filters.value.type
    );
  }

  // Sort by date (newest first)
  filtered.sort((a, b) => {
    return (
      new Date(b.transaction_date || b.created_at) -
      new Date(a.transaction_date || a.created_at)
    );
  });

  return filtered;
});

// Methods
const fetchTransactions = async () => {
  loading.value = true;
  try {
    const response = await api.get("/my/transactions");
    transactions.value = response.data || [];
  } catch (error) {
    console.error("Error fetching transactions:", error);
    toast.error("Failed to load transactions");
    transactions.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchTransactions();
});
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
