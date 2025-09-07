<!-- D:\qelem meda\smart-payroll\frontend\src\components\dashboard\EmploymentDetailsCard.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <h3
      class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center"
    >
      <svg
        class="w-5 h-5 mr-2 text-green-500"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
        ></path>
      </svg>
      Employment Details
    </h3>
    <div class="space-y-3">
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Position:</span>
        <span class="text-gray-800 dark:text-gray-100">{{
          employeeData.position?.name || "N/A"
        }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Employment Type:</span>
        <span class="text-gray-800 dark:text-gray-100 capitalize">{{
          employeeData.employment_type?.name || "N/A"
        }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Base Salary:</span>
        <span class="text-gray-800 dark:text-gray-100 font-medium"
          >Birr {{ formatCurrency(employeeData.base_salary) }}</span
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { formatCurrency } from "@/utils/formatters";

defineProps({
  employeeData: {
    type: Object,
    required: true,
  },
});
</script>
