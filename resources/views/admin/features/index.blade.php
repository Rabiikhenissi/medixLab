@extends('layouts.admin')

@section('title', __('admin.features.index_title'))

@section('page-title', __('admin.features.manage_title'))
@section('page-subtitle', __('admin.features.page_subtitle'))

@section('header-actions')
    <a href="{{ route('admin.features.create') }}" class="btn-add-exam">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        {{ __('admin.features.add_title') }}
    </a>
@endsection

@section('content')
    <div class="data-section anim anim-1">
        <!-- Table Header -->
        <div class="data-header">
            <div class="data-title">{{ __('admin.features.module_list') }}</div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.features.index') }}" id="filter-form">
            <div class="filters-bar">
                <!-- Search -->
                <div>
                    <span class="filter-label">{{ __('admin.common.quick_search') }}</span>
                    <div class="filter-group" style="position:relative;display:inline-block;">
                        <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                            style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:#94a3b8;pointer-events:none;">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="{{ __('admin.features.search_placeholder') }}" class="filter-input" style="padding-left:36px;">
                    </div>
                </div>

                <!-- Archived toggle -->
                <div style="align-self:flex-end;">
                    <label class="filter-checkbox-wrap">
                        <input type="checkbox" name="show_archived" value="1" {{ $showArchived ? 'checked' : '' }}
                            onchange="document.getElementById('filter-form').submit()">
                        {{ __('admin.common.show_archived') }}
                    </label>
                </div>

                <!-- Filter Button -->
                <div style="align-self:flex-end;">
                    <button type="submit" class="btn-filter">{{ __('common.search') }}</button>
                </div>
            </div>
        </form>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 80px;">{{ __('admin.features.module_order') }}</th>
                    <th>{{ __('admin.features.module_code') }}</th>
                    <th>{{ __('admin.features.module_name') }}</th>
                    <th>{{ __('admin.features.navigation_route') }}</th>
                    <th style="width: 70px; text-align: center;">{{ __('admin.features.icon') }}</th>
                    <th>{{ __('admin.features.actions_associated') }}</th>
                    <th>{{ __('common.status') }}</th>
                    <th style="text-align:right;">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($features as $feature)
                    <tr class="{{ $feature->is_archive ? 'archived' : '' }}">
                        <td>
                            <span class="exam-code"
                                style="color: #475569; background: #f1f5f9;">#{{ $feature->order }}</span>
                        </td>
                        <td>
                            <span class="exam-code" style="color: #6366f1; background: #e0e7ff;">{{ $feature->code }}</span>
                        </td>
                        <td>
                            <div class="exam-name">{{ $feature->name }}</div>
                        </td>
                        <td style="font-family: monospace; font-size: 12px; color: #475569;">
                            {{ $feature->route_name ?? __('admin.features.navigation_route_none') }}
                        </td>
                        <td style="text-align: center; color: #0066ff;">
                            @if ($feature->icon)
                                <div
                                    style="width: 28px; height: 28px; margin: 0 auto; display:flex; align-items:center; justify-content:center;">

                                    <x-dynamic-component :component="'heroicon-o-' . $feature->icon" class="icon-svg" />

                                </div>
                            @else
                                <span style="color:#94a3b8; font-size:12px;">
                                    {{ __('admin.features.empty_icon') }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="category-badge cat-other" style="font-weight: 700;">
                                {{ __('admin.features.actions_count', ['count' => $feature->actions_count]) }}
                            </span>
                        </td>
                        <td>
                            @if ($feature->is_archive)
                                <span class="status-badge status-archived"><span class="dot"></span>{{ __('admin.common.archived') }}</span>
                            @elseif ($feature->is_sidebar)
                                <span class="status-badge status-active"><span class="dot"></span>{{ __('admin.common.active') }}</span>
                            @else
                                <span class="status-badge status-inactive"><span class="dot"></span>{{ __('admin.common.inactive') }}</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                                <!-- Edit Link -->
                                <a href="{{ route('admin.features.edit', $feature) }}" class="table-action-btn"
                                    title="{{ __('common.edit') }}">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </a>

                                <!-- Archive/Restore Form -->
                                <form action="{{ route('admin.features.destroy', $feature) }}" method="POST"
                                    style="display:inline;margin:0;"
                                    onsubmit="return swalConfirmSubmit(this, '{{ $feature->is_archive ? __('admin.features.restore_confirm') : __('admin.features.archive_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="table-action-btn {{ $feature->is_archive ? 'restore-btn' : 'archive-btn' }}"
                                        title="{{ $feature->is_archive ? __('admin.common.restore') : __('admin.common.archive') }}">
                                        @if ($feature->is_archive)
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                            </svg>
                                        @else
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                                @if($feature->is_archive)
                                    <form action="{{ route('admin.features.force-delete', $feature) }}" method="POST" style="display:inline;margin:0;"
                                          onsubmit="return swalConfirmSubmit(this, '{{ __('admin.features.force_delete_confirm') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="table-action-btn delete-btn" title="{{ __('admin.common.force_delete') }}">
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-state-icon">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.34 15.84c-.68-.34-1.16-.94-1.34-1.68m8.32.22c-.68.34-1.34.22-1.9-.3M9 10.5h.008v.008H9V10.5zm6 0h.008v.008H15V10.5zM12 21a9 9 0 110-18 9 9 0 010 18z" />
                                    </svg>
                                </div>
                                <h3>{{ __('admin.features.empty_title') }}</h3>
                                <p>{{ __('admin.features.empty_hint') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if ($features->hasPages())
            <div class="pagination-wrap">
                <div style="display:flex;gap:4px;align-items:center;">
                    @if ($features->onFirstPage())
                        <span
                            style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">«
                            {{ __('admin.common.previous') }}</span>
                    @else
                        <a href="{{ $features->previousPageUrl() }}"
                            style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">«
                            {{ __('admin.common.previous') }}</a>
                    @endif

                    @foreach ($features->getUrlRange(max(1, $features->currentPage() - 2), min($features->lastPage(), $features->currentPage() + 2)) as $page => $url)
                        @if ($page == $features->currentPage())
                            <span
                                style="padding:6px 12px;background:#0066ff;color:white;border-radius:6px;font-size:13px;font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($features->hasMorePages())
                        <a href="{{ $features->nextPageUrl() }}"
                            style="padding:6px 12px;background:white;border:1px solid #e2e8f0;color:#374151;border-radius:6px;font-size:13px;font-weight:500;text-decoration:none;">{{ __('admin.common.next') }}
                            »</a>
                    @else
                        <span
                            style="padding:6px 12px;background:#f1f5f9;color:#94a3b8;border-radius:6px;font-size:13px;cursor:not-allowed;">{{ __('admin.common.next') }}
                            »</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection
