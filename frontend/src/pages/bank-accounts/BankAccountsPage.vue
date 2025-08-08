<!-- src/pages/bank-accounts/BankAccountsPage.vue -->
<template>
  <MainLayout>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-indigo-700">🏦 Bank Accounts</h1>
        <button
          @click="openModal"
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
              <th class="px-4 py-2">Account Number</th>
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
              <td class="px-4 py-2">{{ account.account_number }}</td>
            </tr>
            <tr v-if="bankAccounts.length === 0">
              <td colspan="3" class="text-center py-4 text-gray-500">
                No bank accounts found.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Add Account Modal -->
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
          <h2 class="text-xl font-semibold mb-4">Add Bank Account</h2>
          <form @submit.prevent="addBankAccount" class="space-y-4">
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
                Add Account
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
const newAccount = ref({
  bank_name: "",
  account_number: "",
});

const showModal = ref(false);

const openModal = () => {
  newAccount.value = { bank_name: "", account_number: "" };
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
};

const fetchBankAccounts = async () => {
  try {
    const response = await api.get("/bank-accounts");
    bankAccounts.value = response.data;
  } catch (error) {
    console.error("Failed to fetch bank accounts", error);
  }
};

const addBankAccount = async () => {
  try {
    const res = await api.post("/bank-accounts", newAccount.value);
    bankAccounts.value.push(res.data);
    closeModal();
  } catch (error) {
    console.error("Failed to add bank account", error);
  }
};

onMounted(fetchBankAccounts);
</script>
