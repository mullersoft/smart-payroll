<template>
  <MainLayout>
    <div class="transactions-page space-y-6">
      <!-- Header Section -->
      <div class="dashboard-header">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
            <i class="fas fa-exchange-alt mr-3 text-blue-500"></i>
            Payroll Transactions
          </h1>
          <p class="text-gray-600 dark:text-gray-300 mt-1">
            Monitor and manage all payroll transactions
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <button
            class="px-3 py-1 bg-gray-100 rounded-md text-sm hidden sm:inline hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600"
            @click="fetchTransactions"
            :disabled="loading"
            title="Refresh transactions"
          >
            <i class="fas fa-sync-alt mr-1" :class="{ 'fa-spin': loading }"></i>
            {{ loading ? 'Refreshing...' : 'Refresh' }}
          </button>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="summary-card total">
          <div class="card-icon">
            <i class="fas fa-list-alt"></i>
          </div>
          <div class="card-content">
            <h3>Total Transactions</h3>
            <p class="value">{{ summary.total }}</p>
            <p class="label">All time</p>
          </div>
        </div>

        <div class="summary-card completed">
          <div class="card-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-content">
            <h3>Completed</h3>
            <p class="value">{{ summary.completed }}</p>
            <p class="label">{{ summary.completedPercentage }}% of total</p>
          </div>
        </div>

        <div class="summary-card failed">
          <div class="card-icon">
            <i class="fas fa-times-circle"></i>
          </div>
          <div class="card-content">
            <h3>Failed</h3>
            <p class="value">{{ summary.failed }}</p>
            <p class="label">{{ summary.failedPercentage }}% of total</p>
          </div>
        </div>

        <div class="summary-card amount">
          <div class="card-icon">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="card-content">
            <h3>Total Amount</h3>
            <p class="value">Birr {{ formatCurrency(summary.totalAmount) }}</p>
            <p class="label">Processed</p>
          </div>
        </div>
      </div>

      <!-- Filters Section -->
      <div class="filters-section bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
          Filter Transactions
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Status Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Status
            </label>
            <select
              v-model="filters.status"
              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Statuses</option>
              <option value="completed">Completed</option>
              <option value="failed">Failed</option>
              <option value="pending">Pending</option>
            </select>
          </div>

          <!-- Type Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Type
            </label>
            <select
              v-model="filters.type"
              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Types</option>
              <option value="salary">Salary</option>
              <!-- <option value="bonus">Bonus</option> -->
              <!-- <option value="allowance">Allowance</option> -->
              <option value="tax">Tax</option>
              <option value="pension">Pension</option>

            </select>
          </div>

          <!-- Date Range Filter -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              From Date
            </label>
            <input
              type="date"
              v-model="filters.startDate"
              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              To Date
            </label>
            <input
              type="date"
              v-model="filters.endDate"
              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>

        <div class="flex justify-end mt-4 space-x-3">
          <button
            @click="applyFilters"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center"
          >
            <i class="fas fa-filter mr-2"></i> Apply Filters
          </button>
          <button
            @click="resetFilters"
            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 rounded-lg flex items-center"
          >
            <i class="fas fa-redo mr-2"></i> Reset
          </button>
        </div>
      </div>

      <!-- Transactions Table -->
      <div class="transactions-table bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
            Transaction Records
          </h2>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            Showing {{ paginatedTransactions.length }} of {{ filteredTransactions.length }} records
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
          <i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>
          <p class="mt-2 text-gray-500">Loading transactions...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="filteredTransactions.length === 0" class="text-center py-8">
          <i class="fas fa-inbox text-3xl text-gray-300 mb-3"></i>
          <p class="text-gray-500">No transactions found matching your filters.</p>
          <button @click="resetFilters" class="text-blue-500 hover:text-blue-700 mt-2">
            Reset filters
          </button>
        </div>

        <!-- Transactions Table -->
        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  #
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Employee
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Type
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Amount
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Credited Account
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Transaction Time
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Processed By
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="(transaction, index) in paginatedTransactions"
                :key="transaction.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ (currentPage - 1) * pageSize + index + 1 }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8">
                      <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 font-medium text-sm">
                        {{ getInitials(transaction.employee_name || 'N/A') }}
                      </div>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ transaction.employee_name || 'N/A' }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <span :class="typeBadgeClass(transaction.transaction_type)">
                    {{ transaction.transaction_type }}
                  </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <span :class="statusBadgeClass(transaction.status)">
                    {{ transaction.status.toUpperCase() }}
                  </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                  Birr {{ formatCurrency(transaction.amount) }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ transaction.to_account }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ formatDateTime(transaction.transaction_date || transaction.created_at) }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                  {{ transaction.processed_by || '—' }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="relative inline-block text-left" ref="actionMenuRef">
                    <!-- Action Menu Button -->
                    <button
                      @click.stop="toggleActionMenu(transaction.id)"
                      class="inline-flex items-center justify-center w-8 h-8 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                      :class="{ 'bg-gray-100 dark:bg-gray-700': activeActionMenu === transaction.id }"
                      aria-expanded="true"
                      aria-haspopup="true"
                    >
                      <i class="fas fa-ellipsis-v">...</i>
                    </button>

                    <!-- Action Menu Dropdown -->
                    <div
                      v-if="activeActionMenu === transaction.id"
                      class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 dark:divide-gray-700 focus:outline-none z-10"
                    >
                      <div class="py-1">
                        <!-- View Action -->
                        <button
                          @click="viewTransactionDetails(transaction)"
                          class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left"
                        >
                          <i class="fas fa-eye mr-3 text-blue-500 group-hover:text-blue-600"></i>
                          View Details
                        </button>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="filteredTransactions.length > 0" class="mt-6 flex items-center justify-between">
          <div class="text-sm text-gray-700 dark:text-gray-300">
            Page {{ currentPage }} of {{ totalPages }} ({{ filteredTransactions.length }} records)
          </div>
          <div class="flex space-x-2">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="px-3 py-1 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50 flex items-center"
            >
              <i class="fas fa-chevron-left mr-1 text-xs"></i> Previous
            </button>
            <div class="flex space-x-1">
              <button
                v-for="page in visiblePages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  'px-3 py-1 rounded-md border',
                  currentPage === page
                    ? 'bg-blue-600 text-white border-blue-600'
                    : 'border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'
                ]"
              >
                {{ page }}
              </button>
            </div>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50 flex items-center"
            >
              Next <i class="fas fa-chevron-right ml-1 text-xs"></i>
            </button>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-700 dark:text-gray-300">Rows per page:</span>
            <select
              v-model="pageSize"
              class="border border-gray-300 dark:border-gray-600 rounded-md px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm"
            >
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Transaction Detail Modal -->
      <TransactionDetailModal
        :transaction="selectedTransaction"
        :visible="!!selectedTransaction"
        @close="selectedTransaction = null"
      />
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import TransactionDetailModal from "@/pages/transactions/TransactionDetailModal.vue";
import api from "@/services/api";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useToast } from "vue-toastification";
const toast = useToast();

// Data
const transactions = ref([]);
const loading = ref(false);
const selectedTransaction = ref(null);
const currentPage = ref(1);
const pageSize = ref(10);
const activeActionMenu = ref(null);
const actionMenuRef = ref(null);

// Filters
const filters = ref({
  status: "",
  type: "",
  startDate: "",
  endDate: ""
});

// Fetch transactions
const fetchTransactions = async () => {
  loading.value = true;
  try {
    const res = await api.get("/transactions");
    transactions.value = (res.data || []).map((t) => ({
      ...t,
      employee_name: t?.payroll?.employee?.full_name || null,
      status: t?.status || "completed",
      fromBankAccount: t?.from_bank_account || t?.fromBankAccount,
      toBankAccount: t?.to_bank_account || t?.toBankAccount,
    }));
  } catch (error) {
    console.error("Failed to load transactions:", error);
    toast.error("Failed to load transactions.");
  } finally {
    loading.value = false;
  }
};

// Computed properties
const filteredTransactions = computed(() => {
  let result = [...transactions.value];

  // Apply status filter
  if (filters.value.status) {
    result = result.filter(t => t.status === filters.value.status);
  }

  // Apply type filter
  if (filters.value.type) {
    result = result.filter(t => t.transaction_type === filters.value.type);
  }

  // Apply date range filter
  if (filters.value.startDate) {
    const startDate = new Date(filters.value.startDate);
    result = result.filter(t => {
      const transactionDate = new Date(t.transaction_date || t.created_at);
      return transactionDate >= startDate;
    });
  }

  if (filters.value.endDate) {
    const endDate = new Date(filters.value.endDate);
    endDate.setHours(23, 59, 59, 999); // Include entire end day
    result = result.filter(t => {
      const transactionDate = new Date(t.transaction_date || t.created_at);
      return transactionDate <= endDate;
    });
  }

  return result;
});

const paginatedTransactions = computed(() => {
  const start = (currentPage.value - 1) * pageSize.value;
  return filteredTransactions.value.slice(start, start + pageSize.value);
});

const totalPages = computed(() => {
  return Math.ceil(filteredTransactions.value.length / pageSize.value);
});

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  let end = Math.min(totalPages.value, start + maxVisible - 1);

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1);
  }

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }

  return pages;
});

const summary = computed(() => {
  const total = transactions.value.length;
  const completed = transactions.value.filter(t => t.status === 'completed').length;
  const failed = transactions.value.filter(t => t.status === 'failed').length;
  const totalAmount = transactions.value
    .filter(t => t.status === 'completed')
    .reduce((sum, t) => sum + parseFloat(t.amount || 0), 0);

  return {
    total,
    completed,
    failed,
    completedPercentage: total > 0 ? Math.round((completed / total) * 100) : 0,
    failedPercentage: total > 0 ? Math.round((failed / total) * 100) : 0,
    totalAmount
  };
});

// Methods
const applyFilters = () => {
  currentPage.value = 1;
};

const resetFilters = () => {
  filters.value = {
    status: "",
    type: "",
    startDate: "",
    endDate: ""
  };
  currentPage.value = 1;
};

const viewTransactionDetails = (transaction) => {
  selectedTransaction.value = transaction;
  activeActionMenu.value = null;
};

const toggleActionMenu = (transactionId) => {
  if (activeActionMenu.value === transactionId) {
    activeActionMenu.value = null;
  } else {
    activeActionMenu.value = transactionId;
  }
};

const closeActionMenu = (event) => {
  if (actionMenuRef.value && !actionMenuRef.value.contains(event.target)) {
    activeActionMenu.value = null;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const goToPage = (page) => {
  currentPage.value = page;
};

const formatCurrency = (value) => {
  return parseFloat(value || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
};

const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const getInitials = (name) => {
  return name.split(' ')
    .map(part => part.charAt(0))
    .join('')
    .toUpperCase()
    .substring(0, 2);
};

const statusBadgeClass = (status) => {
  const baseClasses = "px-2 py-1 text-xs font-medium rounded-full";

  switch (status) {
    case 'completed':
      return `${baseClasses} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200`;
    case 'failed':
      return `${baseClasses} bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200`;
    case 'pending':
      return `${baseClasses} bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200`;
    default:
      return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200`;
  }
};

const typeBadgeClass = (type) => {
  const baseClasses = "px-2 py-1 text-xs font-medium rounded-full";

  switch (type) {
    case 'salary':
      return `${baseClasses} bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200`;
    case 'tax':
      return `${baseClasses} bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200`;
    case 'pension':
      return `${baseClasses} bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200`;
    default:
      return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200`;
  }
};

// Watch for page size changes
watch(pageSize, () => {
  currentPage.value = 1;
});

// Lifecycle hooks
onMounted(() => {
  fetchTransactions();
  document.addEventListener('click', closeActionMenu);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', closeActionMenu);
});
</script>

<style scoped>
.dashboard-header {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg flex flex-col sm:flex-row justify-between items-start sm:items-center;
}

.summary-card {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg flex items-center;
}

.summary-card.total .card-icon {
  @apply bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300;
}

.summary-card.completed .card-icon {
  @apply bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300;
}

.summary-card.failed .card-icon {
  @apply bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300;
}

.summary-card.amount .card-icon {
  @apply bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300;
}

.card-icon {
  @apply h-12 w-12 rounded-full flex items-center justify-center text-lg mr-4;
}

.card-content h3 {
  @apply text-sm font-medium text-gray-500 dark:text-gray-400;
}

.card-content .value {
  @apply text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1;
}

.card-content .label {
  @apply text-xs text-gray-500 dark:text-gray-400 mt-1;
}

.filters-section {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg;
}

.transactions-table {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg;
}

.transactions-page {
  @apply space-y-6;
}
</style>
