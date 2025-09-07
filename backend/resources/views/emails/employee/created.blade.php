@component('mail::message')
# 🎉 Welcome to Smart Payroll, {{ $user->name }}!

We’re excited to let you know that your account has been successfully created in the **Smart Payroll System**.

You can now access your profile, view your payroll history, and manage your information online.

---

### 🔑 Your Login Credentials
- **Email:** {{ $user->email }}
- **Temporary Password:** {{ $password }}

---

@component('mail::button', ['url' => $loginUrl])
👉 Log in to Your Account
@endcomponent

---

### ⚠️ Important Next Step
For your security, please change your password immediately after your first login.

This ensures that only you have access to your account.

If you have any issues signing in, kindly contact the HR or Payroll Department for assistance.

---

Thanks,
**The {{ config('app.name') }} Team**
@endcomponent
