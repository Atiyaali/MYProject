
<x-mail::message>
    {{ $batch->compain->subject }}
   {!! $bodyContent !!}

Welcome to our platform! 🎉

We’re glad to have you onboard.
You can start exploring right away.

{{-- ![Welcome Banner]({{ asset('storage/' . $participant->banner_image) }}) --}}

{{-- <img src="cid:{{ $bannerCid }}" alt="Welcome Banner" style="width:100%; max-width:600px; margin-bottom:20px;"> --}}

<x-mail::button :url="url('/')">
Go to Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
