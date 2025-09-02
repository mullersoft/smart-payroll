<template>
  <MainLayout>
    <div class="space-y-4">
      <h1 class="text-3xl font-bold text-blue-800 dark:text-blue-400">
        👨‍💼 Admin Dashboard
      </h1>
      <p class="text-gray-600 dark:text-gray-300">Welcome, Admin.</p>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Users -->
        <div
          class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-l-4 border-blue-500 dark:border-blue-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Users
          </h2>
          <p class="text-2xl text-blue-600 dark:text-blue-400 font-bold">
            {{ usersCount }}
          </p>
        </div>

        <!-- Employees -->
        <div
          class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-l-4 border-green-500 dark:border-green-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Employees
          </h2>
          <p class="text-2xl text-green-600 dark:text-green-400 font-bold">
            {{ employeesCount }}
          </p>
        </div>

        <!-- Accounts-->
        <div
          class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-l-4 border-yellow-500 dark:border-yellow-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
Accounts          </h2>
          <p class="text-2xl text-yellow-600 dark:text-yellow-400 font-bold">
            {{ accountsCount }}
          </p>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
const toast = useToast();
const usersCount = ref(0);
const employeesCount = ref(0);
const accountsCount = ref(0);


const fetchDashboardData = async () => {
  try {
    const userRes = await api.get("/users");
    usersCount.value = userRes.data.length;

    const empRes = await api.get("/employees");
    employeesCount.value = empRes.data.length;

    const accRes = await api.get("/bank-accounts");
    accountsCount.value = accRes.data.length;

  } catch (error) {
    console.error("Dashboard fetch error:", error);
    toast.error("Failed to load dashboard data.");

  }
};

onMounted(fetchDashboardData);
</script>
