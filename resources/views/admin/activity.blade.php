@extends('layouts.admin')

@section('title', __('admin.activity.title'))

@section('page-title', __('admin.activity.title'))
@section('page-subtitle', __('admin.activity.page_subtitle'))

@section('header-actions')
    <a href="{{ route('admin.activity.export', request()->query()) }}" class="btn-add-exam" style="background:#0D9488;">
        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
        </svg>
        {{ __('admin.activity.export_pdf') }}
    </a>
@endsection

@section('content')
    <div class="data-section anim anim-1">
        <div class="data-header">
            <div class="data-title">{{ __('admin.activity.actions_log') }}</div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.activity') }}" id="filter-form">
            <div class="filters-bar">
                <div>
                    <span class="filter-label">{{ __('admin.activity.entity') }}</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <select name="entity" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                            <option value="">{{ __('admin.activity.all_entities') }}</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity }}" {{ request('entity') == $entity ? 'selected' : '' }}>{{ $entity }}</option>
                            @endforeach
                        </select>
                        <svg class="select-arrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>

                <div>
                    <span class="filter-label">{{ __('admin.activity.action') }}</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <select name="action" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                            <option value="">{{ __('admin.activity.all_actions') }}</option>
                            @foreach(['created' => __('admin.activity.action_created'), 'updated' => __('admin.activity.action_updated'), 'deleted' => __('admin.activity.action_deleted'), 'restored' => __('admin.activity.action_restored')] as $code => $label)
                                <option value="{{ $code }}" {{ request('action') == $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <svg class="select-arrow" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>

                <div>
                    <span class="filter-label">{{ __('admin.common.quick_search') }}</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.activity.search_description_ip') }}" class="filter-input">
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
                    <th>{{ __('common.date') }}</th>
                    <th>{{ __('admin.activity.user') }}</th>
                    <th>{{ __('admin.activity.action') }}</th>
                    <th>{{ __('admin.activity.entity') }}</th>
                    <th>{{ __('admin.activity.description') }}</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap;color:#64748b;font-size:12px;">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            @if($log->user)
                                <div style="font-weight:500;color:#1e293b;font-size:13px;">{{ $log->user->first_name }} {{ $log->user->last_name }}</div>
                                <div style="font-size:11px;color:#94a3b8;">{{ $log->role ? ucfirst($log->role) : '—' }}</div>
                            @else
                                <span class="category-badge cat-other">{{ __('admin.activity.system') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($log->action === 'created')
                                <span class="status-badge status-active" style="background:#eff6ff;color:#2563eb;"><span class="dot" style="background:#2563eb;"></span>{{ __('admin.activity.action_created') }}</span>
                            @elseif($log->action === 'updated')
                                <span class="status-badge" style="background:#fffbeb;color:#b45309;"><span class="dot" style="background:#f59e0b;"></span>{{ __('admin.activity.action_updated') }}</span>
                            @elseif($log->action === 'deleted')
                                <span class="status-badge" style="background:#fff1f2;color:#e11d48;"><span class="dot" style="background:#ef4444;"></span>{{ __('admin.activity.action_deleted') }}</span>
                            @elseif($log->action === 'restored')
                                <span class="status-badge status-archived"><span class="dot"></span>{{ __('admin.activity.action_restored') }}</span>
                            @else
                                <span class="status-badge status-archived"><span class="dot"></span>{{ ucfirst($log->action) }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $entityLabels = [
                                    'User' => __('admin.activity.entity_user'),
                                    'ExamRequest' => __('admin.activity.entity_exam_request'),
                                    'ExamRequestItem' => __('admin.activity.entity_exam_request_item'),
                                    'Sample' => __('admin.activity.entity_sample'),
                                    'ResultLabo' => __('admin.activity.entity_result'),
                                    'ResultLaboDetail' => __('admin.activity.entity_result_detail'),
                                    'MachineConfiguration' => __('admin.activity.entity_machine_config'),
                                    'ExamParameter' => __('admin.activity.entity_exam_parameter'),
                                    'DoctorPatientAccess' => __('admin.activity.entity_doctor_access'),
                                ];
                            @endphp
                            <span class="exam-code">{{ $entityLabels[$log->entity_type] ?? $log->entity_type }}</span>
                            @if($log->entity_id)
                                <span style="color:#94a3b8;font-size:12px;">#{{ $log->entity_id }}</span>
                            @endif
                        </td>
                        <td style="max-width:340px;">
                            <div style="font-size:13px;color:#374151;">{{ $log->description }}</div>
                            @if($log->changes)
                                <details style="margin-top:4px;">
                                    <summary style="font-size:11px;color:#0066ff;cursor:pointer;">{{ __('admin.activity.changes_details') }}</summary>
                                    <pre style="font-size:11px;background:#f8fafc;border:1px solid #e8eef4;border-radius:8px;padding:10px;margin:6px 0 0;overflow:auto;max-height:180px;color:#475569;font-family:'SF Mono','Consolas',monospace;">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            @endif
                        </td>
                        <td style="font-size:12px;color:#94a3b8;font-family:'SF Mono','Consolas',monospace;">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3>{{ __('admin.activity.empty') }}</h3>
                                <p>{{ __('admin.activity.empty_hint') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="pagination-wrap">
                <div style="display:flex;gap:4px;align-items:center;">
                    @if($logs->onFirstPage())
                        <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">« {{ __('admin.common.previous') }}</span>
                    @else
                        <a href="{{ $logs->previousPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">« {{ __('admin.common.previous') }}</a>
                    @endif

                    @foreach($logs->getUrlRange(max(1, $logs->currentPage()-2), min($logs->lastPage(), $logs->currentPage()+2)) as $page => $url)
                        @if($page == $logs->currentPage())
                            <span style="padding:6px 12px;background:#0066ff;color:white;border-radius:6px;font-size:13px;font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($logs->hasMorePages())
                        <a href="{{ $logs->nextPageUrl() }}" style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">{{ __('admin.common.next') }} »</a>
                    @else
                        <span style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">{{ __('admin.common.next') }} »</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
