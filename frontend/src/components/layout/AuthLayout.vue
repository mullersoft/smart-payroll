<template>
  <div
    class="min-h-screen flex flex-col justify-center items-center bg-gray-100"
  >
    <main class="w-full max-w-md bg-white rounded-xl shadow-md p-6">
      <slot />
    </main>

    <!-- Optional Footer -->
    <footer class="mt-4 text-gray-500 text-sm text-center">
      &copy; {{ new Date().getFullYear() }} Smart Payroll System
    </footer>
  </div>
</template>

<script setup>
// No need for logic here — this is just layout
</script>
