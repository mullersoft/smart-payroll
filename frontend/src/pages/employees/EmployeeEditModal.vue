<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white dark:bg-gray-900 rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col"
    >
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
          {{ isEditing ? "Edit Employee" : "Add Employee" }}
        </h2>
      </div>

      <!-- Scrollable Content -->
      <div class="px-6 py-4 overflow-y-auto flex-1 space-y-4">
        <!-- Full Name -->
        <div>
          <label
            class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Full Name
          </label>
          <input
            v-model="form.full_name"
            class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500"
            placeholder="Enter full name"
            required
          />
        </div>

        <!-- Employee ID -->
        <div>
          <label
            class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Employee ID
          </label>
          <input
            v-model="form.employee_id"
            class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500"
            placeholder="Enter employee ID"
            required
          />
        </div>

        <!-- Position -->
        <div>
          <label
            class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Position
          </label>
          <select
            v-model="form.position_id"
            class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500"
            required
          >
            <option disabled value="">Select Position</option>
            <option v-for="p in positions" :key="p.id" :value="p.id">
              {{ p.name }}
            </option>
          </select>
        </div>

        <!-- Employment Type -->
        <div>
          <label
            class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Employment Type
          </label>
          <select
            v-model="form.employment_type_id"
            class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500"
            required
          >
            <option disabled value="">Select Type</option>
            <option v-for="t in employmentTypes" :key="t.id" :value="t.id">
              {{ t.name }}
            </option>
          </select>
        </div>

        <!-- Base Salary -->
        <div>
          <label
            class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Base Salary
          </label>
          <input
            type="number"
            step="0.01"
            v-model="form.base_salary"
            class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500"
            placeholder="Enter base salary"
            required
          />
        </div>

        <!-- Gender -->
        <div>
          <label
            class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Gender
          </label>
          <select
            v-model="form.gender"
            class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500"
            required
          >
            <option value="Male">Male</option>
            <option value="Female">Female</option>
          </select>
        </div>

        <!-- Employment Date -->
        <div>
          <label
            class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Employment Date
          </label>
          <input
            type="date"
            v-model="form.employment_date"
            class="w-full border border-gray-300 dark:border-gray-700 rounded px-3 py-2 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500"
            required
          />
        </div>
      </div>

      <!-- Footer (Fixed Buttons) -->
      <div
        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3"
      >
        <button
          type="button"
          @click="$emit('close')"
          class="px-4 py-2 bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200 rounded hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
        >
          Cancel
        </button>
        <button
          type="submit"
          @click="save"
          class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded transition-colors"
        >
          Save
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from "vue";

const props = defineProps({
  show: Boolean,
  isEditing: Boolean,
  employee: Object,
  positions: Array,
  employmentTypes: Array,
});
const emit = defineEmits(["close", "save"]);

const form = reactive({
  full_name: "",
  employee_id: "",
  position_id: "",
  employment_type_id: "",
  base_salary: "",
  gender: "Male",
  employment_date: "",
});

watch(
  () => props.employee,
  (emp) => {
    if (emp) Object.assign(form, emp);
    else {
      form.full_name = "";
      form.employee_id = "";
      form.position_id = "";
      form.employment_type_id = "";
      form.base_salary = "";
      form.gender = "Male";
      form.employment_date = "";
    }
  },
  { immediate: true }
);

const save = () => {
  emit("save", { ...form });
};
</script>
