<template>
  <div
    v-if="transaction"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-96">
      <h2 class="text-lg font-bold mb-4">Transaction Details</h2>
      <p><strong>ID:</strong> {{ transaction.id }}</p>
      <p><strong>Type:</strong> {{ transaction.transaction_type }}</p>
      <p><strong>Status:</strong> {{ transaction.status }}</p>
      <p><strong>Amount:</strong> Birr {{ transaction.amount }}</p>
      <p><strong>Debited Account:</strong> {{ transaction.from_account }}</p>
      <p><strong>Credited Account:</strong> {{ transaction.to_account }}</p>
      <p>
        <strong>Sender:</strong>
        {{ transaction.fromBankAccount?.owner_name || "—" }}
      </p>
      <p>
        <strong>Receiver:</strong>
        {{
          transaction.toBankAccount?.owner_name ||
          transaction.employee_name ||
          "—"
        }}
      </p>
      <p><strong>Processed By:</strong> {{ transaction.processed_by }}</p>
      <p>
        <strong>Transaction Time:</strong>
        {{
          new Date(
            transaction.transaction_date || transaction.created_at
          ).toLocaleString()
        }}
      </p>
      <p>
        <strong>Failure Reason:</strong> {{ transaction.failure_reason || "—" }}
      </p>

      <div class="mt-4 text-right">
        <button
          @click="$emit('close')"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Close
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  transaction: Object,
});
defineEmits(["close"]);
</script>
