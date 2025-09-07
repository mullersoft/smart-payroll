<template>
  <MainLayout>
    <div class="preparer-dashboard space-y-6">
      <!-- Header Section -->
      <div class="dashboard-header">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
            <i class="fas fa-file-invoice-dollar mr-3 text-indigo-500"></i>
            Preparer Dashboard
          </h1>
          <p class="text-gray-600 dark:text-gray-300 mt-1">
            Welcome back, {{ auth.user?.name }}. Manage payroll preparation and transactions.
          </p>
        </div>
        <div class="flex items-center space-x-4">
          <span class="px-3 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full text-sm font-medium">
            PREPARER
          </span>
          <button
            class="px-3 py-1 bg-gray-100 rounded-md text-sm hidden sm:inline hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600"
            @click="fetchDashboardData"
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
        <!-- Total Employees Card -->
        <div class="summary-card employees">
          <div class="card-icon">
            <i class="fas fa-users"></i>
          </div>
          <div class="card-content">
            <h3>Total Employees</h3>
            <p class="value">{{ employeeCount }}</p>
            <p class="label">Active: {{ activeEmployeeCount }}</p>
          </div>
        </div>

        <!-- Prepared Payrolls Card -->
        <div class="summary-card payrolls">
          <div class="card-icon">
            <i class="fas fa-file-invoice"></i>
          </div>
          <div class="card-content">
            <h3>Prepared Payrolls</h3>
            <p class="value">{{ payrollCount }}</p>
            <p class="label">This month</p>
          </div>
        </div>

        <!-- Total Expenditure Card -->
        <div class="summary-card expenditure">
          <div class="card-icon">
            <i class="fas fa-money-bill-wave"></i>
          </div>
          <div class="card-content">
            <h3>Total Expenditure</h3>
            <p class="value">Birr {{ formatCurrency(totalExpenditure) }}</p>
            <p class="label">This month</p>
          </div>
        </div>

        <!-- Bank Accounts Card -->
        <div class="summary-card accounts">
          <div class="card-icon">
            <i class="fas fa-university"></i>
          </div>
          <div class="card-content">
            <h3>Bank Accounts</h3>
            <p class="value">{{ bankAccountCount }}</p>
            <p class="label">Company balance: Birr {{ formatCurrency(companyBalance) }}</p>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="quick-actions-section bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">
          Quick Actions
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <router-link
            to="/preparer/payrolls"
            class="quick-action-card payroll-action"
          >
            <div class="action-icon">
              <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-content">
              <h3>Prepare Payroll</h3>
              <p>Create new payroll for employees</p>
            </div>
          </router-link>

          <router-link
            to="/transactions"
            class="quick-action-card transactions-action"
          >
            <div class="action-icon">
              <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="action-content">
              <h3>View Transactions</h3>
              <p>Monitor all payroll transactions</p>
            </div>
          </router-link>

          <router-link
            to="/employees"
            class="quick-action-card employees-action"
          >
            <div class="action-icon">
              <i class="fas fa-user-friends"></i>
            </div>
            <div class="action-content">
              <h3>Manage Employees</h3>
              <p>View and update employee data</p>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payrolls -->
        <div class="recent-payrolls bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
              Recent Payrolls
            </h2>
            <router-link
              to="/preparer/payrolls"
              class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
            >
              View All →
            </router-link>
          </div>

          <div v-if="recentPayrolls.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
            <i class="fas fa-inbox text-3xl mb-3"></i>
            <p>No payrolls found.</p>
          </div>

          <div v-else class="space-y-4">
            <div v-for="payroll in recentPayrolls" :key="payroll.id" class="payroll-item">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-medium mr-3">
                    {{ payroll.employee.full_name.charAt(0) }}
                  </div>
                  <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">
                      {{ payroll.employee.full_name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ formatDate(payroll.pay_month) }}
                    </div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="font-medium text-green-600 dark:text-green-400">
                    Birr {{ formatCurrency(payroll.net_payment) }}
                  </div>
                  <span :class="statusBadgeClass(payroll.status)">
                    {{ payroll.status.toUpperCase() }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Transactions -->
        <div class="recent-transactions bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
              Recent Transactions
            </h2>
            <router-link
              to="/transactions"
              class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm"
            >
              View All →
            </router-link>
          </div>

          <div v-if="recentTransactions.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
            <i class="fas fa-exchange-alt text-3xl mb-3"></i>
            <p>No transactions found.</p>
          </div>

          <div v-else class="space-y-4">
            <div v-for="transaction in recentTransactions" :key="transaction.id" class="transaction-item">
              <div class="flex items-center justify-between">
                <div>
                  <div class="font-medium text-gray-900 dark:text-gray-100">
                    {{ transaction.payroll?.employee?.full_name || 'N/A' }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ transaction.from_account }} → {{ transaction.to_account }}
                  </div>
                </div>
                <div class="text-right">
                  <div class="font-medium text-red-600 dark:text-red-400">
                    - Birr {{ formatCurrency(transaction.amount) }}
                  </div>
                  <span :class="statusBadgeClass(transaction.status)">
                    {{ transaction.status.toUpperCase() }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Employee Demographics Section -->
      <div class="demographics-section bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-6">
          Employee Demographics
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Gender Distribution -->
          <div class="demographic-card">
            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">
              <i class="fas fa-venus-mars mr-2 text-pink-500"></i>
              Gender Distribution
            </h3>
            <div class="h-64">
              <Pie
                v-if="genderChartData.datasets.length"
                :data="genderChartData"
                :options="chartOptions"
              />
            </div>
          </div>

          <!-- Employment Type -->
          <div class="demographic-card">
            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">
              <i class="fas fa-briefcase mr-2 text-blue-500"></i>
              Employment Type
            </h3>
            <div class="h-64">
              <Bar
                v-if="employmentChartData.datasets.length"
                :data="employmentChartData"
                :options="chartOptions"
              />
            </div>
          </div>

          <!-- Position Distribution -->
          <div class="demographic-card">
            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-4">
              <i class="fas fa-user-tie mr-2 text-purple-500"></i>
              Position Distribution
            </h3>
            <div class="h-64">
              <Bar
                v-if="positionChartData.datasets.length"
                :data="positionChartData"
                :options="chartOptions"
              />
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
import { useAuthStore } from "@/store/auth";
import {
  ArcElement,
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  Title,
  Tooltip,
} from "chart.js";
import { computed, onMounted, ref } from "vue";
import { Bar, Pie } from "vue-chartjs";
import { useToast } from "vue-toastification";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
);

const auth = useAuthStore();
const toast = useToast();

// Data
const employees = ref([]);
const payrolls = ref([]);
const transactions = ref([]);
const bankAccounts = ref([]);
const loading = ref(false);

// Computed properties
const employeeCount = computed(() => employees.value.length);
const activeEmployeeCount = computed(() => employees.value.filter(emp => emp.is_active).length);
const payrollCount = computed(() => payrolls.value.length);
const bankAccountCount = computed(() => bankAccounts.value.length);

const totalExpenditure = computed(() => {
  return payrolls.value.reduce((total, payroll) => {
    return total + parseFloat(payroll.net_payment || 0);
  }, 0);
});

const companyBalance = computed(() => {
  const companyAccount = bankAccounts.value.find(acc =>
    acc.owner_name.includes('Qelemeda') || acc.account_number.includes('COMPANY')
  );
  return companyAccount ? parseFloat(companyAccount.balance || 0) : 0;
});

const recentPayrolls = computed(() => {
  return [...payrolls.value]
    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
    .slice(0, 5);
});

const recentTransactions = computed(() => {
  return [...transactions.value]
    .sort((a, b) => new Date(b.transaction_date || b.created_at) - new Date(a.transaction_date || a.created_at))
    .slice(0, 5);
});

// Chart data
const genderStats = computed(() => {
  const stats = { Male: 0, Female: 0 };
  employees.value.forEach(emp => {
    if (emp.gender && stats.hasOwnProperty(emp.gender)) {
      stats[emp.gender]++;
    }
  });
  return stats;
});

const employmentTypeStats = computed(() => {
  const stats = {};
  employees.value.forEach(emp => {
    if (emp.employment_type) {
      const type = emp.employment_type.name;
      stats[type] = (stats[type] || 0) + 1;
    }
  });
  return stats;
});

const positionStats = computed(() => {
  const stats = {};
  employees.value.forEach(emp => {
    if (emp.position) {
      const position = emp.position.name;
      stats[position] = (stats[position] || 0) + 1;
    }
  });
  return stats;
});

const genderChartData = computed(() => {
  return {
    labels: Object.keys(genderStats.value),
    datasets: [
      {
        label: "Employees by Gender",
        backgroundColor: ["#4299e1", "#e53e3e", "#718096"],
        data: Object.values(genderStats.value),
      },
    ],
  };
});

const employmentChartData = computed(() => {
  return {
    labels: Object.keys(employmentTypeStats.value),
    datasets: [
      {
        label: "Employees by Employment Type",
        backgroundColor: ["#667eea", "#48bb78", "#f6ad55"],
        data: Object.values(employmentTypeStats.value),
      },
    ],
  };
});

const positionChartData = computed(() => {
  return {
    labels: Object.keys(positionStats.value),
    datasets: [
      {
        label: "Employees by Position",
        backgroundColor: ["#f6ad55", "#9f7aea", "#4fd1c5", "#f687b3", "#68d391"],
        data: Object.values(positionStats.value),
      },
    ],
  };
});

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'bottom',
    },
  },
};

// Methods
const fetchDashboardData = async () => {
  loading.value = true;
  try {
    const [employeesRes, payrollsRes, transactionsRes, bankAccountsRes] = await Promise.all([
      api.get("/employees"),
      api.get("/payrolls"),
      api.get("/transactions?status=completed"),
      api.get("/bank-accounts"),
    ]);

    employees.value = employeesRes.data || [];
    payrolls.value = payrollsRes.data || [];
    transactions.value = transactionsRes.data || [];
    bankAccounts.value = bankAccountsRes.data || [];
  } catch (error) {
    console.error("Error loading dashboard data:", error);
    toast.error("Failed to load dashboard data.");
  } finally {
    loading.value = false;
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
    case 'completed':
      return `${baseClasses} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200`;
    case 'rejected':
    case 'failed':
      return `${baseClasses} bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200`;
    case 'prepared':
    case 'pending':
      return `${baseClasses} bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200`;
    default:
      return `${baseClasses} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200`;
  }
};

// Lifecycle hooks
onMounted(() => {
  fetchDashboardData();
});
</script>

<style scoped>
.dashboard-header {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg flex flex-col sm:flex-row justify-between items-start sm:items-center;
}

.summary-card {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg flex items-center;
}

.summary-card.employees .card-icon {
  @apply bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300;
}

.summary-card.payrolls .card-icon {
  @apply bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300;
}

.summary-card.expenditure .card-icon {
  @apply bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300;
}

.summary-card.accounts .card-icon {
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

.quick-actions-section {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg;
}

.quick-action-card {
  @apply bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-transparent hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-200 hover:shadow-md;
}

.quick-action-card.payroll-action:hover {
  @apply border-indigo-300 dark:border-indigo-600;
}

.quick-action-card.transactions-action:hover {
  @apply border-blue-300 dark:border-blue-600;
}

.quick-action-card.employees-action:hover {
  @apply border-green-300 dark:border-green-600;
}

.action-icon {
  @apply h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 flex items-center justify-center text-lg mb-3;
}

.quick-action-card.transactions-action .action-icon {
  @apply bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300;
}

.quick-action-card.employees-action .action-icon {
  @apply bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300;
}

.action-content h3 {
  @apply text-lg font-medium text-gray-800 dark:text-gray-200;
}

.action-content p {
  @apply text-sm text-gray-600 dark:text-gray-400 mt-1;
}

.recent-payrolls, .recent-transactions {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg;
}

.payroll-item, .transaction-item {
  @apply p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200;
}

.demographics-section {
  @apply bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg;
}

.demographic-card {
  @apply bg-gray-50 dark:bg-gray-700 rounded-lg p-6;
}

.preparer-dashboard {
  @apply space-y-6;
}
</style>
