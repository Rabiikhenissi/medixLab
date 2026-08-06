@php
    $tourUser = auth()->user();
    $tourSteps = \App\Services\TourService::stepsFor($tourUser);
    $tourAutostart = \App\Services\TourService::shouldAutostart($tourUser, request()->route()?->getName());
    $tourUi = \App\Services\TourService::uiStrings();
    $tourJsonFlags = JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
@endphp

@if (count($tourSteps) > 0)
    <div id="medix-tour-root"
         data-steps="{{ json_encode($tourSteps, $tourJsonFlags) }}"
         data-ui="{{ json_encode($tourUi, $tourJsonFlags) }}"
         data-autostart="{{ $tourAutostart ? '1' : '0' }}"
         data-complete-url="{{ route('tour.complete') }}"
         data-role="{{ $tourUser?->group?->code }}"
         hidden></div>

    <button id="medix-tour-replay" type="button" title="{{ $tourUi['replay'] }}" aria-label="{{ $tourUi['replay'] }}">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/>
        </svg>
    </button>

    <style>
        #medix-tour-replay {
            position: fixed;
            right: 24px;
            bottom: 96px;
            z-index: 10000;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0066FF, #0088FF);
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 28px rgba(0, 102, 255, 0.35);
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        #medix-tour-replay:hover {
            background: linear-gradient(135deg, #0052CC, #0066CC);
            transform: scale(1.08);
            box-shadow: 0 12px 36px rgba(0, 102, 255, 0.45);
        }

        #medix-tour-host {
            display: none;
            font-family: "Outfit", "Instrument Sans", system-ui, sans-serif;
        }

        #medix-tour-host.active {
            display: block;
        }

        #medix-tour-host .mt-mask {
            position: fixed;
            z-index: 10001;
            background: rgba(15, 23, 42, 0.78);
            pointer-events: auto;
        }

        #medix-tour-host .mt-hole-ring {
            position: fixed;
            z-index: 10002;
            border: 3px solid #fff;
            border-radius: 14px;
            box-shadow: 0 0 0 7px rgba(0, 102, 255, 0.5), 0 0 50px rgba(0, 102, 255, 0.8), 0 0 0 9999px rgba(15, 23, 42, 0.6);
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #medix-tour-host .mt-card {
            position: fixed;
            z-index: 10003;
            width: 360px;
            max-width: calc(100vw - 32px);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 24px 64px rgba(2, 6, 23, 0.5), 0 0 0 1px rgba(0, 102, 255, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        #medix-tour-host .mt-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #0066FF, #00A3FF);
            border-radius: 20px 20px 0 0;
        }

        #medix-tour-host .mt-title {
            margin: 0 0 8px;
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.3;
        }

        #medix-tour-host .mt-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
            color: #475569;
        }

        #medix-tour-host .mt-hint {
            margin: 12px 0 0;
            font-size: 11px;
            font-weight: 700;
            color: #0066FF;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        #medix-tour-host .mt-hint::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #0066FF;
            animation: mt-pulse 1.5s ease-in-out infinite;
        }

        @keyframes mt-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.8); }
        }

        #medix-tour-host .mt-dots {
            display: flex;
            gap: 6px;
            margin: 0 0 16px;
        }

        #medix-tour-host .mt-dot {
            width: 8px;
            height: 8px;
            border-radius: 99px;
            background: #e2e8f0;
            transition: all 0.3s ease;
        }

        #medix-tour-host .mt-dot.on {
            background: linear-gradient(135deg, #0066FF, #00A3FF);
            width: 22px;
            box-shadow: 0 2px 8px rgba(0, 102, 255, 0.3);
        }

        #medix-tour-host .mt-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }

        #medix-tour-host .mt-next {
            background: linear-gradient(135deg, #0066FF, #0088FF);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 22px;
            border-radius: 11px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(0, 102, 255, 0.3);
        }

        #medix-tour-host .mt-next:hover {
            background: linear-gradient(135deg, #0052CC, #0066CC);
            box-shadow: 0 6px 20px rgba(0, 102, 255, 0.4);
            transform: translateY(-1px);
        }

        #medix-tour-host .mt-next:active {
            transform: translateY(0) scale(0.97);
        }

        #medix-tour-host .mt-skip {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            padding: 10px 12px;
            border-radius: 9px;
            transition: all 0.2s ease;
        }

        #medix-tour-host .mt-skip:hover {
            color: #475569;
            background: #f1f5f9;
        }
    </style>
@endif
