// frontend/src/store/theme.js
import { ref, watch } from "vue";

export const theme = ref(localStorage.getItem("theme") || "light");

export const toggleTheme = () => {
  theme.value = theme.value === "light" ? "dark" : "light";
};

watch(
  theme,
  (newTheme) => {
    localStorage.setItem("theme", newTheme);
    if (newTheme === "dark") {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
  },
  { immediate: true }
);
