<template>
  <!-- Employee View Details Modal Component -->
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl shadow-xl overflow-y-auto max-h-[90vh] transition-colors duration-300"
    >
      <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
        View Employee Details
      </h2>
      <div class="grid grid-cols-2 gap-4">
        <div class="py-2">
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Full Name
          </p>
          <p class="text-gray-900 dark:text-gray-100">
            {{ employee.full_name }}
          </p>
        </div>
        <div class="py-2">
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Employee ID
          </p>
          <p class="text-gray-900 dark:text-gray-100">
            {{ employee.employee_id }}
          </p>
        </div>
        <div class="py-2">
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Position
          </p>
          <p class="text-gray-900 dark:text-gray-100">
            {{ employee.position }}
          </p>
        </div>
        <div class="py-2">
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Employment Type
          </p>
          <p class="text-gray-900 dark:text-gray-100">
            {{ employee.employment_type }}
          </p>
        </div>
        <div class="py-2">
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Base Salary
          </p>
          <p class="text-gray-900 dark:text-gray-100">
            {{ employee.base_salary }}
          </p>
        </div>
        <div class="py-2">
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Gender
          </p>
          <p class="text-gray-900 dark:text-gray-100">{{ employee.gender }}</p>
        </div>
        <div class="py-2">
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Employment Date
          </p>
          <p class="text-gray-900 dark:text-gray-100">
            {{ employee.employment_date }}
          </p>
        </div>
        <div class="py-2">
          <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Status
          </p>
          <p :class="employee.is_active ? 'text-green-600' : 'text-red-600'">
            {{ employee.is_active ? "Active" : "Inactive" }}
          </p>
        </div>
      </div>
      <div class="flex justify-end space-x-3 mt-4">
        <button
          type="button"
          @click="$emit('close')"
          class="bg-gray-600 text-white px-4 py-2 rounded"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

defineProps({
  show: Boolean,
  employee: Object,
});

defineEmits(["close"]);
</script>
