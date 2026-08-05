<x-legal-layout legalTitle="Mentions Légales" updatedAt="05/08/2026">
    <h2 class="text-base font-bold text-[#1e293b]">1. Éditeur de la plateforme</h2>
    <p>
        <strong>{{ config('legal.company_name') }}</strong><br>
        Pays d'établissement : {{ config('legal.company_country') }}<br>
        Contact : <a href="mailto:{{ config('legal.company_email') }}" class="text-[#0066FF] font-semibold hover:underline">{{ config('legal.company_email') }}</a>
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">2. Directeur de la publication</h2>
    <p>
        La direction de la publication est assurée par le représentant légal de l'éditeur.
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">3. Hébergement</h2>
    <p>
        La plateforme est hébergée chez alwaysdata, fournisseur d'hébergement, dont les serveurs peuvent être
        situés hors de votre pays. Les transferts de données sont encadrés par les garanties contractuelles
        appropriées prévues par le RGPD.
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">4. Propriété intellectuelle</h2>
    <p>
        L'ensemble des contenus, interfaces et codes de la plateforme sont la propriété exclusive de l'éditeur
        et sont protégés par le droit de la propriété intellectuelle. Toute reproduction ou représentation
        totale ou partielle sans autorisation est interdite.
    </p>

    <h2 class="text-base font-bold text-[#1e293b]">5. Cookies</h2>
    <p>
        La plateforme utilise uniquement les cookies strictement nécessaires au fonctionnement et à la
        sécurité (session, protection CSRF). Aucun cookie publicitaire ou de suivi n'est déposé.
    </p>
</x-legal-layout>
