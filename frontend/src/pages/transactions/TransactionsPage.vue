<template>
  <MainLayout>
    <div class="space-y-6">
      <h1 class="text-2xl font-bold text-blue-700 dark:text-blue-400">
        💳 Payroll Transactions
      </h1>

      <div v-if="loading" class="text-gray-500 dark:text-gray-400">
        Loading transactions...
      </div>
      <div
        v-else-if="transactions.length === 0"
        class="text-gray-500 dark:text-gray-400"
      >
        No transactions found.
      </div>

      <div v-else class="overflow-x-auto">
        <table
          class="min-w-full bg-white rounded-md shadow-md dark:bg-gray-800"
        >
          <thead
            class="bg-gray-100 text-left text-sm font-medium text-gray-600 uppercase dark:bg-gray-700 dark:text-gray-300"
          >
            <tr>
              <th class="px-6 py-3">#</th>
              <th class="px-6 py-3">Employee</th>
              <th class="px-6 py-3">Transaction ID</th>
              <th class="px-6 py-3">Type</th>
              <th class="px-6 py-3">Status</th>
              <th class="px-6 py-3">Amount</th>
              <th class="px-6 py-3">Debited Account</th>
              <th class="px-6 py-3">Credited Account</th>
              <th class="px-6 py-3">Sender</th>
              <th class="px-6 py-3">Receiver</th>
              <th class="px-6 py-3">Transaction Time</th>
              <th class="px-6 py-3">Failure Reason</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 text-sm dark:text-gray-200">
            <tr
              v-for="(txn, index) in transactions"
              :key="txn.id"
              class="border-b border-gray-300 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700"
            >
              <td class="px-6 py-3">{{ index + 1 }}</td>
              <td class="px-6 py-3">{{ txn.employee_name || "N/A" }}</td>
              <td class="px-6 py-3">{{ txn.id }}</td>
              <td class="px-6 py-3">
                <span
                  :class="[
                    'inline-block px-2 py-1 rounded-full text-xs font-semibold',
                    txn.transaction_type === 'salary'
                      ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-400'
                      : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-400',
                  ]"
                >
                  {{ txn.transaction_type }}
                </span>
              </td>
              <td class="px-6 py-3">
                <span
                  :class="[
                    'inline-block px-2 py-1 rounded-full text-xs font-semibold',
                    txn.status === 'completed'
                      ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400'
                      : txn.status === 'pending'
                      ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-400'
                      : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400',
                  ]"
                >
                  {{ txn.status || "completed" }}
                </span>
              </td>
              <td class="px-6 py-3">Birr {{ txn.amount }}</td>
              <td class="px-6 py-3">{{ txn.from_account }}</td>
              <td class="px-6 py-3">{{ txn.to_account }}</td>
              <td class="px-6 py-3">
                {{ txn.fromBankAccount?.owner_name || "—" }}
              </td>
              <td class="px-6 py-3">
                {{ txn.toBankAccount?.owner_name || txn.employee_name || "—" }}
              </td>
              <td class="px-6 py-3">
                {{
                  new Date(
                    txn.transaction_date || txn.created_at
                  ).toLocaleString()
                }}
              </td>
              <td class="px-6 py-3">{{ txn.failure_reason || "—" }}</td>
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
    transactions.value = (res.data || []).map((t) => ({
      ...t,
      employee_name: t?.payroll?.employee?.full_name || null,
      status: t?.status || "completed", // Use transaction status, not payroll status
      fromBankAccount: t?.from_bank_account || t?.fromBankAccount,
      toBankAccount: t?.to_bank_account || t?.toBankAccount,
    }));
  } catch (error) {
    console.error("Failed to load transactions:", error);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchTransactions);
</script>
