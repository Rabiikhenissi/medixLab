@extends('layouts.admin')

@section('title', 'Espace Administrateur')


@section('page-title')
Espace <span style="color:#0066ff;">Administrateur</span>
@endsection


@section('page-subtitle')
Gérez la plateforme et supervisez les activités de Medix eSanté.
@endsection



@section('content')


<!-- ================= STATS ================= -->

<div class="stats-grid">


    <!-- Patients -->
    <div class="stat-card anim anim-1">

        <div class="stat-icon green">

            <svg fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M15 19.128a9.38 9.38 0 002.625.372 
                      9.337 9.337 0 004.121-.952 
                      4.125 4.125 0 00-7.533-2.493
                      M15 19.128v-.003
                      c0-1.113-.285-2.16-.786-3.07
                      M12 6.375a3.375 3.375 0 11-6.75 0
                      3.375 3.375 0 016.75 0z" />

            </svg>

        </div>


        <div>

            <div class="stat-label">
                Patients inscrits
            </div>


            <div class="stat-value">
                {{ $stats['total_patients'] ?? 0 }}
            </div>

        </div>


    </div>





    <!-- Examens actifs -->
    <div class="stat-card anim anim-2">


        <div class="stat-icon blue">

            <svg fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M9.75 3.104v5.714
                      a2.25 2.25 0 01-.659 1.591L5 14.5
                      m4.75-11.396a24.3 24.3 0 014.5 0
                      m0 0v5.714
                      c0 .597.237 1.17.659 1.591L19.8 15.3" />

            </svg>


        </div>



        <div>

            <div class="stat-label">
                Examens actifs
            </div>


            <div class="stat-value">
                {{ $stats['total_exams'] ?? 0 }}
            </div>


        </div>


    </div>






    <!-- Archives -->
    <div class="stat-card anim anim-3">


        <div class="stat-icon orange">


            <svg fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 viewBox="0 0 24 24">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M20.25 7.5l-.625 10.632
                      a2.25 2.25 0 01-2.247 2.118H6.622
                      a2.25 2.25 0 01-2.247-2.118L3.75 7.5" />

            </svg>


        </div>



        <div>


            <div class="stat-label">
                Examens archivés
            </div>


            <div class="stat-value">
                {{ $stats['archived_exams'] ?? 0 }}
            </div>


        </div>


    </div>



</div>






<!-- ================= QUICK ACTIONS ================= -->


<div class="data-section anim anim-4">


    <div class="data-header">


        <div>


            <div class="data-title">
                Tableau de bord
            </div>


            <p style="
                margin-top:8px;
                color:#64748b;
                font-size:13px;
            ">
                Gérez les différents modules de la plateforme depuis le menu latéral.
            </p>


        </div>



    </div>





    <div style="
        padding:30px;
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
        gap:20px;
    ">


        <!-- Exams -->


        <a href="{{ route('admin.exams.index') }}"
           style="
            text-decoration:none;
            color:inherit;
           ">


            <div class="feature-card">


                <div class="feature-name">
                    Gestion des examens
                </div>


                <p style="
                    color:#64748b;
                    font-size:13px;
                    margin:0;
                ">
                    Ajouter, modifier, archiver et consulter les examens médicaux.
                </p>



            </div>


        </a>





        <!-- Users -->


        <div class="feature-card">


            <div class="feature-name">
                Utilisateurs
            </div>


            <p style="
                color:#64748b;
                font-size:13px;
                margin:0;
            ">
                Gestion des comptes et permissions.
            </p>


        </div>





        <!-- Settings -->


        <div class="feature-card">


            <div class="feature-name">
                Paramètres
            </div>


            <p style="
                color:#64748b;
                font-size:13px;
                margin:0;
            ">
                Configuration générale de la plateforme.
            </p>


        </div>




    </div>



</div>




@endsection