import { createRouter, createWebHistory } from "vue-router";

// ---- Auth Pages ----
import ForgotPassword from "@/pages/auth/ForgotPassword.vue";
import Login from "@/pages/auth/Login.vue";
import Register from "@/pages/auth/Register.vue";
import ResetPassword from "@/pages/auth/ResetPassword.vue";

// ---- Dashboards ----
import AdminDashboard from "@/pages/dashboard/AdminDashboard.vue";
import ApproverDashboard from "@/pages/dashboard/ApproverDashboard.vue";
import PreparerDashboard from "@/pages/dashboard/PreparerDashboard.vue";

// ---- Admin Pages ----
import BankAccountsPage from "@/pages/bank-accounts/BankAccountsPage.vue";
import EmployeesSection from "@/pages/employees/EmployeesSection.vue";

// ---- Other Pages ----
import PayrollsPage from "@/pages/payrolls/PayrollsPage.vue";
import TransactionsPage from "@/pages/transactions/TransactionsPage.vue";

const routes = [
  // --------------------
  // Admin
  // --------------------
  {
    path: "/admin",
    name: "AdminDashboard",
    component: AdminDashboard,
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/users",
    component: () => import("@/pages/admin/UsersPage.vue"),
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/employees",
    name: "EmployeesSection",
    component: EmployeesSection,
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/admin/accounts",
    component: BankAccountsPage,
    meta: { requiresAuth: true, role: "admin" },
  },

  // --------------------
  // Preparer
  // --------------------
  {
    path: "/preparer",
    name: "PreparerDashboard",
    component: PreparerDashboard,
    meta: { requiresAuth: true, role: "preparer" },
  },
  {
    path: "/preparer/payrolls",
    component: PayrollsPage,
    meta: { requiresAuth: true, role: "preparer" },
  },

  // --------------------
  // Approver
  // --------------------
  {
    path: "/approver",
    name: "ApproverDashboard",
    component: ApproverDashboard,
    meta: { requiresAuth: true, role: "approver" },
  },

  // --------------------
  // Shared (Preparer + Approver)
  // --------------------
  {
    path: "/transactions",
    component: TransactionsPage,
    meta: { requiresAuth: true, role: ["preparer", "approver"] },
  },

  // --------------------
  // Auth Pages (guest only)
  // --------------------
  {
    path: "/login",
    name: "Login",
    component: Login,
    meta: { guestOnly: true },
  },
  {
    path: "/register",
    name: "Register",
    component: Register,
    meta: { guestOnly: true },
  },
  {
    path: "/forgot-password",
    name: "ForgotPassword",
    component: ForgotPassword,
    meta: { guestOnly: true },
  },
  {
    path: "/reset-password",
    name: "ResetPassword",
    component: ResetPassword,
    meta: { guestOnly: true },
  },

  // --------------------
  // User Profile
  // --------------------
  {
    path: "/profile",
    name: "Profile",
    component: () => import("@/pages/auth/Profile.vue"),
    meta: { requiresAuth: true },
  },

  // --------------------
  // External Callbacks
  // --------------------
  {
    path: "/chapa/callback",
    name: "ChapaCallback",
    component: () => import("@/pages/payments/ChapaCallback.vue"),
  },
  {
    path: "/auth/callback",
    name: "GoogleCallback",
    component: () => import("@/pages/auth/GoogleCallback.vue"),
  },

  // --------------------
  // Root Redirect
  // --------------------
  {
    path: "/",
    redirect: () => {
      const storedUser = JSON.parse(localStorage.getItem("user"));
      if (!storedUser) return "/login";
      if (storedUser.role === "admin") return "/admin";
      if (storedUser.role === "preparer") return "/preparer";
      if (storedUser.role === "approver") return "/approver";
      if (storedUser.status === "pending") return "/profile";
      return "/login";
    },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

// --------------------
// 🔐 Navigation Guards
// --------------------
router.beforeEach((to, from, next) => {
  const auth = JSON.parse(localStorage.getItem("user"));
  const token = localStorage.getItem("token");
  const isAuth = !!auth && !!token;

  // Guest-only pages
  if (to.meta.guestOnly && isAuth) {
    return next("/");
  }

  // Auth-required pages
  if (to.meta.requiresAuth && !isAuth) {
    return next("/login");
  }

  // Role checks
  if (to.meta.role) {
    const allowedRoles = Array.isArray(to.meta.role)
      ? to.meta.role
      : [to.meta.role];
    if (!allowedRoles.includes(auth?.role)) {
      return next("/login");
    }
  }

  next();
});

export default router;
