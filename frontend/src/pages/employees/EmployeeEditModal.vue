<template>
  <!-- Employee Add/Edit Modal Component -->
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl shadow-xl overflow-y-auto max-h-[90vh] transition-colors duration-300"
    >
      <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
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
              v-model="employeeForm.full_name"
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
              v-model="employeeForm.employee_id"
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
              v-model="employeeForm.position"
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
              v-model="employeeForm.employment_type"
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
              v-model="employeeForm.base_salary"
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
              v-model="employeeForm.gender"
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
              v-model="employeeForm.employment_date"
              type="date"
              required
              class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
            />
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-4">
          <button
            type="button"
            @click="$emit('close')"
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
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from "vue";

const props = defineProps({
  show: Boolean,
  isEditing: Boolean,
  employee: Object,
});

const emits = defineEmits(["save", "close"]);

const employeeForm = ref({});

// Watch for changes in the employee prop to update the form
watch(
  () => props.employee,
  (newVal) => {
    if (newVal) {
      employeeForm.value = { ...newVal };
    } else {
      employeeForm.value = {
        full_name: "",
        employee_id: "",
        position: "Normal Employee",
        employment_type: "permanent",
        base_salary: "",
        gender: "Male",
        employment_date: "",
      };
    }
  },
  { immediate: true }
);

const handleSubmit = () => {
  emits("save", employeeForm.value);
};
</script>
