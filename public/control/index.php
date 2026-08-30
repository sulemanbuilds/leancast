<?php
/**
 * Lean Cast — Control Screen (Phone 2)
 * Mobile-first operator console for driving the Broadcast Screen overlays.
 * Frontend demonstration only — no backend, no AJAX yet.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lean Cast — Broadcast Control</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>

/* =========================================================
   1. RESET & BASE
   ========================================================= */
*, *::before, *::after { box-sizing: border-box; }

html, body {
  margin: 0;
  padding: 0;
  background: var(--bg);
  color: var(--white);
  font-family: 'Inter', sans-serif;
  -webkit-font-smoothing: antialiased;
}

body {
  min-height: 100vh;
  padding-bottom: 48px;
}

button {
  font-family: inherit;
  cursor: pointer;
  color: inherit;
}

input, textarea {
  font-family: inherit;
}

/* =========================================================
   2. DESIGN TOKENS
   ========================================================= */
:root {
  --bg: #07090D;
  --bg-elevated: #0C0F16;
  --orange: #FF6A00;
  --orange-light: #FF9D42;
  --blue: #4F7CFF;
  --green: #42E8A4;
  --white: #F5F7FB;
  --muted: #9299A8;

  --border-soft: rgba(255, 255, 255, 0.09);
  --border-softer: rgba(255, 255, 255, 0.05);
  --glass-fill: rgba(12, 15, 22, 0.66);

  --font-display: 'Space Grotesk', sans-serif;
  --font-body: 'Inter', sans-serif;
  --font-mono: 'JetBrains Mono', monospace;

  --ease-out: cubic-bezier(0.16, 1, 0.3, 1);

  --edge: clamp(16px, 4vw, 28px);
  --panel-radius: 12px;
}

/* =========================================================
   3. LAYOUT / PAGE SHELL
   ========================================================= */
.page {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 var(--edge);
  background:
    radial-gradient(ellipse 70% 40% at 20% 0%, rgba(79, 124, 255, 0.06), transparent 60%),
    radial-gradient(ellipse 60% 35% at 100% 20%, rgba(255, 106, 0, 0.05), transparent 60%);
}

.control-main {
  display: grid;
  grid-template-columns: 1fr;
  gap: 16px;
  padding-top: 18px;
}

/* =========================================================
   4. HEADER
   ========================================================= */
.control-header {
  position: sticky;
  top: 0;
  z-index: 20;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 14px var(--edge);
  margin: 0 calc(var(--edge) * -1);
  background: rgba(7, 9, 13, 0.82);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid var(--border-soft);
}

.control-header__brand {
  display: flex;
  align-items: center;
  gap: 9px;
  min-width: 0;
}

.control-header__mark {
  width: 15px;
  height: 15px;
  border-radius: 4px;
  background: linear-gradient(135deg, var(--orange-light), var(--orange));
  box-shadow: 0 0 12px rgba(255, 106, 0, 0.5);
  flex-shrink: 0;
}

.control-header__text {
  display: flex;
  flex-direction: column;
  min-width: 0;
}

.control-header__word {
  font-family: var(--font-display);
  font-weight: 600;
  font-size: 15px;
  letter-spacing: 0.01em;
  color: var(--white);
  white-space: nowrap;
}

.control-header__word span {
  color: var(--orange-light);
  font-weight: 500;
}

.control-header__sub {
  font-family: var(--font-mono);
  font-size: 9px;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--muted);
  white-space: nowrap;
}

.control-header__live {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 11px;
  background: var(--bg-elevated);
  border: 1px solid var(--border-soft);
  border-radius: 999px;
  font-family: var(--font-mono);
  font-size: 10.5px;
  font-weight: 500;
  letter-spacing: 0.1em;
  color: var(--white);
}

.control-header__live .dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--green);
  box-shadow: 0 0 8px rgba(66, 232, 164, 0.8);
  animation: pulse 1.8s ease-in-out infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.4; transform: scale(0.8); }
}

/* =========================================================
   5. SHARED PANEL COMPONENT
   ========================================================= */
.panel {
  background: var(--glass-fill);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border: 1px solid var(--border-soft);
  border-radius: var(--panel-radius);
  padding: 16px;
  opacity: 0;
  transform: translateY(10px);
  animation: panelIn 0.5s var(--ease-out) forwards;
}

@keyframes panelIn {
  to { opacity: 1; transform: translateY(0); }
}

.panel__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 12px;
}

.panel__title {
  font-family: var(--font-mono);
  font-size: 10.5px;
  font-weight: 600;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--muted);
}

.panel__title strong {
  color: var(--white);
}

/* =========================================================
   6. BROADCAST STATUS
   ========================================================= */
.status-row {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.status-chip {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 8px 12px;
  background: var(--bg-elevated);
  border: 1px solid var(--border-soft);
  border-radius: 8px;
  font-family: var(--font-mono);
  font-size: 11px;
  letter-spacing: 0.06em;
  color: var(--muted);
}

.status-chip .dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--muted);
  flex-shrink: 0;
}

.status-chip.is-live .dot {
  background: var(--green);
  box-shadow: 0 0 7px rgba(66, 232, 164, 0.7);
  animation: pulse 1.8s ease-in-out infinite;
}

.status-chip.is-live {
  color: var(--white);
  border-color: rgba(66, 232, 164, 0.25);
}

/* =========================================================
   7. PREVIEW
   ========================================================= */
.preview__frame {
  position: relative;
  width: 100%;
  aspect-ratio: 16 / 9;
  border-radius: 8px;
  overflow: hidden;
  border: 1px solid var(--border-soft);
  background:
    radial-gradient(ellipse 60% 45% at 28% 22%, rgba(79, 124, 255, 0.10), transparent 55%),
    radial-gradient(ellipse 55% 42% at 78% 74%, rgba(255, 106, 0, 0.06), transparent 55%),
    linear-gradient(160deg, #0B0D13 0%, #060709 60%, #050608 100%);
}

.preview__logo {
  position: absolute;
  top: 8px;
  left: 8px;
  z-index: 3;
  display: flex;
  align-items: center;
  gap: 4px;
}

.preview__logo .mark {
  width: 7px;
  height: 7px;
  border-radius: 2px;
  background: linear-gradient(135deg, var(--orange-light), var(--orange));
}

.preview__logo .word {
  font-family: var(--font-display);
  font-weight: 600;
  font-size: 8px;
  letter-spacing: 0.02em;
  color: var(--white);
}

.preview__live {
  position: absolute;
  top: 8px;
  right: 8px;
  z-index: 3;
  display: flex;
  align-items: center;
  gap: 4px;
  font-family: var(--font-mono);
  font-size: 7px;
  font-weight: 500;
  letter-spacing: 0.08em;
  color: var(--white);
}

.preview__live .dot {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: var(--green);
  box-shadow: 0 0 5px rgba(66, 232, 164, 0.8);
}

.preview__stack {
  position: absolute;
  left: 8px;
  right: 8px;
  bottom: 22px;
  z-index: 3;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 4px;
}

.preview__lower-third {
  align-self: flex-start;
  max-width: 66%;
  display: flex;
  background: rgba(9, 11, 16, 0.6);
  border-top: 1px solid var(--border-soft);
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.preview__lower-third.is-off {
  opacity: 0;
  transform: translateY(4px);
}

.preview__lower-third .edge {
  width: 2px;
  flex-shrink: 0;
  background: linear-gradient(180deg, var(--orange-light), var(--orange));
}

.preview__lower-third .content {
  padding: 4px 6px;
  min-width: 0;
}

.preview__lower-third .name {
  font-family: var(--font-display);
  font-weight: 600;
  font-size: 8px;
  color: var(--white);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.preview__lower-third .meta {
  font-family: var(--font-mono);
  font-size: 5.5px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--orange-light);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.preview__lower-third .meta span {
  color: var(--muted);
}

.preview__breaking {
  align-self: stretch;
  display: flex;
  background: rgba(9, 11, 16, 0.6);
  border-top: 1px solid rgba(255, 106, 0, 0.4);
  overflow: hidden;
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.preview__breaking.is-off {
  opacity: 0;
  transform: translateY(4px);
}

.preview__breaking .tag {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  padding: 0 6px;
  background: linear-gradient(135deg, var(--orange), #E45800);
  font-family: var(--font-mono);
  font-weight: 600;
  font-size: 5.5px;
  letter-spacing: 0.08em;
  color: #0A0603;
  white-space: nowrap;
}

.preview__breaking .headline {
  display: flex;
  align-items: center;
  padding: 4px 6px;
  font-family: var(--font-display);
  font-weight: 600;
  font-size: 7px;
  color: var(--white);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.preview__ticker {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 3;
  height: 16px;
  display: flex;
  align-items: center;
  background: var(--bg-elevated);
  border-top: 1px solid var(--border-soft);
  overflow: hidden;
  transition: opacity 0.25s ease, transform 0.25s ease;
}

.preview__ticker.is-off {
  opacity: 0;
  transform: translateY(6px);
}

.preview__ticker .label {
  flex-shrink: 0;
  height: 100%;
  display: flex;
  align-items: center;
  padding: 0 6px;
  background: linear-gradient(135deg, var(--blue), #3A5FE0);
  font-family: var(--font-mono);
  font-weight: 600;
  font-size: 5.5px;
  letter-spacing: 0.06em;
  color: var(--white);
  white-space: nowrap;
}

.preview__ticker .text {
  flex: 1;
  padding-left: 6px;
  font-family: var(--font-mono);
  font-size: 6.5px;
  color: var(--muted);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.preview__caption {
  margin-top: 10px;
  font-family: var(--font-mono);
  font-size: 9.5px;
  letter-spacing: 0.06em;
  color: var(--muted);
  text-align: center;
}

/* =========================================================
   8. FORM FIELDS (shared)
   ========================================================= */
.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 12px;
}

.field:last-of-type {
  margin-bottom: 0;
}

.field__label {
  font-family: var(--font-mono);
  font-size: 9.5px;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: var(--muted);
}

.field input,
.field textarea {
  width: 100%;
  background: var(--bg-elevated);
  border: 1px solid var(--border-soft);
  border-radius: 8px;
  padding: 12px 13px;
  font-size: 15px;
  color: var(--white);
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.field textarea {
  resize: none;
  min-height: 64px;
  line-height: 1.45;
  font-family: var(--font-body);
}

.field input:focus,
.field textarea:focus {
  border-color: rgba(255, 106, 0, 0.5);
  box-shadow: 0 0 0 3px rgba(255, 106, 0, 0.12);
}

.field select {
  width: 100%;
  appearance: none;
  background: var(--bg-elevated);
  border: 1px solid var(--border-soft);
  border-radius: 8px;
  padding: 12px 13px;
  font-size: 15px;
  color: var(--white);
  outline: none;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  cursor: pointer;
}

.field select:focus {
  border-color: rgba(255, 106, 0, 0.5);
  box-shadow: 0 0 0 3px rgba(255, 106, 0, 0.12);
}

.field-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

/* =========================================================
   9. ACTIONS (update button + on/off toggle)
   ========================================================= */
.panel__actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 14px;
  padding-top: 14px;
  border-top: 1px solid var(--border-softer);
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  min-height: 44px;
  padding: 0 18px;
  border-radius: 8px;
  font-family: var(--font-mono);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  border: 1px solid transparent;
  transition: transform 0.15s ease, opacity 0.15s ease, background 0.2s ease, border-color 0.2s ease;
  user-select: none;
}

.btn:active {
  transform: scale(0.97);
}

.btn--primary {
  flex: 1;
  background: linear-gradient(135deg, var(--orange-light), var(--orange));
  color: #150900;
  box-shadow: 0 6px 18px -8px rgba(255, 106, 0, 0.55);
}

.btn--primary:hover {
  opacity: 0.94;
}

.btn--secondary {
  background: var(--bg-elevated);
  border-color: var(--border-soft);
  color: var(--text-main, var(--white));
}

.btn--secondary:hover {
  border-color: rgba(255, 255, 255, 0.18);
}

.save-feedback {
  font-family: var(--font-mono);
  font-size: 10px;
  letter-spacing: 0.08em;
  color: var(--green);
  opacity: 0;
  transform: translateY(2px);
  transition: opacity 0.25s ease, transform 0.25s ease;
  white-space: nowrap;
}

.save-feedback.is-shown {
  opacity: 1;
  transform: translateY(0);
}

/* Toggle switch */
.toggle {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
  background: none;
  border: none;
  padding: 0;
}

.toggle__track {
  position: relative;
  width: 38px;
  height: 22px;
  border-radius: 999px;
  background: var(--bg-elevated);
  border: 1px solid var(--border-soft);
  transition: background 0.2s ease, border-color 0.2s ease;
}

.toggle__thumb {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: var(--muted);
  transition: transform 0.2s var(--ease-out), background 0.2s ease;
}

.toggle[aria-pressed="true"] .toggle__track {
  background: rgba(66, 232, 164, 0.14);
  border-color: rgba(66, 232, 164, 0.4);
}

.toggle[aria-pressed="true"] .toggle__thumb {
  transform: translateX(16px);
  background: var(--green);
  box-shadow: 0 0 8px rgba(66, 232, 164, 0.6);
}

.toggle__label {
  font-family: var(--font-mono);
  font-size: 10.5px;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: var(--muted);
  min-width: 26px;
}

.toggle[aria-pressed="true"] .toggle__label {
  color: var(--green);
}

/* =========================================================
   10. BREAKING NEWS EMPHASIS
   ========================================================= */
.panel--breaking {
  border-color: rgba(255, 106, 0, 0.3);
  box-shadow: 0 0 34px -14px rgba(255, 106, 0, 0.25);
}

.panel--breaking .panel__title strong {
  color: var(--orange-light);
}

/* =========================================================
   11. CHANNEL BRANDING
   ========================================================= */
.branding-row {
  display: flex;
  align-items: center;
  gap: 14px;
}

.branding-mark {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--bg-elevated);
  border: 1px solid var(--border-soft);
}

.branding-mark .inner {
  width: 18px;
  height: 18px;
  border-radius: 5px;
  background: linear-gradient(135deg, var(--orange-light), var(--orange));
  box-shadow: 0 0 14px rgba(255, 106, 0, 0.5);
}

.branding-info {
  display: flex;
  flex-direction: column;
  gap: 3px;
  min-width: 0;
}

.branding-name {
  font-family: var(--font-display);
  font-weight: 600;
  font-size: 14px;
  color: var(--white);
}

.branding-note {
  font-family: var(--font-mono);
  font-size: 9.5px;
  letter-spacing: 0.06em;
  color: var(--muted);
}

/* =========================================================
   12. OVERLAY STATUS
   ========================================================= */
.overlay-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.overlay-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 12px;
  background: var(--bg-elevated);
  border: 1px solid var(--border-softer);
  border-radius: 8px;
}

.overlay-row__name {
  font-family: var(--font-mono);
  font-size: 11px;
  letter-spacing: 0.06em;
  color: var(--white);
}

.overlay-row__state {
  display: flex;
  align-items: center;
  gap: 6px;
  font-family: var(--font-mono);
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: var(--muted);
}

.overlay-row__state .dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--muted);
}

.overlay-row.is-on .overlay-row__state {
  color: var(--green);
}

.overlay-row.is-on .overlay-row__state .dot {
  background: var(--green);
  box-shadow: 0 0 7px rgba(66, 232, 164, 0.7);
}

/* =========================================================
   13. SYSTEM FEEDBACK
   ========================================================= */
.system-status {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}

.system-status__connection {
  display: flex;
  align-items: center;
  gap: 7px;
  font-family: var(--font-mono);
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.08em;
  color: var(--green);
}

.system-status__connection .dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--green);
  box-shadow: 0 0 7px rgba(66, 232, 164, 0.7);
  animation: pulse 2.4s ease-in-out infinite;
}

.system-status__update {
  text-align: right;
}

.system-status__update-label {
  display: block;
  font-family: var(--font-mono);
  font-size: 8.5px;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--muted);
  opacity: 0.7;
}

.system-status__update-time {
  display: block;
  font-family: var(--font-mono);
  font-size: 11px;
  color: var(--white);
  margin-top: 2px;
}

/* =========================================================
   14. RESPONSIVE — two-column on larger screens
   ========================================================= */
@media (min-width: 900px) {
  .control-main {
    grid-template-columns: 340px 1fr;
    align-items: start;
    gap: 18px;
  }

  .col-watch {
    grid-column: 1;
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: sticky;
    top: 78px;
  }

  .col-edit {
    grid-column: 2;
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
}

@media (max-width: 480px) {
  .field-row {
    grid-template-columns: 1fr;
  }

  .panel {
    padding: 14px;
  }
}

/* =========================================================
   15. REDUCED MOTION
   ========================================================= */
@media (prefers-reduced-motion: reduce) {
  .control-header__live .dot,
  .status-chip.is-live .dot,
  .system-status__connection .dot {
    animation: none;
    opacity: 1;
  }

  .panel {
    animation: none;
    opacity: 1;
    transform: none;
  }

  .btn:active {
    transform: none;
  }

  .toggle__thumb,
  .save-feedback,
  .preview__lower-third,
  .preview__breaking,
  .preview__ticker {
    transition: none;
  }
}

</style>
</head>
<body>

<div class="page">

  <!-- ============================================================
       HEADER
       ============================================================ -->
  <header class="control-header">
    <div class="control-header__brand">
      <span class="control-header__mark"></span>
      <div class="control-header__text">
        <span class="control-header__word">LEAN<span>CAST</span></span>
        <span class="control-header__sub">Broadcast Control</span>
      </div>
    </div>
    <div class="control-header__live">
      <span class="dot"></span>
      <span>LIVE</span>
    </div>
  </header>

  <main class="control-main">

    <!-- ============================================================
         WATCH COLUMN — status, preview, overlay state, system
         ============================================================ -->
    <div class="col-watch">

      <!-- Broadcast status -->
      <section class="panel" style="animation-delay: 0.02s">
        <div class="panel__header">
          <span class="panel__title"><strong>Broadcast</strong> Status</span>
        </div>
        <div class="status-row">
          <span class="status-chip is-live"><span class="dot"></span>LIVE</span>
          <span class="status-chip is-live"><span class="dot"></span>CONNECTED</span>
          <span class="status-chip"><span class="dot"></span>CONTROL ACTIVE</span>
        </div>
      </section>

      <!-- Preview -->
      <section class="panel" style="animation-delay: 0.06s">
        <div class="panel__header">
          <span class="panel__title"><strong>Current</strong> Broadcast</span>
        </div>
        <div class="preview__frame">
          <div class="preview__logo">
            <span class="mark"></span>
            <span class="word">LEANCAST</span>
          </div>
          <div class="preview__live">
            <span class="dot"></span>
            <span>LIVE</span>
          </div>
          <div class="preview__stack">
            <div class="preview__lower-third" id="pv-lower-third">
              <span class="edge"></span>
              <span class="content">
                <span class="name" id="pv-name">SULEMAN MEMON</span><br>
                <span class="meta"><span id="pv-role">FIELD REPORTER</span> · <span id="pv-location">HYDERABAD</span></span>
              </span>
            </div>
            <div class="preview__breaking" id="pv-breaking">
              <span class="tag">BREAKING</span>
              <span class="headline" id="pv-headline">University announces campus closure tomorrow</span>
            </div>
          </div>
          <div class="preview__ticker" id="pv-ticker">
            <span class="label">HYDERABAD</span>
            <span class="text" id="pv-ticker-text">More updates from Hyderabad • Stay tuned for further information</span>
          </div>
        </div>
        <span class="preview__caption">Reflects the graphics currently active on the Broadcast Screen</span>
      </section>

      <!-- Overlay status -->
      <section class="panel" style="animation-delay: 0.1s">
        <div class="panel__header">
          <span class="panel__title"><strong>Overlay</strong> Status</span>
        </div>
        <div class="overlay-list">
          <div class="overlay-row is-on" id="status-lower-third">
            <span class="overlay-row__name">Lower Third</span>
            <span class="overlay-row__state"><span class="dot"></span><span class="text">ON</span></span>
          </div>
          <div class="overlay-row is-on" id="status-breaking">
            <span class="overlay-row__name">Breaking News</span>
            <span class="overlay-row__state"><span class="dot"></span><span class="text">ON</span></span>
          </div>
          <div class="overlay-row is-on" id="status-ticker">
            <span class="overlay-row__name">Ticker</span>
            <span class="overlay-row__state"><span class="dot"></span><span class="text">ON</span></span>
          </div>
        </div>
      </section>

      <!-- System feedback -->
      <section class="panel" style="animation-delay: 0.14s">
        <div class="panel__header">
          <span class="panel__title"><strong>System</strong> Feedback</span>
        </div>
        <div class="system-status">
          <span class="system-status__connection"><span class="dot"></span>CONNECTED</span>
          <span class="system-status__update">
            <span class="system-status__update-label">Last update</span>
            <span class="system-status__update-time" id="lastUpdateTime">Just now</span>
          </span>
        </div>
      </section>

    </div>

    <!-- ============================================================
         EDIT COLUMN — the controls the operator uses
         ============================================================ -->
    <div class="col-edit">

      <!-- Active overlay -->
      <section class="panel" style="animation-delay: 0.04s">
        <div class="panel__header">
          <span class="panel__title"><strong>Active</strong> Overlay</span>
        </div>
        <div class="field">
          <label class="field__label" for="select-overlay">Select Overlay</label>
          <select id="select-overlay">
            <option value="main">Main Broadcast Overlay</option>
          </select>
        </div>
        <div class="panel__actions">
          <button class="btn btn--secondary" type="button" id="copy-overlay-link">Copy Overlay Link</button>
          <span class="save-feedback" id="feedback-overlay-link">Copied</span>
        </div>
      </section>

      <!-- Lower third -->
      <section class="panel" style="animation-delay: 0.06s">
        <div class="panel__header">
          <span class="panel__title"><strong>Lower</strong> Third</span>
        </div>

        <div class="field-row">
          <div class="field">
            <label class="field__label" for="input-name">Reporter Name</label>
            <input type="text" id="input-name" value="SULEMAN MEMON" maxlength="40" autocomplete="off">
          </div>
          <div class="field">
            <label class="field__label" for="input-role">Role</label>
            <input type="text" id="input-role" value="FIELD REPORTER" maxlength="32" autocomplete="off">
          </div>
        </div>
        <div class="field">
          <label class="field__label" for="input-location">Location</label>
          <input type="text" id="input-location" value="HYDERABAD" maxlength="24" autocomplete="off">
        </div>

        <div class="panel__actions">
          <button class="btn btn--primary" type="button" id="update-lower-third">Update Lower Third</button>
          <span class="save-feedback" id="feedback-lower-third">Updated</span>
          <button class="toggle" type="button" id="toggle-lower-third" aria-pressed="true">
            <span class="toggle__track"><span class="toggle__thumb"></span></span>
            <span class="toggle__label">ON</span>
          </button>
        </div>
      </section>

      <!-- Breaking news -->
      <section class="panel panel--breaking" style="animation-delay: 0.1s">
        <div class="panel__header">
          <span class="panel__title"><strong>Breaking</strong> News</span>
        </div>

        <div class="field">
          <label class="field__label" for="input-headline">Headline</label>
          <textarea id="input-headline" maxlength="120">University announces campus closure tomorrow</textarea>
        </div>

        <div class="panel__actions">
          <button class="btn btn--primary" type="button" id="update-breaking">Update Breaking News</button>
          <span class="save-feedback" id="feedback-breaking">Updated</span>
          <button class="toggle" type="button" id="toggle-breaking" aria-pressed="true">
            <span class="toggle__track"><span class="toggle__thumb"></span></span>
            <span class="toggle__label">ON</span>
          </button>
        </div>
      </section>

      <!-- Ticker -->
      <section class="panel" style="animation-delay: 0.14s">
        <div class="panel__header">
          <span class="panel__title"><strong>Ticker</strong></span>
        </div>

        <div class="field">
          <label class="field__label" for="input-ticker">Ticker Text</label>
          <textarea id="input-ticker" maxlength="140">More updates from Hyderabad • Stay tuned for further information</textarea>
        </div>

        <div class="panel__actions">
          <button class="btn btn--primary" type="button" id="update-ticker">Update Ticker</button>
          <span class="save-feedback" id="feedback-ticker">Updated</span>
          <button class="toggle" type="button" id="toggle-ticker" aria-pressed="true">
            <span class="toggle__track"><span class="toggle__thumb"></span></span>
            <span class="toggle__label">ON</span>
          </button>
        </div>
      </section>

      <!-- Channel branding -->
      <section class="panel" style="animation-delay: 0.18s">
        <div class="panel__header">
          <span class="panel__title"><strong>Channel</strong> Branding</span>
        </div>
        <div class="branding-row">
          <span class="branding-mark"><span class="inner"></span></span>
          <div class="branding-info">
            <span class="branding-name">Lean Cast</span>
            <span class="branding-note">Active on Broadcast Screen</span>
          </div>
        </div>
      </section>

    </div>

  </main>
</div>

<script>
/* =========================================================
   1. STATE
   ========================================================= */
const state = {
  active_overlay: 'main',
  overlays: {
    main: {
      lower_third: { enabled: true, name: 'SULEMAN MEMON', role: 'FIELD REPORTER', location: 'HYDERABAD' },
      breaking_news: { enabled: true, headline: 'University announces campus closure tomorrow' },
      ticker: { enabled: true, label: 'HYDERABAD', text: 'More updates from Hyderabad • Stay tuned for further information' }
    }
  },
  lastUpdate: Date.now()
};

/* =========================================================
   2. ELEMENT REFERENCES
   ========================================================= */
const inputName = document.getElementById('input-name');
const inputRole = document.getElementById('input-role');
const inputLocation = document.getElementById('input-location');
const inputHeadline = document.getElementById('input-headline');
const inputTicker = document.getElementById('input-ticker');
const selectOverlay = document.getElementById('select-overlay');
const copyOverlayLinkBtn = document.getElementById('copy-overlay-link');

const pvName = document.getElementById('pv-name');
const pvRole = document.getElementById('pv-role');
const pvLocation = document.getElementById('pv-location');
const pvHeadline = document.getElementById('pv-headline');
const pvTickerText = document.getElementById('pv-ticker-text');
const pvLowerThird = document.getElementById('pv-lower-third');
const pvBreaking = document.getElementById('pv-breaking');
const pvTicker = document.getElementById('pv-ticker');

const toggleLowerThird = document.getElementById('toggle-lower-third');
const toggleBreaking = document.getElementById('toggle-breaking');
const toggleTicker = document.getElementById('toggle-ticker');
const statusLowerThird = document.getElementById('status-lower-third');
const statusBreaking = document.getElementById('status-breaking');
const statusTicker = document.getElementById('status-ticker');

const updateLowerThirdBtn = document.getElementById('update-lower-third');
const updateBreakingBtn = document.getElementById('update-breaking');
const updateTickerBtn = document.getElementById('update-ticker');
const feedbackLowerThird = document.getElementById('feedback-lower-third');
const feedbackBreaking = document.getElementById('feedback-breaking');
const feedbackTicker = document.getElementById('feedback-ticker');
const feedbackOverlayLink = document.getElementById('feedback-overlay-link');
const lastUpdateTimeEl = document.getElementById('lastUpdateTime');

/* =========================================================
   3. HELPERS
   ========================================================= */
function getActiveOverlay() {
  return state.overlays[state.active_overlay] || state.overlays.main;
}

function getPayload() {
  return {
    active_overlay: state.active_overlay,
    overlays: { [state.active_overlay]: getActiveOverlay() }
  };
}

function showFeedback(el, text = 'Updated') {
  el.textContent = text;
  el.classList.add('is-shown');
  clearTimeout(el._hideTimer);
  el._hideTimer = setTimeout(() => el.classList.remove('is-shown'), 1600);
}

function setSaving(button, saving) {
  button.disabled = saving;
  button.style.opacity = saving ? '0.65' : '';
}

/* =========================================================
   4. PREVIEW SYNC
   ========================================================= */
function syncPreviewText() {
  const overlay = getActiveOverlay();
  inputName.value = overlay.lower_third.name || '';
  inputRole.value = overlay.lower_third.role || '';
  inputLocation.value = overlay.lower_third.location || '';
  inputHeadline.value = overlay.breaking_news.headline || '';
  inputTicker.value = overlay.ticker.text || '';

  pvName.textContent = overlay.lower_third.name || 'REPORTER NAME';
  pvRole.textContent = (overlay.lower_third.role || 'ROLE').toUpperCase();
  pvLocation.textContent = (overlay.lower_third.location || 'LOCATION').toUpperCase();
  pvHeadline.textContent = overlay.breaking_news.headline || 'Breaking headline';
  pvTickerText.textContent = overlay.ticker.text || 'Ticker update';
}

function syncPreviewVisibility() {
  const overlay = getActiveOverlay();
  pvLowerThird.classList.toggle('is-off', !overlay.lower_third.enabled);
  pvBreaking.classList.toggle('is-off', !overlay.breaking_news.enabled);
  pvTicker.classList.toggle('is-off', !overlay.ticker.enabled);
}

/* =========================================================
   5. OVERLAY STATUS SYNC
   ========================================================= */
function syncOverlayRow(rowEl, isOn) {
  rowEl.classList.toggle('is-on', isOn);
  rowEl.querySelector('.overlay-row__state .text').textContent = isOn ? 'ON' : 'OFF';
}

function syncOverlayStatus() {
  const overlay = getActiveOverlay();
  syncOverlayRow(statusLowerThird, overlay.lower_third.enabled);
  syncOverlayRow(statusBreaking, overlay.breaking_news.enabled);
  syncOverlayRow(statusTicker, overlay.ticker.enabled);
}

function syncToggle(buttonEl, enabled) {
  buttonEl.setAttribute('aria-pressed', String(enabled));
  buttonEl.querySelector('.toggle__label').textContent = enabled ? 'ON' : 'OFF';
}

function syncControlsFromState() {
  const overlay = getActiveOverlay();
  selectOverlay.value = state.active_overlay;
  syncToggle(toggleLowerThird, overlay.lower_third.enabled);
  syncToggle(toggleBreaking, overlay.breaking_news.enabled);
  syncToggle(toggleTicker, overlay.ticker.enabled);
  syncPreviewText();
  syncPreviewVisibility();
  syncOverlayStatus();
}

/* =========================================================
   6. SERVER SYNC — AJAX only
   ========================================================= */
async function saveState(feedbackEl, buttonEl = null) {
  if (buttonEl) setSaving(buttonEl, true);

  try {
    const response = await fetch('../../api/update-overlay.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      cache: 'no-store',
      body: JSON.stringify(getPayload())
    });

    const result = await response.json();
    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Update failed');
    }

    state.lastUpdate = Date.now();
    refreshLastUpdateLabel();
    if (feedbackEl) showFeedback(feedbackEl, 'Updated');
  } catch (error) {
    console.error('Lean Cast update failed:', error);
    if (feedbackEl) showFeedback(feedbackEl, 'Failed');
  } finally {
    if (buttonEl) setSaving(buttonEl, false);
  }
}

async function loadState() {
  try {
    const response = await fetch('../../api/broadcast-state.php?t=' + Date.now(), { cache: 'no-store' });
    const result = await response.json();
    if (!response.ok || !result.success || !result.data) return;

    state.active_overlay = result.data.active_overlay || 'main';
    state.overlays = result.data.overlays || state.overlays;
    state.lastUpdate = result.data.updated_at ? result.data.updated_at * 1000 : Date.now();
    syncControlsFromState();
    refreshLastUpdateLabel();
  } catch (error) {
    console.error('Lean Cast state read failed:', error);
  }
}

/* =========================================================
   7. TOGGLE HANDLERS
   ========================================================= */
function wireToggle(buttonEl, key, feedbackEl) {
  buttonEl.addEventListener('click', () => {
    const overlay = getActiveOverlay();
    overlay[key].enabled = !overlay[key].enabled;
    syncControlsFromState();
    saveState(feedbackEl);
  });
}

wireToggle(toggleLowerThird, 'lower_third', feedbackLowerThird);
wireToggle(toggleBreaking, 'breaking_news', feedbackBreaking);
wireToggle(toggleTicker, 'ticker', feedbackTicker);

/* =========================================================
   8. LIVE PREVIEW WHILE TYPING
   ========================================================= */
[inputName, inputRole, inputLocation, inputHeadline, inputTicker].forEach((el) => {
  el.addEventListener('input', () => {
    const overlay = getActiveOverlay();
    overlay.lower_third.name = inputName.value;
    overlay.lower_third.role = inputRole.value;
    overlay.lower_third.location = inputLocation.value;
    overlay.breaking_news.headline = inputHeadline.value;
    overlay.ticker.text = inputTicker.value;

    pvName.textContent = inputName.value.trim() || 'REPORTER NAME';
    pvRole.textContent = (inputRole.value.trim() || 'ROLE').toUpperCase();
    pvLocation.textContent = (inputLocation.value.trim() || 'LOCATION').toUpperCase();
    pvHeadline.textContent = inputHeadline.value.trim() || 'Breaking headline';
    pvTickerText.textContent = inputTicker.value.trim() || 'Ticker update';
  });
});

/* =========================================================
   9. UPDATE ACTIONS
   ========================================================= */
updateLowerThirdBtn.addEventListener('click', () => saveState(feedbackLowerThird, updateLowerThirdBtn));
updateBreakingBtn.addEventListener('click', () => saveState(feedbackBreaking, updateBreakingBtn));
updateTickerBtn.addEventListener('click', () => saveState(feedbackTicker, updateTickerBtn));

selectOverlay.addEventListener('change', async () => {
  if (!state.overlays[selectOverlay.value]) {
    state.overlays[selectOverlay.value] = state.overlays.main;
  }
  state.active_overlay = selectOverlay.value;
  syncControlsFromState();
  await saveState(null, selectOverlay);
});

copyOverlayLinkBtn.addEventListener('click', async () => {
  const overlayUrl = new URL('../broadcast/', window.location.href).href;
  try {
    await navigator.clipboard.writeText(overlayUrl);
    showFeedback(feedbackOverlayLink, 'Copied');
  } catch (error) {
    const temp = document.createElement('input');
    temp.value = overlayUrl;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand('copy');
    temp.remove();
    showFeedback(feedbackOverlayLink, 'Copied');
  }
});

/* =========================================================
   10. RELATIVE "LAST UPDATE" TIMESTAMP
   ========================================================= */
function refreshLastUpdateLabel() {
  const seconds = Math.max(0, Math.floor((Date.now() - state.lastUpdate) / 1000));
  let label;
  if (seconds < 5) label = 'Just now';
  else if (seconds < 60) label = seconds + 's ago';
  else if (seconds < 3600) label = Math.floor(seconds / 60) + 'm ago';
  else label = Math.floor(seconds / 3600) + 'h ago';
  lastUpdateTimeEl.textContent = label;
}

setInterval(refreshLastUpdateLabel, 5000);

/* =========================================================
   11. INIT
   ========================================================= */
loadState().then(() => {
  syncControlsFromState();
  refreshLastUpdateLabel();
});
</script>

</body>
</html>