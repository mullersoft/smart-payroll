<template>
  <div class="relative inline-block text-left mt-6" style="z-index: 30;">
    <div>
      <button
        @click="toggleDropdown"
        type="button"
        class="inline-flex justify-center items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200"
        aria-expanded="true"
        aria-haspopup="true"
        :disabled="isExporting"
        :title="isExporting ? 'Export in progress...' : 'Export payroll data'"
      >
        <template v-if="isExporting">
          <svg
            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          Exporting...
        </template>
        <template v-else>
          <span>📁</span>
          Export
          <svg
            class="-mr-1 ml-1 h-5 w-5 transition-transform duration-200"
            :class="{ 'rotate-180': isOpen }"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 20 20"
            fill="currentColor"
            aria-hidden="true"
          >
            <path
              fill-rule="evenodd"
              d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
              clip-rule="evenodd"
            />
          </svg>
        </template>
      </button>
    </div>

    <transition
      enter-active-class="transition ease-out duration-100"
      enter-from-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-from-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div
        v-if="isOpen && !isExporting"
        class="absolute left-0 z-50 mt-2 w-56 origin-top-left rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none divide-y divide-gray-100 dark:divide-gray-700"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="menu-button"
        tabindex="-1"
      >
        <div class="py-1" role="none">
          <button
            @click="exportReport('excel')"
            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left group"
            role="menuitem"
            tabindex="-1"
            title="Export as Excel spreadsheet"
          >
            <span class="mr-3 text-green-600 dark:text-green-400">📊</span>
            <div>
              <div class="font-medium">Export to Excel</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">
                (.xlsx format)
              </div>
            </div>
          </button>
          <button
            @click="exportReport('pdf')"
            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 w-full text-left group"
            role="menuitem"
            tabindex="-1"
            title="Export as PDF document"
          >
            <span class="mr-3 text-red-600 dark:text-red-400">📄</span>
            <div>
              <div class="font-medium">Export to PDF</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">
                Approved payrolls only
              </div>
            </div>
          </button>
        </div>
      </div>
    </transition>

    <!-- Success/Error Toast Notification -->
    <div v-if="notification.show" class="fixed bottom-4 right-4 z-70">
      <div
        :class="{
          'bg-green-50 dark:bg-green-900 text-green-800 dark:text-green-200':
            notification.type === 'success',
          'bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200':
            notification.type === 'error',
        }"
        class="rounded-md p-4 shadow-lg max-w-sm"
      >
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <span
              v-if="notification.type === 'success'"
              class="text-green-500 dark:text-green-300"
              >✓</span
            >
            <span v-else class="text-red-500 dark:text-red-300">✗</span>
          </div>
          <div class="ml-3">
            <h3 class="text-sm font-medium">
              {{
                notification.type === "success"
                  ? "Export successful"
                  : "Export failed"
              }}
            </h3>
            <div class="mt-1 text-sm">
              {{ notification.message }}
            </div>
          </div>
          <button
            @click="notification.show = false"
            class="ml-auto -mx-1.5 -my-1.5 rounded-md p-1.5 focus:outline-none focus:ring-2"
            :class="{
              'bg-green-50 text-green-500 hover:bg-green-100 focus:ring-green-600 focus:ring-offset-green-50 dark:bg-green-900/50 dark:hover:bg-green-800':
                notification.type === 'success',
              'bg-red-50 text-red-500 hover:bg-red-100 focus:ring-red-600 focus:ring-offset-red-50 dark:bg-red-900/50 dark:hover:bg-red-800':
                notification.type === 'error',
            }"
          >
            <span class="sr-only">Dismiss</span>
            <svg
              class="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"
              />
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import api from "@/services/api";

const props = defineProps({
  month: {
    type: String,
    required: true,
  },
  status: {
    type: String,
    default: "approved",
  },
});

const emit = defineEmits(["export-success", "export-error"]);

const isOpen = ref(false);
const isExporting = ref(false);
const notification = ref({
  show: false,
  type: "",
  message: "",
});

const toggleDropdown = () => {
  if (!isExporting.value) {
    isOpen.value = !isOpen.value;
  }
};

const closeDropdown = () => {
  isOpen.value = false;
};

const showNotification = (type, message) => {
  notification.value = {
    show: true,
    type,
    message,
  };

  setTimeout(() => {
    notification.value.show = false;
  }, 5000);
};

const exportReport = async (type) => {
  closeDropdown();

  if (!props.month) {
  showNotification("error", "Please select a month first");
  emit("export-error", {
    type,
    error: "No month selected",
  });
  return;
}

  isExporting.value = true;

  try {
    const token = localStorage.getItem("token");
    const response = await api.get(
      `/reports/monthly/${props.month}/export-${type}?status=${props.status}`,
      {
        responseType: "blob",
        headers: { Authorization: `Bearer ${token}` },
      }
    );

    const blob = new Blob([response.data], {
      type:
        type === "excel"
          ? "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
          : "application/pdf",
    });
    const link = document.createElement("a");
    link.href = window.URL.createObjectURL(blob);
    link.download =
      type === "excel"
        ? `payroll-report-${props.month}.xlsx`
        : `payroll-report-${props.month}.pdf`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    const successMessage =
      type === "excel"
        ? `Excel file for ${props.month} downloaded`
        : `PDF report for ${props.month} downloaded`;

    showNotification("success", successMessage);
    emit("export-success", {
      type,
      month: props.month,
      status: props.status,
    });
  } catch (error) {
    console.error("Error exporting report:", error);
    showNotification(
      "error",
      `Failed to export ${type.toUpperCase()}: ${error.message}`
    );
    emit("export-error", {
      type,
      error: error.message,
    });
  } finally {
    isExporting.value = false;
  }
};

const handleClickOutside = (event) => {
  if (
    !event.target.closest(".relative.inline-block.text-left") &&
    !isExporting.value
  ) {
    closeDropdown();
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>

<style scoped>
.relative.inline-block.text-left {
  display: inline-block;
  position: relative;
  z-index: 30;
}

.absolute {
  left: 0;
  right: auto !important;
  z-index: 50 !important;
}

button[disabled] {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Responsive positioning for mobile */
@media (max-width: 768px) {
  .absolute {
    left: 0;
    right: auto;
    transform-origin: top left;
  }

  .relative.inline-block.text-left {
    margin-left: 0;
  }
}

/* For very small screens, ensure dropdown doesn't go off-screen */
@media (max-width: 640px) {
  .absolute {
    left: 0;
    right: auto;
    width: 95vw;
    max-width: 280px;
  }
}
</style>
