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
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #0066FF;
            color: #fff;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(0, 102, 255, 0.4);
            transition: transform 0.15s ease, background 0.15s ease;
        }

        #medix-tour-replay:hover {
            background: #0052CC;
            transform: scale(1.06);
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
            background: rgba(15, 23, 42, 0.72);
            pointer-events: auto;
        }

        #medix-tour-host .mt-hole-ring {
            position: fixed;
            z-index: 10002;
            border: 2px solid #fff;
            border-radius: 12px;
            box-shadow: 0 0 0 6px rgba(0, 102, 255, 0.55), 0 0 44px rgba(0, 102, 255, 0.85);
            pointer-events: none;
            transition: all 0.25s ease;
        }

        #medix-tour-host .mt-card {
            position: fixed;
            z-index: 10003;
            width: 340px;
            max-width: calc(100vw - 32px);
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 20px 50px rgba(2, 6, 23, 0.45);
            border: 1px solid #e2e8f0;
        }

        #medix-tour-host .mt-title {
            margin: 0 0 6px;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
        }

        #medix-tour-host .mt-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: #475569;
        }

        #medix-tour-host .mt-hint {
            margin: 10px 0 0;
            font-size: 11px;
            font-weight: 700;
            color: #0066FF;
        }

        #medix-tour-host .mt-dots {
            display: flex;
            gap: 5px;
            margin: 0 0 12px;
        }

        #medix-tour-host .mt-dot {
            width: 7px;
            height: 7px;
            border-radius: 99px;
            background: #e2e8f0;
            transition: all 0.2s ease;
        }

        #medix-tour-host .mt-dot.on {
            background: #0066FF;
            width: 18px;
        }

        #medix-tour-host .mt-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-top: 16px;
        }

        #medix-tour-host .mt-next {
            background: #0066FF;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 9px 18px;
            border-radius: 10px;
            transition: background 0.15s ease;
        }

        #medix-tour-host .mt-next:hover {
            background: #0052CC;
        }

        #medix-tour-host .mt-skip {
            background: transparent;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-weight: 600;
            font-size: 12px;
            padding: 9px 10px;
            border-radius: 8px;
        }

        #medix-tour-host .mt-skip:hover {
            color: #475569;
            background: #f8fafc;
        }
    </style>
@endif
