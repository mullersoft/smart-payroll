import ForgotPassword from "@/pages/auth/ForgotPassword.vue";
import Login from "@/pages/auth/Login.vue";
import Register from "@/pages/auth/Register.vue";
import ResetPassword from "@/pages/auth/ResetPassword.vue";
import { createRouter, createWebHistory } from "vue-router";
import BankAccountsPage from "@/pages/bank-accounts/BankAccountsPage.vue";
import AdminDashboard from "@/pages/dashboard/AdminDashboard.vue";
import ApproverDashboard from "@/pages/dashboard/ApproverDashboard.vue";
import PreparerDashboard from "@/pages/dashboard/PreparerDashboard.vue";
import EmployeesSection from "@/pages/employees/EmployeesSection.vue";
import PayrollsPage from "@/pages/payrolls/PayrollsPage.vue";
import TransactionsPage from "@/pages/transactions/TransactionsPage.vue";

const routes = [
  {
    path: "/admin/users",
    component: () => import("@/pages/admin/UsersPage.vue"),
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/profile",
    name: "Profile",
    component: () => import("@/pages/auth/Profile.vue"),
    meta: { requiresAuth: true },
  },

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

  {
    path: "/employees",
    name: "EmployeesSection",
    component: EmployeesSection,
    meta: { requiresAuth: true, roles: ["preparer"] },
  },

  {
    path: "/transactions",
    component: TransactionsPage,
    meta: { requiresAuth: true, role: ["admin", "preparer", "approver"] },
  },

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
  {
    path: "/admin",
    name: "AdminDashboard",
    component: AdminDashboard,
    meta: { requiresAuth: true, role: "admin" },
  },
  {
    path: "/preparer",
    name: "PreparerDashboard",
    component: PreparerDashboard,
    meta: { requiresAuth: true, role: "preparer" },
  },
  {
    path: "/approver",
    name: "ApproverDashboard",
    component: ApproverDashboard,
    meta: { requiresAuth: true, role: "approver" },
  },

  {
    path: "/accounts",
    component: BankAccountsPage,
    meta: { requiresAuth: true, role: "preparer" },
  },
  ,
  {
    path: "/payrolls",
    component: PayrollsPage,
    meta: { requiresAuth: true, role: "preparer" },
  },
  {
    path: "/",
    redirect: () => {
      const storedUser = JSON.parse(localStorage.getItem("user"));
      if (!storedUser) return "/login";
      if (storedUser.role === "admin") return "/admin";
      if (storedUser.role === "preparer") return "/preparer";
      if (storedUser.role === "approver") return "/approver";
      return "/login";
    },
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const auth = JSON.parse(localStorage.getItem("user"));
  const token = localStorage.getItem("token");

  const isAuth = !!auth && !!token;

  // Block logged-in users from visiting guest-only pages(vs code recompensation)
  if (to.meta.guestOnly && isAuth) {
    return next("/");
  }
  // try {
  //   // Refresh token expiration on each navigation
  //   const tokenData = JSON.parse(atob(token.split(".")[1]));
  //   const exp = tokenData.exp * 1000; // Convert to milliseconds
  //   const now = Date.now();
  //   if (exp > now) {
  //     // Token is still valid, refresh it by updating the timestamp
  //     localStorage.setItem("token", token);
  //   }
  // } catch (error) {
  //   // Invalid token, log out the user
  //   localStorage.removeItem("user");
  //   localStorage.removeItem("token");
  //   return next("/login");
  // }

  // Block non-authenticated users from protected pages
  if (to.meta.requiresAuth && !isAuth) {
    return next("/login");
  }

  // Block users with wrong roles
  if (to.meta.role) {
    const allowedRoles = Array.isArray(to.meta.role)
      ? to.meta.role
      : [to.meta.role];
    if (!allowedRoles.includes(auth?.role)) {
      return next("/login");
    }
  }

  return next();
});

export default router;
