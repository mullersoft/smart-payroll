<!-- src/pages/transactions/TransactionsPage.vue -->
<template>
  <MainLayout>
    <div class="space-y-6">
      <h1 class="text-2xl font-bold text-blue-700">💳 Payroll Transactions</h1>

      <div v-if="loading" class="text-gray-500">Loading transactions...</div>
      <div v-else-if="transactions.length === 0" class="text-gray-500">No transactions found.</div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-md shadow-md">
          <thead class="bg-gray-100 text-left text-sm font-medium text-gray-600 uppercase">
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3">Employee</th>
              <th class="px-6 py-3">Payroll ID</th>
              <th class="px-6 py-3">Amount</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Date</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 text-sm">
            <tr v-for="(txn, index) in transactions" :key="txn.id" class="border-b hover:bg-gray-50">
              <td class="px-6 py-3">{{ index + 1 }}</td>
              <td class="px-6 py-3">{{ txn.employee_name || 'N/A' }}</td>
              <td class="px-6 py-3">{{ txn.payroll_id }}</td>
              <td class="px-6 py-3">Birr {{ txn.amount }}</td>
              <td class="px-6 py-3">
                <span
                  :class="[
                    'inline-block px-2 py-1 rounded-full text-xs font-semibold',
                    txn.status === 'approved' ? 'bg-green-100 text-green-700' :
                    txn.status === 'pending' ? 'bg-yellow-100 text-yellow-700' :
                    'bg-red-100 text-red-700'
                  ]"
                >
                  {{ txn.status }}
                </span>
              </td>
              <td class="px-6 py-3">{{ new Date(txn.created_at).toLocaleDateString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";

const transactions = ref([]);
const loading = ref(true);

const fetchTransactions = async () => {
  try {
    const res = await api.get("/transactions");
    transactions.value = res.data;
  } catch (error) {
    console.error("Failed to load transactions:", error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchTransactions);
</script>
