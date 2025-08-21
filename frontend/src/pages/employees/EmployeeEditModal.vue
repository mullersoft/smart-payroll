<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    @click.self="$emit('close')"
  >
    <div
      class="bg-white dark:bg-gray-900 rounded-xl shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col"
    >
      <!-- Header -->
      <div
        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 sticky top-0 bg-white dark:bg-gray-900 z-10"
      >
        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
          {{ isEditing ? "Edit Employee" : "Add New Employee" }}
        </h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
          Fill in the employee details below
        </p>
      </div>

      <!-- Scrollable Content -->
      <div class="px-6 py-4 overflow-y-auto flex-1 space-y-6">
        <!-- Personal Information Section -->
        <div>
          <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
            👤 Personal Information
          </h3>
          <div class="space-y-4">
            <!-- Full Name -->
            <div>
              <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Full Name *
              </label>
              <input
                v-model="form.full_name"
                class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-3 
                      bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                      placeholder-gray-400 dark:placeholder-gray-500
                      focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                      transition-colors duration-200"
                placeholder="Enter employee's full name"
                required
                autofocus
              />
            </div>

            <!-- Email -->
            <div>
              <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Email Address
              </label>
              <input
                type="email"
                v-model="form.email"
                class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-3 
                      bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                      placeholder-gray-400 dark:placeholder-gray-500
                      focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                      transition-colors duration-200"
                placeholder="employee@company.com"
              />
            </div>

            <!-- Gender -->
            <div>
              <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Gender *
              </label>
              <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center p-3 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                      :class="{'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': form.gender === 'Male'}">
                  <input type="radio" v-model="form.gender" value="Male" class="mr-2 text-indigo-600">
                  <span class="text-gray-700 dark:text-gray-300">Male</span>
                </label>
                <label class="flex items-center p-3 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                      :class="{'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20': form.gender === 'Female'}">
                  <input type="radio" v-model="form.gender" value="Female" class="mr-2 text-indigo-600">
                  <span class="text-gray-700 dark:text-gray-300">Female</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <!-- Employment Details Section -->
        <div>
          <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
            💼 Employment Details
          </h3>
          <div class="space-y-4">
            <!-- Employment Date -->
            <div>
              <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Employment Date *
              </label>
              <input
                type="date"
                v-model="form.employment_date"
                class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-3 
                      bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                      focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                      transition-colors duration-200"
                required
              />
            </div>

            <!-- Position -->
            <div>
              <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Position *
              </label>
              <select
                v-model="form.position_id"
                class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-3 
                      bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                      focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                      transition-colors duration-200"
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
              <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Employment Type *
              </label>
              <select
                v-model="form.employment_type_id"
                class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-3 
                      bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                      focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                      transition-colors duration-200"
                required
              >
                <option disabled value="">Select Type</option>
                <option v-for="t in employmentTypes" :key="t.id" :value="t.id">
                  {{ t.name }}
                </option>
              </select>
            </div>
          </div>
        </div>

        <!-- Compensation Section -->
        <div>
          <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
            💰 Compensation & Benefits
          </h3>
          <div class="space-y-4">
            <!-- Base Salary -->
            <div>
              <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Base Salary (Birr) *
              </label>
              <div class="relative">
                <span class="absolute left-3 top-3 text-gray-500 dark:text-gray-400">Birr</span>
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  v-model="form.base_salary"
                  class="w-full border border-gray-300 dark:border-gray-700 rounded-lg pl-12 pr-4 py-3 
                        bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                        placeholder-gray-400 dark:placeholder-gray-500
                        focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                        transition-colors duration-200"
                  placeholder="0.00"
                  required
                />
            </div>
            </div>

            <!-- Allowances -->
            <div class="relative">
              <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                Allowances
              </label>
              <div
                @click="toggleAllowanceDropdown"
                class="w-full border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-3 
                      bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                      cursor-pointer flex justify-between items-center hover:border-gray-400 dark:hover:border-gray-600
                      transition-colors duration-200"
                :class="{'border-indigo-500 ring-2 ring-indigo-500/20': allowanceDropdownOpen}"
              >
                <span class="truncate">
                  {{ selectedAllowancesText || 'Select allowances (optional)' }}
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 transition-transform duration-200"
                     :class="{'rotate-180': allowanceDropdownOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>

              <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 translate-y-1"
              >
                <div
                  v-if="allowanceDropdownOpen"
                  class="absolute mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg shadow-lg max-h-48 overflow-y-auto z-20"
                >
                  <div
                    v-for="a in allowances"
                    :key="a.id"
                    class="px-4 py-3 flex items-center hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors"
                  >
                    <input 
                      type="checkbox" 
                      :id="'allowance-' + a.id" 
                      :value="a.id" 
                      v-model="form.allowances" 
                      class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    />
                    <label :for="'allowance-' + a.id" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                      {{ a.name }}
                    </label>
                  </div>
                </div>
              </transition>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3 sticky bottom-0 bg-white dark:bg-gray-900 z-10"
      >
        <button 
          type="button" 
          @click="$emit('close')"
          class="px-6 py-2.5 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-lg 
                hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200
                font-medium"
        >
          Cancel
        </button>
        <button 
          type="submit" 
          @click="save"
          :disabled="!isFormValid"
          class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg 
                hover:bg-indigo-700 disabled:bg-indigo-400 disabled:cursor-not-allowed
                transition-colors duration-200 font-medium
                flex items-center gap-2"
        >
          <svg v-if="isEditing" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          {{ isEditing ? 'Update Employee' : 'Create Employee' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, ref, computed, onMounted, onUnmounted } from "vue";

const props = defineProps({
  show: Boolean,
  isEditing: Boolean,
  employee: Object,
  positions: Array,
  employmentTypes: Array,
  allowances: Array,
});
const emit = defineEmits(["close", "save"]);

const allowanceDropdownOpen = ref(false);

// Set today's date as default
const today = new Date().toISOString().slice(0, 10);

// Default values
const getDefaultType = (types) =>
  types?.find((t) => t.name.toLowerCase().includes("permanent"))?.id || "";
const getDefaultPosition = (positions) =>
  positions?.find((p) => p.name.toLowerCase().includes("employee"))?.id || "";

const form = reactive({
  full_name: "",
  email: "",
  position_id: "",
  employment_type_id: "",
  base_salary: "",
  gender: "Male",
  employment_date: today,
  allowances: [],
});

// Reset form function - MOVED BEFORE WATCH
const resetForm = () => {
  form.full_name = "";
  form.email = "";
  form.position_id = getDefaultPosition(props.positions);
  form.employment_type_id = getDefaultType(props.employmentTypes);
  form.base_salary = "";
  form.gender = "Male";
  form.employment_date = today;
  form.allowances = [];
};

// Computed properties
const selectedAllowancesText = computed(() => {
  if (!form.allowances.length) return '';
  return props.allowances
    .filter(a => form.allowances.includes(a.id))
    .map(a => a.name)
    .join(', ');
});

const isFormValid = computed(() => {
  return form.full_name.trim() && 
         form.position_id && 
         form.employment_type_id && 
         form.base_salary > 0 &&
         form.employment_date;
});

// Toggle dropdown with better UX
const toggleAllowanceDropdown = () => {
  allowanceDropdownOpen.value = !allowanceDropdownOpen.value;
};

// Close dropdown when clicking outside
const handleClickOutside = (e) => {
  if (!e.target.closest(".relative")) {
    allowanceDropdownOpen.value = false;
  }
};

// Watch for employee prop changes
watch(
  () => props.employee,
  (emp) => {
    if (emp) {
      Object.assign(form, emp);
      form.allowances = emp.allowances ? emp.allowances.map((a) => a.id) : [];
    } else {
      resetForm();
    }
  },
  { immediate: true }
);

// Watch for dropdown data availability
watch(
  () => [props.positions, props.employmentTypes, props.allowances],
  ([positions, employmentTypes, allowances]) => {
    if (!form.position_id && positions?.length) {
      form.position_id = getDefaultPosition(positions);
    }
    if (!form.employment_type_id && employmentTypes?.length) {
      form.employment_type_id = getDefaultType(employmentTypes);
    }
  },
  { immediate: true }
);

// Save with validation
const save = () => {
  if (!isFormValid.value) return;
  emit("save", { ...form });
};

// Event listeners
onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
}
</style>