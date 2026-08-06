<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ __('admin.activity.title') }} — Medix eSanté</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; color: #1e293b; font-size: 9px; line-height: 1.4; margin: 0; padding: 0; }
        .header { border-bottom: 3px solid #0066FF; padding-bottom: 10px; margin-bottom: 12px; }
        .brand { font-size: 16px; font-weight: bold; color: #1e293b; }
        .brand-sub { font-size: 9px; color: #64748b; }
        .meta { text-align: right; font-size: 9px; color: #64748b; }
        .meta .ref { font-size: 12px; font-weight: bold; color: #0066FF; }
        .summary { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; margin-bottom: 12px; font-size: 10px; color: #475569; }
        table.logs { width: 100%; border-collapse: collapse; }
        table.logs th { background: #1e293b; color: #ffffff; padding: 6px 8px; font-size: 8px; text-align: left; text-transform: uppercase; letter-spacing: 0.05em; }
        table.logs td { padding: 6px 8px; border-top: 1px solid #e2e8f0; font-size: 9px; vertical-align: top; }
        table.logs tr:nth-child(even) td { background: #f8fafc; }
        .user { font-weight: bold; }
        .system { font-style: italic; color: #94a3b8; }
        .action { font-weight: bold; }
        .action-created { color: #2563eb; }
        .action-updated { color: #b45309; }
        .action-deleted { color: #e11d48; }
        .entity { font-weight: bold; }
        .changes { font-size: 8px; color: #64748b; white-space: pre-wrap; font-family: 'Courier New', monospace; }
        .footer { margin-top: 18px; padding-top: 8px; border-top: 1px solid #e2e8f0; font-size: 8px; color: #94a3b8; }
        .empty { padding: 12px; color: #94a3b8; font-style: italic; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td style="vertical-align:top;">
                    <div class="brand">Medix eSanté</div>
                    <div class="brand-sub">{{ __('admin.activity.pdf_subtitle') }}</div>
                </td>
                <td class="meta" style="vertical-align:top;">
                    <div class="ref">{{ __('admin.activity.exported_on', ['date' => now()->format('d/m/Y H:i')]) }}</div>
                    <div>{{ __('admin.activity.generated_by', ['name' => $generatedBy->first_name . ' ' . $generatedBy->last_name]) }}</div>
                    <div>{{ __('admin.activity.entries_count', ['count' => $logs->count()]) }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if(request()->filled('entity') || request()->filled('action') || request()->filled('search'))
        <div class="summary">
            {{ __('admin.activity.filters_applied') }}
            @if(request()->filled('entity')) {{ __('admin.activity.entity') }} : <strong>{{ request('entity') }}</strong>@endif
            @if(request()->filled('action')) {{ __('admin.activity.action') }} : <strong>{{ request('action') }}</strong>@endif
            @if(request()->filled('search')) {{ __('admin.activity.search') }} : <strong>"{{ request('search') }}"</strong>@endif
        </div>
    @endif

    @if($logs->isEmpty())
        <div class="empty">{{ __('admin.activity.empty_no_match') }}</div>
    @else
        <table class="logs">
            <thead>
                <tr>
                    <th style="width:11%;">{{ __('common.date') }}</th>
                    <th style="width:13%;">{{ __('admin.activity.user') }}</th>
                    <th style="width:9%;">{{ __('admin.activity.role') }}</th>
                    <th style="width:9%;">{{ __('admin.activity.action') }}</th>
                    <th style="width:12%;">{{ __('admin.activity.entity') }}</th>
                    <th style="width:26%;">{{ __('admin.activity.description') }}</th>
                    <th style="width:12%;">{{ __('admin.activity.changes') }}</th>
                    <th style="width:8%;">IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td style="white-space:nowrap;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>
                            @if($log->user)
                                <span class="user">{{ $log->user->first_name }} {{ $log->user->last_name }}</span>
                            @else
                                <span class="system">{{ __('admin.activity.system') }}</span>
                            @endif
                        </td>
                        <td>{{ $log->role ? ucfirst($log->role) : '—' }}</td>
                        <td><span class="action action-{{ $log->action }}">{{ $log->action }}</span></td>
                        <td>
                            <span class="entity">{{ $log->entity_type }}</span>
                            @if($log->entity_id) #{{ $log->entity_id }}@endif
                        </td>
                        <td>{{ $log->description }}</td>
                        <td class="changes">{{ $log->changes ? json_encode($log->changes, JSON_UNESCAPED_UNICODE) : '—' }}</td>
                        <td style="font-family:'Courier New',monospace;">{{ $log->ip_address ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        Medix eSanté — {{ __('admin.activity.footer', ['date' => now()->format('d/m/Y H:i')]) }}
    </div>

</body>
</html>
