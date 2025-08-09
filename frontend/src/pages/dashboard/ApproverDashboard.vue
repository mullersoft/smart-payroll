<template>
  <MainLayout>
    <div class="space-y-6">
      <h1 class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">
        🕒 Pending Payrolls
      </h1>

      <!-- Filters -->
      <div class="flex flex-col md:flex-row md:items-center gap-4">
        <div>
          <label
            class="block text-sm font-medium text-gray-600 dark:text-gray-300"
            >Filter by Month</label
          >
          <input
            type="month"
            v-model="filters.month"
            class="border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
          />
        </div>

        <div>
          <label
            class="block text-sm font-medium text-gray-600 dark:text-gray-300"
            >Filter by Status</label
          >
          <select
            v-model="filters.status"
            class="border rounded px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
          >
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>

        <button
          @click="fetchPayrolls"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded mt-2 md:mt-6"
        >
          Apply Filter
        </button>
      </div>

      <!-- Table -->
      <div v-if="loading" class="text-gray-500 dark:text-gray-400">
        Loading...
      </div>
      <div
        v-else-if="payrolls.length === 0"
        class="text-gray-500 dark:text-gray-400"
      >
        No payrolls found.
      </div>

      <div v-else class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 shadow rounded">
          <thead
            class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm uppercase"
          >
            <tr>
              <th class="px-4 py-2 text-left">#</th>
              <th class="px-4 py-2 text-left">Month</th>
              <th class="px-4 py-2 text-left">Total</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(payroll, index) in payrolls"
              :key="payroll.id"
              class="border-b hover:bg-gray-50 dark:hover:bg-gray-700"
            >
              <td class="px-4 py-2">{{ index + 1 }}</td>
              <td class="px-4 py-2">{{ payroll.month }}</td>
              <td class="px-4 py-2">Birr {{ payroll.total_amount }}</td>
              <td class="px-4 py-2">
                <span :class="statusClass(payroll.status)">{{
                  payroll.status
                }}</span>
              </td>
              <td class="px-4 py-2 space-x-2">
                <button
                  @click="viewPayroll(payroll)"
                  class="text-sm text-blue-600 dark:text-blue-400 underline"
                >
                  Details
                </button>
                <button
                  v-if="payroll.status === 'pending'"
                  @click="approvePayroll(payroll.id)"
                  class="bg-green-600 hover:bg-green-700 text-white text-sm px-3 py-1 rounded"
                >
                  Approve
                </button>
                <button
                  v-if="payroll.status === 'pending'"
                  @click="promptRejection(payroll.id)"
                  class="bg-red-600 hover:bg-red-700 text-white text-sm px-3 py-1 rounded"
                >
                  Reject
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Modal -->
      <div
        v-if="showRejectModal"
        class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center"
      >
        <div
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-md"
        >
          <h2
            class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4"
          >
            Reject Payroll
          </h2>
          <textarea
            v-model="rejectionReason"
            placeholder="Enter reason..."
            class="w-full border rounded p-2 mb-4 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
            rows="4"
          ></textarea>
          <div class="flex justify-end gap-2">
            <button
              @click="showRejectModal = false"
              class="px-4 py-2 text-gray-700 dark:text-gray-300"
            >
              Cancel
            </button>
            <button
              @click="submitRejection"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
            >
              Submit
            </button>
          </div>
        </div>
      </div>

      <!-- Modal for View -->
      <div
        v-if="showViewModal"
        class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center"
      >
        <div
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-full max-w-lg"
        >
          <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">
            Payroll Details
          </h2>
          <p><strong>Month:</strong> {{ selectedPayroll.month }}</p>
          <p><strong>Status:</strong> {{ selectedPayroll.status }}</p>
          <p>
            <strong>Total Amount:</strong> Birr
            {{ selectedPayroll.total_amount }}
          </p>
          <p>
            <strong>Employees:</strong>
            {{ selectedPayroll.employees?.length ?? "N/A" }}
          </p>

          <div class="flex justify-end mt-4">
            <button
              @click="showViewModal = false"
              class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded"
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
import { ref } from "vue";
import api from "@/services/api";
import MainLayout from "@/components/layout/MainLayout.vue";

const payrolls = ref([]);
const loading = ref(true);

const showRejectModal = ref(false);
const rejectionReason = ref("");
const selectedPayrollId = ref(null);

const showViewModal = ref(false);
const selectedPayroll = ref({});

const filters = ref({ month: "", status: "pending" });

const fetchPayrolls = async () => {
  loading.value = true;
  try {
    let url = "/payrolls";
    if (filters.value.status === "pending") {
      url = "/payrolls/pending";
    }
    const res = await api.get(url);
    payrolls.value = res.data.filter((p) => {
      const monthMatch = filters.value.month
        ? p.month === filters.value.month
        : true;
      const statusMatch = filters.value.status
        ? p.status === filters.value.status
        : true;
      return monthMatch && statusMatch;
    });
  } catch (e) {
    console.error("Failed to fetch payrolls", e);
  } finally {
    loading.value = false;
  }
};

const approvePayroll = async (id) => {
  await api.post(`/payrolls/${id}/approve`);
  await fetchPayrolls();
};

const promptRejection = (id) => {
  selectedPayrollId.value = id;
  rejectionReason.value = "";
  showRejectModal.value = true;
};

const submitRejection = async () => {
  await api.post(`/payrolls/${selectedPayrollId.value}/reject`, {
    reason: rejectionReason.value,
  });
  showRejectModal.value = false;
  await fetchPayrolls();
};

const viewPayroll = (payroll) => {
  selectedPayroll.value = payroll;
  showViewModal.value = true;
};

const statusClass = (status) => {
  switch (status) {
    case "approved":
      return "text-green-600 font-semibold";
    case "rejected":
      return "text-red-600 font-semibold";
    case "pending":
      return "text-yellow-600 font-semibold";
    default:
      return "text-gray-600";
  }
};

fetchPayrolls();
</script>
