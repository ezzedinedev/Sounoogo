import styles from "./page.module.css";

export default function HomePage() {
  return (
    <main className={styles.main}>
      <section className={`${styles.hero} calm-gradient`}>
        <p className={styles.eyebrow}>SUNOOGO · Site vitrine</p>
        <h1 className={styles.title}>Template Next.js à intégrer</h1>
        <p className={styles.subtitle}>
          L&apos;ancien site Vite a été retiré. Copiez votre template dans{" "}
          <code>web/</code> (App Router) en conservant les tokens{" "}
          <code>app/globals.css</code>.
        </p>
        <a href="#integrer" className={styles.cta}>
          Voir web/README.md
        </a>
      </section>
      <section id="integrer" className={styles.note}>
        <h2>Prochaine étape</h2>
        <ol>
          <li>Déposer le template (dossier ou archive) dans le dépôt.</li>
          <li>Fusionner avec <code>app/layout.tsx</code> et metadata SUNOOGO.</li>
          <li>Remapper les couleurs du template vers les CSS vars noir &amp; or.</li>
          <li>Brancher le contenu US-MKT-01 → US-MKT-04 (PRD).</li>
        </ol>
      </section>
    </main>
  );
}
