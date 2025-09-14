// src/services/api.js
import axios from "axios";

const api = axios.create({
  baseURL: "https://smart-payroll-api.onrender.com/api",

  // baseURL: import.meta.env.VITE_API_URL,
  headers: {
    Accept: "application/json",
  },
  withCredentials: true, //
});

// Automatically include token if available
api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
