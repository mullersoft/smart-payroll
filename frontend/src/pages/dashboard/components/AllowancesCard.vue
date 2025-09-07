<!-- D:\qelem meda\smart-payroll\frontend\src\components\dashboard\AllowancesCard.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <h3
      class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center"
    >
      <svg
        class="w-5 h-5 mr-2 text-yellow-500"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 01118 0z"
        ></path>
      </svg>
      Allowances
    </h3>
    <div
      v-if="employeeData.allowances && employeeData.allowances.length > 0"
      class="space-y-3"
    >
      <div
        v-for="allowance in employeeData.allowances"
        :key="allowance.id"
        class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700 last:border-b-0"
      >
        <div>
          <span class="text-gray-800 dark:text-gray-100 font-medium">{{
            allowance.name
          }}</span>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ allowance.description }}
          </p>
        </div>
        <span class="text-green-600 dark:text-green-400 font-medium"
          >Birr {{ formatCurrency(allowance.value) }}</span
        >
      </div>
      <div class="pt-2 mt-2 border-t border-gray-200 dark:border-gray-600">
        <div class="flex justify-between font-semibold">
          <span class="text-gray-800 dark:text-gray-100"
            >Total Allowances:</span
          >
          <span class="text-green-700 dark:text-green-300"
            >Birr {{ formatCurrency(totalAllowances) }}</span
          >
        </div>
      </div>
    </div>
    <div v-else class="text-center py-4 text-gray-500 dark:text-gray-400">
      No allowances assigned
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { formatCurrency } from "@/utils/formatters";

const props = defineProps({
  employeeData: {
    type: Object,
    required: true,
  },
});

const totalAllowances = computed(() => {
  if (!props.employeeData.allowances) return 0;
  return props.employeeData.allowances.reduce((sum, allowance) => {
    return sum + parseFloat(allowance.value || 0);
  }, 0);
});
</script>
