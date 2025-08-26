<template>
  <div class="flex items-center justify-center min-h-screen">
    <p class="text-gray-700 dark:text-gray-200">Logging in with Google...</p>
  </div>
</template>
<!-- <script setup>
import { onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useAuthStore } from "@/store/auth";
import api from "@/services/api";

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

onMounted(async () => {
  const token = route.query.token;
  const email = route.query.email;

  if (token) {
    try {
      // Attach token globally
      api.defaults.headers.common["Authorization"] = `Bearer ${token}`;

      // Option 1: Use the /me endpoint
      const { data } = await api.get("/me");

      // Save token & user
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
</script> -->
<script setup>
import { onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import api from "@/services/api";
import { useAuthStore } from "@/store/auth";
import { useToast } from "vue-toastification";
const toast = useToast();
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

onMounted(async () => {
  const token = route.query.token;

  if (token) {
    try {
      api.defaults.headers.common["Authorization"] = `Bearer ${token}`;

      const { data } = await api.get("/me");

      authStore.setAuth(token, data.user);

      // 🚦 Role-based redirects
      if (data.user.role === "admin") router.push("/admin");
      else if (data.user.role === "preparer") router.push("/preparer");
      else if (data.user.role === "approver") router.push("/approver");
      else router.push("/profile"); // 👈 pending users land here
    } catch (err) {
      console.error("Google login failed", err);
      toast.error("❌ Google login failed. Please try again.");
      router.push("/login");
    }
  } else {
    router.push("/login");
  }
});

</script>
