@extends('layouts.admin')

@section('title', __('admin.available_exams.edit_title'))

@section('page-title')
{{ __('admin.available_exams.edit_prefix') }}<span style="color:#0066ff;">{{ __('admin.available_exams.title') }}</span>
@endsection

@section('content')

@if ($errors->any())
    <div class="form-errors">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="data-section anim anim-1" style="max-width:600px;">
    <div style="padding:28px;">
        <form action="{{ route('admin.available-exams.update', $availableExam) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label">{{ __('admin.common.laboratory') }}</label>
                <input type="text" class="form-control" value="{{ $availableExam->labo->name }}" readonly style="background:#f1f5f9; cursor:not-allowed;">
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('admin.available_exams.exam') }}</label>
                <input type="text" class="form-control" value="[{{ $availableExam->exam->code }}] {{ $availableExam->exam->name }}" readonly style="background:#f1f5f9; cursor:not-allowed;">
            </div>

            <div class="form-group">
                <label class="form-label">{{ __('admin.available_exams.price') }} <span class="required-star">*</span></label>
                <input type="number" name="price" class="form-control" value="{{ old('price', $availableExam->price) }}" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $availableExam->is_active) ? 'checked' : '' }} style="accent-color:#0066ff; width:16px; height:16px;">
                    <span class="form-label" style="margin:0;">{{ __('admin.common.active') }}</span>
                </label>
            </div>

            <div style="display:flex; gap:10px; margin-top:24px;">
                <button type="submit" class="btn-submit">{{ __('admin.common.update') }}</button>
                <a href="{{ route('admin.available-exams.index') }}" class="btn-cancel">{{ __('common.cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
