<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-6xl shadow-xl transition-colors duration-300 flex flex-col max-h-[90vh]">
      <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-gray-100">
        Prepare Payroll (Bulk or Single)
      </h2>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pay Month</label>
        <input
          v-model="bulkPayMonth"
          type="month"
          class="w-64 border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
        />
      </div>

      <div class="flex-grow overflow-y-auto mb-4">
        <table
          class="min-w-full border border-gray-300 dark:border-gray-600 text-sm text-gray-900 dark:text-gray-100"
        >
          <thead class="bg-gray-100 dark:bg-gray-700 sticky top-0">
            <tr>
              <th class="px-3 py-2">
                <input
                  type="checkbox"
                  @change="toggleAllEmployees"
                  :checked="allSelected"
                  class="dark:bg-gray-700"
                />
              </th>
              <th class="px-3 py-2">Full Name</th>
              <th class="px-3 py-2">Working Days</th>
              <th class="px-3 py-2">Other Commission</th>
              <th class="px-3 py-2">Loan/Penalty</th>
              <th class="px-3 py-2 text-center" colspan="4">Overtime (hours)</th>
            </tr>
            <tr class="bg-gray-50 dark:bg-gray-600">
              <th colspan="5"></th>
              <th class="px-2 py-1 text-xs font-medium">Weekday Evening</th>
              <th class="px-2 py-1 text-xs font-medium">Night</th>
              <th class="px-2 py-1 text-xs font-medium">Rest Day</th>
              <th class="px-2 py-1 text-xs font-medium">Holiday</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="emp in employees"
              :key="emp.id"
              class="border-b border-gray-300 dark:border-gray-600 align-top"
            >
              <td class="px-3 py-2">
                <input
                  type="checkbox"
                  :value="emp.id"
                  v-model="selectedEmployees"
                  class="dark:bg-gray-700"
                />
              </td>
              <td class="px-3 py-2">{{ emp.full_name }}</td>
              <td class="px-3 py-2">
                <input
                  type="number"
                  min="0"
                  max="30"
                  v-model.number="emp.working_days"
                  class="w-20 border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </td>
              <td class="px-3 py-2">
                <input
                  type="number"
                  step="0.01"
                  v-model.number="emp.other_commission"
                  class="w-24 border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </td>
              <td class="px-3 py-2">
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  v-model.number="emp.loan_penalty"
                  class="w-24 border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </td>
              <td class="px-2 py-2">
                <input
                  type="number"
                  min="0"
                  step="0.5"
                  v-model.number="emp.overtime_weekday_evening"
                  class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </td>
              <td class="px-2 py-2">
                <input
                  type="number"
                  min="0"
                  step="0.5"
                  v-model.number="emp.overtime_night"
                  class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </td>
              <td class="px-2 py-2">
                <input
                  type="number"
                  min="0"
                  step="0.5"
                  v-model.number="emp.overtime_rest_day"
                  class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </td>
              <td class="px-2 py-2">
                <input
                  type="number"
                  min="0"
                  step="0.5"
                  v-model.number="emp.overtime_holiday"
                  class="w-full border rounded px-2 py-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:outline-none focus:ring focus:ring-indigo-300"
                />
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="flex justify-end space-x-3 mt-auto">
        <button
          @click="$emit('close')"
          class="px-4 py-2 text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        >
          Cancel
        </button>
        <button
          @click="createBulkPayroll"
          class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition-colors"
          :disabled="selectedEmployees.length === 0 || !bulkPayMonth"
          :class="{ 'opacity-50 cursor-not-allowed': selectedEmployees.length === 0 || !bulkPayMonth }"
        >
          Prepare Payroll
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import api from "@/services/api";
import { computed, onMounted, ref } from "vue";
import { useToast } from "vue-toastification";
const toast = useToast();

const emit = defineEmits(["close", "payroll-created"]);

const employees = ref([]);
const bulkPayMonth = ref("");
const selectedEmployees = ref([]);

const allSelected = computed(() => {
  return (
    employees.value.length > 0 &&
    selectedEmployees.value.length === employees.value.length
  );
});

const fetchEmployees = async () => {
  try {
    const res = await api.get("/employees");
    employees.value = res.data.map((emp) => ({
      ...emp,
      working_days: 30,
      other_commission: 0,
      loan_penalty: 0, // ✅ Added loan_penalty field
      overtime_weekday_evening: 0,
      overtime_night: 0,
      overtime_rest_day: 0,
      overtime_holiday: 0,
    }));
  } catch (err) {
    console.error("Failed to fetch employees", err);
    toast.error("❌ Failed to load employees.");
  }
};

const toggleAllEmployees = () => {
  if (allSelected.value) {
    selectedEmployees.value = [];
  } else {
    selectedEmployees.value = employees.value.map((e) => e.id);
  }
};

const createBulkPayroll = async () => {
  try {
    const payload = selectedEmployees.value.map((empId) => {
      const emp = employees.value.find((e) => e.id === empId);
      return {
        employee_id: emp.id,
        pay_month: bulkPayMonth.value + "-01",
        working_days: emp.working_days,
        other_commission: emp.other_commission,
        loan_penalty: emp.loan_penalty, // ✅ Added to payload
        prepared_by: JSON.parse(localStorage.getItem("user"))?.id,
        overtimes: [
          { rate_type: "weekday_evening", hours: emp.overtime_weekday_evening },
          { rate_type: "night", hours: emp.overtime_night },
          { rate_type: "rest_day", hours: emp.overtime_rest_day },
          { rate_type: "holiday", hours: emp.overtime_holiday },
        ].filter((ot) => ot.hours > 0),
      };
    });

    await api.post("/payrolls/bulk", { payrolls: payload });
    toast.success("✅ Payrolls prepared successfully!");

    emit("payroll-created");
    emit("close");
  } catch (error) {
    console.error("Error creating payrolls", error);
    toast.error("❌ Failed to prepare payrolls.");
    if (error.response?.data?.errors) {
      const errs = error.response.data.errors;
      Object.values(errs).forEach((msgArr) => {
        toast.error(msgArr[0]);
      });
    } else {
      toast.error("❌ Failed to prepare payrolls.");
    }
  }
};

onMounted(() => {
  fetchEmployees();
});
</script>
