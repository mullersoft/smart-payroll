@component('mail::message')
    # Welcome, {{ $user->name }}

    Your account has been created in **Smart Payroll System**.

    Here are your login details:

    - **Email:** {{ $user->email }}
    - **Temporary Password:** {{ $password }}

    @component('mail::button', ['url' => $loginUrl])
        Login to Smart Payroll
    @endcomponent

    ⚠️ Please change your password after logging in.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
