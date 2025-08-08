import { createRouter, createWebHistory } from "vue-router";
import Login from "@/pages/auth/Login.vue";
import Register from "@/pages/auth/Register.vue";
import ForgotPassword from "@/pages/auth/ForgotPassword.vue";
import ResetPassword from "@/pages/auth/ResetPassword.vue";

import AdminDashboard from "@/pages/dashboard/AdminDashboard.vue";
import PreparerDashboard from "@/pages/dashboard/PreparerDashboard.vue";
import ApproverDashboard from "@/pages/dashboard/ApproverDashboard.vue";
import EmployeesPage from "@/pages/employees/EmployeesPage.vue";
import BankAccountsPage from "@/pages/bank-accounts/BankAccountsPage.vue";
import PayrollsPage from "@/pages/payrolls/PayrollsPage.vue";
import MonthlyReports from "@/pages/reports/MonthlyReports.vue";
import TransactionsPage from "@/pages/transactions/TransactionsPage.vue";
  import PendingPayrollsPage from "@/pages/approver/PendingPayrollsPage.vue";



const routes = [
{
  path: "/admin/users",
  component: () => import("@/pages/admin/UsersPage.vue"),
  meta: { requiresAuth: true, role: "admin" },
}
,
{
  path: "/approver/pending-payrolls",
  component: PendingPayrollsPage,
  meta: { requiresAuth: true, role: "approver" },
}
,
{
  path: "/transactions",
  component: TransactionsPage,
  meta: { requiresAuth: true, role: ["admin", "preparer", "approver"] },
},
  {
  
  path: "/reports/monthly",
  component: MonthlyReports,
  meta: { requiresAuth: true, role: "preparer" },
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
    path: "/employees",
    component: EmployeesPage,
    meta: { requiresAuth: true, role: "preparer" },
  },
  {
    path: "/bank-accounts",
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

  // Block logged-in users from visiting guest-only pages
  if (to.meta.guestOnly && isAuth) {
    return next("/");
  }

  // Block non-authenticated users from protected pages
  if (to.meta.requiresAuth && !isAuth) {
    return next("/login");
  }

  // Block users with wrong roles
  if (to.meta.role) {
    const allowedRoles = Array.isArray(to.meta.role) ? to.meta.role : [to.meta.role];
    if (!allowedRoles.includes(auth?.role)) {
      return next("/login");
    }
  }

  return next();
});

export default router;
