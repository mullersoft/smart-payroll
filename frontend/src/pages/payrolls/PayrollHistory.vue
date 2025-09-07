<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
          Payroll History
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
          Filter Payroll Records
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
              >Status</label
            >
            <select
              v-model="filters.status"
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            >
              <option value="">All Status</option>
              <option value="draft">Draft</option>
              <option value="prepared">Prepared</option>
              <option value="approved">Approved</option>
              <option value="paid">Paid</option>
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
          Loading payroll history...
        </p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="filteredPayrolls.length === 0"
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
          No payroll records found
        </h3>
        <p class="text-gray-500 dark:text-gray-400">
          No payroll records match your current filters.
        </p>
      </div>

      <!-- Payroll Records -->
      <div v-else class="space-y-4">
        <div
          v-for="payroll in filteredPayrolls"
          :key="payroll.id"
          class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden"
        >
          <div class="p-6">
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3
                  class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                >
                  {{ formatDate(payroll.pay_month, "monthYear") }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Payroll ID: {{ payroll.id }}
                </p>
              </div>
              <span
                :class="{
                  'px-3 py-1 rounded-full text-xs font-medium': true,
                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200':
                    payroll.status === 'prepared',
                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200':
                    payroll.status === 'approved',
                  'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200':
                    payroll.status === 'paid',
                  'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200':
                    payroll.status === 'draft',
                }"
              >
                {{ payroll.status?.toUpperCase() }}
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Gross Pay
                </p>
                <p
                  class="text-lg font-semibold text-gray-800 dark:text-gray-100"
                >
                  Birr {{ formatCurrency(payroll.gross_pay) }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                  Deductions
                </p>
                <p class="text-lg font-semibold text-red-600 dark:text-red-400">
                  Birr {{ formatCurrency(payroll.total_deduction) }}
                </p>
              </div>
              <div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Net Pay</p>
                <p
                  class="text-lg font-semibold text-green-600 dark:text-green-400"
                >
                  Birr {{ formatCurrency(payroll.net_payment) }}
                </p>
              </div>
            </div>

            <div class="flex justify-between items-center">
              <div class="text-sm text-gray-600 dark:text-gray-400">
                Processed on {{ formatDate(payroll.created_at) }}
              </div>
              <button
                @click="viewPayrollDetails(payroll)"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition-colors"
              >
                View Details
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="filteredPayrolls.length > 0"
        class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4"
      >
        <div class="flex justify-between items-center">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Showing {{ filteredPayrolls.length }} of
            {{ payrolls.length }} records
          </p>
          <div class="flex space-x-2">
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Previous
            </button>
            <button
              @click="currentPage++"
              :disabled="currentPage * itemsPerPage >= filteredPayrolls.length"
              class="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Payroll Details Modal -->
      <PayrollDetailsModal
        :visible="showPayrollModal"
        :payroll="selectedPayroll"
        @close="showPayrollModal = false"
      />
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import PayrollDetailsModal from "@/components/payroll/PayrollDetailsModal.vue";
import api from "@/services/api";
import { computed, onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();

const payrolls = ref([]);
const loading = ref(true);
const showPayrollModal = ref(false);
const selectedPayroll = ref(null);
const currentPage = ref(1);
const itemsPerPage = ref(10);

const filters = ref({
  year: new Date().getFullYear(),
  month: "",
  status: "",
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
  payrolls.value.forEach((payroll) => {
    if (payroll.pay_month) {
      const year = new Date(payroll.pay_month).getFullYear();
      years.add(year);
    }
  });
  return Array.from(years).sort((a, b) => b - a);
});

const filteredPayrolls = computed(() => {
  let filtered = payrolls.value;

  // Filter by year
  if (filters.value.year) {
    filtered = filtered.filter((payroll) => {
      if (!payroll.pay_month) return false;
      const year = new Date(payroll.pay_month).getFullYear();
      return year === parseInt(filters.value.year);
    });
  }

  // Filter by month
  if (filters.value.month) {
    filtered = filtered.filter((payroll) => {
      if (!payroll.pay_month) return false;
      const month = new Date(payroll.pay_month).getMonth() + 1;
      return month === parseInt(filters.value.month);
    });
  }

  // Filter by status
  if (filters.value.status) {
    filtered = filtered.filter(
      (payroll) => payroll.status === filters.value.status
    );
  }

  // Sort by date (newest first)
  filtered.sort((a, b) => {
    return (
      new Date(b.pay_month || b.created_at) -
      new Date(a.pay_month || a.created_at)
    );
  });

  return filtered;
});

// Methods
const formatCurrency = (value) => {
  const num = parseFloat(value || 0);
  return num.toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });
};

const formatDate = (dateString, format = "full") => {
  if (!dateString) return "N/A";

  const date = new Date(dateString);
  if (format === "monthYear") {
    return date.toLocaleDateString("en-US", { year: "numeric", month: "long" });
  }

  return date.toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

const viewPayrollDetails = (payroll) => {
  selectedPayroll.value = payroll;
  showPayrollModal.value = true;
};

const fetchPayrollHistory = async () => {
  loading.value = true;
  try {
    const response = await api.get("/my/payrolls");
    payrolls.value = response.data || [];
  } catch (error) {
    console.error("Error fetching payroll history:", error);
    toast.error("Failed to load payroll history");
    payrolls.value = [];
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchPayrollHistory();
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
