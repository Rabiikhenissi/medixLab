@extends('layouts.center')

@section('title', 'Gestion des Horaires - Medix eSanté')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left 2 Cols: Regular Working Hours -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 shadow-xs">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-[#e2e8f0]/60">
                    <div class="w-9 h-9 rounded-xl bg-[#7C3AED]/10 flex items-center justify-center text-[#7C3AED]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider">Horaires Hebdomadaires Réguliers</h3>
                        <p class="text-xs text-[#64748b] mt-0.5">Définissez les heures d'ouverture et de fermeture ordinaires pour chaque jour de la semaine.</p>
                    </div>
                </div>

                <form action="{{ route('center.working-hours.update') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    @foreach($regularHours as $hour)
                        @php
                            $dayName = $hour->day;
                            $label = $dayLabels[$dayName] ?? $dayName;
                        @endphp
                        
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 bg-[#F8FAFC]/60 border border-[#e2e8f0]/50 rounded-xl space-y-3 sm:space-y-0 hover:bg-[#F8FAFC] transition select-none">
                            <div class="flex items-center space-x-3 min-w-[120px]">
                                <span class="text-sm font-bold text-[#1e293b]">{{ $label }}</span>
                            </div>
                            
                            <div class="flex flex-wrap items-center gap-4">
                                <!-- Closed checkbox toggle -->
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="closed_{{ $dayName }}" id="closed_{{ $dayName }}" value="1" {{ $hour->is_closed ? 'checked' : '' }} onchange="toggleDayInputs('{{ $dayName }}')" class="sr-only peer">
                                    <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-red-500"></div>
                                    <span class="ms-2 text-xs font-bold text-gray-500 peer-checked:text-red-500">Jour de repos / Fermé</span>
                                </label>

                                <!-- Time inputs -->
                                <div class="flex items-center space-x-2" id="time-inputs-{{ $dayName }}">
                                    <input type="time" name="start_{{ $dayName }}" value="{{ $hour->start_time ? substr($hour->start_time, 0, 5) : '08:00' }}" {{ $hour->is_closed ? 'disabled' : '' }} class="px-2.5 py-1.5 border border-[#e2e8f0] rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-[#7C3AED] bg-white text-[#1e293b] disabled:opacity-50 disabled:bg-gray-100">
                                    <span class="text-xs text-[#64748b] font-medium">à</span>
                                    <input type="time" name="end_{{ $dayName }}" value="{{ $hour->end_time ? substr($hour->end_time, 0, 5) : '17:00' }}" {{ $hour->is_closed ? 'disabled' : '' }} class="px-2.5 py-1.5 border border-[#e2e8f0] rounded-lg text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-[#7C3AED] bg-white text-[#1e293b] disabled:opacity-50 disabled:bg-gray-100">
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="pt-4 border-t border-[#e2e8f0]/60 flex justify-end">
                        <x-button type="submit" color="purple" :fullWidth="false" class="!py-2.5 !px-6 !text-xs font-bold uppercase tracking-wider shadow-md shadow-[#7C3AED]/20">
                            Enregistrer les horaires
                        </x-button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Col: Exceptions & Holiday list -->
        <div class="space-y-6">
            
            <!-- Add Exception Form -->
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 shadow-xs">
                <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-[#e2e8f0]/60">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center text-[#7C3AED]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#1e293b] uppercase tracking-wider">Ajouter une Exception</h3>
                        <p class="text-xs text-[#64748b] mt-0.5">Indiquez les fermetures exceptionnelles (jours fériés, congés, imprévus).</p>
                    </div>
                </div>

                <form action="{{ route('center.working-hours.exceptions.store') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="space-y-1">
                        <label for="date_close" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Date d'Exception</label>
                        <input type="date" name="date_close" id="date_close" required class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] transition text-[#1e293b] bg-white">
                    </div>

                    <div class="space-y-1">
                        <label for="reason" class="text-xs font-bold text-[#475569] uppercase tracking-wider">Motif / Description</label>
                        <input type="text" name="reason" id="reason" required placeholder="Ex: Jour de l'An, Fête Nationale" class="w-full px-3 py-2.5 border border-[#e2e8f0] rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-[#7C3AED]/10 focus:border-[#7C3AED] transition text-[#1e293b] bg-white">
                    </div>

                    <x-button type="submit" color="purple" :fullWidth="true" class="!py-2.5 !text-xs font-bold uppercase tracking-wider">
                        Ajouter l'Exception
                    </x-button>
                </form>
            </div>

            <!-- Exceptions List -->
            <div class="bg-white border border-[#e2e8f0] rounded-2xl p-6 shadow-xs">
                <h3 class="text-xs font-bold text-[#1e293b] uppercase tracking-wider mb-4 pb-3 border-b border-[#e2e8f0]/60 flex items-center">
                    <svg class="w-4 h-4 text-[#7C3AED] mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                    </svg>
                    Fermetures Exceptionnelles
                </h3>

                @if($exceptions->count() > 0)
                    <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                        @foreach($exceptions as $exception)
                            <div class="flex items-center justify-between p-3 bg-red-50/30 border border-red-100 rounded-xl">
                                <div>
                                    <div class="text-xs font-bold text-[#1e293b]">{{ $exception->day }}</div>
                                    <div class="text-[10px] font-bold text-red-500 mt-0.5">
                                        {{ \Carbon\Carbon::parse($exception->date_close)->translatedFormat('d F Y') }}
                                    </div>
                                </div>
                                
                                <form action="{{ route('center.working-hours.exceptions.destroy', $exception) }}" method="POST" onsubmit="return confirm('Supprimer cette exception de fermeture ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-xs text-[#94a3b8] font-medium">Aucune fermeture exceptionnelle enregistrée.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>
@endsection

@section('scripts')
<script>
    function toggleDayInputs(day) {
        var checkbox = document.getElementById('closed_' + day);
        var timeInputsContainer = document.getElementById('time-inputs-' + day);
        if (!checkbox || !timeInputsContainer) return;
        
        var inputs = timeInputsContainer.getElementsByTagName('input');
        
        if (checkbox.checked) {
            // Disable start/end inputs
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].setAttribute('disabled', 'disabled');
            }
        } else {
            // Enable start/end inputs
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].removeAttribute('disabled');
            }
        }
    }
</script>
@endsection
