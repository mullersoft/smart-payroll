<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
            👨‍💼 Admin Dashboard
          </h1>
          <p class="text-gray-600 dark:text-gray-300 mt-1">Welcome back, {{ auth.user?.name }}. Here's your system overview.</p>
        </div>
        <div class="flex items-center space-x-4">
          <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full text-sm font-medium">
            ADMIN
          </span>
        </div>
      </div>

      <!-- Quick Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Users Card -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-l-4 border-blue-500">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Total Users</h2>
              <p class="text-3xl text-blue-600 dark:text-blue-400 font-bold mt-2">
                {{ usersCount }}
              </p>
              <div class="flex items-center mt-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Active: {{ activeUsersCount }}</span>
              </div>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
              </svg>
            </div>
          </div>
          <router-link
            to="/admin/users"
            class="inline-block mt-4 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium"
          >
            Manage Users →
          </router-link>
        </div>

        <!-- Employees Card -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-l-4 border-green-500">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Employees</h2>
              <p class="text-3xl text-green-600 dark:text-green-400 font-bold mt-2">
                {{ employeesCount }}
              </p>
              <div class="flex items-center mt-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Active: {{ activeEmployeesCount }}</span>
              </div>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
          </div>
          <router-link
            to="/admin/employees"
            class="inline-block mt-4 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm font-medium"
          >
            Manage Employees →
          </router-link>
        </div>

        <!-- Bank Accounts Card -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-l-4 border-purple-500">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Bank Accounts</h2>
              <p class="text-3xl text-purple-600 dark:text-purple-400 font-bold mt-2">
                {{ accountsCount }}
              </p>
              <div class="flex items-center mt-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Balance: Birr {{ formatCurrency(totalBalance) }}</span>
              </div>
            </div>
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
              </svg>
            </div>
          </div>
          <router-link
            to="/admin/accounts"
            class="inline-block mt-4 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 text-sm font-medium"
          >
            Manage Accounts →
          </router-link>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Users -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Recent Users</h2>
            <router-link
              to="/admin/users"
              class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm"
            >
              View All →
            </router-link>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Email</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Role</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="user in recentUsers" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ user.name }}</td>
                  <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ user.email }}</td>
                  <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 capitalize">{{ user.role }}</td>
                  <td class="px-4 py-3">
                    <span :class="{
                      'px-2 py-1 text-xs font-medium rounded-full': true,
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': user.status === 'active',
                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': user.status === 'deactivated',
                      'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': user.status === 'pending'
                    }">
                      {{ user.status?.toUpperCase() }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="users.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
            No users found.
          </div>
        </div>

        <!-- Recent Employees -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Recent Employees</h2>
            <router-link
              to="/admin/employees"
              class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 text-sm"
            >
              View All →
            </router-link>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Position</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Salary</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="employee in recentEmployees" :key="employee.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                  <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ employee.full_name }}</td>
                  <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">{{ employee.position?.name || 'N/A' }}</td>
                  <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">Birr {{ formatCurrency(employee.base_salary) }}</td>
                  <td class="px-4 py-3">
                    <span :class="{
                      'px-2 py-1 text-xs font-medium rounded-full': true,
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': employee.is_active,
                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': !employee.is_active
                    }">
                      {{ employee.is_active ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div v-if="employees.length === 0" class="text-center py-4 text-gray-500 dark:text-gray-400">
            No employees found.
          </div>
        </div>
      </div>

      <!-- System Overview Section -->
      <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">System Overview</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="text-center">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ usersByRole.admin }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Admin Users</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ usersByRole.preparer }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Preparers</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ usersByRole.approver }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Approvers</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ usersByRole.employee }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Employees</div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <!-- Add User -->
          <button
            @click="openModal('user')"
            class="p-4 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg text-center transition-colors w-full"
          >
            <div class="text-blue-600 dark:text-blue-400 text-lg mb-2">➕</div>
            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">Add User</div>
          </button>

          <!-- Add Employee -->
          <button
            @click="openModal('employee')"
            class="p-4 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg text-center transition-colors w-full"
          >
            <div class="text-green-600 dark:text-green-400 text-lg mb-2">👥</div>
            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">Add Employee</div>
          </button>

          <!-- Add Account -->
          <button
            @click="openModal('account')"
            class="p-4 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-lg text-center transition-colors w-full"
          >
            <div class="text-purple-600 dark:text-purple-400 text-lg mb-2">🏦</div>
            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">Add Account</div>
          </button>
        </div>
      </div>

      <!-- Modals -->
      <CreateUserModal
        v-if="activeModal === 'user'"
        @close="closeModal"
        @success="handleUserCreated"
      />
      <EmployeeEditModal
        v-if="activeModal === 'employee'"
        @close="closeModal"
        @success="handleEmployeeCreated"
      />
      <CreateAccountModal
        v-if="activeModal === 'account'"
        @close="closeModal"
        @success="handleAccountCreated"
      />
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";
import { useAuthStore } from "@/store/auth";
import { formatCurrency } from "@/utils/formatters";
import { computed, onMounted, ref } from "vue";
import { useToast } from "vue-toastification";

// Import modals
import CreateAccountModal from "@/pages/admin/bank-accounts/createAndEditModal.vue";
import EmployeeEditModal from "@/pages/admin/employees/EmployeeEditModal.vue";
import CreateUserModal from "@/pages/admin/users/createUserModal.vue";

const auth = useAuthStore();
const toast = useToast();

// State for dashboard
const users = ref([]);
const employees = ref([]);
const accounts = ref([]);
const isLoading = ref(true);

// State for modal management
const activeModal = ref(null);

// Computed properties
const usersCount = computed(() => users.value.length);
const employeesCount = computed(() => employees.value.length);
const accountsCount = computed(() => accounts.value.length);

const activeUsersCount = computed(() =>
  users.value.filter((user) => user.status === "active").length
);

const activeEmployeesCount = computed(() =>
  employees.value.filter((emp) => emp.is_active).length
);

const totalBalance = computed(() =>
  accounts.value.reduce(
    (total, account) => total + parseFloat(account.balance || 0),
    0
  )
);

const recentUsers = computed(() => users.value.slice(0, 5));
const recentEmployees = computed(() => employees.value.slice(0, 5));

const usersByRole = computed(() => {
  const roles = {
    admin: 0,
    preparer: 0,
    approver: 0,
    employee: 0,
  };

  users.value.forEach((user) => {
    if (user.role && roles.hasOwnProperty(user.role)) {
      roles[user.role]++;
    }
  });

  return roles;
});

// Modal methods
const openModal = (modalType) => {
  console.log('Opening modal:', modalType);
  activeModal.value = modalType;
};

const closeModal = () => {
  console.log('Closing modal');
  activeModal.value = null;
};

// Success handlers
const handleUserCreated = () => {
  closeModal();
  fetchDashboardData();
  toast.success("User created successfully!");
};

const handleEmployeeCreated = () => {
  closeModal();
  fetchDashboardData();
  toast.success("Employee created successfully!");
};

const handleAccountCreated = () => {
  closeModal();
  fetchDashboardData();
  toast.success("Account created successfully!");
};

// Fetch dashboard data
const fetchDashboardData = async () => {
  isLoading.value = true;
  try {
    const [usersRes, employeesRes, accountsRes] = await Promise.all([
      api.get("/users"),
      api.get("/employees"),
      api.get("/bank-accounts"),
    ]);

    users.value = usersRes.data || [];
    employees.value = employeesRes.data || [];
    accounts.value = accountsRes.data || [];
  } catch (error) {
    console.error("Dashboard fetch error:", error);
    toast.error("Failed to load dashboard data.");
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchDashboardData();
});
</script>

<style scoped>
.grid {
  display: grid;
}

.grid-cols-1 {
  grid-template-columns: repeat(1, minmax(0, 1fr));
}

@media (min-width: 768px) {
  .md\:grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .md\:grid-cols-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
}

@media (min-width: 1024px) {
  .lg\:grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
  .lg\:grid-cols-4 {
    grid-template-columns: repeat(4, minmax(0, 1fr));
  }
}

.hover\:bg-gray-50:hover {
  background-color: #f9fafb;
}

.dark .hover\:bg-gray-700:hover {
  background-color: #374151;
}

.transition-colors {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>
