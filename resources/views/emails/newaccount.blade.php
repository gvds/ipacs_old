@component('mail::message')
# New IPACS Account Notification

Dear {{$username}}

An account has been created for you in IPACS.

Please complete the process by resetting your password.  
You can do this by visiting the [IPACS home page](https://hermes.mb.sun.ac.za/ipacs) 
and clicking on the **'Forgot Your Password or New User'** link.

Thank you  
IPACS Administrator
    
@endcomponent