<!-- D:\qelem meda\smart-payroll\frontend\src\components\dashboard\BankAccountCard.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center">
      <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
      </svg>
      Bank Account
    </h3>
    <div v-if="employeeData.bank_account" class="space-y-3">
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Bank Name:</span>
        <span class="text-gray-800 dark:text-gray-100">{{ employeeData.bank_account.bank_name }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Account Number:</span>
        <span class="text-gray-800 dark:text-gray-100">{{ employeeData.bank_account.account_number }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Account Holder:</span>
        <span class="text-gray-800 dark:text-gray-100">{{ employeeData.bank_account.owner_name }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Current Balance:</span>
        <span class="text-gray-800 dark:text-gray-100 font-medium">Birr {{ formatCurrency(employeeData.bank_account.balance) }}</span>
      </div>
    </div>
    <div v-else class="text-center py-4 text-gray-500 dark:text-gray-400">
      No bank account information available
    </div>
  </div>
</template>

<script setup>
import { formatCurrency } from "@/utils/formatters";

defineProps({
  employeeData: {
    type: Object,
    required: true
  }
});
</script>
