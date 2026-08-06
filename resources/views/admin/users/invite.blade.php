@extends('layouts.admin')

@section('title', __('admin.users.invite_title'))

@section('page-title', __('admin.users.invite_page_title'))
@section('page-subtitle', __('admin.users.invite_subtitle'))

@section('content')
    <div class="data-section anim anim-1" style="padding: 28px;">
        <h3 class="data-title" style="margin-top: 0; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-size: 16px;">
            {{ __('admin.users.invite_details') }}
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

        <form action="{{ route('admin.users.invite.store') }}" method="POST">
            @csrf

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('auth.first_name') }}</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" placeholder="{{ __('admin.users.first_name_placeholder') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('auth.last_name') }}</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" placeholder="{{ __('admin.users.last_name_placeholder') }}" class="form-control">
                </div>
            </div>

            <!-- Form Row -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">{{ __('admin.users.email_label') }}<span class="required-star">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="{{ __('admin.users.email_placeholder') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">{{ __('admin.users.group_label') }}<span class="required-star">*</span></label>
                    <div style="position:relative;">
                        <select name="group_id" id="group_id" required class="form-control" onchange="toggleLabField()">
                            <option value="">{{ __('admin.users.select_group') }}</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}" data-code="{{ $group->code }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>
            </div>

            <!-- Laboratory Select (Conditional) -->
            <div class="form-group" id="laboratory-group" style="display: none; margin-bottom: 16px;">
                <label class="form-label">{{ __('admin.users.associated_laboratory') }}<span class="required-star">*</span></label>
                <div style="position:relative;">
                    <select name="laboratory_id" id="laboratory_id" class="form-control">
                        <option value="">{{ __('admin.users.select_laboratory') }}</option>
                        @foreach($laboratories as $labo)
                            <option value="{{ $labo->id }}" {{ old('laboratory_id') == $labo->id ? 'selected' : '' }}>{{ $labo->name }} ({{ $labo->city }})</option>
                        @endforeach
                    </select>
                    <svg style="position:absolute;right:12px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#94a3b8;pointer-events:none;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </div>
            </div>

            <div class="info-box" style="display:flex;gap:12px;align-items:flex-start;background:#eff6ff;border:1px solid #dbeafe;border-radius:12px;padding:14px 16px;margin-bottom:24px;">
                <svg style="width:18px;height:18px;color:#2563eb;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                <div style="font-size:13px;color:#1e40af;line-height:1.6;">
                    @lang('admin.users.invite_info_1')
                    <strong>{{ config('legal.invite_days') }} {{ __('admin.users.invite_days') }}</strong>.
                    @lang('admin.users.invite_info_2')
                </div>
            </div>

            <!-- Footer actions -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 24px; margin-top: 24px;">
                <a href="{{ route('admin.users.index') }}" class="btn-cancel">{{ __('common.cancel') }}</a>
                <button type="submit" class="btn-submit">{{ __('admin.users.send_invitation') }}</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        function toggleLabField() {
            var groupSelect = document.getElementById('group_id');
            var labGroup = document.getElementById('laboratory-group');
            var labSelect = document.getElementById('laboratory_id');

            if (!groupSelect) return;

            var selectedOption = groupSelect.options[groupSelect.selectedIndex];
            var code = selectedOption ? selectedOption.getAttribute('data-code') : '';

            if (code === 'center') {
                labGroup.style.display = 'flex';
                labSelect.setAttribute('required', 'required');
            } else {
                labGroup.style.display = 'none';
                labSelect.removeAttribute('required');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleLabField();
        });
    </script>
@endsection
