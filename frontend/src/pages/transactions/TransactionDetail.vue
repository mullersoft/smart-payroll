<template>
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
      <p>
        <strong>Debited Account:</strong> {{ selectedTransaction.from_account }}
      </p>
      <p>
        <strong>Credited Account:</strong> {{ selectedTransaction.to_account }}
      </p>
      <p>
        <strong>Sender:</strong>
        {{ selectedTransaction.fromBankAccount?.owner_name || "—" }}
      </p>
      <p>
        <strong>Receiver:</strong>
        {{
          selectedTransaction.toBankAccount?.owner_name ||
          selectedTransaction.employee_name ||
          "—"
        }}
      </p>
      <p>
        <strong>Processed By:</strong> {{ selectedTransaction.processed_by }}
      </p>
      <p>
        <strong>Transaction Time:</strong>
        {{
          new Date(
            selectedTransaction.transaction_date ||
              selectedTransaction.created_at
          ).toLocaleString()
        }}
      </p>
      <p>
        <strong>Failure Reason:</strong>
        {{ selectedTransaction.failure_reason || "—" }}
      </p>

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
</template>
<script></script>
