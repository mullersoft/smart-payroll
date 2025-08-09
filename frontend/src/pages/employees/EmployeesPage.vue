<template>
  <MainLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
          👥 Employees
        </h1>
        <button
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded"
          @click="openAddModal"
        >
          ➕ Add New Employee
        </button>
      </div>

      <!-- Employee Table -->
      <div
        class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg transition-colors duration-300"
      >
        <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
          <thead
            class="bg-gray-100 dark:bg-gray-700 text-left text-gray-600 dark:text-gray-300"
          >
            <tr>
              <th class="py-3 px-4">#</th>
              <th class="py-3 px-4">Full Name</th>
              <th class="py-3 px-4">Emp. ID</th>
              <th class="py-3 px-4">Position</th>
              <th class="py-3 px-4">Type</th>
              <th class="py-3 px-4">Salary</th>
              <th class="py-3 px-4">Gender</th>
              <th class="py-3 px-4">Employment Date</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(emp, index) in employees"
              :key="emp.id"
              class="border-b border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="py-2 px-4">{{ index + 1 }}</td>
              <td class="py-2 px-4">{{ emp.full_name }}</td>
              <td class="py-2 px-4">{{ emp.employee_id }}</td>
              <td class="py-2 px-4">{{ emp.position }}</td>
              <td class="py-2 px-4">{{ emp.employment_type }}</td>
              <td class="py-2 px-4">{{ emp.base_salary }}</td>
              <td class="py-2 px-4">{{ emp.gender }}</td>
              <td class="py-2 px-4">{{ emp.employment_date }}</td>
              <td class="py-2 px-4">
                <span
                  :class="emp.is_active ? 'text-green-600' : 'text-red-600'"
                >
                  {{ emp.is_active ? "Active" : "Inactive" }}
                </span>
              </td>
              <td class="py-2 px-4 space-x-2">
                <button
                  class="text-blue-600 hover:underline"
                  @click="openEditModal(emp)"
                >
                  Edit
                </button>
                <button
                  class="text-yellow-600 hover:underline"
                  @click="toggleStatus(emp)"
                >
                  {{ emp.is_active ? "Deactivate" : "Activate" }}
                </button>
              </td>
            </tr>
            <tr v-if="employees.length === 0">
              <td
                colspan="10"
                class="py-4 px-4 text-center text-gray-500 dark:text-gray-400"
              >
                No employees found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add/Edit Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl shadow-xl overflow-y-auto max-h-[90vh] transition-colors duration-300"
        >
          <h2
            class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
          >
            {{ isEditing ? "Edit Employee" : "Add New Employee" }}
          </h2>
          <form @submit.prevent="handleSubmit">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                  >Full Name</label
                >
                <input
                  v-model="form.full_name"
                  required
                  class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </div>
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                  >Employee ID</label
                >
                <input
                  v-model="form.employee_id"
                  required
                  class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </div>
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                  >Position</label
                >
                <select
                  v-model="form.position"
                  required
                  class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                >
                  <option>CEO</option>
                  <option>COO</option>
                  <option>CTO</option>
                  <option>CISO</option>
                  <option>Director</option>
                  <option>Dept Lead</option>
                  <option>Normal Employee</option>
                </select>
              </div>
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                  >Employment Type</label
                >
                <select
                  v-model="form.employment_type"
                  required
                  class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                >
                  <option>permanent</option>
                  <option>contract</option>
                </select>
              </div>
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                  >Base Salary</label
                >
                <input
                  v-model="form.base_salary"
                  type="number"
                  step="0.01"
                  required
                  class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </div>
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                  >Gender</label
                >
                <select
                  v-model="form.gender"
                  required
                  class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                >
                  <option>Male</option>
                  <option>Female</option>
                  <option>Other</option>
                </select>
              </div>
              <div>
                <label
                  class="block text-sm font-medium text-gray-700 dark:text-gray-300"
                  >Employment Date</label
                >
                <input
                  v-model="form.employment_date"
                  type="date"
                  required
                  class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </div>
            </div>

            <div class="flex justify-end space-x-3 mt-4">
              <button
                type="button"
                @click="showModal = false"
                class="text-gray-600 dark:text-gray-300"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded"
              >
                {{ isEditing ? "Update" : "Create" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";

const employees = ref([]);
const showModal = ref(false);
const isEditing = ref(false);
const editId = ref(null);

const form = ref({
  full_name: "",
  employee_id: "",
  position: "Normal Employee",
  employment_type: "permanent",
  base_salary: "",
  gender: "Male",
  employment_date: "",
});

const fetchEmployees = async () => {
  try {
    const res = await api.get("/employees");
    employees.value = res.data;
  } catch (err) {
    console.error("Failed to fetch employees:", err);
  }
};

const openAddModal = () => {
  isEditing.value = false;
  editId.value = null;
  form.value = {
    full_name: "",
    employee_id: "",
    position: "Normal Employee",
    employment_type: "permanent",
    base_salary: "",
    gender: "Male",
    employment_date: "",
  };
  showModal.value = true;
};

const openEditModal = (emp) => {
  isEditing.value = true;
  editId.value = emp.id;
  form.value = { ...emp };
  showModal.value = true;
};

const handleSubmit = async () => {
  try {
    if (isEditing.value) {
      await api.put(`/employees/${editId.value}`, form.value);
    } else {
      await api.post("/employees", form.value);
    }
    showModal.value = false;
    fetchEmployees();
  } catch (err) {
    console.error("Save failed:", err);
  }
};

const toggleStatus = async (emp) => {
  try {
    await api.post(`/employees/${emp.id}/toggle-status`);
    fetchEmployees();
  } catch (err) {
    console.error("Toggle status failed:", err);
  }
};

onMounted(fetchEmployees);
</script>
