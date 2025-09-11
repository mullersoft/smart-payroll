<template>
  <div class="space-y-6">
    <div
      class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
    >
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
        Employees Management
      </h1>

      <div
        class="flex flex-col sm:flex-row items-end sm:items-center gap-4 w-full sm:w-auto"
      >
        <!-- Status Filter -->
        <div class="w-full sm:w-40">

          <select
            v-model="statusFilter"
            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none dark:bg-gray-700 dark:text-gray-200"
          >
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="all">All</option>
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
        <!-- Loading -->
      <div v-if="loading" class="text-center text-gray-700 dark:text-gray-300">
⏳ Loading employees, please wait...      </div>
    <!-- Table -->
    <div v-else class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
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
    @click="openViewModal(emp)"
    class="block w-full text-left px-4 py-2 hover:bg-gray-500"
  >
    👁 View
  </button>
  <button
    @click="openEditModal(emp)"
    class="block w-full text-left px-4 py-2 hover:bg-gray-500"
  >
    ✏ Edit
  </button>
  <button
    @click="toggleStatus(emp)"
    class="block w-full text-left px-4 py-2 hover:bg-gray-500"
  >
    {{ emp.is_active ? "Deactivate" : "Activate" }}
  </button>
</div>

                  <!-- <div
                    v-if="openDropdownId === emp.id"
                    class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10"
                  >
                    <button
                      @click="openEditModal(emp)"
                      class="block w-full text-left px-4 py-2 hover:bg-gray-500"
                    >
                      ✏ Edit
                    </button>
                    <button
                      @click="toggleStatus(emp)"
                      class="block w-full text-left px-4 py-2 hover:bg-gray-500"
                    >
                      {{ emp.is_active ? "Deactivate" : "Activate" }}
                    </button>
                  </div> -->
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
  :allowances="allowances"
  @save="handleSave"
  @close="closeEditModal"
/>
<!--  to render the view modal -->
<EmployeeViewModal
  :show="showViewModal"
  :employee="selectedViewEmployee"
  @close="showViewModal = false"
/>
  </div>
</template>

<script setup>
import api from "@/services/api";
import { computed, onMounted, ref, watch } from "vue";
import EmployeeEditModal from "./EmployeeEditModal.vue";
import EmployeeViewModal from "./EmployeeViewModal.vue";


import { useToast } from "vue-toastification";
const showViewModal = ref(false);
const selectedViewEmployee = ref(null);

const openViewModal = (emp) => {
  selectedViewEmployee.value = {
    ...emp,
    position: emp.position?.name,
    employment_type: emp.employment_type?.name,
  };
  showViewModal.value = true;
  openDropdownId.value = null; // close dropdown
};

const toast = useToast();
const allowances = ref([]);
const loading = ref(false);


const fetchAllowances = async () => {
  try {
    const res = await api.get("/allowances");
    allowances.value = res.data;
  } catch (error) {
    console.error("Failed to fetch allowances:", error);
    toast.error("Failed to load allowances data.");
  }
};



const employees = ref([]);
const positions = ref([]);
const employmentTypes = ref([]);
const statusFilter = ref("all");
const showEditModal = ref(false);
const isEditing = ref(false);
const selectedEmployee = ref(null);
const openDropdownId = ref(null);


const fetchEmployees = async () => {
  loading.value = true;
  try {
    loading.value = true;
    const params = {};
    if (statusFilter.value !== "all") {
      params.is_active = statusFilter.value === "active";
    }
    const res = await api.get("/employees", { params });
    employees.value = res.data;
  } catch (error) {
    console.error("Failed to fetch employees:", error);
    toast.error("Failed to load employees. Please try again.");
  } finally {
    loading.value = false;
  }
};




const fetchPositions = async () => {
  try {
    const res = await api.get("/positions");
    positions.value = res.data;
  } catch (error) {
    console.error("Failed to fetch positions:", error);
    toast.error("Failed to load positions data.");
  }
};




const fetchEmploymentTypes = async () => {
  try {
    const res = await api.get("/employment-types");
    employmentTypes.value = res.data;
  } catch (error) {
    console.error("Failed to fetch employment types:", error);
    toast.error("Failed to load employment types data.");
  }
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
    allowances: emp.allowances ? emp.allowances.map((a) => a.id) : [], // ✅ added
  };
  showEditModal.value = true;
  openDropdownId.value = null;
};

const closeEditModal = () => {
  showEditModal.value = false;
  // Reset loading state when modal is closed
  loading.value = false;
};


const handleSave = async (data, resetLoading) => {
  try {
    if (isEditing.value) {
      await api.put(`/employees/${selectedEmployee.value.id}`, data);
      toast.success("Employee updated successfully!");
    } else {
      await api.post("/employees", data);
      toast.success("New employee added successfully!");
    }
    showEditModal.value = false;
    fetchEmployees(); // Re-fetch data after a successful save
  } catch (error) {
    console.error("Error saving employee", error);
    // Reset loading state through the callback
    if (resetLoading) resetLoading();

    if (error.response?.data?.errors) {
      const errs = error.response.data.errors;
      Object.values(errs).forEach((msgArr) => {
        toast.error(msgArr[0]); // Show each validation error in a toast
      });
    } else {
      toast.error("Failed to save employee."); // Fallback error message
    }
  }
};




const toggleStatus = async (emp) => {
  try {
    loading.value = true;
    await api.post(`/employees/${emp.id}/toggle-status`);
    toast.success(`Employee ${emp.is_active ? 'deactivated' : 'activated'} successfully!`);
    fetchEmployees();
  } catch (error) {
    console.error("Error toggling employee status", error);
    toast.error("Failed to update employee status.");
  } finally {
    loading.value = false;
    openDropdownId.value = null;
  }
};


const toggleDropdown = (id) => {
  openDropdownId.value = openDropdownId.value === id ? null : id;
};

onMounted(() => {
  fetchEmployees();
  fetchPositions();
  fetchEmploymentTypes();
  fetchAllowances();
});

</script>
