
<template>
  <MainLayout>
    <div class="space-y-6">
      <!-- Filters and Actions -->

      <div class="flex flex-wrap justify-between items-center gap-4">
        <h1 class="text-2xl font-bold text-indigo-700 dark:text-indigo-400">
          Payrolls
        </h1>

        <div class="flex gap-4 items-end">
          <!-- Month Picker -->

          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Select Month</label
            >

            <input
              v-model="selectedMonth"
              type="month"
              class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:bg-gray-700 dark:text-gray-100"
            />
          </div>

          <!-- Status Dropdown -->

          <div>
            <label
              class="block text-sm font-medium text-gray-700 dark:text-gray-300"
              >Status</label
            >

            <select
              v-model="selectedStatus"
              class="px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:bg-gray-700 dark:text-gray-100"
            >
              <option value="prepared">Prepared</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
              <option value="">All</option>
            </select>
          </div>

          <!-- Prepare Payroll Button -->

          <button
            @click="showBulkModal = true"
            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md"
          >
            ➕ Prepare Payroll
          </button>
        </div>
      </div>

      <!-- Payroll Table -->

      <ExportButtons
        v-if="!loading && filteredPayrolls.length > 0"
        :month="selectedMonth"
        :status="selectedStatus"
        @export-success="handleExportSuccess"
        @export-error="handleExportError"
      />

      <PayrollTable
        :payrolls="filteredPayrolls"
        :loading="loading"
        :show-actions="true"
        :show-view-button="false"

        @payroll-pay="payPayroll"
        @process-payroll="processPayroll"
        @view-payroll="viewPayroll"
        @edit-payroll="editPayroll"
        @delete-payroll="promptDelete"
      />

      <!-- Prepare Payroll Modal -->

      <PreparePayrollModal
        v-if="showBulkModal"
        @close="showBulkModal = false"
        @payroll-created="fetchPayrolls"
      />

      <!-- Edit Modal -->

      <div
        v-if="showEditModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl"
        >
          <h2
            class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100"
          >
            Edit Payroll Record
          </h2>

          <form @submit.prevent="submitEdit">
            <div class="space-y-4">
              <div>
                <label
                  class="block text-sm font-medium text-gray-600 dark:text-gray-300"
                  >Employee Full Name</label
                >

                <input
                  v-model="editPayrollData.employee_name"
                  type="text"
                  readonly
                  class="w-full px-4 py-2 border rounded-md bg-gray-100 dark:bg-gray-700 cursor-not-allowed"
                />
              </div>

              <div>
                <label
                  class="block text-sm font-medium text-gray-600 dark:text-gray-300"
                  >Working Days</label
                >

                <input
                  v-model="editPayrollData.working_days"
                  type="number"
                  required
                  class="w-full px-4 py-2 border rounded-md bg-white dark:bg-gray-700"
                />
              </div>

              <div>
                <label
                  class="block text-sm font-medium text-gray-600 dark:text-gray-300"
                  >Other Commission</label
                >

                <input
                  v-model="editPayrollData.other_commission"
                  type="number"
                  required
                  class="w-full px-4 py-2 border rounded-md bg-white dark:bg-gray-700"
                />
              </div>
            </div>

            <div class="flex justify-end mt-6 space-x-2">
              <button
                type="button"
                @click="showEditModal = false"
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md"
              >
                Cancel
              </button>

              <button
                type="submit"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md"
              >
                Save Changes
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Modal -->

      <div
        v-if="showDeleteModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
      >
        <div
          class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-sm shadow-xl"
        >
          <h2 class="text-xl font-semibold mb-4 text-red-600">
            ⚠️ Confirm Deletion
          </h2>

          <p class="text-gray-700 dark:text-gray-300 mb-4">
            Are you sure you want to permanently delete this payroll record?
            This action cannot be undone.
          </p>

          <div class="flex justify-end mt-4 space-x-2">
            <button
              type="button"
              @click="showDeleteModal = false"
              class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md"
            >
              Cancel
            </button>

            <button
              type="button"
              @click="confirmDelete"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md"
            >
              Delete
            </button>
          </div>
        </div>
      </div>

      <PayrollDetailsModal
        :visible="showViewModal"
        :payroll="selectedPayroll"
        @close="showViewModal = false"
      />
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from "@/components/layout/MainLayout.vue";
import PayrollDetailsModal from "@/components/payroll/PayrollDetailsModal.vue";
import PayrollTable from "@/components/payroll/PayrollTable.vue";
import PreparePayrollModal from "@/components/payroll/PreparePayrollModal.vue";
import ExportButtons from "@/components/reports/ExportButtons.vue";
import api from "@/services/api";
import { payWithChapa } from "@/services/chapa";
import { computed, onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
const toast = useToast();

// State

const payrolls = ref([]);
const loading = ref(true);
const showBulkModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editPayrollData = ref({});
const payrollToDeleteId = ref(null);
const showViewModal = ref(false);
const selectedPayroll = ref({});

// Filters

const selectedMonth = ref(new Date().toISOString().slice(0, 7)); // Default current month
const selectedStatus = ref("prepared");
// Fetch payrolls
const fetchPayrolls = async () => {
  loading.value = true;
  try {
    const res = await api.get("/payrolls");

    const items = Array.isArray(res.data) ? res.data : res.data?.payrolls ?? [];

    payrolls.value = items.map((p) => ({
      ...p,

      employee_name: p.employee?.full_name,

      employment_date: p.employee?.employment_date,

      position: p.employee?.position,
    }));
  } catch (error) {
    console.error("Error loading payrolls", error);
    toast.error("Error loading payrolls")
  } finally {
    loading.value = false;
  }
};

// Computed filtered payrolls

const filteredPayrolls = computed(() => {
  return payrolls.value.filter((p) => {
    const matchMonth = selectedMonth.value
      ? p.pay_month?.slice(0, 7) === selectedMonth.value
      : true;

    const matchStatus = selectedStatus.value
      ? p.status === selectedStatus.value
      : true;

    return matchMonth && matchStatus;
  });
});

// View payroll
const viewPayroll = (payroll) => {
  selectedPayroll.value = payroll;
  showViewModal.value = true;
};

// Edit payroll
const editPayroll = (payroll) => {
try {
    editPayrollData.value = { ...payroll };
    showEditModal.value = true;
  } catch (error) {
    console.error("Error preparing edit modal:", error);
    toast.error("Failed to open edit modal.");
  }
  /*  editPayrollData.value = { ...payroll };
  showEditModal.value = true;*/
};

const submitEdit = async () => {
  try {
    await api.put(`/payrolls/${editPayrollData.value.id}`, {
      working_days: editPayrollData.value.working_days,
      other_commission: editPayrollData.value.other_commission,
    });
    toast.success("Payroll updated successfully.");

    await fetchPayrolls();

    showEditModal.value = false;
  } catch (error) {
    console.error("Error updating payroll:", error);
    toast.error("Failed to update payroll.");
  }
};

// Delete payroll

const promptDelete = (id) => {
  payrollToDeleteId.value = id;

  showDeleteModal.value = true;
};

const confirmDelete = async () => {
  try {
    await api.delete(`/payrolls/${payrollToDeleteId.value}`);

    await fetchPayrolls();

    showDeleteModal.value = false;
  } catch (error) {
    console.error("Error deleting payroll:", error);
    toast.error("Failed to delete payroll.");
  }
};

const handleExportSuccess = (data) => {
  console.log(
    `${data.type.toUpperCase()} export successful for ${data.month}`
  );
};

const handleExportError = (data) => {
  console.error(`${data.type.toUpperCase()} export failed:`, data.error);
};

onMounted(() => {
  fetchPayrolls();
});

// Process payroll
const processPayroll = async (id) => {
  try {
    await api.post(`/payrolls/${id}/process`);
    await fetchPayrolls();
  } catch (error) {
console.error("Error processing payroll:", error?.response?.data || error);
toast.error(
      error?.response?.data?.error ||
        "Failed to process payroll. Ensure it's approved and accounts exist."
    );

  }
};
// Payment with Chapa
const payPayroll = async (payroll) => {
  try {
    await payWithChapa(payroll); // just pass payroll object
  } catch (error) {
    console.error("Error processing payroll via Chapa:", error);
    toast.error("Payment initialization failed.");
  }
};

</script>
