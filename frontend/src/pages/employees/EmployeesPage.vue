<template>
  <div class="space-y-6">
    <div
      class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
    >
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
        👥 Employees Management
      </h1>

      <div
        class="flex flex-col sm:flex-row items-end sm:items-center gap-4 w-full sm:w-auto"
      >
        <!-- Status Filter -->
        <div class="w-full sm:w-40">
          <label
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
            >Filter Status</label
          >
          <select
            v-model="statusFilter"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none dark:bg-gray-700 dark:text-gray-200"
          >
            <option value="active">Active Employees</option>
            <option value="inactive">Inactive Employees</option>
            <option value="all">All Employees</option>
          </select>
        </div>

        <button
          class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm flex items-center gap-2"
          @click="openAddModal"
        >
          ➕ Add Employee
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
              >
                #
              </th>
              <th class="px-6 py-3">Full Name</th>
              <th class="px-6 py-3">Position</th>
              <th class="px-6 py-3">Type</th>
              <th class="px-6 py-3">Salary</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="(emp, index) in filteredEmployees" :key="emp.id">
              <td class="px-6 py-4">{{ index + 1 }}</td>
              <td class="px-6 py-4">{{ emp.full_name }}</td>
              <td class="px-6 py-4">{{ emp.position?.name }}</td>
              <td class="px-6 py-4 capitalize">
                {{ emp.employment_type?.name }}
              </td>
              <td class="px-6 py-4">
                Birr {{ Number(emp.base_salary).toLocaleString() }}
              </td>
              <td class="px-6 py-4">
                <span
                  :class="
                    emp.is_active
                      ? 'bg-green-100 text-green-800 px-2 py-1 rounded-full'
                      : 'bg-red-100 text-red-800 px-2 py-1 rounded-full'
                  "
                >
                  {{ emp.is_active ? "Active" : "Inactive" }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="relative inline-block text-left">
                  <button
                    @click.stop="toggleDropdown(emp.id)"
                    class="px-3 py-1 border rounded-md bg-white dark:bg-gray-700"
                  >
                    ⋮
                  </button>
                  <div
                    v-if="openDropdownId === emp.id"
                    class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10"
                  >
                    <button
                      @click="openEditModal(emp)"
                      class="block w-full text-left px-4 py-2 hover:bg-gray-100"
                    >
                      ✏ Edit
                    </button>
                    <button
                      @click="toggleStatus(emp)"
                      class="block w-full text-left px-4 py-2 hover:bg-gray-100"
                    >
                      {{ emp.is_active ? "Deactivate" : "Activate" }}
                    </button>
                  </div>
                </div>
              </td>
            </tr>
            <tr v-if="filteredEmployees.length === 0">
              <td colspan="7" class="px-6 py-4 text-center">
                No employees found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <EmployeeEditModal
      :show="showEditModal"
      :is-editing="isEditing"
      :employee="selectedEmployee"
      :positions="positions"
      :employmentTypes="employmentTypes"
      @save="handleSave"
      @close="showEditModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import api from "@/services/api";
import EmployeeEditModal from "./EmployeeEditModal.vue";

const employees = ref([]);
const positions = ref([]);
const employmentTypes = ref([]);
const statusFilter = ref("active");
const showEditModal = ref(false);
const isEditing = ref(false);
const selectedEmployee = ref(null);
const openDropdownId = ref(null);

const fetchEmployees = async () => {
  const params = {};
  if (statusFilter.value !== "all") {
    params.is_active = statusFilter.value === "active";
  }
  const res = await api.get("/employees", { params });
  employees.value = res.data;
};

const fetchPositions = async () => {
  const res = await api.get("/positions");
  positions.value = res.data;
};

const fetchEmploymentTypes = async () => {
  const res = await api.get("/employment-types");
  employmentTypes.value = res.data;
};

watch(statusFilter, fetchEmployees);

const filteredEmployees = computed(() => {
  if (statusFilter.value === "all") return employees.value;
  return employees.value.filter((e) =>
    statusFilter.value === "active" ? e.is_active : !e.is_active
  );
});

const openAddModal = () => {
  isEditing.value = false;
  selectedEmployee.value = null;
  showEditModal.value = true;
};

const openEditModal = (emp) => {
  isEditing.value = true;
  selectedEmployee.value = {
    ...emp,
    position_id: emp.position?.id,
    employment_type_id: emp.employment_type?.id,
  };
  showEditModal.value = true;
  openDropdownId.value = null;
};

const handleSave = async (data) => {
  if (isEditing.value) {
    await api.put(`/employees/${selectedEmployee.value.id}`, data);
  } else {
    await api.post("/employees", data);
  }
  showEditModal.value = false;
  fetchEmployees();
};

const toggleStatus = async (emp) => {
  await api.post(`/employees/${emp.id}/toggle-status`);
  fetchEmployees();
};

const toggleDropdown = (id) => {
  openDropdownId.value = openDropdownId.value === id ? null : id;
};

onMounted(() => {
  fetchEmployees();
  fetchPositions();
  fetchEmploymentTypes();
});
</script>
