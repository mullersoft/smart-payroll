<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl relative transition-colors duration-300"
    >
      <button
        @click="$emit('close')"
        class="absolute top-2 right-2 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100"
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
            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          />
        </div>

        <!-- Owner Name -->
        <div>
          <label
            class="block text-sm font-medium text-gray-700 dark:text-gray-300"
          >
            Owner Name (select employee or type manually)
          </label>
          <input
            list="employeeList"
            v-model="newAccount.owner_name"
            type="text"
            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
            placeholder="Start typing to search employee"
          />
          <datalist id="employeeList">
            <option
              v-for="emp in employees"
              :key="emp.id"
              :value="emp.full_name"
            ></option>
          </datalist>
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
            class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
          />
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-3">
          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2 text-gray-600 dark:text-gray-300"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md font-semibold"
          >
            {{ isEditMode ? "Save Changes" : "Add Account" }}
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
});

defineEmits(["close", "save"]);
</script>
