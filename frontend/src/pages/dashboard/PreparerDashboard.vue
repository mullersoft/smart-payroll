<template>
  <MainLayout>
    <div class="space-y-4">
      <h1 class="text-3xl font-bold text-indigo-800 dark:text-indigo-400">
        Preparer Dashboard
      </h1>
      <p class="text-gray-600 dark:text-gray-300">
        Welcome, Preparer. Here's a summary of the payroll and employee data.
      </p>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
          class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-indigo-500 dark:border-indigo-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Total Employees
          </h2>
          <p class="text-indigo-600 dark:text-indigo-400 font-bold text-2xl">
            {{ employeeCount }}
          </p>
        </div>

        <div
          class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-green-500 dark:border-green-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Prepared Payrolls
          </h2>
          <p class="text-green-600 dark:text-green-400 font-bold text-2xl">
            {{ payrollCount }}
          </p>
        </div>

        <div
          class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-yellow-500 dark:border-yellow-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Total Approved Expenditure
          </h2>
          <p class="text-yellow-600 dark:text-yellow-400 font-bold text-2xl">
            ETB {{ totalExpenditure.toLocaleString() }}
          </p>
        </div>

        <div
          class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 border-l-4 border-blue-500 dark:border-blue-400"
        >
          <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Bank Accounts
          </h2>
          <p class="text-blue-600 dark:text-blue-400 font-bold text-2xl">
            {{ bankAccountCount }}
          </p>
        </div>
      </div>

      <h2 class="text-2xl font-bold text-indigo-800 dark:text-indigo-400 mt-8">
        Employee Demographics
      </h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
          <h3
            class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4"
          >
            Employees by Gender
          </h3>
          <div class="h-64">
            <Pie
              v-if="genderChartData.datasets.length"
              :data="genderChartData"
              :options="chartOptions"
            />
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
          <h3
            class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4"
          >
            Employees by Employment Type
          </h3>
          <div class="h-64">
            <Bar
              v-if="employmentChartData.datasets.length"
              :data="employmentChartData"
              :options="chartOptions"
            />
          </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
          <h3
            class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-4"
          >
            Employees by Position
          </h3>
          <div class="h-64">
            <Bar
              v-if="positionChartData.datasets.length"
              :data="positionChartData"
              :options="chartOptions"
            />
          </div>
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
// Import components for charts
import {
  ArcElement,
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  Title,
  Tooltip,
} from "chart.js";
import { Bar, Pie } from "vue-chartjs";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  ArcElement
);

const employeeCount = ref(0);
const payrollCount = ref(0);
const bankAccountCount = ref(0);
const totalExpenditure = ref(0);
const genderStats = ref({});
const employmentTypeStats = ref({});
const positionStats = ref({});

const genderChartData = ref({ labels: [], datasets: [] });
const employmentChartData = ref({ labels: [], datasets: [] });
const positionChartData = ref({ labels: [], datasets: [] });

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

const fetchData = async () => {
  try {
    const [employeeRes, payrollRes, bankRes] = await Promise.all([
      api.get("/employees"),
      api.get("/payrolls"),
      api.get("/bank-accounts"),
      // api.get("/dashboard-summary"), // Call the new endpoint
    ]);

    employeeCount.value = employeeRes.data.length ?? 0;
    payrollCount.value = payrollRes.data.length ?? 0;
    bankAccountCount.value = bankRes.data.length ?? 0;

    // Update new dashboard data
    // totalExpenditure.value = summaryRes.data.totalExpenditure;
    // genderStats.value = summaryRes.data.genderStats;
    // employmentTypeStats.value = summaryRes.data.employmentTypeStats;
    // positionStats.value = summaryRes.data.positionStats;

    // Prepare chart data
    genderChartData.value = {
      labels: Object.keys(genderStats.value),
      datasets: [
        {
          label: "Employees by Gender",
          backgroundColor: ["#4299e1", "#e53e3e", "#718096"],
          data: Object.values(genderStats.value),
        },
      ],
    };

    employmentChartData.value = {
      labels: Object.keys(employmentTypeStats.value),
      datasets: [
        {
          label: "Employees by Employment Type",
          backgroundColor: ["#667eea", "#48bb78"],
          data: Object.values(employmentTypeStats.value),
        },
      ],
    };

    positionChartData.value = {
      labels: Object.keys(positionStats.value),
      datasets: [
        {
          label: "Employees by Position",
          backgroundColor: ["#f6ad55", "#9f7aea", "#4fd1c5", "#f687b3"],
          data: Object.values(positionStats.value),
        },
      ],
    };
  } catch (err) {
    console.error("Failed to fetch preparer dashboard data", err);
    toast.error("Failed to load dashboard data.");
  }
};

onMounted(fetchData);
</script>
