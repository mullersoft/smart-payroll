<template>
  <MainLayout>
    <div>
      <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
        <nav class="flex space-x-4">
          <button
            v-for="tab in tabs"
            :key="tab"
            @click="currentTab = tab"
            :class="
              currentTab === tab
                ? 'text-indigo-600 border-b-2 border-indigo-600'
                : 'text-gray-500'
            "
            class="px-3 py-2 font-medium"
          >
            {{ tab }}
          </button>
        </nav>
      </div>

      <div v-if="currentTab === 'Employees'">
        <EmployeesPage />
      </div>
      <div v-else-if="currentTab === 'Positions'">
        <PositionsPage />
      </div>
      <div v-else-if="currentTab === 'Employment Types'">
        <EmploymentTypesPage />
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref } from "vue";
import MainLayout from "@/components/layout/MainLayout.vue";
import EmployeesPage from "./EmployeesPage.vue";
import PositionsPage from "./PositionsPage.vue";
import EmploymentTypesPage from "./EmploymentTypesPage.vue";

const tabs = ["Employees", "Positions", "Employment Types"];
const currentTab = ref("Employees");
</script>
