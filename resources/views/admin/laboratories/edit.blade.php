@extends('layouts.admin')

@section('title', __('admin.laboratories.edit_title'))

@section('page-title', __('admin.laboratories.edit_title'))
@section('page-subtitle', __('admin.laboratories.edit_subtitle'))

@section('content')
    <div class="data-section anim anim-1" style="padding: 28px;">
        <h3 class="data-title" style="margin-top: 0; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-size: 16px;">
            {{ __('admin.laboratories.general_info') }}
        </h3>

        @if($errors->any())
            <div class="form-errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.laboratories.update', $laboratory) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('admin.laboratories.name_label') }}<span class="required-star">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $laboratory->name) }}" required placeholder="{{ __('admin.laboratories.name_placeholder') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('admin.laboratories.city') }}</label>
                    <input type="text" name="city" value="{{ old('city', $laboratory->city) }}" placeholder="{{ __('admin.laboratories.city_placeholder') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('admin.laboratories.country') }}</label>
                    <input type="text" name="country" value="{{ old('country', $laboratory->country) }}" placeholder="{{ __('admin.laboratories.country_placeholder') }}" class="form-control">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row" style="margin-bottom: 16px;">
                <div class="form-group">
                    <label class="form-label">{{ __('admin.laboratories.email_label') }}</label>
                    <input type="email" name="email" value="{{ old('email', $laboratory->email) }}" placeholder="{{ __('admin.laboratories.email_placeholder') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('admin.laboratories.phone_label') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $laboratory->phone) }}" placeholder="{{ __('admin.laboratories.phone_placeholder') }}" class="form-control">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label">{{ __('admin.laboratories.physical_address') }}</label>
                <textarea name="address" rows="3" placeholder="{{ __('admin.laboratories.physical_address_placeholder') }}" class="form-control" style="resize: vertical;">{{ old('address', $laboratory->address) }}</textarea>
            </div>

            <!-- Footer actions -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 24px; margin-top: 24px;">
                <a href="{{ route('admin.laboratories.index') }}" class="btn-cancel">{{ __('common.cancel') }}</a>
                <button type="submit" class="btn-submit">{{ __('admin.laboratories.save_changes') }}</button>
            </div>
        </form>
    </div>
@endsection
