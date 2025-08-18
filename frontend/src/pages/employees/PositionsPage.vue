<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">
         Positions
      </h2>
      <button
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded"
        @click="openAddModal"
      >
        Add Position
      </button>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">#</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">
              Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">
              Description
            </th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase">
              Allowance
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">
              Actions
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(pos, i) in positions"
            :key="pos.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <td class="px-6 py-4">{{ i + 1 }}</td>
            <td class="px-6 py-4">{{ pos.name }}</td>
            <td class="px-6 py-4">{{ pos.description }}</td>
            <td class="px-6 py-4">{{ pos.allowance }}</td>

            <td class="px-6 py-4">{{ pos.all }}</td>

            <td class="px-6 py-4 space-x-2">
              <button
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm"
                @click="openEditModal(pos)"
              >
                Edit
              </button>
              <button
                class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm"
                @click="confirmDelete(pos)"
              >
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="positions.length === 0">
            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
              No positions found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
    >
      <div
        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md"
      >
        <h2 class="text-lg font-semibold mb-4">
          {{ isEditing ? "Edit Position" : "Add Position" }}
        </h2>
        <input
          v-model="form.name"
          type="text"
          placeholder="Position Name"
          class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700"
        />
        <input
          v-model="form.description"
          type="text"
          placeholder="Description"
          class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700"
        />
         <input
          v-model="form.allowance"
          type="number"
          placeholder="Allowance"
          class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700"
        />
        <div class="flex justify-end space-x-2">
          <button @click="closeModal" class="px-4 py-2">Cancel</button>
          <button
            @click="savePosition"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded"
          >
            Save
          </button>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation -->
    <div
      v-if="showDeleteModal"
      class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50"
    >
      <div
        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md"
      >
        <h2 class="text-lg font-semibold mb-4">Delete Position</h2>
        <p>
          Are you sure you want to delete
          <strong>{{ selectedPosition?.name }}</strong
          >?
        </p>
        <div class="flex justify-end space-x-2 mt-4">
          <button @click="showDeleteModal = false" class="px-4 py-2">
            Cancel
          </button>
          <button
            @click="deletePosition"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";

const positions = ref([]);
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const form = ref({ name: "", description: "" });
const selectedPosition = ref(null);

const fetchPositions = async () => {
  const res = await api.get("/positions");
  positions.value = res.data;
};

const openAddModal = () => {
  isEditing.value = false;
  form.value = { name: "", description: "" };
  showModal.value = true;
};

const openEditModal = (pos) => {
  isEditing.value = true;
  selectedPosition.value = pos;
  form.value = { name: pos.name, description: pos.description };
  showModal.value = true;
};

const savePosition = async () => {
  if (isEditing.value) {
    await api.put(`/positions/${selectedPosition.value.id}`, form.value);
  } else {
    await api.post("/positions", form.value);
  }
  showModal.value = false;
  fetchPositions();
};

const confirmDelete = (pos) => {
  selectedPosition.value = pos;
  showDeleteModal.value = true;
};

const deletePosition = async () => {
  await api.delete(`/positions/${selectedPosition.value.id}`);
  showDeleteModal.value = false;
  fetchPositions();
};

const closeModal = () => {
  showModal.value = false;
};

onMounted(fetchPositions);
</script>
