@extends('layouts.admin')

@section('title', __('admin.gdpr.title'))

@section('page-title', __('admin.gdpr.title'))
@section('page-subtitle', __('admin.gdpr.page_subtitle'))

@section('content')
    <div class="data-section anim anim-1">
        <div class="data-header">
            <div class="data-title">{{ __('admin.gdpr.user_accounts') }}</div>
            <a href="{{ route('admin.gdpr.incidents') }}" class="btn-export">{{ __('admin.gdpr.incidents_link') }} &rarr;</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.gdpr') }}" id="filter-form">
            <div class="filters-bar">
                <div>
                    <span class="filter-label">{{ __('common.search') }}</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.gdpr.search_placeholder') }}" class="filter-input">
                    </div>
                </div>
                <div style="align-self:flex-end;">
                    <button type="submit" class="btn-filter">{{ __('admin.common.filter') }}</button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('admin.gdpr.name') }}</th>
                    <th>{{ __('common.email') }}</th>
                    <th>{{ __('admin.gdpr.roles') }}</th>
                    <th>{{ __('admin.gdpr.registered_on') }}</th>
                    <th>{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>#{{ $user->id }}</td>
                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roles = [];
                                if ($user->patient) $roles[] = __('admin.gdpr.role_patient');
                                if ($user->doctor) $roles[] = __('admin.gdpr.role_doctor');
                                if ($user->staff) $roles[] = __('admin.gdpr.role_staff');
                            @endphp
                            @forelse($roles as $role)
                                <span class="feature-badge">{{ $role }}</span>
                            @empty
                                <span class="text-muted">{{ __('admin.gdpr.no_profile') }}</span>
                            @endforelse
                        </td>
                        <td>{{ optional($user->created_at)->format('d/m/Y H:i') }}</td>
                        <td>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <a class="btn-export" href="{{ route('admin.gdpr.export', $user) }}" target="_blank">{{ __('admin.gdpr.export_json') }}</a>

                                <form method="POST" action="{{ route('admin.gdpr.erase', $user) }}" onsubmit="return confirm('{{ __('admin.gdpr.erase_confirm') }}')" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="confirm" value="1">
                                    <button type="submit" class="btn-erase">{{ __('admin.gdpr.anonymize') }}</button>
                                </form>

                                <form method="POST" action="{{ route('admin.gdpr.erase', $user) }}" onsubmit="return confirm('{{ __('admin.gdpr.total_erase_confirm') }}')" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="confirm" value="1">
                                    <input type="hidden" name="hard" value="1">
                                    <button type="submit" class="btn-erase-hard">{{ __('admin.gdpr.total_erase') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('admin.gdpr.no_users') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->hasPages())
            <div class="pagination-wrap">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <div class="data-section anim anim-2">
        <div class="data-header">
            <div class="data-title">{{ __('admin.gdpr.dpia_title') }}</div>
        </div>
        <p style="font-size:13px;color:#475569;line-height:1.7;margin:0;">
            {!! __('admin.gdpr.dpia') !!}
        </p>
    </div>
@endsection

@section('styles')
    <style>
        .btn-export {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }
        .btn-export:hover { background: #e0e7ff; }
        .btn-erase {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }
        .btn-erase:hover { background: #ffedd5; }
        .btn-erase-hard {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .btn-erase-hard:hover { background: #fee2e2; }
        .feature-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            background: #f0f9ff;
            color: #0369a1;
            border: 1px solid #bae6fd;
            margin-right: 4px;
        }
        .alert-success { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; }
        .alert-danger { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; }
        .text-muted { color: #94a3b8; }
    </style>
@endsection
