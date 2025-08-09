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

        <!-- Payrolls Processed -->
        <div
          class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-l-4 border-green-500 dark:border-green-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Payrolls Processed
          </h2>
          <p class="text-2xl text-green-600 dark:text-green-400 font-bold">
            {{ payrollsCount }}
          </p>
        </div>

        <!-- Pending Approvals -->
        <div
          class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-6 border-l-4 border-yellow-500 dark:border-yellow-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Pending Approvals
          </h2>
          <p class="text-2xl text-yellow-600 dark:text-yellow-400 font-bold">
            {{ pendingApprovals }}
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

const usersCount = ref(0);
const payrollsCount = ref(0);
const pendingApprovals = ref(0);

const fetchDashboardData = async () => {
  try {
    const userRes = await api.get("/users");
    usersCount.value = userRes.data.length;

    const payrollRes = await api.get("/payrolls");
    payrollsCount.value = payrollRes.data.length;

    const pendingRes = await api.get("/payrolls/pending");
    pendingApprovals.value = pendingRes.data.count;
  } catch (error) {
    console.error("Dashboard fetch error:", error);
  }
};

onMounted(fetchDashboardData);
</script>
