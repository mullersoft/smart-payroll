<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">💰 Payrolls</h1>
        <button
          @click="openBulkModal"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md"
        >
          ➕ Prepare Payroll
        </button>
      </div>

      <!-- Payroll Table -->
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md overflow-x-auto transition-colors duration-300">
        <table class="min-w-full table-auto text-sm text-gray-900 dark:text-gray-100">
          <thead class="bg-gray-100 dark:bg-gray-700 text-left text-gray-600 dark:text-gray-300">
            <tr>
              <th class="px-4 py-2">#</th>
              <th class="px-4 py-2">Employee</th>
              <th class="px-4 py-2">Month</th>
              <th class="px-4 py-2">Base Salary</th>
              <th class="px-4 py-2">Net Payment</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(payroll, index) in payrolls"
              :key="payroll.id"
              class="border-b border-gray-300 dark:border-gray-600"
            >
              <td class="px-4 py-2">{{ index + 1 }}</td>
              <td class="px-4 py-2">{{ payroll.employee?.full_name }}</td>
              <td class="px-4 py-2">{{ payroll.pay_month }}</td>
              <td class="px-4 py-2">{{ payroll.base_salary }}</td>
              <td class="px-4 py-2">{{ payroll.net_payment }}</td>
              <td class="px-4 py-2 capitalize">
                <span
                  :class="{
                    'text-green-600': payroll.status === 'approved',
                    'text-yellow-600': payroll.status === 'prepared',
                    'text-red-600': payroll.status === 'rejected',
                    'text-blue-600': payroll.status === 'paid',
                  }"
                >
                  {{ payroll.status }}
                </span>
              </td>
              <td class="px-4 py-2 space-x-2">
                <button
                  class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs"
                  @click="viewPayroll(payroll)"
                >
                  View
                </button>
              </td>
            </tr>
            <tr v-if="payrolls.length === 0">
              <td colspan="7" class="text-center py-4 text-gray-500 dark:text-gray-400">
                No payrolls found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Bulk Create Modal -->
      <div
        v-if="showBulkModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-4xl shadow-xl transition-colors duration-300">
          <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
            Prepare Payroll (Bulk or Single)
          </h2>

          <!-- Common Pay Month -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pay Month</label>
            <input
              v-model="bulkPayMonth"
              type="month"
              class="w-64 border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
            />
          </div>

          <!-- Employee Table with Editable Fields -->
          <table class="min-w-full border border-gray-300 dark:border-gray-600 text-sm mb-4 text-gray-900 dark:text-gray-100">
            <thead class="bg-gray-100 dark:bg-gray-700">
              <tr>
                <th class="px-3 py-2">
                  <input
                    type="checkbox"
                    @change="toggleAllEmployees"
                    :checked="allSelected"
                    class="dark:bg-gray-700"
                  />
                </th>
                <th class="px-3 py-2">Full Name</th>
                <th class="px-3 py-2">Working Days</th>
                <th class="px-3 py-2">Other Commission</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="emp in employees" :key="emp.id" class="border-b border-gray-300 dark:border-gray-600">
                <td class="px-3 py-2">
                  <input
                    type="checkbox"
                    :value="emp.id"
                    v-model="selectedEmployees"
                    class="dark:bg-gray-700"
                  />
                </td>
                <td class="px-3 py-2">{{ emp.full_name }}</td>
                <td class="px-3 py-2">
                  <input
                    type="number"
                    min="0"
                    max="30"
                    v-model.number="emp.working_days"
                    class="w-20 border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                  />
                </td>
                <td class="px-3 py-2">
                  <input
                    type="number"
                    step="0.01"
                    v-model.number="emp.other_commission"
                    class="w-24 border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                  />
                </td>
              </tr>
            </tbody>
          </table>

          <div class="flex justify-end space-x-3 mt-6">
            <button
              @click="closeBulkModal"
              class="px-4 py-2 text-gray-600 dark:text-gray-300"
            >
              Cancel
            </button>
            <button
              @click="createBulkPayroll"
              class="bg-indigo-600 text-white px-4 py-2 rounded"
              :disabled="selectedEmployees.length === 0 || !bulkPayMonth"
            >
              Prepare Payroll
            </button>
          </div>
        </div>
      </div>

      <!-- View Payroll Modal -->
      <div
        v-if="showViewModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl shadow-xl transition-colors duration-300">
          <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">Payroll Details</h2>
          <div class="grid grid-cols-2 gap-4 text-gray-900 dark:text-gray-100">
            <p>
              <strong>Employee:</strong>
              {{ selectedPayroll.employee?.full_name }}
            </p>
            <p><strong>Month:</strong> {{ selectedPayroll.pay_month }}</p>
            <p>
              <strong>Base Salary:</strong> {{ selectedPayroll.base_salary }}
            </p>
            <p>
              <strong>Net Payment:</strong> {{ selectedPayroll.net_payment }}
            </p>
            <p><strong>Status:</strong> {{ selectedPayroll.status }}</p>
            <p v-if="selectedPayroll.rejection_reason">
              <strong>Reason:</strong> {{ selectedPayroll.rejection_reason }}
            </p>
          </div>
          <div class="flex justify-end mt-4">
            <button
              @click="showViewModal = false"
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";

const payrolls = ref([]);
const employees = ref([]);
const showBulkModal = ref(false);
const showViewModal = ref(false);
const selectedPayroll = ref({});
const bulkPayMonth = ref("");
const selectedEmployees = ref([]);

const allSelected = computed(() => {
  return (
    employees.value.length > 0 &&
    selectedEmployees.value.length === employees.value.length
  );
});

const fetchPayrolls = async () => {
  try {
    const res = await api.get("/payrolls");
    payrolls.value = res.data;
  } catch (error) {
    console.error("Error loading payrolls", error);
  }
};

const fetchEmployees = async () => {
  try {
    const res = await api.get("/employees");
    employees.value = res.data.map((emp) => ({
      ...emp,
      working_days: 30, // default
      other_commission: 0,
    }));
  } catch (err) {
    console.error("Failed to fetch employees", err);
  }
};

const toggleAllEmployees = () => {
  if (allSelected.value) {
    selectedEmployees.value = [];
  } else {
    selectedEmployees.value = employees.value.map((e) => e.id);
  }
};

const openBulkModal = () => {
  selectedEmployees.value = [];
  bulkPayMonth.value = "";
  showBulkModal.value = true;
};

const closeBulkModal = () => {
  showBulkModal.value = false;
};

const createBulkPayroll = async () => {
  try {
    const payload = selectedEmployees.value.map((empId) => {
      const emp = employees.value.find((e) => e.id === empId);
      return {
        employee_id: emp.id,
        pay_month: bulkPayMonth.value + "-01",
        working_days: emp.working_days,
        other_commission: emp.other_commission,
        prepared_by: JSON.parse(localStorage.getItem("user"))?.id,
      };
    });

    await api.post("/payrolls/bulk", { payrolls: payload });
    showBulkModal.value = false;
    fetchPayrolls();
  } catch (error) {
    console.error("Error creating payrolls", error);
  }
};

const viewPayroll = (payroll) => {
  selectedPayroll.value = payroll;
  showViewModal.value = true;
};

onMounted(() => {
  fetchPayrolls();
  fetchEmployees();
});
</script>
