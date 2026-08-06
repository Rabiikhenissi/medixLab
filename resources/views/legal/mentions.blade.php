<x-legal-layout legalTitle="{{ __('legal.mentions.title') }}" updatedAt="05/08/2026">
    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.mentions.editor_heading') }}</h2>
    <p>
        <strong>{{ config('legal.company_name') }}</strong><br>
        {{ __('legal.mentions.editor_country', ['country' => config('legal.company_country')]) }}<br>
        {{ __('legal.mentions.editor_contact') }} <a href="mailto:{{ config('legal.company_email') }}" class="text-[#0066FF] font-semibold hover:underline">{{ config('legal.company_email') }}</a>
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.mentions.publisher_heading') }}</h2>
    <p>
        {{ __('legal.mentions.publisher_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.mentions.hosting_heading') }}</h2>
    <p>
        {{ __('legal.mentions.hosting_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.mentions.ip_heading') }}</h2>
    <p>
        {{ __('legal.mentions.ip_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.mentions.cookies_heading') }}</h2>
    <p>
        {{ __('legal.mentions.cookies_desc') }}
    </p>
</x-legal-layout>
