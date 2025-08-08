<!-- src/pages/bank-accounts/BankAccountsPage.vue -->
<template>
  <MainLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-indigo-700">🏦 Bank Accounts</h1>
        <button
          @click="openModal()"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md"
        >
          ➕ Add Account
        </button>
      </div>

      <!-- List of Bank Accounts -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">
          Bank Account List
        </h2>
        <table class="min-w-full table-auto">
          <thead class="bg-gray-100 text-left">
            <tr>
              <th class="px-4 py-2">#</th>
              <th class="px-4 py-2">Bank Name</th>
              <th class="px-4 py-2">Owner Name</th>
              <th class="px-4 py-2">Account Number</th>
              <th class="px-4 py-2">Balance</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(account, index) in bankAccounts"
              :key="account.id"
              class="border-b"
            >
              <td class="px-4 py-2">{{ index + 1 }}</td>
              <td class="px-4 py-2">{{ account.bank_name }}</td>
              <td class="px-4 py-2">
                {{ account.owner_display_name || account.owner_name }}
              </td>
              <td class="px-4 py-2">{{ account.account_number }}</td>
              <td class="px-4 py-2">{{ formatCurrency(account.balance) }}</td>
              <td class="px-4 py-2 space-x-2">
                <button
                  @click="openModal(account)"
                  class="text-blue-600 hover:underline"
                  title="Edit"
                >
                  ✏️
                </button>
                <button
                  @click="deleteBankAccount(account.id)"
                  class="text-red-600 hover:underline"
                  title="Delete"
                >
                  🗑️
                </button>
              </td>
            </tr>
            <tr v-if="bankAccounts.length === 0">
              <td colspan="6" class="text-center py-4 text-gray-500">
                No bank accounts found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add/Edit Account Modal -->
      <div
        v-if="showModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl relative">
          <button
            @click="closeModal"
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
          >
            ✖
          </button>
          <h2 class="text-xl font-semibold mb-4">
            {{ isEditMode ? "Edit Bank Account" : "Add Bank Account" }}
          </h2>
          <form @submit.prevent="submitBankAccount" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700"
                >Bank Name</label
              >
              <input
                v-model="newAccount.bank_name"
                type="text"
                required
                class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700"
                >Owner Name (select employee or type manually)</label
              >
              <input
                list="employeeList"
                v-model="newAccount.owner_name"
                @input="onOwnerNameInput"
                type="text"
                class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300"
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

            <div>
              <label class="block text-sm font-medium text-gray-700"
                >Account Number</label
              >
              <input
                v-model="newAccount.account_number"
                type="text"
                required
                class="w-full mt-1 px-4 py-2 border rounded-md focus:ring focus:outline-none focus:ring-indigo-300"
              />
            </div>

            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 text-gray-600"
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
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import MainLayout from "@/components/layout/MainLayout.vue";

const bankAccounts = ref([]);
const employees = ref([]);

const showModal = ref(false);
const isEditMode = ref(false);
const editingAccountId = ref(null);

const newAccount = ref({
  bank_name: "",
  owner_name: "",
  account_number: "",
  employee_id: null,
});

const openModal = (account = null) => {
  if (account) {
    // Edit mode
    isEditMode.value = true;
    editingAccountId.value = account.id;
    newAccount.value = {
      bank_name: account.bank_name || "",
      owner_name: account.owner_display_name || account.owner_name || "",
      account_number: account.account_number || "",
      employee_id: account.employee_id || null,
    };
  } else {
    // Add mode
    isEditMode.value = false;
    editingAccountId.value = null;
    newAccount.value = {
      bank_name: "",
      owner_name: "",
      account_number: "",
      employee_id: null,
    };
  }
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  isEditMode.value = false;
  editingAccountId.value = null;
};

// Fetch bank accounts from API
const fetchBankAccounts = async () => {
  try {
    const response = await api.get("/bank-accounts");
    bankAccounts.value = response.data;
  } catch (error) {
    console.error("Failed to fetch bank accounts", error);
  }
};

// Fetch employees for owner name dropdown
const fetchEmployees = async () => {
  try {
    const res = await api.get("/employees"); // Adjust API route if needed
    employees.value = res.data;
  } catch (error) {
    console.error("Failed to fetch employees", error);
  }
};

// Handle owner name input to link employee_id if matches
const onOwnerNameInput = () => {
  const inputName = newAccount.value.owner_name.trim().toLowerCase();
  const selected = employees.value.find(
    (emp) => emp.full_name.toLowerCase() === inputName
  );
  if (selected) {
    newAccount.value.employee_id = selected.id;
    newAccount.value.owner_name = selected.full_name; // ensure exact casing
  } else {
    newAccount.value.employee_id = null;
  }
};

// Submit Add or Edit form
const submitBankAccount = async () => {
  try {
    if (isEditMode.value) {
      // Update existing account
      const res = await api.put(
        `/bank-accounts/${editingAccountId.value}`,
        newAccount.value
      );
      // Update local list
      const index = bankAccounts.value.findIndex(
        (acc) => acc.id === editingAccountId.value
      );
      if (index !== -1) {
        bankAccounts.value[index] = res.data;
      }
    } else {
      // Create new account
      const res = await api.post("/bank-accounts", newAccount.value);
      bankAccounts.value.push(res.data);
    }
    closeModal();
  } catch (error) {
    console.error("Failed to submit bank account", error);
  }
};

// Delete bank account with confirmation
const deleteBankAccount = async (id) => {
  const confirmed = window.confirm(
    "Are you sure you want to delete this bank account?"
  );
  if (!confirmed) return;

  try {
    await api.delete(`/bank-accounts/${id}`);
    bankAccounts.value = bankAccounts.value.filter((acc) => acc.id !== id);
  } catch (error) {
    console.error("Failed to delete bank account", error);
  }
};

const formatCurrency = (value) => {
  if (typeof value !== "number") return value;
  return new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
  }).format(value);
};

onMounted(() => {
  fetchBankAccounts();
  fetchEmployees();
});
</script>
