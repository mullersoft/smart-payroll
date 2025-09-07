<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Header Section -->
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">
          🏦 Bank Accounts
        </h1>
        <button
          @click="openModal()"
          class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md"
        >
          ➕ Add Account
        </button>
      </div>

      <!-- Table Section -->
      <div
        class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md transition-colors duration-300"
      >
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
          Bank Account List
        </h2>
        <table class="min-w-full table-auto text-gray-900 dark:text-gray-100">
          <thead class="bg-gray-100 dark:bg-gray-700 text-left">
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
              class="border-b border-gray-300 dark:border-gray-600"
            >
              <td class="px-4 py-2">{{ index + 1 }}</td>
              <td class="px-4 py-2">{{ account.bank_name }}</td>
              <td class="px-4 py-2">
                {{ account.owner_display_name || account.owner_name }}
              </td>
              <td class="px-4 py-2">{{ account.account_number }}</td>
              <td class="px-4 py-2">{{ formatCurrency(account.balance) }}</td>
              <td class="px-4 py-2 relative">
                <!-- Actions Dropdown -->
                <button
                  @click="toggleDropdown(account.id)"
                  class="px-2 py-1 rounded-md text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none"
                >
                  <span class="text-2xl font-bold leading-none">...</span>
                </button>
                <div
                  v-if="openDropdownId === account.id"
                  class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg z-10"
                >
                  <button
                    @click="openModal(account)"
                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                  >
                    ✏️ Edit
                  </button>
                  <button
                    @click="deleteBankAccount(account.id)"
                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600"
                  >
                    🗑️ Delete
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="bankAccounts.length === 0">
              <td
                colspan="6"
                class="text-center py-4 text-gray-500 dark:text-gray-400"
              >
                No bank accounts found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Reusable Modal Component -->
      <CreateAndEditModal
        v-if="showModal"
        :is-edit-mode="isEditMode"
        :editing-account-id="editingAccountId"
        :new-account="newAccount"
        :employees="employees"
        @close="closeModal"
        @save="submitBankAccount"
      />
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
import CreateAndEditModal from "./createAndEditModal.vue";

const toast = useToast();
const bankAccounts = ref([]);
const employees = ref([]);

const showModal = ref(false);
const isEditMode = ref(false);
const editingAccountId = ref(null);
const openDropdownId = ref(null);

const newAccount = ref({
  bank_name: "",
  owner_name: "",
  account_number: "",
  employee_id: null,
});

const openModal = (account = null) => {
  if (account) {
    isEditMode.value = true;
    editingAccountId.value = account.id;
    newAccount.value = {
      bank_name: account.bank_name || "",
      owner_name: account.owner_display_name || account.owner_name || "",
      account_number: account.account_number || "",
      employee_id: account.employee_id || null,
    };
  } else {
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
  openDropdownId.value = null;
};

const closeModal = () => {
  showModal.value = false;
  isEditMode.value = false;
  editingAccountId.value = null;
  newAccount.value = {
    bank_name: "",
    owner_name: "",
    account_number: "",
    employee_id: null,
  };
};

const toggleDropdown = (id) => {
  openDropdownId.value = openDropdownId.value === id ? null : id;
};

const fetchBankAccounts = async () => {
  try {
    const response = await api.get("/bank-accounts");
    bankAccounts.value = response.data;
  } catch {
    toast.error("❌ Failed to load bank accounts data.");
  }
};

const fetchEmployees = async () => {
  try {
    const res = await api.get("/employees");
    employees.value = res.data;
  } catch {
    toast.error("❌ Failed to load employees data.");
  }
};

const submitBankAccount = async () => {
  try {
    if (isEditMode.value) {
      const res = await api.put(
        `/bank-accounts/${editingAccountId.value}`,
        newAccount.value
      );
      const index = bankAccounts.value.findIndex(
        (acc) => acc.id === editingAccountId.value
      );
      if (index !== -1) bankAccounts.value[index] = res.data;
      toast.success("✅ Bank account updated successfully!");
    } else {
      const res = await api.post("/bank-accounts", newAccount.value);
      bankAccounts.value.push(res.data);
      toast.success("✅ Bank account added successfully!");
    }
    closeModal();
  } catch {
    toast.error("❌ Failed to save bank account.");
  }
};

const deleteBankAccount = async (id) => {
  if (!window.confirm("Are you sure you want to delete this bank account?"))
    return;
  try {
    await api.delete(`/bank-accounts/${id}`);
    bankAccounts.value = bankAccounts.value.filter((acc) => acc.id !== id);
    openDropdownId.value = null;
    toast.success("✅ Bank account deleted successfully!");
  } catch {
    toast.error("❌ Failed to delete bank account.");
  }
};

const formatCurrency = (value) =>
  typeof value === "number"
    ? new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
      }).format(value)
    : value;

onMounted(() => {
  fetchBankAccounts();
  fetchEmployees();
});
</script>
