<template>
  <div
    v-if="visible"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl shadow-xl transition-colors duration-300"
    >
      <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
        Payroll Details
      </h2>

      <div class="grid grid-cols-2 gap-4 text-gray-900 dark:text-gray-100">
        <p><strong>Employee:</strong> {{ payroll?.employee?.full_name || payroll?.employee_name }}</p>
        <p><strong>Pay Month:</strong> {{ payroll?.pay_month }}</p>
        <p><strong>Employment Date:</strong>{{ payroll?.employee?.employment_date || payroll?.employment_date }}</p>
        <p><strong>Position:</strong>{{ payroll?.employee?.position || payroll?.position }}</p>
        <p><strong>Base Salary:</strong> Birr{{ formatNumber(payroll?.base_salary) }}</p>
        <p><strong>Working Days:</strong> {{ payroll?.working_days }}</p>
        <p><strong>Earned Salary:</strong> Birr{{ formatNumber(payroll?.earned_salary) }} </p>
        <p><strong>Position Allowance (Non-Tax):</strong> Birr{{ formatNumber(payroll?.position_allowance_non_tax) }}</p>
        <p><strong>Position Allowance (Taxable):</strong> Birr{{ formatNumber(payroll?.position_allowance_taxable) }}</p>
        <p><strong>Transport Allowance:</strong> Birr{{ formatNumber(payroll?.transport_allowance) }}</p>
        <p><strong>Other Commission:</strong> Birr {{ formatNumber(payroll?.other_commission) }} </p>
        <p><strong>Gross Pay:</strong> Birr{{ formatNumber(payroll?.gross_pay) }}</p>
        <p><strong>Taxable Income:</strong> Birr{{ formatNumber(payroll?.taxable_income) }}</p>
        <p><strong>Income Tax:</strong> Birr{{ formatNumber(payroll?.income_tax) }}</p>
        <p><strong>Employee Pension:</strong> Birr{{ formatNumber(payroll?.employee_pension) }} </p>
        <p><strong>Employer Pension:</strong> Birr  {{ formatNumber(payroll?.employer_pension) }}</p>
        <p><strong>Total Pension Contribution:</strong> Birr  {{ formatNumber(payroll?.pension_contribution) }}</p>
        <p><strong>Total Deduction:</strong> Birr{{ formatNumber(payroll?.total_deduction) }}</p>
        <p><strong>Net Payment:</strong> Birr{{ formatNumber(payroll?.net_payment) }}</p>
        <p><strong>Status:</strong> {{ payroll?.status }}</p>

        <p v-if="payroll?.rejection_reason">
          <strong>Reason:</strong> {{ payroll?.rejection_reason }}
        </p>
      </div>

      <div class="flex justify-end mt-4">
        <button
          @click="$emit('close')"
          class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  visible: { type: Boolean, default: false },
  payroll: { type: Object, default: null },
});

defineEmits(["close"]);

const formatNumber = (value) => {
  const numberValue = Number(value ?? 0);
  return Number.isFinite(numberValue) ? numberValue.toLocaleString() : "0";
};
</script>
