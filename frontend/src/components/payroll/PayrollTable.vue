<template>
  <div>
    <div v-if="loading" class="text-center text-gray-500 dark:text-gray-400">
      <p class="animate-pulse">Loading payroll data...</p>
    </div>

    <div
      v-else-if="payrolls.length === 0"
      class="text-center text-gray-500 dark:text-gray-400 p-8 border-2 border-dashed rounded-lg"
    >
      <p>No payrolls found for this status and month.</p>
    </div>

    <div v-else class="overflow-x-auto">
      <table class="min-w-full bg-white shadow-md rounded-lg dark:bg-gray-800">
        <thead
          class="bg-gray-100 text-gray-600 text-sm uppercase dark:bg-gray-700 dark:text-gray-300"
        >
          <tr>
            <th class="px-4 py-2 text-left">#</th>

            <th class="px-4 py-2 text-left">Employee</th>

            <th class="px-4 py-2 text-left">Employment Date</th>

            <th class="px-4 py-2 text-left">Position</th>

            <th class="px-4 py-2 text-left">Base Salary</th>

            <th class="px-4 py-2 text-left">Gross Pay</th>

            <th class="px-4 py-2 text-left">Total Deduction</th>

            <th class="px-4 py-2 text-left">Net Payment</th>

            <th class="px-4 py-2 text-left">Status</th>

            <th
              v-if="showActions || showViewButton || showApproverActions"
              class="px-4 py-2 text-left"
            >
              Actions
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(payroll, index) in payrolls"
            :key="payroll.id"
            class="border-b hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700"
          >
            <td class="px-4 py-2">{{ index + 1 }}</td>

            <td class="px-4 py-2">
              {{ payroll.employee?.full_name || "N/A" }}
            </td>

            <td class="px-4 py-2">
              {{ payroll.employee?.employment_date || "N/A" }}
            </td>

            <td class="px-4 py-2">{{ payroll.employee?.position || "N/A" }}</td>

            <td class="px-4 py-2">
              Birr {{ Number(payroll.base_salary).toLocaleString() }}
            </td>

            <td class="px-4 py-2">
              Birr {{ Number(payroll.gross_pay).toLocaleString() }}
            </td>

            <td class="px-4 py-2">
              Birr {{ Number(payroll.total_deduction).toLocaleString() }}
            </td>

            <td class="px-4 py-2">
              Birr {{ Number(payroll.net_payment).toLocaleString() }}
            </td>

            <td class="px-4 py-2">
              <span :class="statusClass(payroll.status)">
                {{ payroll.status }}
              </span>
            </td>

            <!-- Consolidated Actions Column -->

            <td
              v-if="showActions || showViewButton || showApproverActions"
              class="px-4 py-2 relative"
            >
              <!-- Single button for all dropdown actions -->

              <div
                v-if="
                  showActions ||
                  (showApproverActions && payroll.status === 'prepared') ||
                  showViewButton
                "
              >
                <button
                  @click="toggleDropdown(payroll.id)"
                  class="p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-600 text-sm font-semibold"
                  title="Click for actions"
                >
                  ...
                </button>

                <div
                  v-if="openDropdownId === payroll.id"
                  class="absolute right-0 mt-2 w-32 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded shadow-lg z-50"
                >
                  <!-- Always show View button if the prop is true -->

                  <button
                    v-if="showViewButton"
                    @click="
                      $emit('view-payroll', payroll);

                      toggleDropdown(payroll.id);
                    "
                    class="block w-full text-left px-4 py-2 text-sm hover:bg-blue-100 dark:hover:bg-blue-700 text-blue-700 dark:text-blue-300"
                  >
                    View
                  </button>

                  <!-- Conditional display of Approver actions -->

                  <template v-if="showApproverActions">
                    <button
                      @click="
                        $emit('payroll-approved', payroll.id);

                        toggleDropdown(payroll.id);
                      "
                      class="block w-full text-left px-4 py-2 text-sm hover:bg-green-100 dark:hover:bg-green-700 text-green-700 dark:text-green-300"
                    >
                      Approve
                    </button>

                    <button
                      @click="
                        $emit('payroll-rejected', payroll.id);

                        toggleDropdown(payroll.id);
                      "
                      class="block w-full text-left px-4 py-2 text-sm hover:bg-red-100 dark:hover:bg-red-700 text-red-700 dark:text-red-300"
                    >
                      Reject
                    </button>
                  </template>

                  <!-- Conditional display of Preparer actions -->

                  <template v-else-if="showActions">
                    <button
                      @click="
                        $emit('view-payroll', payroll);
                        toggleDropdown(payroll.id);
                      "
                      class="block w-full text-left px-4 py-2 text-sm hover:bg-blue-100 dark:hover:bg-blue-700 text-blue-700 dark:text-blue-300"
                    >
                      View
                    </button>
                    <button
                      @click="
                        $emit('edit-payroll', payroll);
                        toggleDropdown(payroll.id);
                      "
                      class="block w-full text-left px-4 py-2 text-sm hover:bg-yellow-100 dark:hover:bg-yellow-700 text-yellow-700 dark:text-yellow-300"
                    >
                      Edit
                    </button>

                    <button
                      @click="
                        $emit('delete-payroll', payroll.id);
                        toggleDropdown(payroll.id);
                      "
                      class="block w-full text-left px-4 py-2 text-sm hover:bg-red-100 dark:hover:bg-red-700 text-red-700 dark:text-red-300"
                    >
                      Delete
                    </button>
                  </template>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const props = defineProps({
  payrolls: Array,

  loading: Boolean,

  showActions: Boolean, // For Preparer (Edit/Delete)

  showViewButton: {
    type: Boolean,

    default: false,
  },

  showApproverActions: {
    // New prop for Approver (Approve/Reject)

    type: Boolean,

    default: false,
  },
});

const emit = defineEmits([
  "payroll-approved",

  "payroll-rejected",

  "view-payroll",

  "edit-payroll",

  "delete-payroll",
]);

const openDropdownId = ref(null);

const toggleDropdown = (id) => {
  openDropdownId.value = openDropdownId.value === id ? null : id;
};

const statusClass = (status) => {
  switch (status) {
    case "approved":
      return "text-green-600 font-semibold";

    case "rejected":
      return "text-red-600 font-semibold";

    case "prepared":
      return "text-yellow-600 font-semibold";

    default:
      return "text-gray-600";
  }
};

onMounted(() => {
  document.addEventListener("click", (e) => {
    if (!e.target.closest("td")) openDropdownId.value = null;
  });
});
</script>
