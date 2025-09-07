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
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600"
          />
        </div>

        <!-- Email -->
        <div class="mb-4">
          <label class="block text-sm font-medium">Email</label>
          <input
            v-model="localForm.email"
            type="email"
            required
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600"
          />
        </div>

        <!-- Password (only for new users) -->
        <!-- <div class="mb-4" v-if="!isEditing">
          <label class="block text-sm font-medium">Password</label>
          <input
            v-model="localForm.password"
            type="password"
            required
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600"
          />
        </div> -->

        <!-- Role -->
        <div class="mb-4">
          <label class="block text-sm font-medium">Role</label>
          <select
            v-model="localForm.role"
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600"
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
            class="w-full border rounded-lg px-3 py-2 dark:bg-gray-700 dark:border-gray-600"
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
            class="text-gray-600 dark:text-gray-300"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800"
          >
            {{ isEditing ? "Update" : "Create" }}
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
  emit("submit", payload);
};

</script>
