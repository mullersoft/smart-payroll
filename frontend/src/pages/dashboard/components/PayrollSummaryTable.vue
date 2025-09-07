<template>
  <div
    v-if="payrolls.length > 0"
    class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden"
  >
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase"
            >
              Period
            </th>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase"
            >
              Net Pay
            </th>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase"
            >
              Status
            </th>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase"
            >
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="payroll in payrolls"
            :key="payroll.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
              {{ formatDate(payroll.pay_month, "monthYear") }}
            </td>
            <td
              class="px-4 py-3 text-sm font-medium text-green-600 dark:text-green-400"
            >
              Birr {{ formatCurrency(payroll.net_payment) }}
            </td>
            <td class="px-4 py-3">
              <span :class="statusClasses(payroll.status)">
                {{ payroll.status?.toUpperCase() }}
              </span>
            </td>
            <td class="px-4 py-3 text-sm font-medium">
              <button
                @click="$emit('view-details', payroll)"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
              >
                View
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { formatCurrency, formatDate } from "@/utils/formatters";

defineProps({
  payrolls: {
    type: Array,
    required: true,
  },
});

defineEmits(["view-details"]);

const statusClasses = (status) => {
  const base = "px-2 py-1 text-xs font-medium rounded-full";
  switch (status) {
    case "prepared":
      return `${base} bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200`;
    case "approved":
      return `${base} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200`;
    case "paid":
      return `${base} bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200`;
    default:
      return `${base} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200`;
  }
};
</script>
