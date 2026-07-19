@extends('layouts.center')

@section('title', 'Demandes d\'analyses - Medix eSanté')

@section('content')

<div class="space-y-6 select-none">


    <!-- Header -->
    <div>

        <h1 class="text-3xl font-bold text-[#1e293b]">
            Demandes d'analyses
        </h1>

        <p class="text-sm text-[#64748b] mt-2">
            Consultez et prenez en charge les demandes d'analyses assignées à votre laboratoire.
        </p>

    </div>



    <!-- Alerts -->

    @if(session('success'))

    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl p-4 text-sm font-semibold">
        {{ session('success') }}
    </div>

    @endif


    @if(session('error'))

    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm font-semibold">
        {{ session('error') }}
    </div>

    @endif


    <!-- Search & Filter -->
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-4">
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Recherche</label>
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Nom patient, médecin, n° demande..."
                class="border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm text-[#1e293b] placeholder:text-[#94a3b8] focus:border-[#0066ff] focus:ring-1 focus:ring-[#0066ff] outline-none w-64">
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-[10px] font-bold text-[#64748b] uppercase tracking-wider">Statut</label>
            <select name="status" class="border border-[#e2e8f0] rounded-xl px-4 py-2.5 text-sm text-[#1e293b] focus:border-[#0066ff] focus:ring-1 focus:ring-[#0066ff] outline-none">
                <option value="">Tous</option>
                <option value="pending" {{ ($status ?? '') === 'pending' ? 'selected' : '' }}>En attente</option>
                <option value="assigned" {{ ($status ?? '') === 'assigned' ? 'selected' : '' }}>Assignée</option>
                <option value="collected" {{ ($status ?? '') === 'collected' ? 'selected' : '' }}>Collectée</option>
                <option value="processing" {{ ($status ?? '') === 'processing' ? 'selected' : '' }}>En traitement</option>
                <option value="completed" {{ ($status ?? '') === 'completed' ? 'selected' : '' }}>Terminée</option>
                <option value="cancelled" {{ ($status ?? '') === 'cancelled' ? 'selected' : '' }}>Annulée</option>
            </select>
        </div>
        <button type="submit" class="bg-[#0066ff] text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-[#0052cc] transition">
            Filtrer
        </button>
        @if(($search ?? '') || ($status ?? ''))
            <a href="{{ route('center.exam-requests') }}" class="text-sm text-[#64748b] hover:text-[#0066ff] font-medium px-3 py-2.5">
                Réinitialiser
            </a>
        @endif
    </form>


    <!-- Table -->

    <div class="bg-white border border-[#e2e8f0] rounded-2xl overflow-hidden shadow-sm">


        <table class="w-full text-left border-collapse">


            <thead>

                <tr class="bg-[#F8FAFC] border-b border-[#e2e8f0]">


                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                        Patient
                    </th>


                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                        Médecin
                    </th>


                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                        Examens
                    </th>


                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-center">
                        Statut
                    </th>


                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider">
                        Date
                    </th>


                    <th class="p-4 text-[10px] font-bold text-[#64748b] uppercase tracking-wider text-right">
                        Actions
                    </th>


                </tr>

            </thead>



            <tbody class="divide-y divide-gray-100">


            @forelse($requests as $request)



                <tr class="hover:bg-[#F8FAFC]/70 transition">



                    <!-- Patient -->

                    <td class="p-4">

                        <div class="font-bold text-[#1e293b]">

                            {{ $request->patient->user->first_name }}
                            {{ $request->patient->user->last_name }}

                        </div>


                        <div class="text-xs text-[#64748b] mt-1">

                            Demande #{{ $request->id }}

                        </div>


                    </td>




                    <!-- Doctor -->

                    <td class="p-4">


                        <div class="text-sm font-semibold text-[#475569]">

                            Dr.
                            {{ $request->doctor->user->first_name }}
                            {{ $request->doctor->user->last_name }}

                        </div>


                    </td>





                    <!-- Exams -->

                    <td class="p-4">


                        <div class="space-y-2">


                        @foreach($request->items as $item)


                            <div class="flex items-center gap-2 text-xs font-semibold text-[#475569]">

                                <span class="w-1.5 h-1.5 rounded-full bg-[#7C3AED]"></span>

                                {{ $item->exam->name }}

                            </div>


                        @endforeach


                        </div>


                    </td>






                    <!-- Status -->

                    <td class="p-4 text-center">



                        @if($request->status === 'assigned')


                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-orange-50 text-orange-600 border border-orange-200">

                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>

                            Assignée

                        </span>



                        @elseif($request->status === 'processing')



                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-purple-50 text-purple-700 border border-purple-200">

                            <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>

                            En traitement

                        </span>




                        @else



                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-emerald-50 text-emerald-600 border border-emerald-200">

                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>

                            Terminé

                        </span>


                        @endif



                    </td>





                    <!-- Date -->

                    <td class="p-4 text-xs font-semibold text-[#64748b]">

                        {{ $request->created_at->format('d/m/Y H:i') }}

                    </td>






                    <!-- Actions -->

                    <td class="p-4 text-right">


                        <div class="inline-flex gap-2 flex-wrap justify-end">



                        @if($request->status === 'assigned')



                            <form method="POST"
                            action="{{ route('center.exam-requests.claim',$request) }}">


                                @csrf


                                <button
                                class="px-4 py-2 bg-[#7C3AED] hover:bg-[#6D28D9] text-white rounded-xl text-xs font-bold uppercase tracking-wider transition shadow-md shadow-purple-200">


                                    Prendre en charge


                                </button>



                            </form>




                        @else




                            @foreach($request->items as $item)



                                @if($item->resultLabo)



                                <a href="{{ route('center.results.edit',$item->resultLabo) }}"
                                class="px-4 py-2 bg-orange-50 hover:bg-orange-100 text-orange-600 border border-orange-200 rounded-xl text-xs font-bold uppercase tracking-wider transition">


                                    Modifier résultats


                                </a>




                                @else




                                <a href="{{ route('center.results.create',$item) }}"
                                class="px-4 py-2 bg-[#7C3AED]/10 hover:bg-[#7C3AED]/20 text-[#7C3AED] rounded-xl text-xs font-bold uppercase tracking-wider transition">


                                    Ajouter résultats


                                </a>



                                @endif



                            @endforeach




                        @endif



                        </div>


                    </td>




                </tr>



            @empty



                <tr>

                    <td colspan="6" class="p-10 text-center text-gray-400 font-semibold">

                        Aucune demande d'analyse assignée.

                    </td>

                </tr>



            @endforelse



            </tbody>


        </table>


    </div>


</div>


@endsection