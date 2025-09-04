<template>
  <MainLayout>
    <div class="space-y-6">
      <h1 class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">
        Approver Payrolls
      </h1>

      <div class="flex flex-col md:flex-row md:items-center gap-4">
        <div>
          <label class="block text-sm font-medium">Filter by Month</label>
          <input
            type="month"
            v-model="filters.month"
            @change="fetchPayrolls"
            class="border rounded px-3 py-2 bg-white dark:bg-gray-700"
          />
        </div>

        <div>
          <label class="block text-sm font-medium">Filter by Status</label>
          <select
            v-model="filters.status"
            @change="fetchPayrolls"
            class="border rounded px-3 py-2 bg-white dark:bg-gray-700"
          >
            <option value="">All</option>
            <option value="prepared">Pending</option>
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

      <PayrollTable
        :payrolls="payrolls"
        :loading="loading"
        :show-approver-actions="true"
        :show-view-button="true"
        @payroll-approved="approvePayroll"
        @payroll-rejected="promptReject"
        @view-payroll="viewPayroll"
      />

      <PayrollDetailsModal
        :visible="showViewModal"
        :payroll="selectedPayroll"
        @close="showViewModal = false"
      />

      <div
        v-if="showRejectModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl"
        >
          <h2
            class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
          >
            Reject Payroll
          </h2>
          <p class="text-gray-700 dark:text-gray-300 mb-4">
            Please provide a reason for rejecting this payroll.
          </p>
          <textarea
            v-model="rejectionReason"
            rows="4"
            class="w-full border rounded-md p-2 bg-white dark:bg-gray-700 dark:text-gray-100"
          ></textarea>
          <div class="flex justify-end space-x-2 mt-4">
            <button
              @click="showRejectModal = false"
              class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md"
            >
              Cancel
            </button>
            <button
              @click="confirmReject"
              :disabled="!rejectionReason"
              class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md"
            >
              Confirm Rejection
            </button>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import PayrollDetailsModal from "@/components/payroll/PayrollDetailsModal.vue";
import PayrollTable from "@/components/payroll/PayrollTable.vue";
import api from "@/services/api";
import { onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
const toast = useToast();
const payrolls = ref([]);
const loading = ref(true);
const filters = ref({ month: "", status: "prepared" });
const showViewModal = ref(false);
const selectedPayroll = ref({});

// New state for rejection
const showRejectModal = ref(false);
const payrollToRejectId = ref(null);
const rejectionReason = ref("");

const getCurrentMonth = () => {
  const date = new Date();
  const year = date.getFullYear();
  const month = (date.getMonth() + 1).toString().padStart(2, "0");
  return `${year}-${month}`;
};

const fetchPayrolls = async () => {
  loading.value = true;
  try {
    // const res = await api.get("/payrolls/list", {
    const res = await api.get("/payrolls", {
      params: {
        month: filters.value.month,
        status: filters.value.status,
      },
    });
    payrolls.value = res.data.payrolls || [];
  } catch (error) {
    console.error("Error loading payrolls:", error);
    toast.error("❌ Failed to load payrolls.");
    // alert("❌ Failed to load payrolls.");
  } finally {
    loading.value = false;
  }
};

const approvePayroll = async (payrollId) => {
  try {
    await api.post(`/payrolls/${payrollId}/approve`);
    alert("Payroll approved successfully.");
    fetchPayrolls();
  } catch (error) {
    console.error("Error approving payroll:", error);
    // alert("❌ Failed to approve payroll.");
    toast.error("Failed to approve payroll.");
  }
};

// Function to open the rejection modal
const promptReject = (payrollId) => {
  payrollToRejectId.value = payrollId;
  rejectionReason.value = ""; // Clear previous reason
  showRejectModal.value = true;
};

// Function to handle the actual rejection
const confirmReject = async () => {
  try {
    await api.post(`/payrolls/${payrollToRejectId.value}/reject`, {
      rejection_reason: rejectionReason.value,
    });
    toast.success("✅ Payroll rejected successfully!");
    // alert("✅ Payroll rejected successfully.");
    showRejectModal.value = false; // Close the modal
    fetchPayrolls(); // Refresh the payroll list
  } catch (error) {
    console.error("Error rejecting payroll:", error);
    // alert("❌ Failed to reject payroll.");
    toast.error("Failed to reject payroll.");
  }
};

const viewPayroll = (payroll) => {
  selectedPayroll.value = payroll;
  showViewModal.value = true;
};

onMounted(() => {
  filters.value.month = getCurrentMonth();
  fetchPayrolls();
});
</script>
