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
   <!-- Loading -->
      <div v-if="loading" class="text-center text-gray-700 dark:text-gray-300">
        ⏳ Loading positions, please wait...
      </div>


    <!-- Table -->
    <div v-else class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
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
              Value
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Type</th>
<th class="px-6 py-3 text-left text-xs font-medium uppercase">Taxable</th>

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
<td class="px-6 py-4 capitalize">{{ pos.type }}</td>
<td class="px-6 py-4">
  <span :class="pos.is_taxable ? 'text-green-600' : 'text-gray-500'">
    {{ pos.is_taxable ? 'Yes' : 'No' }}
  </span>
</td>


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
            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
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
          placeholder="Value"
          class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700"
        />
        <select
  v-model="form.type"
  class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700"
>
  <option disabled value="">Select Type</option>
  <option value="fixed">Fixed</option>
  <option value="percent">Percent</option>
</select>

<select
  v-model="form.is_taxable"
  class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700"
>
  <option :value="true">Taxable</option>
  <option :value="false">Non-Taxable</option>
</select>

        <div class="flex justify-end space-x-2">
          <button @click="closeModal" class="px-4 py-2">Cancel</button>



           <button
          type="submit"
          @click="savePosition"
          :disabled="loading"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded flex items-center gap-2"
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
          <svg v-else-if="isEditing" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          {{ loading ? "Saving..." : (isEditing ? 'Update Position' : 'Create Position') }}
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
import api from "@/services/api";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
const toast = useToast();

const positions = ref([]);
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
// const form = ref({ name: "", description: "", allowance: "" }); // Added allowance field
const form = ref({
  name: "",
  description: "",
  allowance: "",
  type: "fixed",
  is_taxable: true,
});
const selectedPosition = ref(null);
const loading = ref(true)

const fetchPositions = async () => {
  loading.value = true
  try {
    const res = await api.get("/positions");
    positions.value = res.data;
  } catch (error) {
    console.error("Failed to fetch positions:", error);
    toast.error("Failed to load positions data.");
  }

   finally {
    loading.value = false
  }
};

// const openAddModal = () => {
//   isEditing.value = false;
//   form.value = { name: "", description: "", allowance: "" }; // Added allowance field
//   showModal.value = true;
// };

// const openEditModal = (pos) => {
//   isEditing.value = true;
//   selectedPosition.value = pos;
//   form.value = {
//     name: pos.name,
//     description: pos.description,
//     allowance: pos.allowance // Added allowance field
//   };
//   showModal.value = true;
// };
const openAddModal = () => {
  isEditing.value = false;
  form.value = { name: "", description: "", allowance: "", type: "fixed", is_taxable: true };
  showModal.value = true;
};

const openEditModal = (pos) => {
  isEditing.value = true;
  selectedPosition.value = pos;
  form.value = { ...pos };
  showModal.value = true;
};
const savePosition = async () => {
  loading.value = true;
  try {
    if (isEditing.value) {
      // Update existing position
      await api.put(`/positions/${selectedPosition.value.id}`, form.value);
      toast.success("Position updated successfully.");
    } else {
      // Create new position
      await api.post("/positions", form.value);
      toast.success("Position created successfully.");
    }
    showModal.value = false;
    fetchPositions();
  } catch (error) {
    console.error("Failed to save position:", error);
    toast.error("Failed to save position.");
  }
  finally {
  loading.value = false
}
};

const confirmDelete = (pos) => {
  selectedPosition.value = pos;
  showDeleteModal.value = true;
};

const deletePosition = async () => {
try {
    await api.delete(`/positions/${selectedPosition.value.id}`);
    toast.success("Position deleted successfully.");
    showDeleteModal.value = false;
    fetchPositions();
  } catch (error) {
    console.error("Failed to delete position:", error);
    toast.error("Failed to delete position.");
  }
};

const closeModal = () => {
  showModal.value = false;
};

onMounted(fetchPositions);
</script>
