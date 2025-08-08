import { defineStore } from "pinia";
import api from "@/services/api";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || null,
    token: localStorage.getItem("token") || null,
  }),

  actions: {
    async login(email, password) {
      const res = await api.post("/login", { email, password });

      this.user = res.data.user;
      this.token = res.data.token;

      api.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;

      localStorage.setItem("user", JSON.stringify(this.user));
      localStorage.setItem("token", this.token);
    },

    logout() {
      this.user = null;
      this.token = null;
      delete api.defaults.headers.common["Authorization"];
      localStorage.removeItem("user");
      localStorage.removeItem("token");
    },
  },
});
