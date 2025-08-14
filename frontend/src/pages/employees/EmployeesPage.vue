<template>
  <MainLayout>
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
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-gray-200"
            >
              <option value="active">Active Employees</option>
              <option value="inactive">Inactive Employees</option>
              <option value="all">All Employees</option>
            </select>
          </div>

          <button
            class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md shadow-sm flex items-center justify-center gap-2"
            @click="openAddModal"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fill-rule="evenodd"
                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                clip-rule="evenodd"
              />
            </svg>
            Add Employee
          </button>
        </div>
      </div>

      <!-- Employee Table -->
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table
            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
          >
            <thead class="bg-gray-50 dark:bg-gray-700">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  #
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Full Name
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Position
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Type
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Salary
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Status
                </th>
                <th
                  scope="col"
                  class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider"
                >
                  Actions
                </th>
              </tr>
            </thead>
            <tbody
              class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700"
            >
              <tr
                v-for="(emp, index) in filteredEmployees"
                :key="emp.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
              >
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"
                >
                  {{ index + 1 }}
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200"
                >
                  {{ emp.full_name }}
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"
                >
                  {{ emp.position }}
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 capitalize"
                >
                  {{ emp.employment_type }}
                </td>
                <td
                  class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200"
                >
                  Birr {{ Number(emp.base_salary).toLocaleString() }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                  <span
                    :class="{
                      'px-2 py-1 rounded-full text-xs font-semibold': true,
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200':
                        emp.is_active,
                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200':
                        !emp.is_active,
                    }"
                  >
                    {{ emp.is_active ? "Active" : "Inactive" }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="relative inline-block text-left">
                    <button
                      @click.stop="toggleDropdown(emp.id)"
                      class="inline-flex items-center px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none"
                    >
                      Actions
                      <svg
                        class="-mr-1 ml-2 h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </button>

                    <transition
                      enter-active-class="transition ease-out duration-100"
                      enter-from-class="transform opacity-0 scale-95"
                      enter-to-class="transform opacity-100 scale-100"
                      leave-active-class="transition ease-in duration-75"
                      leave-from-class="transform opacity-100 scale-100"
                      leave-to-class="transform opacity-0 scale-95"
                    >
                      <div
                        v-if="openDropdownId === emp.id"
                        class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                      >
                        <div class="py-1" role="none">
                          <button
                            @click="openViewModal(emp)"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                          >
                            <span class="flex items-center">
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                />
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                />
                              </svg>
                              View Details
                            </span>
                          </button>
                          <button
                            @click="openEditModal(emp)"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                          >
                            <span class="flex items-center">
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                />
                              </svg>
                              Edit
                            </span>
                          </button>
                          <button
                            @click="toggleStatus(emp)"
                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                          >
                            <span class="flex items-center">
                              <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                              >
                                <path
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M5 13l4 4L19 7"
                                />
                              </svg>
                              {{ emp.is_active ? "Deactivate" : "Activate" }}
                            </span>
                          </button>
                        </div>
                      </div>
                    </transition>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredEmployees.length === 0">
                <td
                  colspan="7"
                  class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                >
                  No employees found matching your criteria.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination would go here if implemented -->

      <!-- Modals -->
      <EmployeeEditModal
        :show="showEditModal"
        :is-editing="isEditing"
        :employee="selectedEmployee"
        @save="handleSave"
        @close="showEditModal = false"
      />

      <EmployeeViewModal
        :show="showViewModal"
        :employee="selectedEmployee"
        @close="showViewModal = false"
      />
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";
import EmployeeEditModal from "./EmployeeEditModal.vue";
import EmployeeViewModal from "./EmployeeViewModal.vue";

const employees = ref([]);
const statusFilter = ref("active");
const showEditModal = ref(false);
const showViewModal = ref(false);
const isEditing = ref(false);
const selectedEmployee = ref(null);
const openDropdownId = ref(null);

// Fetch employees with optional status filter
const fetchEmployees = async () => {
  try {
    const params = {};
    if (statusFilter.value !== "all") {
      params.is_active = statusFilter.value === "active";
    }

    const res = await api.get("/employees", { params });
    employees.value = res.data;
  } catch (err) {
    console.error("Failed to fetch employees:", err);
  }
};

// Computed property for filtered employees (client-side fallback)
const filteredEmployees = computed(() => {
  if (statusFilter.value === "all") return employees.value;
  return employees.value.filter((emp) =>
    statusFilter.value === "active" ? emp.is_active : !emp.is_active
  );
});

// Watch for filter changes
watch(statusFilter, () => {
  fetchEmployees();
});

const openAddModal = () => {
  isEditing.value = false;
  selectedEmployee.value = null;
  showEditModal.value = true;
};

const openViewModal = (emp) => {
  selectedEmployee.value = { ...emp };
  showViewModal.value = true;
  openDropdownId.value = null;
};

const openEditModal = (emp) => {
  isEditing.value = true;
  selectedEmployee.value = { ...emp };
  showEditModal.value = true;
  openDropdownId.value = null;
};

const handleSave = async (employeeData) => {
  try {
    if (isEditing.value) {
      await api.put(`/employees/${selectedEmployee.value.id}`, employeeData);
    } else {
      await api.post("/employees", employeeData);
    }
    showEditModal.value = false;
    fetchEmployees();
  } catch (err) {
    console.error("Save failed:", err);
  }
};

const toggleStatus = async (emp) => {
  try {
    await api.post(`/employees/${emp.id}/toggle-status`);
    fetchEmployees();
    openDropdownId.value = null;
  } catch (err) {
    console.error("Toggle status failed:", err);
  }
};

const toggleDropdown = (employeeId) => {
  openDropdownId.value =
    openDropdownId.value === employeeId ? null : employeeId;
};

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest(".relative.inline-block.text-left")) {
    openDropdownId.value = null;
  }
};

onMounted(() => {
  fetchEmployees();
  document.addEventListener("click", handleClickOutside);
});
</script>
