<template>
  <div
    v-if="transactions.length > 0"
    class="bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden"
  >
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase"
            >
              Date
            </th>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase"
            >
              Type
            </th>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase"
            >
              Amount
            </th>
            <th
              class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase"
            >
              Status
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          <tr
            v-for="transaction in transactions"
            :key="transaction.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">
              {{ formatDate(transaction.transaction_date) }}
            </td>
            <td
              class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200 capitalize"
            >
              {{ transaction.transaction_type }}
            </td>
            <td
              class="px-4 py-3 text-sm font-medium"
              :class="amountColor(transaction)"
            >
              Birr {{ formatCurrency(transaction.amount) }}
            </td>
            <td class="px-4 py-3">
              <span :class="statusClasses(transaction.status)">
                {{ transaction.status?.toUpperCase() }}
              </span>
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
  transactions: {
    type: Array,
    required: true,
  },
});

const amountColor = (transaction) => {
  return transaction.transaction_type === "deduction"
    ? "text-red-600 dark:text-red-400"
    : "text-green-600 dark:text-green-400";
};

const statusClasses = (status) => {
  const base = "px-2 py-1 text-xs font-medium rounded-full";
  switch (status) {
    case "completed":
      return `${base} bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200`;
    case "pending":
      return `${base} bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200`;
    case "failed":
      return `${base} bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200`;
    default:
      return `${base} bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200`;
  }
};
</script>
