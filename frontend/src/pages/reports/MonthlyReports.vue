<template>
  <MainLayout>
    <div class="space-y-6">
      <h1 class="text-2xl font-bold text-purple-700 dark:text-purple-400">
        📊 Monthly Payroll Report
      </h1>

      <!-- Month Picker -->
      <form @submit.prevent="fetchReport" class="flex items-end space-x-4">
        <div>
          <label
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
            >Select Month</label
          >
          <input
            v-model="selectedMonth"
            type="month"
            required
            class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-400 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          />
        </div>
        <button
          type="submit"
          class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold"
        >
          View Report
        </button>
      </form>

      <!-- Totals Summary -->
      <div v-if="report" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          class="bg-white shadow-md rounded-lg p-6 border-l-4 border-green-500 dark:bg-gray-800 dark:text-gray-100"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Total Employees
          </h2>
          <p class="text-2xl font-bold text-green-600 dark:text-green-400">
            {{ report.total_employees }}
          </p>
        </div>
        <div
          class="bg-white shadow-md rounded-lg p-6 border-l-4 border-indigo-500 dark:bg-gray-800 dark:text-gray-100"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Total Net Payment
          </h2>
          <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
            {{ report.total_net_payment.toLocaleString() }}
          </p>
        </div>
      </div>

      <!-- Export Buttons -->
      <div v-if="report" class="flex space-x-4">
        <button
          @click="exportReport('excel')"
          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md"
        >
          📥 Export to Excel
        </button>
        <button
          @click="exportReport('pdf')"
          class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md"
        >
          📄 Export to PDF
        </button>
      </div>

      <!-- Detailed Report Table -->
      <div
        v-if="report"
        class="bg-white p-6 rounded-lg shadow-md overflow-x-auto dark:bg-gray-800 dark:text-gray-100"
      >
        <table class="min-w-full table-auto text-sm">
          <thead class="bg-gray-100 dark:bg-gray-700">
            <tr>
              <th class="px-4 py-2">#</th>
              <th class="px-4 py-2">Employee</th>
              <th class="px-4 py-2">Employment Date</th>
              <th class="px-4 py-2">Position</th>
              <th class="px-4 py-2">Base Salary</th>
              <th class="px-4 py-2">Working Days</th>
              <th class="px-4 py-2">Earned Salary</th>
              <th class="px-4 py-2">Position Allowance</th>
              <th class="px-4 py-2">Transport Allowance</th>
              <th class="px-4 py-2">Other Commission</th>
              <th class="px-4 py-2">Gross Pay</th>
              <th class="px-4 py-2">Taxable Income</th>
              <th class="px-4 py-2">Income Tax</th>
              <th class="px-4 py-2">Pension (Emp)</th>
              <th class="px-4 py-2">Pension (Er)</th>
              <th class="px-4 py-2">Total Deduction</th>
              <th class="px-4 py-2">Net Payment</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(row, index) in report.records"
              :key="index"
              class="border-b border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="px-4 py-2">{{ index + 1 }}</td>
              <td class="px-4 py-2">{{ row.employee_name }}</td>
              <td class="px-4 py-2">{{ row.employment_date }}</td>
              <td class="px-4 py-2">{{ row.position }}</td>
              <td class="px-4 py-2">{{ row.base_salary }}</td>
              <td class="px-4 py-2">{{ row.working_days }}</td>
              <td class="px-4 py-2">{{ row.earned_salary }}</td>
              <td class="px-4 py-2">{{ row.position_allowance }}</td>
              <td class="px-4 py-2">{{ row.transport_allowance }}</td>
              <td class="px-4 py-2">{{ row.other_commission }}</td>
              <td class="px-4 py-2">{{ row.gross_pay }}</td>
              <td class="px-4 py-2">{{ row.taxable_income }}</td>
              <td class="px-4 py-2">{{ row.income_tax }}</td>
              <td class="px-4 py-2">{{ row.employee_pension }}</td>
              <td class="px-4 py-2">{{ row.employer_pension }}</td>
              <td class="px-4 py-2">{{ row.total_deduction }}</td>
              <td class="px-4 py-2">{{ row.net_payment }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from "vue";
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";

const selectedMonth = ref("");
const report = ref(null);

const fetchReport = async () => {
  try {
    const res = await api.get(`/reports/monthly/${selectedMonth.value}`);
    report.value = res.data;
  } catch (error) {
    console.error("Error fetching report:", error);
  }
};

const exportReport = async (type) => {
  if (!selectedMonth.value) return;
  const url =
    type === "excel"
      ? `/reports/monthly/${selectedMonth.value}/export-excel`
      : `/reports/monthly/${selectedMonth.value}/export-pdf`;
  window.open(api.defaults.baseURL + url, "_blank");
};
</script>
