<template>
  <MainLayout>
    <div class="space-y-4">
      <h1 class="text-3xl font-bold text-indigo-800 dark:text-indigo-400">
        Preparer Dashboard
      </h1>
      <p class="text-gray-600 dark:text-gray-300">
        Welcome, Preparer. You can create payrolls, manage employees, and run
        reports.
      </p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div
          class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-indigo-500 dark:border-indigo-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Employees
          </h2>
          <p class="text-indigo-600 dark:text-indigo-400 font-bold text-xl">
            {{ employeeCount }}
          </p>
        </div>
        <div
          class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-indigo-500 dark:border-indigo-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Prepared Payrolls
          </h2>
          <p class="text-indigo-600 dark:text-indigo-400 font-bold text-xl">
            {{ payrollCount }}
          </p>
        </div>
        <div
          class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-indigo-500 dark:border-indigo-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Bank Accounts
          </h2>
          <p class="text-indigo-600 dark:text-indigo-400 font-bold text-xl">
            {{ bankAccountCount }}
          </p>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";

const employeeCount = ref(0);
const payrollCount = ref(0);
const bankAccountCount = ref(0);

const fetchData = async () => {
  try {
    const [employeeRes, payrollRes, bankRes] = await Promise.all([
      api.get("/employees"),
      api.get("/payrolls"),
      api.get("/bank-accounts"),
    ]);
    employeeCount.value = employeeRes.data.length ?? 0;
    payrollCount.value = payrollRes.data.length ?? 0;
    bankAccountCount.value = bankRes.data.length ?? 0;
  } catch (err) {
    console.error("Failed to fetch preparer dashboard data", err);
  }
};

onMounted(fetchData);
</script>
