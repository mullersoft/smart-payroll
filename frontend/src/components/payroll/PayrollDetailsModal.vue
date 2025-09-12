<template>
  <div
    v-if="visible"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-3xl shadow-xl transition-colors duration-300 max-h-[90vh] overflow-y-auto"
    >
      <h2 class="text-xl mb-4 text-gray-900 dark:text-gray-100 underline font-bold">
        Payroll Details:-
      </h2>

      <!-- Employee Info -->
      <div class="grid grid-cols-2 gap-4 text-gray-900 dark:text-gray-100 mb-6">
        <p><strong>Name:</strong> {{ payroll?.employee?.full_name || payroll?.employee_name }}</p>
        <p><strong>Pay Month:</strong> {{ payroll?.pay_month }}</p>
        <p><strong>Employment Date:</strong> {{ payroll?.employee?.employment_date || payroll?.employment_date }}</p>
        <p><strong>Position:</strong> {{ payroll?.employee?.position?.name || "N/A" }}</p>
        <p><strong>Employment Type:</strong> {{ payroll?.employee?.employment_type?.name || "N/A" }}</p>
        <p><strong>Working Days:</strong> {{ payroll?.working_days }}</p>
      </div>

      <!-- Earnings Section -->
      <div class="mb-6">
        <h3 class="font-semibold text-green-700 mb-2">Earnings:</h3>
        <div class="grid grid-cols-2 gap-4">
          <p><strong>Base Salary:</strong> <span class="text-green-600">Birr {{ formatNumber(payroll?.base_salary) }}</span></p>
          <p><strong>Earned Salary:</strong> <span class="text-green-600">Birr {{ formatNumber(payroll?.earned_salary) }}</span></p>
          <p><strong>Position Allowance (Non-Tax):</strong> <span class="text-green-600">Birr {{ formatNumber(payroll?.position_allowance_non_tax) }}</span></p>
          <p><strong>Position Allowance (Taxable):</strong> <span class="text-green-600">Birr {{ formatNumber(payroll?.position_allowance_taxable) }}</span></p>
          <!-- <p><strong>Transport Allowance (Non-Tax):</strong> <span class="text-green-600">Birr {{ formatNumber(payroll?.transport_allowance) }}</span></p> -->
          <p><strong>Other Commission:</strong> <span class="text-green-600">Birr {{ formatNumber(payroll?.other_commission) }}</span></p>
          <!-- <p><strong>Gross Pay:</strong> <span class="text-green-700 font-semibold">Birr {{ formatNumber(payroll?.gross_pay) }}</span></p> -->
        </div>
      </div>





      <!-- Dynamic Allowances -->
      <div v-if="payroll?.allowances?.length" class="mb-6">
        <p><strong class="font-semibold text-yellow-700 red:text-gray-200 mb-2">Allowances</strong></p>
        <ul class="list-disc list-inside space-y-1">
          <li v-for="al in payroll.allowances" :key="al.id">
            {{ al.name }}:
            <strong class="font-semibold text-yellow-700 red:text-gray-200 mb-2">
               Birr {{ formatNumber(al.amount) }}
</strong>
            <span class="text-xs italic text-gray-500">
              ({{ al.is_taxable ? "Taxable" : "Non-Taxable" }})
            </span>
          </li>
        </ul>
      </div>

      <!-- Overtime -->
      <div v-if="payroll?.overtimes?.length" class="mb-6">
        <p><strong class="font-semibold text-yellow-700 red:text-gray-200 mb-2">
         Overtime:
</strong></p>
        <ul class="list-disc list-inside space-y-1">
          <li v-for="ot in payroll.overtimes" :key="ot.id">
            {{ formatNumber(ot.hours) }} hrs × {{ formatNumber(ot.multiplier) }} × hourly rate =
<strong class="font-semibold text-yellow-700 red:text-gray-200 mb-2">
   Birr {{ formatNumber(ot.amount) }}
</strong>
            <span class="text-xs italic text-gray-500">
              ({{ mapOvertimeType(ot.rate_type) }})
            </span>
          </li>
        </ul>
      </div>
      <div>
          <p><strong>Gross Pay:</strong> <span class="text-green-700 font-semibold">Birr {{ formatNumber(payroll?.gross_pay) }}</span></p>
          <p><strong>Taxable Income:</strong> <span class="text-green-700 font-semibold">Birr {{ formatNumber(payroll?.taxable_income) }}</span></p>


      </div>
      <!-- Deductions Section -->
      <div class="mb-6">
        <h3 class="font-semibold text-red-700 mb-2">Deductions:</h3>
        <div class="grid grid-cols-2 gap-4">
          <!-- <p><strong>Taxable Income:</strong> <span class="text-red-600">Birr {{ formatNumber(payroll?.taxable_income) }}</span></p> -->
          <p><strong>Income Tax:</strong> <span class="text-red-600">Birr {{ formatNumber(payroll?.income_tax) }}</span></p>
          <p><strong>Employee Pension:</strong> <span class="text-red-600">Birr {{ formatNumber(payroll?.employee_pension) }}</span></p>
          <p><strong>Employer Pension:</strong> <span class="text-red-600">Birr {{ formatNumber(payroll?.employer_pension) }}</span></p>
          <p><strong>Total Pension Contribution:</strong> <span class="text-red-700">Birr {{ formatNumber(payroll?.pension_contribution) }}</span></p>
          <p><strong>Total Deduction:</strong> <span class="text-red-700 font-semibold">Birr {{ formatNumber(payroll?.total_deduction) }}</span></p>
        </div>
        <div>
          <p><strong>Penalty/Loan:</strong> <span class="text-red-600">Birr {{ formatNumber(payroll?.loan_penalty) }}</span></p>

        </div>
      </div>
       <!-- Summary Section -->
      <div class="mb-6">
        <p class="text-lg">
          <strong>Net Payment : </strong>
          <span class="text-blue-700 font-bold underline">Birr {{ formatNumber(payroll?.net_payment) }}</span>
        </p>
      </div>

      <!-- Metadata -->
      <div class="grid grid-cols-2 gap-4 text-gray-900 dark:text-gray-100">
        <p><strong>Prepared By:</strong> {{ payroll?.prepared_by?.name || 'Unknown' }}</p>
        <p><strong>Approved By:</strong> {{ payroll?.approved_by?.name || 'Not Approved' }}</p>
      </div>

      <p v-if="payroll?.rejection_reason" class="text-red-600 dark:text-red-400 mt-2">
        <strong>Rejection Reason:</strong> {{ payroll.rejection_reason }}
      </p>

      <div class="flex justify-end mt-6">
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
const props = defineProps({
  visible: { type: Boolean, default: false },
  payroll: { type: Object, default: null },
});

defineEmits(["close"]);

const formatNumber = (value) => {
  const numberValue = Number(value ?? 0);
  return Number.isFinite(numberValue) ? numberValue.toLocaleString() : "0";
};
const mapOvertimeType = (type) => {
  switch (type) {
    case "weekday_evening": return "Weekday (6:30 a.m - 10:00 p.m)";
    case "night": return "Night (10:00 p.m - 6:00 a.m)";
    case "rest_day": return "Weekly Rest Day";
    case "holiday": return "Public Holiday";
    default: return "Other";
  }
};

</script>
