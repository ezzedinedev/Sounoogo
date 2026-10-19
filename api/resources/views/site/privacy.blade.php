<x-layouts.site title="Confidentialité" description="Comment SUNOOGO protège ton anonymat et tes données.">
    <section class="py-16 md:py-24">
        <x-site.container class="max-w-3xl">
            <h1 class="font-display text-4xl sm:text-5xl font-semibold tracking-tight">Confidentialité</h1>
            <p class="mt-4 text-muted-foreground">Dernière mise à jour : {{ now()->translatedFormat('j F Y') }}</p>
            <div class="mt-10 prose prose-lg max-w-none prose-headings:font-display prose-headings:font-semibold prose-a:text-primary-700">
                <h2>Ce que nous ne demandons pas</h2>
                <p>Pour parler à un écoutant, tu n'as besoin ni de ton nom, ni de ton numéro de téléphone, ni de ton adresse e-mail. Nous te générons un pseudonyme et un code de récupération que toi seul·e connais.</p>
                <h2>Ce que nous conservons</h2>
                <ul>
                    <li>Les messages de tes conversations, <strong>chiffrés</strong> dans notre base de données ;</li>
                    <li>Si tu réserves une consultation : le créneau, le format et le paiement ;</li>
                    <li>Ta progression dans les formations.</li>
                </ul>
                <h2>Qui peut lire tes échanges</h2>
                <p>L'écoutant qui te répond, et un superviseur clinique en cas d'alerte ou de signalement. Ils ne voient que ton pseudonyme.</p>
                <h2>Tes droits</h2>
                <p>Tu peux supprimer ton compte et toutes tes données à tout moment depuis l'application (Profil → Supprimer mon compte). La suppression est immédiate et définitive.</p>
                <h2>Ce que nous ne ferons jamais</h2>
                <p>Vendre tes données, les utiliser pour de la publicité, ou les transmettre à des tiers, sauf obligation légale ou danger grave et imminent pour ta vie ou celle d'autrui.</p>
            </div>
        </x-site.container>
    </section>
</x-layouts.site>
