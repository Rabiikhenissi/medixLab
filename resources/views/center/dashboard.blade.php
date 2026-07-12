@extends('layouts.center')

@section('title', 'Tableau de bord Centre Médical')

@section('content')

<div class="space-y-8 select-none">

    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-[#1e293b]">
            Bienvenue,
            <span class="text-[#7C3AED]">
                {{ auth()->user()->staff->laboratory->name }}
            </span>
            !
        </h1>

        <p class="text-sm text-[#64748b] mt-2">
            Gérez votre laboratoire, les demandes d'analyses, les stocks et les équipements médicaux.
        </p>
    </div>


    <!-- Statistics -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">


        <!-- Equipment -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
            <div class="text-3xl font-black text-slate-800">
                {{ $stats['equipment_count'] }}
            </div>

            <p class="text-xs uppercase font-bold text-slate-500 mt-2">
                Équipements
            </p>
        </div>



        <!-- Consumables -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="text-3xl font-black text-slate-800">
                {{ $stats['consumables_count'] }}
            </div>

            <p class="text-xs uppercase font-bold text-slate-500 mt-2">
                Consommables
            </p>

        </div>



        <!-- Low stock -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="text-3xl font-black 
                {{ $stats['low_stock_count'] > 0 ? 'text-red-600':'text-slate-800' }}">
                {{ $stats['low_stock_count'] }}
            </div>

            <p class="text-xs uppercase font-bold text-slate-500 mt-2">
                Stock faible
            </p>

        </div>



        <!-- Maintenance -->
        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

            <div class="text-3xl font-black
                {{ $stats['active_maintenance_count'] > 0 ? 'text-orange-600':'text-slate-800' }}">
                {{ $stats['active_maintenance_count'] }}
            </div>

            <p class="text-xs uppercase font-bold text-slate-500 mt-2">
                Maintenance
            </p>

        </div>



        <!-- Exam requests -->
        <a href="{{ route('center.exam-requests') }}"
           class="bg-purple-50 border border-purple-200 rounded-2xl p-5 shadow-sm hover:border-purple-400 transition">

            <div class="text-3xl font-black text-[#7C3AED]">
                →
            </div>

            <p class="text-xs uppercase font-bold text-purple-700 mt-2">
                Demandes d'analyses
            </p>

        </a>


    </div>



    <!-- Main sections -->

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">



        <!-- Laboratory information -->

        <div class="bg-white border border-slate-200 rounded-2xl p-6">

            <h3 class="font-bold text-slate-800 mb-5">
                Informations du laboratoire
            </h3>


            <div class="space-y-3 text-sm">


                <div class="flex justify-between border-b pb-2">

                    <span class="text-slate-500">
                        Nom
                    </span>

                    <span class="font-semibold">
                        {{ auth()->user()->staff->laboratory->name }}
                    </span>

                </div>



                <div class="flex justify-between border-b pb-2">

                    <span class="text-slate-500">
                        Responsable
                    </span>

                    <span class="font-semibold">
                        {{ auth()->user()->first_name }}
                        {{ auth()->user()->last_name }}
                    </span>

                </div>



                <div class="flex justify-between border-b pb-2">

                    <span class="text-slate-500">
                        Email
                    </span>

                    <span class="font-semibold">
                        {{ auth()->user()->email }}
                    </span>

                </div>



                <div class="flex justify-between">

                    <span class="text-slate-500">
                        Ville
                    </span>

                    <span class="font-semibold">
                        {{ auth()->user()->staff->laboratory->city ?? 'Non renseignée' }}
                    </span>

                </div>


            </div>

        </div>




        <!-- Quick actions -->

        <div class="bg-white border border-slate-200 rounded-2xl p-6">


            <h3 class="font-bold text-slate-800 mb-5">
                Actions rapides
            </h3>



            <div class="grid grid-cols-2 gap-4">


                <a href="{{ route('center.working-hours') }}"
                   class="p-4 rounded-xl bg-slate-50 hover:bg-purple-50 text-sm font-semibold text-center transition">
                    Horaires
                </a>



                <a href="{{ route('center.consumables') }}"
                   class="p-4 rounded-xl bg-slate-50 hover:bg-purple-50 text-sm font-semibold text-center transition">
                    Stock
                </a>



                <a href="{{ route('center.equipment') }}"
                   class="p-4 rounded-xl bg-slate-50 hover:bg-purple-50 text-sm font-semibold text-center transition">
                    Équipements
                </a>



                <a href="{{ route('center.exam-requests') }}"
                   class="p-4 rounded-xl bg-purple-50 text-purple-700 font-semibold text-center transition">
                    Analyses
                </a>


            </div>


        </div>


    </div>




    <!-- Footer note -->

    <div class="bg-purple-50 border border-purple-100 rounded-2xl p-6">

        <h4 class="font-bold text-purple-700 mb-2">
            Centre médical connecté
        </h4>

        <p class="text-sm text-slate-600">
            Votre espace permet la gestion complète des demandes d'analyses,
            du matériel médical et des ressources du laboratoire.
        </p>

    </div>



</div>


@endsection