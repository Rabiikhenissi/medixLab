<x-layouts.auth>
    <x-slot:title>Solutions Santé Avancées - Medix eSanté</x-slot:title>

    <div class="flex flex-col items-center justify-center w-full py-10">
        <!-- Main Headings -->
        <h1
            class="text-4xl md:text-5xl font-extrabold text-center tracking-tight text-[#1e293b] max-w-2xl leading-tight mb-4 select-none">
            Des <span class="text-[#0066FF] relative inline-block">Solutions</span> Santé Avancées pour Tous.
        </h1>
        <p class="text-sm md:text-base text-[#64748b] text-center max-w-xl mb-14 font-medium leading-relaxed">
            Découvrez une plateforme de santé moderne, sécurisée et intelligente.
        </p>

        <!-- Portals Cards Grid -->
        <div class="flex flex-col lg:flex-row items-stretch justify-center gap-8 w-full max-w-5xl px-4">
            <!-- Doctor Portal -->
            <x-portal-card title="Espace Médecin"
                description="Accédez aux dossiers patients, gérez les prescriptions et suivez les résultats d'analyses."
                iconBg="bg-emerald-100/60" primaryColor="blue" loginUrl="{{ route('doctor.login') }}"
                registerUrl="{{ route('doctor.register') }}" loginText="Connexion Médecin">
                <svg class="w-8 h-8 text-[#0066FF]" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 2v6m0 0a4 4 0 00-4 4v2a4 4 0 008 0v-2a4 4 0 00-4-4zm0 0V4m-6 8h12M6 12a6 6 0 0012 0" />
                </svg>
            </x-portal-card>

            <!-- Patient Portal -->
            <x-portal-card title="Espace Patient"
                description="Consultez votre historique médical, suivez vos analyses et communiquez avec votre médecin."
                iconBg="bg-sky-100/60" primaryColor="teal" loginUrl="{{ route('patient.login') }}"
                registerUrl="{{ route('patient.register') }}" loginText="Connexion Patient">
                <svg class="w-8 h-8 text-[#0D9488]" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
            </x-portal-card>

            <!-- Medical Center Portal -->
            <x-portal-card title="Espace Laboratoire"
                description="Gérez votre établissement, supervisez le personnel, les stocks et la facturation."
                iconBg="bg-rose-100/60" primaryColor="purple" loginUrl="{{ route('center.login') }}"
                registerUrl="{{ route('center.register') }}" loginText="Connexion Laboratoire">
                <svg class="w-8 h-8 text-[#7C3AED]" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" />
                </svg>
            </x-portal-card>
        </div>
    </div>
</x-layouts.auth>
