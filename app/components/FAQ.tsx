"use client";

import { useState } from "react";
import { ChevronDown } from "lucide-react";
import styles from "./FAQ.module.css";

interface FAQItem {
  question: string;
  answer: string;
}

export default function FAQ({ items }: { items: FAQItem[] }) {
  const [open, setOpen] = useState<number | null>(null);

  return (
    <div className={styles.faq}>
      {items.map((item, i) => (
        <div key={i} className={styles.item}>
          <button
            className={styles.question}
            onClick={() => setOpen(open === i ? null : i)}
            aria-expanded={open === i}
            aria-controls={`faq-answer-${i}`}
          >
            <span>{item.question}</span>
            <ChevronDown
              size={20}
              strokeWidth={1.75}
              aria-hidden="true"
              className={`${styles.chevron} ${open === i ? styles.chevronOpen : ""}`}
            />
          </button>
          <div
            id={`faq-answer-${i}`}
            className={`${styles.answer} ${open === i ? styles.answerOpen : ""}`}
            role="region"
            aria-hidden={open !== i}
          >
            <p className={styles.answerText}>{item.answer}</p>
          </div>
        </div>
      ))}
    </div>
  );
}
