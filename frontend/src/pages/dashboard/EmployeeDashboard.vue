<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Profile Card -->
      <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">My Profile</h2>
        <p><strong>Name:</strong> {{ auth.user?.name }}</p>
        <p><strong>Email:</strong> {{ auth.user?.email }}</p>
        <p><strong>Employee ID:</strong> {{ auth.user?.employee_id }}</p>
      </div>

      <!-- Latest Payroll -->
      <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Latest Payroll</h2>
        <div v-if="payroll">
          <p><strong>Month:</strong> {{ payroll.pay_month }}</p>
          <p><strong>Net Payment:</strong> ${{ payroll.net_payment }}</p>
          <p>
            <strong>Status:</strong>
            <span
              :class="{
                'text-yellow-600': payroll.status === 'prepared',
                'text-green-600': payroll.status === 'approved',
                'text-blue-600': payroll.status === 'paid',
              }"
              >{{ payroll.status }}</span
            >
          </p>
        </div>
        <div v-else class="text-gray-500">No payroll found.</div>
      </div>

      <!-- Transactions -->
      <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">My Transactions</h2>
        <ul v-if="transactions.length > 0" class="divide-y">
          <li v-for="tx in transactions" :key="tx.id" class="py-2">
            <p>
              <strong>{{ tx.transaction_type }}</strong>
              - ${{ tx.amount }}
              <span class="text-sm text-gray-500">
                ({{ new Date(tx.transaction_date).toLocaleDateString() }})
              </span>
            </p>
          </li>
        </ul>
        <p v-else class="text-gray-500">No transactions available.</p>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import api from "@/services/api";
import { useAuthStore } from "@/store/auth";
import { onMounted, ref } from "vue";

const auth = useAuthStore();
const payroll = ref(null);
const transactions = ref([]);

onMounted(async () => {
  try {
    // Fetch payrolls
    const resPayroll = await api.get("/my/payrolls");
    payroll.value = resPayroll.data[0] || null;

    // Fetch transactions
    const resTx = await api.get("/my/transactions");
    transactions.value = resTx.data || [];
  } catch (err) {
    console.error("Error fetching employee data:", err);
  }
});
</script>
