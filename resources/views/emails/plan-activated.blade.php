<x-mail::message>
# Plan Activated Successfully

Hi {{ $event->user->name }},

We're excited to let you know that the plan for your event **"{{ $event->title }}"** has been successfully activated to the **{{ ucfirst($event->pricing_plan) }}** plan.

@if($event->pricing_plan === 'unlimited')
You now have unlimited entries for this event! You can freely add as many chandla entries as you need without any restrictions.
@elseif($event->pricing_plan === 'payg')
Your event is now on the Pay-as-you-go plan.
@endif

<x-mail::button :url="route('client.events.show', $event->id)">
View Your Event
</x-mail::button>

Thank you for choosing Chandla Book!

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
