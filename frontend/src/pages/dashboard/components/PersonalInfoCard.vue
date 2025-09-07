<!-- D:\qelem meda\smart-payroll\frontend\src\components\dashboard\PersonalInfoCard.vue -->
<template>
  <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <h3
      class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center"
    >
      <svg
        class="w-5 h-5 mr-2 text-blue-500"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
        ></path>
      </svg>
      Personal Information
    </h3>
    <div class="space-y-3">
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Full Name:</span>
        <span class="text-gray-800 dark:text-gray-100 font-medium">{{
          employeeData.full_name || auth.user?.name || "N/A"
        }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Email:</span>
        <span class="text-gray-800 dark:text-gray-100">{{
          employeeData.email || auth.user?.email || "N/A"
        }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Gender:</span>
        <span class="text-gray-800 dark:text-gray-100 capitalize">{{
          employeeData.gender?.toLowerCase() || "N/A"
        }}</span>
      </div>
      <div class="flex justify-between">
        <span class="text-gray-600 dark:text-gray-300">Employment Date:</span>
        <span class="text-gray-800 dark:text-gray-100">{{
          formatDate(employeeData.employment_date)
        }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from "@/store/auth";
import { formatDate } from "@/utils/formatters";

const auth = useAuthStore();
defineProps({
  employeeData: {
    type: Object,
    required: true,
  },
});
</script>
