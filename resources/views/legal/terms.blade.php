<x-legal-layout legalTitle="{{ __('legal.terms.title') }}" updatedAt="05/08/2026">
    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.terms.object_heading') }}</h2>
    <p>
        {{ __('legal.terms.object_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.terms.account_heading') }}</h2>
    <p>
        {{ __('legal.terms.account_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.terms.usage_heading') }}</h2>
    <p>
        {{ __('legal.terms.usage_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.terms.health_data_heading') }}</h2>
    <p>
        {{ __('legal.terms.health_data_desc') }} <a href="{{ route('legal.privacy') }}" class="text-[#0066FF] font-semibold hover:underline">{{ __('legal.terms.privacy_link') }}</a>.
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.terms.liability_heading') }}</h2>
    <p>
        {{ __('legal.terms.liability_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.terms.termination_heading') }}</h2>
    <p>
        {{ __('legal.terms.termination_desc') }}
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">{{ __('legal.terms.governing_law_heading') }}</h2>
    <p>
        {{ __('legal.terms.governing_law_desc') }}
    </p>
</x-legal-layout>
