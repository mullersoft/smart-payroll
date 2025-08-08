<!-- src/pages/reports/MonthlyReports.vue -->
<template>
  <MainLayout>
    <div class="space-y-6">
      <h1 class="text-2xl font-bold text-purple-700">📊 Monthly Payroll Report</h1>

      <!-- Month Picker -->
      <form @submit.prevent="fetchReport" class="flex items-end space-x-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Select Month</label>
          <input
            v-model="selectedMonth"
            type="month"
            required
            class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-purple-400"
          />
        </div>
        <button
          type="submit"
          class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md font-semibold"
        >
          View Report
        </button>
      </form>

      <!-- Report Display -->
      <div v-if="report" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow-md rounded-lg p-6 border-l-4 border-green-500">
          <h2 class="text-lg font-semibold text-gray-700">Approved Payrolls</h2>
          <p class="text-2xl font-bold text-green-600">{{ report.approved }}</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6 border-l-4 border-yellow-500">
          <h2 class="text-lg font-semibold text-gray-700">Pending Payrolls</h2>
          <p class="text-2xl font-bold text-yellow-600">{{ report.pending }}</p>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6 border-l-4 border-red-500">
          <h2 class="text-lg font-semibold text-gray-700">Rejected Payrolls</h2>
          <p class="text-2xl font-bold text-red-600">{{ report.rejected }}</p>
        </div>
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
</script>
