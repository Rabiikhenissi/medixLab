<x-legal-layout legalTitle="{{ __('legal.privacy.title') }}" updatedAt="05/08/2026">
    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.privacy.controller_heading') }}</h2>
    <p>
        {{ __('legal.privacy.controller_intro') }} <strong>{{ config('legal.company_name') }}</strong>
        ({{ config('legal.company_country') }}){{ __('legal.privacy.controller_contact') }}
        <a href="mailto:{{ config('legal.company_email') }}" class="text-[#0066FF] font-semibold hover:underline">{{ config('legal.company_email') }}</a>.
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.privacy.data_heading') }}</h2>
    <p>
        {{ __('legal.privacy.data_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.privacy.legal_basis_heading') }}</h2>
    <p>
        {!! __('legal.privacy.legal_basis_desc') !!}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.privacy.retention_heading') }}</h2>
    <p>
        {{ __('legal.privacy.retention_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.privacy.sharing_heading') }}</h2>
    <p>
        {{ __('legal.privacy.sharing_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.privacy.rights_heading') }}</h2>
    <p>
        {!! __('legal.privacy.rights_desc') !!}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.privacy.security_heading') }}</h2>
    <p>
        {{ __('legal.privacy.security_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.privacy.breach_heading') }}</h2>
    <p>
        {{ __('legal.privacy.breach_desc') }}
    </p>
</x-legal-layout>
