<template>
  <div class="flex items-center justify-center min-h-screen">
    <p class="text-gray-700 dark:text-gray-200">Logging in with Google...</p>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/services/api";
import { useAuthStore } from "@/store/auth";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

onMounted(async () => {
  const token = route.query.token;

  if (token) {
    try {
      // Attach token to axios for authenticated requests
      api.defaults.headers.common["Authorization"] = `Bearer ${token}`;

      // Fetch the full user info (including role)
      const { data } = await api.get("/me");

      // Save to Pinia + localStorage
      authStore.setAuth(token, data.user);

      // Redirect based on role
      if (data.user.role === "admin") router.push("/admin");
      else if (data.user.role === "preparer") router.push("/preparer");
      else if (data.user.role === "approver") router.push("/approver");
      else router.push("/");
    } catch (err) {
      console.error("Google login failed", err);
      router.push("/login");
    }
  } else {
    router.push("/login");
  }
});
</script>
