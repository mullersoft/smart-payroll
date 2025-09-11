<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div
      class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl dark:bg-gray-800 dark:text-gray-200"
    >
      <h2 class="text-xl font-semibold mb-4">
        {{ isEditing ? "Edit User" : "Create New User" }}
      </h2>
      <form @submit.prevent="handleSubmit">
        <!-- Full Name -->
        <div class="mb-4">
          <label class="block text-sm font-medium">Full Name</label>
          <input
            v-model="localForm.name"
            type="text"
            required
            :disabled="loading"
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 disabled:opacity-50"
          />
        </div>

        <!-- Email -->
        <div class="mb-4">
          <label class="block text-sm font-medium">Email</label>
          <input
            v-model="localForm.email"
            type="email"
            required
            :disabled="loading"
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 disabled:opacity-50"
          />
        </div>

        <!-- Password (only for new users) -->
        <!-- <div class="mb-4" v-if="!isEditing">
          <label class="block text-sm font-medium">Password</label>
          <input
            v-model="localForm.password"
            type="password"
            required
            :disabled="loading"
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 disabled:opacity-50"
          />
        </div> -->

        <!-- Role -->
        <div class="mb-4">
          <label class="block text-sm font-medium">Role</label>
          <select
            v-model="localForm.role"
            :disabled="loading"
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 disabled:opacity-50"
          >
            <option disabled value="">-- Select Role --</option>
            <option value="preparer">Preparer</option>
            <option value="approver">Approver</option>
            <option value="admin">Admin</option>
            <option value="pending">Pending</option>
          </select>
        </div>

        <!-- Status -->
        <div class="mb-4">
          <label class="block text-sm font-medium">Status</label>
          <select
            v-model="localForm.status"
            :disabled="loading"
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600 disabled:opacity-50"
          >
            <option value="pending">Pending</option>
            <option value="active">Active</option>
            <option value="deactivated">Deactivated</option>
          </select>
        </div>

        <!-- Buttons -->
        <div class="flex justify-end space-x-3">
          <button
            @click="$emit('close')"
            type="button"
            :disabled="loading"
            class="text-gray-600 dark:text-gray-300 disabled:opacity-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 disabled:opacity-50 flex items-center gap-2"
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
            {{ loading ? "Processing..." : (isEditing ? "Update" : "Create") }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from "vue";

const props = defineProps({
  show: Boolean,
  isEditing: Boolean,
  form: Object,
  loading: Boolean
});
const emit = defineEmits(["close", "submit"]);

const localForm = reactive({ ...props.form });

// keep local form synced with parent props
watch(
  () => props.form,
  (newForm) => {
    Object.assign(localForm, newForm);
  },
  { deep: true }
);

const handleSubmit = () => {
  const payload = {
    name: localForm.name,
    email: localForm.email,
    role: localForm.role,
    status: localForm.status,
  };

  // Only include password for new users
  // if (!props.isEditing) {
  //   payload.password = localForm.password;
  // }

  emit("submit", payload);
};
</script>
