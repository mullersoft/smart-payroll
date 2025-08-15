<template>
  <MainLayout>
    <div class="space-y-6 h-full">
      <h1 class="text-2xl font-bold text-blue-700 dark:text-blue-400">
        💳 Payroll Transactions
      </h1>

      <!-- Filter -->
      <div class="flex items-center gap-4">
        <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">
          Filter by Status:
        </label>
        <select
          id="status"
          v-model="statusFilter"
          @change="fetchTransactions"
          class="border rounded px-3 py-1 text-sm dark:bg-gray-800 dark:text-gray-200"
        >
          <option value="completed">Completed</option>
          <option value="failed">Failed</option>
        </select>
      </div>

      <!-- Loading & Empty states -->
      <div v-if="loading" class="text-gray-500 dark:text-gray-400">
        Loading transactions...
      </div>
      <div v-else-if="transactions.length === 0" class="text-gray-500 dark:text-gray-400">
        No transactions found.
      </div>

      <!-- Table -->
      <div v-else class="relative h-[calc(100%-3rem)]">
        <div
          class="absolute inset-0 overflow-x-auto shadow-md sm:rounded-lg table-container"
          ref="tableContainer"
        >
          <table
            class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800"
          >
            <thead
              class="bg-gray-100 dark:bg-gray-700 text-left text-sm font-medium text-gray-600 uppercase dark:text-gray-300"
            >
              <tr>
                <th class="px-6 py-3 sticky left-0 bg-gray-100 dark:bg-gray-700 z-10">#</th>
                <th class="px-6 py-3 sticky left-12 bg-gray-100 dark:bg-gray-700 z-10">Employee</th>
                <!-- <th class="px-6 py-3">Transaction ID</th> -->
                <th class="px-6 py-3">Type</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3">Amount</th>
                <!-- <th class="px-6 py-3">Debited Account</th> -->
                <th class="px-6 py-3">Credited Acc</th>
                <th class="px-6 py-3">Transaction Time</th>
                <th class="px-6 py-3">View</th>
              </tr>
            </thead>
            <tbody
              class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-700 text-sm dark:text-gray-200"
            >
              <tr
                v-for="(txn, index) in transactions"
                :key="txn.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-700"
              >
                <td class="px-6 py-3 sticky left-0 bg-white dark:bg-gray-800 z-10">
                  {{ index + 1 }}
                </td>
                <td class="px-6 py-3 sticky left-12 bg-white dark:bg-gray-800 z-10">
                  {{ txn.employee_name || "N/A" }}
                </td>
                <!-- <td class="px-6 py-3">{{ txn.id }}</td> -->
                <td class="px-6 py-3">
                  <span
                    :class="[ 'inline-block px-2 py-1 rounded-full text-xs font-semibold',
                      txn.transaction_type === 'salary'
                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-400'
                        : 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-400'
                    ]"
                  >
                    {{ txn.transaction_type }}
                  </span>
                </td>
                <td class="px-6 py-3">
                  <span
                    :class="[ 'inline-block px-2 py-1 rounded-full text-xs font-semibold',
                      txn.status === 'completed'
                        ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400'
                        : txn.status === 'pending'
                        ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-400'
                        : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-400'
                    ]"
                  >
                    {{ txn.status || "completed" }}
                  </span>
                </td>
                <td class="px-6 py-3">Birr {{ txn.amount }}</td>
                <!-- <td class="px-6 py-3">{{ txn.from_account }}</td> -->
                <td class="px-6 py-3">{{ txn.to_account }}</td>
                <td class="px-6 py-3">
                  {{ new Date(txn.transaction_date || txn.created_at).toLocaleString() }}
                </td>
                <td class="px-6 py-3 text-center">
                  <button
                    @click="openDetail(txn)"
                    class="px-2 py-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                  >
                    ...
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Detail Modal -->
      <div
        v-if="selectedTransaction"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-96">
          <h2 class="text-lg font-bold mb-4">Transaction Details</h2>
          <p><strong>ID:</strong> {{ selectedTransaction.id }}</p>
          <p><strong>Type:</strong> {{ selectedTransaction.transaction_type }}</p>
          <p><strong>Status:</strong> {{ selectedTransaction.status }}</p>
          <p><strong>Amount:</strong> Birr {{ selectedTransaction.amount }}</p>
          <p><strong>Debited Account:</strong> {{ selectedTransaction.from_account }}</p>
          <p><strong>Credited Account:</strong> {{ selectedTransaction.to_account }}</p>
          <p><strong>Sender:</strong> {{ selectedTransaction.fromBankAccount?.owner_name || "—" }}</p>
          <p><strong>Receiver:</strong> {{ selectedTransaction.toBankAccount?.owner_name || selectedTransaction.employee_name || "—" }}</p>
          <p><strong>Transaction Time:</strong> {{ new Date(selectedTransaction.transaction_date || selectedTransaction.created_at).toLocaleString() }}</p>
          <p><strong>Failure Reason:</strong> {{ selectedTransaction.failure_reason || "—" }}</p>

          <div class="mt-4 text-right">
            <button
              @click="selectedTransaction = null"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, inject } from "vue";
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";

const transactions = ref([]);
const loading = ref(true);
const statusFilter = ref("completed");
const selectedTransaction = ref(null);
const tableContainer = ref(null);

const scrollControl = inject("scrollControl", {
  disableWindowScroll: () => {
    document.body.style.overflow = "hidden";
    document.documentElement.style.overflow = "hidden";
  },
  enableWindowScroll: () => {
    document.body.style.overflow = "";
    document.documentElement.style.overflow = "";
  },
});

const checkScroll = () => {
  if (tableContainer.value) {
    const container = tableContainer.value;
    const isScrollable = container.scrollWidth > container.clientWidth;
    if (isScrollable) {
      scrollControl.disableWindowScroll();
    } else {
      scrollControl.enableWindowScroll();
    }
  }
};

const fetchTransactions = async () => {
  loading.value = true;
  try {
    const res = await api.get("/transactions", {
      params: { status: statusFilter.value }
    });
    transactions.value = (res.data || []).map((t) => ({
      ...t,
      employee_name: t?.payroll?.employee?.full_name || null,
      status: t?.status || "completed",
      fromBankAccount: t?.from_bank_account || t?.fromBankAccount,
      toBankAccount: t?.to_bank_account || t?.toBankAccount,
    }));
  } catch (error) {
    console.error("Failed to load transactions:", error);
  } finally {
    loading.value = false;
    setTimeout(checkScroll, 0);
  }
};

const openDetail = (txn) => {
  selectedTransaction.value = txn;
};

onMounted(() => {
  fetchTransactions();
  window.addEventListener("resize", checkScroll);
});

onBeforeUnmount(() => {
  scrollControl.enableWindowScroll();
  window.removeEventListener("resize", checkScroll);
});
</script>

<style scoped>
.table-container {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f7fafc;
}
.table-container::-webkit-scrollbar {
  height: 8px;
}
.table-container::-webkit-scrollbar-track {
  background: #f7fafc;
  border-radius: 4px;
}
.table-container::-webkit-scrollbar-thumb {
  background-color: #cbd5e0;
  border-radius: 4px;
}
.dark .table-container {
  scrollbar-color: #4b5563 #1f2937;
}
.dark .table-container::-webkit-scrollbar-track {
  background: #1f2937;
}
.dark .table-container::-webkit-scrollbar-thumb {
  background-color: #4b5563;
}
.sticky {
  position: sticky;
}
</style>
