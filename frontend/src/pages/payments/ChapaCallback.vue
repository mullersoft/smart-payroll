<template>
  <MainLayout>
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6 space-y-4">
      <h1 class="text-xl font-semibold">Chapa Payment</h1>
      <p v-if="loading">Verifying your payment… please wait.</p>
      <div v-else>
        <div v-if="ok" class="text-green-700">
          ✅ Payment verified successfully.
        </div>
        <div v-else class="text-red-700">
          ❌ Payment verification failed. {{ errorMessage }}
        </div>
        <router-link
          to="/payrolls"
          class="inline-block mt-4 bg-indigo-600 text-white px-4 py-2 rounded"
        >
          Back to Payrolls
        </router-link>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import api from "@/services/api";
import MainLayout from "@/components/layout/MainLayout.vue";

const route = useRoute();
const ok = ref(false);
const loading = ref(true);
const errorMessage = ref("");

onMounted(async () => {
  // Chapa usually appends tx_ref as a query or you pass it yourself
  const txRef = route.query.tx_ref || route.params.tx_ref;
  if (!txRef) {
    loading.value = false;
    ok.value = false;
    errorMessage.value = "Missing tx_ref";
    return;
  }

  try {
    const res = await api.get(`/payments/chapa/verify/${txRef}`);
    ok.value = true;
  } catch (e) {
    ok.value = false;
    errorMessage.value = e.response?.data?.error || "Verification failed";
  } finally {
    loading.value = false;
  }
});
</script>
