<template>
  <MainLayout>
    <!-- Header with Profile Dropdown -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Employee Dashboard</h1>
      <div class="relative">
        <!-- Profile dropdown code remains the same -->
      </div>
    </div>

    <!-- Welcome Header -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
      <!-- Welcome message and status badge -->
    </div>

    <!-- Summary Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <PersonalInfoCard :employee-data="employeeData" />
      <EmploymentDetailsCard :employee-data="employeeData" />
      <BankAccountCard :employee-data="employeeData" />
      <AllowancesCard :employee-data="employeeData" />
    </div>

    <!-- Payroll Summary Section -->
    <div class="mb-8">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Recent Payroll</h2>
        <router-link
          to="/payroll-history"
          class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm flex items-center"
        >
          View Full History
          <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </router-link>
      </div>

      <PayrollSummaryTable
        :payrolls="recentPayrolls"
        @view-details="viewPayrollDetails"
      />

      <div v-if="payrolls.length === 0" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
        <p class="text-gray-500 dark:text-gray-400">No payroll records found.</p>
      </div>
    </div>

    <!-- Transactions Summary Section -->
    <div class="mb-8">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Recent Transactions</h2>
        <router-link
            to="/payroll-transactions"
            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm flex items-center"
          >
            View All Transactions
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </router-link>
      </div>

      <TransactionsSummaryTable
        :transactions="recentTransactions"
      />

      <div v-if="transactions.length === 0" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
        <p class="text-gray-500 dark:text-gray-400">No transactions found.</p>
      </div>
    </div>

    <!-- Quick Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ payrolls.length }}</div>
        <div class="text-gray-600 dark:text-gray-400 mt-2">Total Payroll Records</div>
      </div>

      <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
        <div class="text-2xl font-bold text-green-600 dark:text-green-400">Birr {{ formatCurrency(totalEarnings) }}</div>
        <div class="text-gray-600 dark:text-gray-400 mt-2">Total Earnings</div>
      </div>

      <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ transactions.length }}</div>
        <div class="text-gray-600 dark:text-gray-400 mt-2">Total Transactions</div>
      </div>
    </div>

    <!-- Payroll Details Modal -->
    <PayrollDetailsModal
      :visible="showPayrollModal"
      :payroll="selectedPayroll"
      @close="showPayrollModal = false"
    />
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import PayrollDetailsModal from "@/components/payroll/PayrollDetailsModal.vue";
import AllowancesCard from "@/pages/dashboard/components/AllowancesCard.vue";
import BankAccountCard from "@/pages/dashboard/components/BankAccountCard.vue";
import EmploymentDetailsCard from "@/pages/dashboard/components/EmploymentDetailsCard.vue";
import PayrollSummaryTable from "@/pages/dashboard/components/PayrollSummaryTable.vue";
import PersonalInfoCard from "@/pages/dashboard/components/PersonalInfoCard.vue";
import TransactionsSummaryTable from "@/pages/dashboard/components/TransactionsSummaryTable.vue";
import api from "@/services/api";
import { useAuthStore } from "@/store/auth";
import { formatCurrency } from "@/utils/formatters";
import { computed, onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

const auth = useAuthStore();
const toast = useToast();

const employeeData = ref({});
const payrolls = ref([]);
const transactions = ref([]);
const isLoading = ref(true);
const showProfileDropdown = ref(false);
const showPayrollModal = ref(false);
const selectedPayroll = ref(null);

// Computed properties
const recentPayrolls = computed(() => payrolls.value.slice(0, 3));
const recentTransactions = computed(() => transactions.value.slice(0, 5));
const totalEarnings = computed(() => {
  return payrolls.value.reduce((total, payroll) => {
    return total + parseFloat(payroll.net_payment || 0);
  }, 0);
});

// Methods
const toggleProfileDropdown = () => {
  showProfileDropdown.value = !showProfileDropdown.value;
};

const viewPayrollDetails = (payroll) => {
  selectedPayroll.value = payroll;
  showPayrollModal.value = true;
};

const fetchEmployeeData = async () => {
  try {
    const response = await api.get('/my/profile');
    employeeData.value = response.data;
  } catch (error) {
    console.error("Error fetching employee data:", error);
    employeeData.value = {
      full_name: auth.user?.name,
      email: auth.user?.email,
      employee_id: 'N/A',
      is_active: auth.user?.status === 'active',
    };
    toast.error("Failed to load detailed employee data");
  }
};

const fetchPayrolls = async () => {
  try {
    const response = await api.get('/my/payrolls');
    payrolls.value = response.data || [];
  } catch (error) {
    console.error("Error fetching payrolls:", error);
    payrolls.value = [];
    toast.error("Failed to load payroll data");
  }
};

const fetchTransactions = async () => {
  try {
    const response = await api.get('/my/transactions');
    transactions.value = response.data || [];
  } catch (error) {
    console.error("Error fetching transactions:", error);
    transactions.value = [];
    toast.error("Failed to load transaction data");
  }
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showProfileDropdown.value = false;
  }
};

onMounted(async () => {
  try {
    await Promise.all([
      fetchEmployeeData(),
      fetchPayrolls(),
      fetchTransactions()
    ]);
  } catch (error) {
    console.error("Error loading dashboard data:", error);
  } finally {
    isLoading.value = false;
  }

  document.addEventListener('click', handleClickOutside);
});
</script>
