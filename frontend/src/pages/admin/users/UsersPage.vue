<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Header -->
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-800 dark:text-blue-400">
          User Management
        </h1>
        <button
          @click="openCreateModal"
          class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800"
        >
          + Create User
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center text-gray-700 dark:text-gray-300">
        <div class="flex justify-center items-center">
          <svg
            class="animate-spin h-5 w-5 mr-2 text-blue-600"
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
          ⏳ Loading users, please wait...
        </div>
      </div>

      <!-- Users Table -->
      <div v-else>
        <table class="min-w-full bg-white rounded-lg shadow dark:bg-gray-800">
          <thead
            class="bg-gray-100 text-left text-sm uppercase dark:bg-gray-700 dark:text-gray-300"
          >
            <tr>
              <th class="p-3">Full Name</th>
              <th class="p-3">Email</th>
              <th class="p-3">Role</th>
              <th class="p-3">Status</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="user in users"
              :key="user.id"
              class="border-t border-gray-200 dark:border-gray-600"
            >
              <td class="p-3 text-gray-800 dark:text-gray-200">
                {{ user.name }}
              </td>
              <td class="p-3 text-gray-800 dark:text-gray-200">
                {{ user.email }}
              </td>
              <td class="p-3 capitalize text-gray-800 dark:text-gray-200">
                {{ user.role ?? "—" }}
              </td>
              <td class="p-3">
                <span
                  :class="{
                    'text-yellow-600 dark:text-yellow-400':
                      user.status === 'pending',
                    'text-green-600 dark:text-green-400':
                      user.status === 'active',
                    'text-red-600 dark:text-red-400':
                      user.status === 'deactivated',
                  }"
                >
                  {{ user.status }}
                </span>
              </td>

              <!-- Actions Dropdown -->
              <td class="p-3 relative">
                <div class="relative" ref="dropdownRefs[user.id]">
                  <button
                    @click="toggleDropdown(user.id)"
                    :disabled="actionLoading[user.id]"
                    class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50"
                  >
                    <svg
                      v-if="actionLoading[user.id]"
                      class="animate-spin h-4 w-4 text-gray-600"
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
                    <span v-else>...</span>
                  </button>
                  <div
                    v-if="dropdownOpen[user.id] && !actionLoading[user.id]"
                    class="absolute right-0 mt-1 w-36 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50"
                  >
                    <button
                      @click="
                        toggleStatus(user);
                        closeDropdown(user.id);
                      "
                      class="block w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                      {{ user.status === "active" ? "Deactivate" : "Activate" }}
                    </button>
                    <button
                      @click="
                        editUser(user);
                        closeDropdown(user.id);
                      "
                      class="block w-full text-left px-3 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                      Edit
                    </button>
                    <button
                      @click="
                        deleteUser(user.id);
                        closeDropdown(user.id);
                      "
                      class="block w-full text-left px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Reusable Create/Edit Modal -->
      <CreateUserModal
        :show="showModal"
        :isEditing="isEditing"
        :form="form"
        :loading="modalLoading"
        @close="showModal = false"
        @submit="handleSubmit"
      />
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import CreateUserModal from "@/pages/admin/users/createUserModal.vue";
import api from "@/services/api";
import { onBeforeUnmount, onMounted, reactive, ref } from "vue";
import { useToast } from "vue-toastification";

const toast = useToast();
const users = ref([]);
const loading = ref(true);
const showModal = ref(false);
const isEditing = ref(false);
const editingUserId = ref(null);
const modalLoading = ref(false);
const actionLoading = reactive({});

const form = ref({
  name: "",
  email: "",
  // password: "",
  role: "",
  status: "pending",
});

// Dropdown logic
const dropdownOpen = reactive({});
const dropdownRefs = reactive({});
const toggleDropdown = (id) => (dropdownOpen[id] = !dropdownOpen[id]);
const closeDropdown = (id) => (dropdownOpen[id] = false);

const handleClickOutside = (event) => {
  for (const id in dropdownRefs) {
    if (dropdownRefs[id] && !dropdownRefs[id].contains(event.target)) {
      dropdownOpen[id] = false;
    }
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
  fetchUsers();
});
onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

// API Calls
const fetchUsers = async () => {
  loading.value = true;
  try {
    const res = await api.get("/users");
    users.value = res.data;
  } catch (err) {
    console.error("Failed to fetch users:", err);
    toast.error("Failed to load users data.");
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  form.value = {
    name: "",
    email: "",
    // password: "",
    role: "",
    status: "pending",
  };
  isEditing.value = false;
  editingUserId.value = null;
  showModal.value = true;
};

const editUser = (user) => {
  form.value = {
    name: user.name,
    email: user.email,
    role: user.role,
    status: user.status,
    // password: "",
  };
  isEditing.value = true;
  editingUserId.value = user.id;
  showModal.value = true;
};

const handleSubmit = async (payload) => {
  modalLoading.value = true;
  try {
    if (isEditing.value) {
      await api.put(`/users/${editingUserId.value}`, {
        name: payload.name,
        email: payload.email,
        role: payload.role,
        status: payload.status,
      });
    } else {
      await api.post("/register", payload);
    }
    toast.success(
      `User ${isEditing.value ? "updated" : "created"} successfully.`
    );
    showModal.value = false;
    fetchUsers();
  } catch (err) {
    console.error("Failed to save user:", err);
    toast.error("Failed to save user.");
  } finally {
    modalLoading.value = false;
  }
};

const toggleStatus = async (user) => {
  actionLoading[user.id] = true;
  try {
    await api.post(`/users/${user.id}/toggle-status`);
    fetchUsers();
  } catch (err) {
    console.error("Failed to toggle user status:", err);
    toast.error("Failed to toggle user status.");
  } finally {
    actionLoading[user.id] = false;
  }
};

const deleteUser = async (id) => {
  if (!confirm("Are you sure you want to delete this user?")) return;
  actionLoading[id] = true;
  try {
    await api.delete(`/users/${id}`);
    fetchUsers();
  } catch (err) {
    console.error("Failed to delete user:", err);
    toast.error("Failed to delete user.");
  } finally {
    actionLoading[id] = false;
  }
};
</script>
