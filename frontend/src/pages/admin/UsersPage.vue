<template>
  <MainLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-blue-800">User Management</h1>
        <button
          @click="openCreateModal"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          + Create User
        </button>
      </div>

      <div v-if="loading" class="text-center">Loading users...</div>

      <div v-else>
        <table class="min-w-full bg-white rounded-lg overflow-hidden shadow">
          <thead class="bg-gray-100 text-left text-sm uppercase">
            <tr>
              <th class="p-3">Email</th>
              <th class="p-3">Role</th>
              <th class="p-3">Status</th>
              <th class="p-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id" class="border-t">
              <td class="p-3">{{ user.email }}</td>
              <td class="p-3 capitalize">{{ user.role }}</td>
              <td class="p-3">
                <span :class="user.is_active ? 'text-green-600' : 'text-red-600'">
                  {{ user.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="p-3 space-x-2">
                <button
                  @click="toggleStatus(user)"
                  class="text-sm text-white px-3 py-1 rounded"
                  :class="user.is_active ? 'bg-red-600' : 'bg-green-600'"
                >
                  {{ user.is_active ? 'Deactivate' : 'Activate' }}
                </button>
                <button @click="editUser(user)" class="text-sm text-blue-600">Edit</button>
                <button @click="deleteUser(user.id)" class="text-sm text-red-600">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Create/Edit User Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
          <h2 class="text-xl font-semibold mb-4">
            {{ isEditing ? 'Edit User' : 'Create New User' }}
          </h2>
          <form @submit.prevent="handleSubmit">
            <div class="mb-4">
              <label class="block text-sm font-medium">Email</label>
              <input
                v-model="form.email"
                type="email"
                required
                class="w-full border rounded px-3 py-2"
              />
            </div>

            <div class="mb-4" v-if="!isEditing">
              <label class="block text-sm font-medium">Password</label>
              <input
                v-model="form.password"
                type="password"
                required
                class="w-full border rounded px-3 py-2"
              />
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium">Role</label>
              <select v-model="form.role" class="w-full border rounded px-3 py-2">
                <option value="admin">Admin</option>
                <option value="preparer">Preparer</option>
                <option value="approver">Approver</option>
              </select>
            </div>

            <div class="flex justify-end space-x-3">
              <button @click="showModal = false" type="button" class="text-gray-600">
                Cancel
              </button>
              <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                {{ isEditing ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import MainLayout from '@/components/layout/MainLayout.vue';
import api from '@/services/api';

const users = ref([]);
const loading = ref(true);
const showModal = ref(false);
const isEditing = ref(false);
const editingUserId = ref(null);

const form = ref({
  email: '',
  password: '',
  role: 'preparer',
});

const fetchUsers = async () => {
  loading.value = true;
  try {
    const res = await api.get('/users');
    users.value = res.data;
  } catch (err) {
    console.error('Failed to fetch users:', err);
  } finally {
    loading.value = false;
  }
};

const openCreateModal = () => {
  form.value = { email: '', password: '', role: 'preparer' };
  isEditing.value = false;
  editingUserId.value = null;
  showModal.value = true;
};

const editUser = (user) => {
  form.value = {
    email: user.email,
    role: user.role,
    password: '',
  };
  isEditing.value = true;
  editingUserId.value = user.id;
  showModal.value = true;
};

const handleSubmit = async () => {
  try {
    if (isEditing.value) {
      // You must create this endpoint in the backend
      await api.put(`/users/${editingUserId.value}`, {
        email: form.value.email,
        role: form.value.role,
        // optional password update logic can go here
      });
    } else {
      await api.post('/register', form.value);
    }
    showModal.value = false;
    fetchUsers();
  } catch (err) {
    console.error('Failed to save user:', err);
  }
};

const toggleStatus = async (user) => {
  try {
    await api.post(`/users/${user.id}/toggle-status`);
    fetchUsers();
  } catch (err) {
    console.error('Failed to toggle user status:', err);
  }
};

const deleteUser = async (id) => {
  if (!confirm('Are you sure you want to delete this user?')) return;
  try {
    await api.delete(`/users/${id}`);
    fetchUsers();
  } catch (err) {
    console.error('Failed to delete user:', err);
  }
};

onMounted(fetchUsers);
</script>
