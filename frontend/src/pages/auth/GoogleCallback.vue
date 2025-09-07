<template>
  <div class="flex items-center justify-center min-h-screen">
    <p class="text-gray-700 dark:text-gray-200">Logging in with Google...</p>
  </div>
</template>

<script setup>
import api from "@/services/api";
import { useAuthStore } from "@/store/auth";
import { onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useToast } from "vue-toastification";

const toast = useToast();
const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

onMounted(async () => {
  const token = route.query.token;

  if (!token) {
    router.push("/login");
    return;
  }

  try {
    api.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    const { data } = await api.get("/me");

    authStore.setAuth(token, data.user);

    // 🔑 Check status first
    if (data.user.status === "pending") {
      toast.info("Your account is pending approval.");
      router.push("/PendingProfile");
      return;
    }

    if (data.user.status === "deactivated") {
      toast.error("Your account has been deactivated. Contact support.");
      router.push("/login");
      return;
    }

    // Active users get role-based redirect
    if (data.user.role === "admin") router.push("/admin");
    else if (data.user.role === "preparer") router.push("/preparer");
    else if (data.user.role === "approver") router.push("/approver");
    else  router.push("/employee-dashboard");
  } catch (err) {
    console.error("Google login failed", err);
    toast.error("Google login failed. Please try again.");
    router.push("/login");
  }
});
</script>
