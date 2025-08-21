<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
        💰 Manage Allowances
      </h1>
      <button
        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded"
        @click="openAddModal"
      >
        Add Allowance
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
              Type
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">
              Value
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">
              Taxable
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">
              Description
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">
              Actions
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(allowance, i) in allowances"
            :key="allowance.id"
            class="hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <td class="px-6 py-4">{{ i + 1 }}</td>
            <td class="px-6 py-4">{{ allowance.name }}</td>
            <td class="px-6 py-4 capitalize">{{ allowance.type }}</td>
            <td class="px-6 py-4">
              {{
                allowance.type === "percent"
                  ? allowance.value + "%"
                  : allowance.value
              }}
            </td>
            <td class="px-6 py-4">
              <span
                :class="
                  allowance.is_taxable ? 'text-green-600' : 'text-gray-500'
                "
              >
                {{ allowance.is_taxable ? "Yes" : "No" }}
              </span>
            </td>
            <td class="px-6 py-4">{{ allowance.description }}</td>

            <td class="px-6 py-4">
              <div class="relative inline-block text-left">
                <button
                  @click.stop="toggleDropdown(allowance.id)"
                  class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-600 dark:text-gray-300"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 6h.01M12 12h.01M12 18h.01"
                    />
                  </svg>
                </button>

                <transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <div
                    v-if="openDropdownId === allowance.id"
                    class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10"
                  >
                    <div class="py-1">
                      <button
                        @click="openEditModal(allowance)"
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                      >
                        Edit
                      </button>
                      <button
                        @click="confirmDelete(allowance)"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100 dark:hover:bg-red-700"
                      >
                        Delete
                      </button>
                    </div>
                  </div>
                </transition>
              </div>
            </td>
          </tr>
          <tr v-if="allowances.length === 0">
            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
              No allowances found.
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
        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md overflow-y-auto max-h-[90vh]"
      >
        <h2 class="text-lg font-semibold mb-4">
          {{ isEditing ? "Edit Allowance" : "Add Allowance" }}
        </h2>
        <input
          v-model="form.name"
          type="text"
          placeholder="Allowance Name"
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
        <input
          v-model="form.value"
          type="number"
          placeholder="Value"
          class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700"
        />
        <select
          v-model="form.is_taxable"
          class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700"
        >
          <option :value="true">Taxable</option>
          <option :value="false">Non-Taxable</option>
        </select>
        <textarea
          v-model="form.description"
          placeholder="Description"
          class="w-full border rounded px-3 py-2 mb-4 dark:bg-gray-700"
        ></textarea>

        <div class="flex justify-end space-x-2">
          <button @click="closeModal" class="px-4 py-2">Cancel</button>
          <button
            @click="saveAllowance"
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
        <h2 class="text-lg font-semibold mb-4">Delete Allowance</h2>
        <p>
          Are you sure you want to delete
          <strong>{{ selectedAllowance?.name }}</strong
          >?
        </p>
        <div class="flex justify-end space-x-2 mt-4">
          <button @click="showDeleteModal = false" class="px-4 py-2">
            Cancel
          </button>
          <button
            @click="deleteAllowance"
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

const allowances = ref([]);
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditing = ref(false);
const form = ref({
  name: "",
  type: "",
  value: "",
  is_taxable: true,
  description: "",
});
const selectedAllowance = ref(null);
const openDropdownId = ref(null);

const fetchAllowances = async () => {
  const res = await api.get("/allowances");
  allowances.value = res.data;
};

const openAddModal = () => {
  isEditing.value = false;
  form.value = {
    name: "",
    type: "",
    value: "",
    is_taxable: true,
    description: "",
  };
  showModal.value = true;
};

const openEditModal = (allowance) => {
  isEditing.value = true;
  selectedAllowance.value = allowance;
  form.value = { ...allowance };
  showModal.value = true;
  openDropdownId.value = null;
};

const saveAllowance = async () => {
  if (isEditing.value) {
    await api.put(`/allowances/${selectedAllowance.value.id}`, form.value);
  } else {
    await api.post("/allowances", form.value);
  }
  showModal.value = false;
  fetchAllowances();
};

const confirmDelete = (allowance) => {
  selectedAllowance.value = allowance;
  showDeleteModal.value = true;
  openDropdownId.value = null;
};

const deleteAllowance = async () => {
  await api.delete(`/allowances/${selectedAllowance.value.id}`);
  showDeleteModal.value = false;
  fetchAllowances();
};

const closeModal = () => {
  showModal.value = false;
};

const toggleDropdown = (id) => {
  openDropdownId.value = openDropdownId.value === id ? null : id;
};

const handleClickOutside = (event) => {
  if (!event.target.closest(".relative")) {
    openDropdownId.value = null;
  }
};

onMounted(() => {
  fetchAllowances();
  document.addEventListener("click", handleClickOutside);
});
</script>
