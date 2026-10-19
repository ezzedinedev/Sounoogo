"use client";

import { useState, useEffect } from "react";
import styles from "./Header.module.css";

export default function Header() {
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 60);
    handleScroll();
    window.addEventListener("scroll", handleScroll, { passive: true });
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const headerClass = `${styles.header} ${scrolled ? styles.scrolled : styles.transparent}`;

  return (
    <header className={headerClass} role="banner">
      <div className={styles.inner}>
        <a href="#accueil" className={styles.logo} aria-label="SUNOOGO — retour à l'accueil">
          SUNOOGO
        </a>
        <nav className={styles.nav} aria-label="Navigation principale">
          <a href="#services">Nos services</a>
          <a href="#confiance">Confiance</a>
          <a href="#comment">Comment ça marche</a>
          <a href="#faq">FAQ</a>
        </nav>
        <div className={styles.actions}>
          <a href="/admin" className={styles.adminLink}>
            Admin
          </a>
          <a href="#telecharger" className={styles.navCta}>
            Télécharger
          </a>
        </div>
      </div>
    </header>
  );
}
