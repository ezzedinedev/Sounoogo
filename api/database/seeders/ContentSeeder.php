<?php

namespace Database\Seeders;

use App\Domain\Content\Enums\ExerciseCategory;
use App\Domain\Content\Models\Exercise;
use App\Domain\Content\Models\Guide;
use Illuminate\Database\Seeder;

/**
 * Contenus de départ : exercices et guides fondés sur des techniques reconnues (respiration lente,
 * ancrage sensoriel, détente musculaire progressive, premiers secours en santé mentale).
 * À relire par la supervision clinique avant publication ; les modifications faites ensuite
 * dans le back-office ne sont jamais écrasées (firstOrCreate).
 */
class ContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->exercises() as $position => $exercise) {
            Exercise::firstOrCreate(['slug' => $exercise['slug']], [...$exercise, 'position' => $position, 'published_at' => now()]);
        }

        foreach ($this->guides() as $position => $guide) {
            Guide::firstOrCreate(['slug' => $guide['slug']], [...$guide, 'position' => $position, 'published_at' => now()]);
        }
    }

    /** @return list<array<string, mixed>> */
    private function exercises(): array
    {
        return [
            [
                'slug' => 'respiration-apaisante',
                'title' => 'Respiration apaisante',
                'summary' => 'Inspirer sur 4 temps, expirer sur 6 : une expiration plus longue aide le corps à se calmer.',
                'category' => ExerciseCategory::Breathing,
                'duration_minutes' => 2,
                'breathing' => ['inhale' => 4, 'hold' => 2, 'exhale' => 6, 'rest' => 0, 'cycles' => 10],
            ],
            [
                'slug' => 'respiration-carree',
                'title' => 'Respiration carrée',
                'summary' => 'Quatre temps égaux pour retrouver un rythme régulier quand tout s\'accélère.',
                'category' => ExerciseCategory::Breathing,
                'duration_minutes' => 2,
                'breathing' => ['inhale' => 4, 'hold' => 4, 'exhale' => 4, 'rest' => 4, 'cycles' => 7],
            ],
            [
                'slug' => 'ancrage-5-4-3-2-1',
                'title' => 'Ancrage 5-4-3-2-1',
                'summary' => 'Revenir dans l\'instant présent grâce à tes cinq sens, quand l\'angoisse ou les pensées t\'emportent.',
                'category' => ExerciseCategory::Grounding,
                'duration_minutes' => 4,
                'steps' => [
                    ['text' => 'Installe-toi comme tu peux. Pose tes pieds bien à plat et prends une respiration lente.', 'seconds' => 20],
                    ['text' => 'Regarde autour de toi et nomme, dans ta tête, 5 choses que tu vois.', 'seconds' => 40],
                    ['text' => 'Repère 4 choses que tu peux toucher : tes vêtements, le sol, une table… Sens leur texture.', 'seconds' => 40],
                    ['text' => 'Écoute. Trouve 3 sons autour de toi, proches ou lointains.', 'seconds' => 35],
                    ['text' => 'Cherche 2 odeurs. S\'il n\'y en a pas, pense à deux odeurs que tu aimes.', 'seconds' => 30],
                    ['text' => 'Remarque 1 goût dans ta bouche, ou prends une gorgée d\'eau.', 'seconds' => 25],
                    ['text' => 'Respire encore une fois lentement. Tu es ici, maintenant. Tu as fait quelque chose pour toi.', 'seconds' => 20],
                ],
            ],
            [
                'slug' => 'stop-quand-ca-monte',
                'title' => 'STOP, quand ça monte',
                'summary' => 'Quatre étapes pour reprendre la main quand une émotion forte arrive d\'un coup.',
                'category' => ExerciseCategory::Grounding,
                'duration_minutes' => 2,
                'steps' => [
                    ['text' => 'S — Stop. Arrête ce que tu fais, juste un instant. Tu n\'as rien à décider tout de suite.', 'seconds' => 20],
                    ['text' => 'T — Temps de respirer. Inspire par le nez, expire doucement par la bouche, trois fois.', 'seconds' => 30],
                    ['text' => 'O — Observe. Qu\'est-ce que tu ressens dans ton corps ? Quelle émotion est là ? Nomme-la, sans la juger.', 'seconds' => 40],
                    ['text' => 'P — Poursuis. Choisis une petite chose utile à faire maintenant : boire de l\'eau, sortir prendre l\'air, écrire à quelqu\'un.', 'seconds' => 30],
                ],
            ],
            [
                'slug' => 'detente-musculaire',
                'title' => 'Détente musculaire',
                'summary' => 'Contracter puis relâcher chaque partie du corps pour libérer la tension accumulée.',
                'category' => ExerciseCategory::Relaxation,
                'duration_minutes' => 6,
                'steps' => [
                    ['text' => 'Assieds-toi ou allonge-toi. Ferme les yeux si tu te sens en sécurité.', 'seconds' => 20],
                    ['text' => 'Serre fort les poings pendant 5 secondes… puis relâche. Sens la différence.', 'seconds' => 25],
                    ['text' => 'Plie les bras et contracte-les… puis laisse-les retomber, lourds.', 'seconds' => 25],
                    ['text' => 'Monte les épaules vers les oreilles… puis laisse-les redescendre.', 'seconds' => 25],
                    ['text' => 'Plisse le visage, serre les dents doucement… puis détends la mâchoire et le front.', 'seconds' => 25],
                    ['text' => 'Rentre le ventre et serre-le… puis relâche et respire dedans.', 'seconds' => 25],
                    ['text' => 'Tends les jambes et les pieds… puis relâche complètement.', 'seconds' => 25],
                    ['text' => 'Parcours ton corps du regard intérieur. Là où il reste de la tension, expire dedans.', 'seconds' => 40],
                    ['text' => 'Reste encore un moment dans ce calme. Bouge doucement les doigts avant de te relever.', 'seconds' => 30],
                ],
            ],
            [
                'slug' => 'preparer-le-sommeil',
                'title' => 'Préparer le sommeil',
                'summary' => 'Un rituel court pour poser la journée et laisser venir le sommeil.',
                'category' => ExerciseCategory::Sleep,
                'duration_minutes' => 5,
                'steps' => [
                    ['text' => 'Éloigne ton téléphone dès la fin de cet exercice et baisse la lumière.', 'seconds' => 20],
                    ['text' => 'Allonge-toi. Inspire en comptant jusqu\'à 4, expire en comptant jusqu\'à 6, cinq fois.', 'seconds' => 60],
                    ['text' => 'Repense à ta journée et choisis un moment, même petit, qui s\'est bien passé.', 'seconds' => 45],
                    ['text' => 'Si une inquiétude revient, imagine que tu la poses dans une boîte jusqu\'à demain.', 'seconds' => 45],
                    ['text' => 'Relâche ton front, tes épaules, tes mains. Laisse ton corps peser sur le lit.', 'seconds' => 45],
                    ['text' => 'Continue simplement de respirer lentement. Tu n\'as plus rien à faire ce soir.', 'seconds' => 30],
                ],
            ],
        ];
    }

    /** @return list<array<string, string>> */
    private function guides(): array
    {
        return [
            [
                'slug' => 'ecouter-un-proche-qui-va-mal',
                'title' => 'Écouter un proche qui va mal',
                'summary' => 'Tu n\'as pas besoin d\'avoir les solutions. Être présent et écouter, c\'est déjà beaucoup.',
                'body' => <<<'MD'
                    Quand quelqu'un qu'on aime traverse une période difficile, on voudrait trouver les mots justes et régler le problème. Pourtant, ce qui aide le plus est souvent plus simple : **se sentir écouté sans être jugé**.

                    ## Choisir le bon moment
                    Trouve un moment calme, à l'abri des regards. Dis simplement ce que tu as remarqué : « Je te trouve fatigué ces temps-ci, comment tu vas, vraiment ? »

                    ## Écouter plus que parler
                    - Laisse des silences : ils permettent à l'autre de continuer.
                    - Reformule ce que tu entends : « Si je comprends bien, tu te sens seul depuis que… »
                    - Pose des questions ouvertes : « Qu'est-ce qui est le plus dur pour toi en ce moment ? »

                    ## Accueillir sans minimiser
                    Évite « ce n'est rien », « d'autres ont pire » ou « secoue-toi ». Ce que vit ton proche est réel pour lui. Tu peux dire : « Ça a l'air vraiment lourd, merci de m'en parler. »

                    ## Proposer, sans imposer
                    Demande ce dont il a besoin : parler, être accompagné, se changer les idées. Tu peux lui faire connaître SUNOOGO : l'écoute y est anonyme et gratuite.

                    ## Et si tu es inquiet pour sa sécurité
                    Lis le guide « Reconnaître les signes d'alerte ». En cas de danger immédiat, appelle les numéros d'urgence affichés dans l'application.
                    MD,
            ],
            [
                'slug' => 'reconnaitre-les-signes-d-alerte',
                'title' => 'Reconnaître les signes d\'alerte',
                'summary' => 'Certains signes doivent t\'alerter. En parler franchement ne donne pas l\'idée du suicide : cela peut sauver une vie.',
                'body' => <<<'MD'
                    Une personne qui pense au suicide ne le dit pas toujours clairement. Certains signes doivent te pousser à en parler avec elle.

                    ## Les signes à prendre au sérieux
                    - Elle parle de mourir, de disparaître, ou d'être un poids pour les autres.
                    - Elle se renferme, abandonne ce qu'elle aimait, s'isole de ses proches.
                    - Elle donne ses affaires, fait des adieux, règle ses comptes.
                    - Elle consomme plus d'alcool ou d'autres produits.
                    - Après une longue période très difficile, elle semble soudain apaisée, sans raison apparente.

                    ## Oser poser la question
                    Demande-lui directement, avec douceur : « Est-ce que tu penses au suicide ? » Poser la question **ne donne pas l'idée** du suicide. Au contraire, cela montre que tu peux tout entendre.

                    ## Si la réponse est oui
                    - Reste calme, écoute, ne fais pas la morale.
                    - Demande s'il y a un plan, un moyen, un moment prévu.
                    - Ne la laisse pas seule si le danger est immédiat, et éloigne ce qui pourrait servir à passer à l'acte.
                    - Appelle les numéros d'urgence affichés dans l'application, ou accompagne-la vers un centre de santé.
                    - Encourage-la à parler à un professionnel. Sur SUNOOGO, un écoutant peut répondre tout de suite, anonymement.

                    ## Tu n'es pas seul non plus
                    Aider quelqu'un dans cette situation est éprouvant. Parles-en à une personne de confiance ou à un écoutant SUNOOGO.
                    MD,
            ],
            [
                'slug' => 'que-dire-que-ne-pas-dire',
                'title' => 'Que dire, que ne pas dire',
                'summary' => 'Quelques phrases qui aident, et d\'autres qui blessent sans le vouloir.',
                'body' => <<<'MD'
                    Les mots comptent, surtout quand l'autre est fragile. Voici des repères simples.

                    ## Des phrases qui aident
                    - « Je suis là. »
                    - « Tu n'as pas à traverser ça seul. »
                    - « Ce que tu ressens compte. »
                    - « Qu'est-ce qui t'aiderait, là, maintenant ? »
                    - « Je ne comprends pas tout, mais je veux comprendre. »

                    ## Des phrases à éviter
                    - « Ça va passer » : cela peut donner l'impression que la douleur n'est pas prise au sérieux.
                    - « Pense à ceux qui ont pire » : cela ajoute de la culpabilité.
                    - « Tu devrais… » : les conseils donnés trop vite coupent la parole.
                    - « Moi à ta place… » : chacun vit les choses à sa façon.

                    ## Respecter ce qu'on te confie
                    Garde pour toi ce qu'on te raconte. La seule exception : si sa vie est en danger, sa sécurité passe avant le secret.
                    MD,
            ],
            [
                'slug' => 'prendre-soin-de-soi-quand-on-aide',
                'title' => 'Prendre soin de soi quand on aide',
                'summary' => 'Soutenir quelqu\'un fatigue. Pour aider dans la durée, il faut aussi se préserver.',
                'body' => <<<'MD'
                    Accompagner un proche qui souffre demande beaucoup d'énergie. Prendre soin de toi n'est pas de l'égoïsme : c'est ce qui te permet de continuer.

                    ## Reconnaître tes propres limites
                    Fatigue, irritabilité, sommeil perturbé, sentiment d'impuissance : ce sont des signaux à écouter.

                    ## Partager la charge
                    Tu n'as pas à tout porter seul. Implique d'autres personnes de confiance, et oriente ton proche vers des professionnels.

                    ## Garder du temps pour toi
                    Continue ce qui te fait du bien : voir des amis, bouger, prier ou méditer, te reposer. Les exercices de l'application peuvent aussi t'aider.

                    ## Parler, toi aussi
                    Les écoutants SUNOOGO sont là pour toi aussi, de façon anonyme et gratuite.
                    MD,
            ],
        ];
    }
}
