<template>
  <MainLayout>
    <div class="space-y-4">
      <h1 class="text-2xl font-bold text-yellow-700">🕒 Pending Payrolls</h1>

      <div v-if="loading" class="text-gray-500">Loading...</div>
      <div v-else-if="payrolls.length === 0" class="text-gray-500">No pending payrolls.</div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full bg-white shadow-md rounded-lg">
          <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
            <tr>
              <th class="px-4 py-2 text-left">#</th>
              <th class="px-4 py-2 text-left">Month</th>
              <th class="px-4 py-2 text-left">Total Amount</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(payroll, index) in payrolls"
              :key="payroll.id"
              class="border-b hover:bg-gray-50"
            >
              <td class="px-4 py-2">{{ index + 1 }}</td>
              <td class="px-4 py-2">{{ payroll.month }}</td>
              <td class="px-4 py-2">Birr {{ payroll.total_amount }}</td>
              <td class="px-4 py-2">
                <span class="text-yellow-600 font-semibold">{{ payroll.status }}</span>
              </td>
              <td class="px-4 py-2 space-x-2">
                <button
                  class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded"
                  @click="approvePayroll(payroll.id)"
                >
                  Approve
                </button>
                <button
                  class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded"
                  @click="promptRejection(payroll.id)"
                >
                  Reject
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal for rejection reason -->
      <div v-if="showRejectModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
          <h2 class="text-lg font-semibold text-gray-700 mb-4">Reject Payroll</h2>
          <textarea
            v-model="rejectionReason"
            placeholder="Enter reason for rejection..."
            class="w-full border rounded p-2 mb-4"
            rows="4"
          ></textarea>
          <div class="flex justify-end space-x-2">
            <button @click="showRejectModal = false" class="px-4 py-2 text-gray-700">Cancel</button>
            <button @click="submitRejection" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
              Submit
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import MainLayout from "@/components/layout/MainLayout.vue";

const payrolls = ref([]);
const loading = ref(true);

const showRejectModal = ref(false);
const selectedPayrollId = ref(null);
const rejectionReason = ref("");

const fetchPendingPayrolls = async () => {
  loading.value = true;
  try {
    const res = await api.get("/payrolls/pending");
    payrolls.value = res.data;
  } catch (error) {
    console.error("Error loading payrolls:", error);
  } finally {
    loading.value = false;
  }
};

const approvePayroll = async (id) => {
  try {
    await api.post(`/payrolls/${id}/approve`);
    payrolls.value = payrolls.value.filter(p => p.id !== id);
    alert("✅ Payroll approved successfully.");
  } catch (error) {
    console.error("Approval failed:", error);
    alert("❌ Approval failed.");
  }
};

const promptRejection = (id) => {
  selectedPayrollId.value = id;
  rejectionReason.value = "";
  showRejectModal.value = true;
};

const submitRejection = async () => {
  try {
    await api.post(`/payrolls/${selectedPayrollId.value}/reject`, {
      reason: rejectionReason.value,
    });
    payrolls.value = payrolls.value.filter(p => p.id !== selectedPayrollId.value);
    showRejectModal.value = false;
    alert("🚫 Payroll rejected.");
  } catch (error) {
    console.error("Rejection failed:", error);
    alert("❌ Rejection failed.");
  }
};

onMounted(fetchPendingPayrolls);
</script>
