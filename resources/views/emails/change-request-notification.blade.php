@component('mail::message')
# New Change Request Submitted

A new change request has been submitted.

**Title:** {{ $changeRequest->title }}  
**Submitted by:** {{ $changeRequest->user->name }}  
**Status:** {{ ucfirst($changeRequest->status) }}

@component('mail::button', ['url' => route('change-requests.show', $changeRequest->id)])
View Change Request
@endcomponent

Please review this request at your earliest convenience.

Thanks,<br>
{{ config('app.name') }}
@endcomponent