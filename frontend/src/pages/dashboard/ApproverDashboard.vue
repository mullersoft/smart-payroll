<template>
  <MainLayout>
    <div class="approver-dashboard space-y-6">
      <!-- Header Section -->
      <div class="dashboard-header">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
            <i class="fas fa-clipboard-check mr-3 text-yellow-500"></i>
            Approver Dashboard
          </h1>
          <p class="text-gray-600 dark:text-gray-300 mt-1">
            Review, approve, or reject payroll submissions
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full text-sm font-medium">
            APPROVER
          </span>
          <button
            class="px-3 py-1 bg-gray-100 rounded-md text-sm hidden sm:inline hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600"
            @click="fetchPayrolls"
            :disabled="loading"
            title="Refresh dashboard"
          >
            <i class="fas fa-sync-alt mr-1" :class="{ 'fa-spin': loading }"></i>
            {{ loading ? 'Refreshing...' : 'Refresh' }}
          </button>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Pending Approval Card -->
        <div class="summary-card pending">
          <div class="card-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="card-content">
            <h3>Pending Approval</h3>
            <p class="value">{{ summary.pending || 0 }}</p>
            <p class="label">Awaiting review</p>
          </div>
        </div>

        <!-- Approved Card -->
        <div class="summary-card approved">
          <div class="card-icon">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="card-content">
            <h3>Approved</h3>
            <p class="value">{{ summary.approved || 0 }}</p>
            <p class="label">This month</p>
          </div>
        </div>

        <!-- Rejected Card -->
        <div class="summary-card rejected">
          <div class="card-icon">
            <i class="fas fa-times-circle"></i>
          </div>
          <div class="card-content">
            <h3>Rejected</h3>
            <p class="value">{{ summary.rejected || 0 }}</p>
            <p class="label">This month</p>
          </div>
        </div>

        <!-- Total Processed Card -->
        <div class="summary-card processed">
          <div class="card-icon">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <div class="card-content">
            <h3>Total Processed</h3>
            <p class="value">{{ summary.total || 0 }}</p>
            <p class="label">Payroll records</p>
          </div>
        </div>
      </div>

      <!-- Filters and Actions -->
      <div class="filters-section bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
            <!-- Month Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                <i class="fas fa-calendar-alt mr-1"></i> Month
              </label>
              <input
                type="month"
                v-model="filters.month"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>

            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                <i class="fas fa-filter mr-1"></i> Status
              </label>
              <select
                v-model="filters.status"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">All Statuses</option>
                <option value="prepared">Pending Approval</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            <!-- Employee Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                <i class="fas fa-user mr-1"></i> Employee
              </label>
              <select
                v-model="filters.employee"
                class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">All Employees</option>
                <option v-for="employee in employeeList" :key="employee.id" :value="employee.id">
                  {{ employee.full_name }} ({{ employee.employee_id }})
                </option>
              </select>
            </div>
          </div>

          <div class="flex gap-2">
            <button
              @click="applyFilters"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center"
            >
              <i class="fas fa-search mr-2"></i> Apply Filters
            </button>
            <button
              @click="resetFilters"
              class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 rounded-lg flex items-center"
            >
              <i class="fas fa-redo mr-2"></i> Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Payroll List -->
      <div class="payroll-list-section bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
            Payroll Records
          </h2>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            Showing {{ filteredPayrolls.length }} of {{ payrolls.length }} records
          </div>
        </div>

        <div v-if="loading" class="text-center py-8">
          <i class="fas fa-spinner fa-spin text-2xl text-blue-500"></i>
          <p class="mt-2 text-gray-500">Loading payroll data...</p>
        </div>

        <div v-else-if="filteredPayrolls.length === 0" class="text-center py-8">
          <i class="fas fa-inbox text-3xl text-gray-300 mb-3"></i>
          <p class="text-gray-500">No payroll records found matching your filters.</p>
          <button @click="resetFilters" class="text-blue-500 hover:text-blue-700 mt-2">
            Reset filters
          </button>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Employee
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Period
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Gross Pay
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Deductions
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Net Pay
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
              <tr v-for="payroll in paginatedPayrolls" :key="payroll.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 font-medium">
                        {{ payroll.employee.full_name.charAt(0) }}
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ payroll.employee.full_name }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ payroll.employee.employee_id }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-gray-100">{{ formatDate(payroll.pay_month) }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ payroll.working_days }} days</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                    Birr {{ formatCurrency(payroll.gross_pay) }}
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-gray-100">
                    Birr {{ formatCurrency(payroll.total_deduction) }}
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-green-600 dark:text-green-400">
                    Birr {{ formatCurrency(payroll.net_payment) }}
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                  <span :class="statusBadgeClass(payroll.status)">
                    {{ payroll.status.toUpperCase() }}
                  </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="relative" ref="actionMenuRef">
                    <!-- Action Menu Button -->
                    <button
                      @click="toggleActionMenu(payroll.id)"
                      class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                      :class="{ 'bg-gray-100 dark:bg-gray-700': activeActionMenu === payroll.id }"
                    >
                      <i class="fas fa-ellipsis-v">...</i>
                    </button>

                    <!-- Action Menu Dropdown -->
                    <div
                      v-if="activeActionMenu === payroll.id"
                      class="absolute right-0 mt-1 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-10 border border-gray-200 dark:border-gray-700"
                    >
                      <!-- View Action -->
                      <button
                        @click="viewPayroll(payroll); activeActionMenu = null"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center"
                      >
                        <i class="fas fa-eye mr-3 text-blue-500"></i>
                        View Details
                      </button>

                      <!-- Approve Action (only for prepared status) -->
                      <button
                        v-if="payroll.status === 'prepared'"
                        @click="approvePayroll(payroll.id); activeActionMenu = null"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center"
                      >
                        <i class="fas fa-check mr-3 text-green-500"></i>
                        Approve
                      </button>

                      <!-- Reject Action (only for prepared status) -->
                      <button
                        v-if="payroll.status === 'prepared'"
                        @click="promptReject(payroll.id); activeActionMenu = null"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center"
                      >
                        <i class="fas fa-times mr-3 text-red-500"></i>
                        Reject
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="filteredPayrolls.length > 0" class="mt-6 flex items-center justify-between">
          <div class="text-sm text-gray-700 dark:text-gray-300">
            Page {{ currentPage }} of {{ totalPages }}
          </div>
          <div class="flex space-x-2">
            <button
              @click="prevPage"
              :disabled="currentPage === 1"
              class="px-3 py-1 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50"
            >
              Previous
            </button>
            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      </div>

      <!-- Payroll Details Modal -->
      <PayrollDetailsModal
        :visible="showViewModal"
        :payroll="selectedPayroll"
        @close="showViewModal = false"
      />

      <!-- Rejection Modal -->
      <div
        v-if="showRejectModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl"
        >
          <h2
            class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
          >
            <i class="fas fa-times-circle text-red-500 mr-2"></i>
            Reject Payroll
          </h2>
          <p class="text-gray-700 dark:text-gray-300 mb-4">
            Please provide a reason for rejecting this payroll.
          </p>
          <textarea
            v-model="rejectionReason"
            rows="4"
            placeholder="Enter rejection reason..."
            class="w-full border rounded-md p-3 bg-white dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent"
          ></textarea>
          <div class="flex justify-end space-x-3 mt-4">
            <button
              @click="showRejectModal = false"
              class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-600"
            >
              Cancel
            </button>
            <button
              @click="confirmReject"
              :disabled="!rejectionReason"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Confirm Rejection
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import PayrollDetailsModal from "@/components/payroll/PayrollDetailsModal.vue";
import api from "@/services/api";
import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { useToast } from "vue-toastification";
const toast = useToast();

// Data
const payrolls = ref([]);
const loading = ref(true);
const filters = ref({
  month: "",
  status: "prepared",
  employee: ""
});
const showViewModal = ref(false);
const selectedPayroll = ref({});
const showRejectModal = ref(false);
const payrollToRejectId = ref(null);
const rejectionReason = ref("");
const currentPage = ref(1);
const pageSize = 10;
const employeeList = ref([]);
const activeActionMenu = ref(null);
const actionMenuRef = ref(null);

// Computed properties
const filteredPayrolls = computed(() => {
  let result = [...payrolls.value];

  // Filter by month
  if (filters.value.month) {
    result = result.filter(p => {
      const payrollMonth = new Date(p.pay_month).toISOString().slice(0, 7);
      return payrollMonth === filters.value.month;
    });
  }

  // Filter by status
  if (filters.value.status) {
    result = result.filter(p => p.status === filters.value.status);
  }

  // Filter by employee
  if (filters.value.employee) {
    result = result.filter(p => p.employee.id == filters.value.employee);
  }

  return result;
});

const paginatedPayrolls = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredPayrolls.value.slice(start, start + pageSize);
});

const totalPages = computed(() => {
  return Math.ceil(filteredPayrolls.value.length / pageSize);
});

const summary = computed(() => {
  const currentMonth = filters.value.month || new Date().toISOString().slice(0, 7);
  const monthPayrolls = payrolls.value.filter(p => {
    const payrollMonth = new Date(p.pay_month).toISOString().slice(0, 7);
    return payrollMonth === currentMonth;
  });

  return {
    pending: monthPayrolls.filter(p => p.status === 'prepared').length,
    approved: monthPayrolls.filter(p => p.status === 'approved').length,
    rejected: monthPayrolls.filter(p => p.status === 'rejected').length,
    total: monthPayrolls.length
  };
});

// Methods
const getCurrentMonth = () => {
  const date = new Date();
  const year = date.getFullYear();
  const month = (date.getMonth() + 1).toString().padStart(2, "0");
  return `${year}-${month}`;
};

const fetchPayrolls = async () => {
  loading.value = true;
  try {
    const res = await api.get("/payrolls", {
      params: {
        month: filters.value.month,
        status: filters.value.status,
      },
    });
    payrolls.value = res.data.payrolls || [];

    // Extract unique employees for filter
    const employeesMap = {};
    payrolls.value.forEach(p => {
      if (p.employee && !employeesMap[p.employee.id]) {
        employeesMap[p.employee.id] = p.employee;
      }
    });
    employeeList.value = Object.values(employeesMap);
  } catch (error) {
    console.error("Error loading payrolls:", error);
    toast.error("Failed to load payrolls.");
  } finally {
    loading.value = false;
  }
};

const fetchEmployees = async () => {
  try {
    const res = await api.get("/employees");
    employeeList.value = res.data || [];
  } catch (error) {
    console.error("Error loading employees:", error);
  }
};

const approvePayroll = async (payrollId) => {
  try {
    await api.post(`/payrolls/${payrollId}/approve`);
    toast.success("Payroll approved successfully.");
    fetchPayrolls();
  } catch (error) {
    console.error("Error approving payroll:", error);
    toast.error("Failed to approve payroll.");
  }
};

const promptReject = (payrollId) => {
  payrollToRejectId.value = payrollId;
  rejectionReason.value = "";
  showRejectModal.value = true;
};

const confirmReject = async () => {
  try {
    await api.post(`/payrolls/${payrollToRejectId.value}/reject`, {
      rejection_reason: rejectionReason.value,
    });
    toast.success("Payroll rejected successfully.");
    showRejectModal.value = false;
    fetchPayrolls();
  } catch (error) {
    console.error("Error rejecting payroll:", error);
    toast.error("Failed to reject payroll.");
  }
};

const viewPayroll = (payroll) => {
  selectedPayroll.value = payroll;
  showViewModal.value = true;
};

const applyFilters = () => {
  currentPage.value = 1;
  fetchPayrolls();
};

const resetFilters = () => {
  filters.value = {
    month: getCurrentMonth(),
    status: "prepared",
    employee: ""
  };
  currentPage.value = 1;
  fetchPayrolls();
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

const toggleActionMenu = (payrollId) => {
  if (activeActionMenu.value === payrollId) {
    activeActionMenu.value = null;
  } else {
    activeActionMenu.value = payrollId;
  }
};

const closeActionMenu = (event) => {
  if (actionMenuRef.value && !actionMenuRef.value.contains(event.target)) {
    activeActionMenu.value = null;
  }
};

const formatCurrency = (value) => {
  return parseFloat(value || 0).toLocaleString('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
};

const statusBadgeClass = (status) => {
  const baseClasses = "px-2 py-1 text-xs font-medium rounded-full";

  switch (status) {
    case 'approved':
      return `${baseClasses} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200`;
    case 'rejected':
      return `${baseClasses} bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200`;
    case 'prepared':
      return `${baseClasses} bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200`;
    default:
      return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200`;
  }
};

// Lifecycle hooks
onMounted(() => {
  filters.value.month = getCurrentMonth();
  fetchPayrolls();
  fetchEmployees();
  document.addEventListener('click', closeActionMenu);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', closeActionMenu);
});

// Watch for filter changes
watch(() => filters.value, () => {
  currentPage.value = 1;
}, { deep: true });
</script>

<style scoped>
.dashboard-header {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg flex flex-col sm:flex-row justify-between items-start sm:items-center;
}

.summary-card {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg flex items-center;
}

.summary-card.pending .card-icon {
  @apply bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300;
}

.summary-card.approved .card-icon {
  @apply bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300;
}

.summary-card.rejected .card-icon {
  @apply bg-red-100 text-red-600 dark:bg-red-900 dark:text-red-300;
}

.summary-card.processed .card-icon {
  @apply bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300;
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

.payroll-list-section {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg;
}

.approver-dashboard {
  @apply space-y-6;
}
</style>
