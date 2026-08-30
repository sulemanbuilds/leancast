<?php
/**
 * Lean Cast — Broadcast Screen (Phone 1)
 * Transparent overlay canvas rendered on top of the live camera feed.
 * Display only — no controls, no backend logic yet.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Lean Cast — Broadcast Screen</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>

/* =========================================================
   1. RESET & BASE
   ========================================================= */
*, *::before, *::after { box-sizing: border-box; }

html, body {
  width: 100%;
  height: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background: transparent !important;
  color: #F5F7FB;
  font-family: 'Inter', sans-serif;
  -webkit-font-smoothing: antialiased;
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
  --glass-fill: rgba(9, 11, 16, 0.55);

  --font-display: 'Space Grotesk', sans-serif;
  --font-body: 'Inter', sans-serif;
  --font-mono: 'JetBrains Mono', monospace;

  --ease-out: cubic-bezier(0.16, 1, 0.3, 1);

  /* consistent safe-area spacing for every anchored element */
  --edge: clamp(16px, 2.2vw, 32px);
  --ticker-height: 38px;
  --stack-gap: 14px;
}

/* =========================================================
   3. BROADCAST CANVAS — fully transparent overlay layer
   ========================================================= */
.broadcast-canvas {
  position: relative;
  width: 100vw;
  height: 100dvh;
  overflow: hidden;
  background: transparent !important;
  isolation: isolate;
}

/* =========================================================
   4. LOGO / LIVE STATUS — top edge, equal spacing
   ========================================================= */
.channel-logo {
  position: absolute;
  top: var(--edge);
  left: var(--edge);
  z-index: 5;
  display: flex;
  align-items: center;
  gap: 7px;
}

.channel-logo .mark {
  width: 13px;
  height: 13px;
  border-radius: 3px;
  background: linear-gradient(135deg, var(--orange-light), var(--orange));
  box-shadow: 0 0 12px rgba(255, 106, 0, 0.5);
  flex-shrink: 0;
}

.channel-logo .word {
  font-family: var(--font-display);
  font-weight: 600;
  font-size: 13px;
  letter-spacing: 0.02em;
  color: var(--white);
  text-shadow: 0 1px 6px rgba(0,0,0,0.7);
  white-space: nowrap;
}

.channel-logo .word span {
  color: var(--orange-light);
  font-weight: 500;
}

.live-status {
  position: absolute;
  top: var(--edge);
  right: var(--edge);
  z-index: 5;
  display: flex;
  align-items: center;
  gap: 6px;
  font-family: var(--font-mono);
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.1em;
  color: var(--white);
  text-shadow: 0 1px 6px rgba(0,0,0,0.7);
}

.live-status .live-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--green);
  box-shadow: 0 0 8px rgba(66, 232, 164, 0.85);
  animation: livePulse 1.8s ease-in-out infinite;
}

@keyframes livePulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.4; transform: scale(0.8); }
}

/* =========================================================
   5. BOTTOM STACK — anchors lower third + breaking news
   above the ticker with guaranteed, non-overlapping order.
   Flow-based (flex column) so real content height never
   causes elements to collide, regardless of text length.
   ========================================================= */
.bottom-stack {
  position: absolute;
  left: var(--edge);
  right: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge));
  z-index: 6;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: var(--stack-gap);
}

/* =========================================================
   6. LOWER THIRD — sharp broadcast graphic, not a card
   ========================================================= */
.lower-third {
  align-self: flex-start;
  max-width: min(56%, 460px);
  transform: translateY(10px);
  opacity: 0;
  transition: transform 0.55s var(--ease-out), opacity 0.4s ease;
}

.lower-third.is-visible {
  transform: translateY(0);
  opacity: 1;
}

.lower-third__bar {
  display: flex;
  align-items: stretch;
  background: var(--glass-fill);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-top: 1px solid var(--border-soft);
  border-bottom: 1px solid var(--border-softer);
  border-radius: 2px;
}

.lower-third__accent-edge {
  width: 3px;
  flex-shrink: 0;
  background: linear-gradient(180deg, var(--orange-light), var(--orange));
}

.lower-third__content {
  padding: 8px 14px 9px;
  display: flex;
  flex-direction: column;
  gap: 1px;
  min-width: 0;
}

.lower-third__name {
  font-family: var(--font-display);
  font-weight: 600;
  font-size: clamp(14px, 1.7vw, 18px);
  letter-spacing: 0.01em;
  color: var(--white);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.lower-third__role-row {
  display: flex;
  align-items: center;
  gap: 7px;
}

.lower-third__role {
  font-family: var(--font-mono);
  font-size: clamp(9px, 0.9vw, 10.5px);
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: var(--orange-light);
  white-space: nowrap;
}

.lower-third__divider {
  width: 1px;
  height: 9px;
  background: var(--border-soft);
  flex-shrink: 0;
}

.lower-third__location {
  font-family: var(--font-mono);
  font-size: clamp(9px, 0.9vw, 10.5px);
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: var(--muted);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* =========================================================
   7. BREAKING NEWS — full stack width, below lower third
   ========================================================= */
.breaking-news {
  align-self: stretch;
  display: flex;
  align-items: stretch;
  border-radius: 2px;
  overflow: hidden;
  border-top: 1px solid rgba(255, 106, 0, 0.4);
  border-bottom: 1px solid rgba(255, 106, 0, 0.15);
  background: var(--glass-fill);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  box-shadow: 0 0 30px rgba(255, 106, 0, 0.14);
  transform: translateY(14px);
  opacity: 0;
  transition: transform 0.5s var(--ease-out), opacity 0.4s ease;
}

.breaking-news.is-visible {
  transform: translateY(0);
  opacity: 1;
}

.breaking-news__tag {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 0 14px;
  background: linear-gradient(135deg, var(--orange), #E45800);
}

.breaking-news__tag .dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #0A0603;
  animation: livePulse 1.4s ease-in-out infinite;
}

.breaking-news__tag span {
  font-family: var(--font-mono);
  font-weight: 600;
  font-size: 10.5px;
  letter-spacing: 0.13em;
  color: #0A0603;
  white-space: nowrap;
}

.breaking-news__headline {
  display: flex;
  align-items: center;
  padding: 9px 16px;
  font-family: var(--font-display);
  font-weight: 600;
  font-size: clamp(12.5px, 1.4vw, 15px);
  color: var(--white);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* =========================================================
   8. TICKER — pinned to the absolute bottom edge, full width
   ========================================================= */
.ticker {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 6;
  height: var(--ticker-height);
  display: flex;
  align-items: center;
  background: var(--bg-elevated);
  border-top: 1px solid var(--border-soft);
  overflow: hidden;
  transform: translateY(0);
  opacity: 1;
  transition: transform 0.4s var(--ease-out), opacity 0.3s ease;
}

.ticker.is-hidden {
  transform: translateY(100%);
  opacity: 0;
}

.ticker__label {
  flex-shrink: 0;
  height: 100%;
  display: flex;
  align-items: center;
  padding: 0 16px;
  background: linear-gradient(135deg, var(--blue), #3A5FE0);
  font-family: var(--font-mono);
  font-weight: 600;
  font-size: 10px;
  letter-spacing: 0.12em;
  color: var(--white);
  white-space: nowrap;
}

.ticker__track {
  flex: 1;
  overflow: hidden;
  position: relative;
  height: 100%;
}

.ticker__track-inner {
  position: absolute;
  white-space: nowrap;
  display: flex;
  align-items: center;
  height: 100%;
  animation: tickerScroll 24s linear infinite;
  font-family: var(--font-mono);
  font-size: 12px;
  letter-spacing: 0.02em;
  color: var(--muted);
  will-change: transform;
}

.ticker__track-inner span {
  padding-right: 64px;
}

@keyframes tickerScroll {
  from { transform: translateX(0%); }
  to   { transform: translateX(-50%); }
}

/* =========================================================
   9. RESPONSIVE — overlays stay edge-anchored, not restacked
   ========================================================= */
@media (max-width: 860px) {
  .lower-third { max-width: 68%; }
}

@media (max-width: 620px) {
  :root {
    --ticker-height: 32px;
    --stack-gap: 10px;
  }

  .channel-logo .word { display: none; }

  .lower-third { max-width: none; align-self: stretch; }
  .lower-third__content { padding: 7px 12px 8px; }
  .lower-third__role-row { flex-wrap: wrap; row-gap: 3px; }

  .breaking-news { flex-direction: column; }
  .breaking-news__tag { padding: 5px 12px; }
  .breaking-news__headline {
    padding: 7px 12px 9px;
    white-space: normal;
    line-height: 1.28;
  }

  .ticker__label { padding: 0 10px; font-size: 9px; }
  .ticker__track-inner { font-size: 11px; }
}

@media (max-width: 400px) {
  .live-status { font-size: 10px; }
}

/* =========================================================
   10. REDUCED MOTION
   ========================================================= */
@media (prefers-reduced-motion: reduce) {
  .live-status .live-dot,
  .breaking-news__tag .dot {
    animation: none;
    opacity: 1;
  }

  .ticker__track-inner {
    animation-duration: 70s;
  }

  .lower-third,
  .breaking-news,
  .ticker {
    transition: none;
  }
}

</style>
</head>
<body>

<!-- ============================================================
     BROADCAST CANVAS — transparent overlay over the live feed
     ============================================================ -->
<div class="broadcast-canvas" id="broadcastCanvas">

  <!-- Channel logo -->
  <div class="channel-logo">
    <span class="mark"></span>
    <span class="word">LEAN<span>CAST</span></span>
  </div>

  <!-- Live indicator -->
  <div class="live-status">
    <span class="live-dot"></span>
    <span>LIVE</span>
  </div>

  <!-- Lower third + breaking news, stacked so they never overlap -->
  <div class="bottom-stack">

    <div class="lower-third is-visible" id="lowerThird">
      <div class="lower-third__bar">
        <div class="lower-third__accent-edge"></div>
        <div class="lower-third__content">
          <div class="lower-third__name" id="lt-name">SULEMAN MEMON</div>
          <div class="lower-third__role-row">
            <span class="lower-third__role" id="lt-role">FIELD REPORTER</span>
            <span class="lower-third__divider"></span>
            <span class="lower-third__location" id="lt-location">HYDERABAD</span>
          </div>
        </div>
      </div>
    </div>

    <div class="breaking-news is-visible" id="breakingNews">
      <div class="breaking-news__tag">
        <span class="dot"></span>
        <span>BREAKING NEWS</span>
      </div>
      <div class="breaking-news__headline" id="bn-headline">University announces campus closure tomorrow</div>
    </div>

  </div>

  <!-- Ticker -->
  <div class="ticker" id="ticker">
    <div class="ticker__label">HYDERABAD</div>
    <div class="ticker__track">
      <div class="ticker__track-inner" id="tickerInner">
        <span>More updates from Hyderabad • Stay tuned for further information</span>
        <span>More updates from Hyderabad • Stay tuned for further information</span>
      </div>
    </div>
  </div>

</div>

<script>
/* =========================================================
   Presentation-only behavior.
   No demo controls, no backend calls — overlays are static
   on load and will later be driven by Phone 2 / the PHP
   backend.
   ========================================================= */

/* Entrance timing: let the lower third and breaking news
   settle in slightly staggered rather than popping in at once. */
document.addEventListener('DOMContentLoaded', () => {
  const lowerThird = document.getElementById('lowerThird');
  const breakingNews = document.getElementById('breakingNews');

  lowerThird.classList.remove('is-visible');
  breakingNews.classList.remove('is-visible');

  requestAnimationFrame(() => {
    setTimeout(() => lowerThird.classList.add('is-visible'), 200);
    setTimeout(() => breakingNews.classList.add('is-visible'), 480);
  });
});
</script>

</body>
</html>