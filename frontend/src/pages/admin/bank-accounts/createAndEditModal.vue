<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl relative transition-colors duration-300"
    >
      <button
        @click="$emit('close')"
        :disabled="loading"
        class="absolute top-2 right-2 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 disabled:opacity-50"
      >
        ✖
      </button>
      <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
        {{ isEditMode ? "Edit Bank Account" : "Add Bank Account" }}
      </h2>

      <form @submit.prevent="$emit('save')" class="space-y-4">
        <!-- Bank Name -->
        <div>
          <label
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Bank Name
          </label>
          <input
            v-model="newAccount.bank_name"
            type="text"
            required
            :disabled="loading"
            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-50"
          />
        </div>

        <!-- Employee Selection -->
        <div>
          <label
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Select Employee
          </label>
          <select
            v-model="newAccount.employee_id"
            :disabled="loading"
            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-50"
          >
            <option :value="null">-- Select Employee --</option>
            <option
              v-for="emp in employees"
              :key="emp.id"
              :value="emp.id"
            >
              {{ emp.full_name }} ({{ emp.email }})
            </option>
          </select>
        </div>

        <!-- Manual Owner Name (only show if no employee selected) -->
        <div v-if="!newAccount.employee_id">
          <label
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Manual Owner Name
          </label>
          <input
            v-model="newAccount.owner_name"
            type="text"
            :disabled="loading"
            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-50"
            placeholder="Enter owner name manually"
          />
        </div>

        <!-- Account Number -->
        <div>
          <label
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Account Number
          </label>
          <input
            v-model="newAccount.account_number"
            type="text"
            required
            :disabled="loading"
            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white disabled:opacity-50"
          />
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="$emit('close')"
            :disabled="loading"
            class="px-4 py-2 text-gray-600 dark:text-gray-300 disabled:opacity-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-semibold disabled:opacity-50 flex items-center gap-2"
          >
            <svg
              v-if="loading"
              class="animate-spin h-5 w-5 text-white"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
              ></path>
            </svg>
            {{ loading ? "Processing..." : (isEditMode ? "Save Changes" : "Add Account") }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isEditMode: Boolean,
  editingAccountId: Number,
  newAccount: Object,
  employees: Array,
  loading: Boolean
});

defineEmits(["close", "save"]);
</script>
