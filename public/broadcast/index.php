<?php
/**
 * Lean Cast — Broadcast Screen (Phone 1)
 * Transparent overlay canvas rendered on top of the live camera feed.
 * Display only — no controls. Reads initial state server-side from
 * data/broadcast.json (if present) so the very first paint already
 * reflects reality, then keeps itself in sync via polling.
 */

$validDesigns = ['design-01', 'design-02', 'design-03', 'design-04', 'design-05'];

$defaultState = [
    'active_overlay' => 'main',
    'active_design'  => 'design-01',
    'overlays' => [
        'main' => [
            'lower_third'   => ['enabled' => true, 'name' => 'SULEMAN MEMON', 'role' => 'FIELD REPORTER', 'location' => 'HYDERABAD'],
            'breaking_news' => ['enabled' => true, 'headline' => 'University announces campus closure tomorrow'],
            'ticker'        => ['enabled' => true, 'label' => 'HYDERABAD', 'text' => 'More updates from Hyderabad \u2022 Stay tuned for further information'],
        ],
    ],
];

$state = $defaultState;
$dataFile = __DIR__ . '/../../data/broadcast.json';

if (is_file($dataFile)) {
    $decoded = json_decode(file_get_contents($dataFile), true);
    if (is_array($decoded)) {
        $state = array_replace_recursive($defaultState, $decoded);
    }
}

if (!in_array($state['active_design'], $validDesigns, true)) {
    $state['active_design'] = 'design-01';
}

$activeOverlayKey = is_string($state['active_overlay']) && $state['active_overlay'] !== '' ? $state['active_overlay'] : 'main';
$overlay = $state['overlays'][$activeOverlayKey] ?? $state['overlays']['main'];

$lt = $overlay['lower_third'] ?? [];
$bn = $overlay['breaking_news'] ?? [];
$tk = $overlay['ticker'] ?? [];

function lc_text($value, $fallback) {
    $value = is_string($value) ? trim($value) : '';
    return htmlspecialchars($value !== '' ? $value : $fallback, ENT_QUOTES, 'UTF-8');
}

$name         = lc_text($lt['name'] ?? null, 'REPORTER NAME');
$role         = lc_text($lt['role'] ?? null, 'ROLE');
$location     = lc_text($lt['location'] ?? null, 'LOCATION');
$headline     = lc_text($bn['headline'] ?? null, 'Breaking headline');
$tickerLabel  = lc_text($tk['label'] ?? null, 'LIVE');
$tickerText   = lc_text($tk['text'] ?? null, 'Stay tuned for further updates');

$ltHidden = empty($lt['enabled']) ? ' is-hidden' : '';
$bnHidden = empty($bn['enabled']) ? ' is-hidden' : '';
$tkHidden = empty($tk['enabled']) ? ' is-hidden' : '';

$activeDesign = $state['active_design'];
function lc_active($id, $active) { return $id === $active ? ' is-active' : ''; }
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
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
  background: transparent;
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

  /* consistent safe-area spacing — unchanged across every design */
  --edge: clamp(16px, 2.2vw, 32px);
  --ticker-height: 38px;
  --stack-gap: 14px;
}

/* =========================================================
   3. BROADCAST CANVAS — fully transparent overlay layer
   (size/behaviour preserved exactly; only the composition
   inside changes between designs)
   ========================================================= */
.broadcast-canvas {
  position: relative;
  width: 100vw;
  height: 100dvh;
  overflow: hidden;
  background: transparent;
  isolation: isolate;
}

/* =========================================================
   4. LOGO / LIVE STATUS — persistent across all designs
   ========================================================= */
.channel-logo {
  position: absolute;
  top: var(--edge);
  left: var(--edge);
  z-index: 8;
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

.channel-logo .word span { color: var(--orange-light); font-weight: 500; }

.channel-logo .rule {
  width: 100%;
  height: 1px;
  background: linear-gradient(90deg, var(--orange-light), transparent);
  margin-top: 3px;
  display: none;
}

.live-status {
  position: absolute;
  top: var(--edge);
  right: var(--edge);
  z-index: 8;
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

/* subtle per-design logo variation (style only — position never moves) */
.broadcast-canvas[data-active-design="design-04"] .channel-logo .word { display: none; }
.broadcast-canvas[data-active-design="design-05"] .channel-logo .rule { display: block; }

/* =========================================================
   5. DESIGN SWITCHER SHELL
   ========================================================= */
.design {
  position: absolute;
  inset: 0;
  z-index: 2;
  display: none;
}

.design.is-active { display: block; }

/* Shared enable/disable behaviour for lower third, breaking
   news and ticker — identical mechanism in every design, only
   their own CSS gives them a different look. */
.overlay-toggle {
  transition: opacity 0.3s var(--ease-out), transform 0.35s var(--ease-out);
}

.overlay-toggle.is-hidden {
  opacity: 0;
  pointer-events: none;
}

[data-overlay="lower-third"].is-hidden,
[data-overlay="breaking-news"].is-hidden {
  transform: translateY(10px);
}

[data-overlay="ticker"].is-hidden {
  transform: translateY(100%);
}

/* Shared ticker scroll motion — reused by every design */
@keyframes tickerScroll {
  from { transform: translateX(0%); }
  to   { transform: translateX(-50%); }
}

/* =========================================================================================
   6. DESIGN 01 — "CLASSIC STRIP"
   Full-width accent-edge lower third, ribbon breaking news, two-tone ticker.
   The established Lean Cast baseline look.
   ========================================================================================= */
.d1-stack {
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

.d1-lower-third { align-self: flex-start; max-width: min(56%, 460px); }

.d1-lower-third__bar {
  display: flex;
  align-items: stretch;
  background: var(--glass-fill);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-top: 1px solid var(--border-soft);
  border-bottom: 1px solid var(--border-softer);
  border-radius: 2px;
}

.d1-lower-third__edge { width: 3px; flex-shrink: 0; background: linear-gradient(180deg, var(--orange-light), var(--orange)); }
.d1-lower-third__content { padding: 8px 14px 9px; display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.d1-lower-third__name { font-family: var(--font-display); font-weight: 600; font-size: clamp(14px, 1.7vw, 18px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.d1-lower-third__meta { display: flex; align-items: center; gap: 7px; }
.d1-lower-third__role { font-family: var(--font-mono); font-size: clamp(9px, 0.9vw, 10.5px); letter-spacing: 0.09em; text-transform: uppercase; color: var(--orange-light); white-space: nowrap; }
.d1-lower-third__divider { width: 1px; height: 9px; background: var(--border-soft); flex-shrink: 0; }
.d1-lower-third__location { font-family: var(--font-mono); font-size: clamp(9px, 0.9vw, 10.5px); letter-spacing: 0.09em; text-transform: uppercase; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.d1-breaking {
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
}

.d1-breaking__tag { flex-shrink: 0; display: flex; align-items: center; gap: 6px; padding: 0 14px; background: linear-gradient(135deg, var(--orange), #E45800); }
.d1-breaking__tag .dot { width: 5px; height: 5px; border-radius: 50%; background: #0A0603; animation: livePulse 1.4s ease-in-out infinite; }
.d1-breaking__tag span { font-family: var(--font-mono); font-weight: 600; font-size: 10.5px; letter-spacing: 0.13em; color: #0A0603; white-space: nowrap; }
.d1-breaking__headline { display: flex; align-items: center; padding: 9px 16px; font-family: var(--font-display); font-weight: 600; font-size: clamp(12.5px, 1.4vw, 15px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.design[data-design="design-01"] .ticker {
  position: absolute; left: 0; right: 0; bottom: 0; z-index: 6; height: var(--ticker-height);
  display: flex; align-items: center; background: var(--bg-elevated); border-top: 1px solid var(--border-soft); overflow: hidden;
}
.design[data-design="design-01"] .ticker__label { flex-shrink: 0; height: 100%; display: flex; align-items: center; padding: 0 16px; background: linear-gradient(135deg, var(--blue), #3A5FE0); font-family: var(--font-mono); font-weight: 600; font-size: 10px; letter-spacing: 0.12em; color: var(--white); white-space: nowrap; }
.design[data-design="design-01"] .ticker__track { flex: 1; overflow: hidden; position: relative; height: 100%; }
.design[data-design="design-01"] .ticker__track-inner { position: absolute; white-space: nowrap; display: flex; align-items: center; height: 100%; animation: tickerScroll 24s linear infinite; font-family: var(--font-mono); font-size: 12px; letter-spacing: 0.02em; color: var(--muted); }
.design[data-design="design-01"] .ticker__track-inner span { padding-right: 64px; }

/* =========================================================================================
   7. DESIGN 02 — "CORNER PANEL"
   Boxed name-card lower third, angled breaking-news ribbon, gradient ticker tab.
   ========================================================================================= */
.design[data-design="design-02"] { --ticker-height: 36px; }

.d2-panel {
  position: absolute;
  left: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge));
  z-index: 6;
  max-width: min(46%, 340px);
  background: var(--glass-fill);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border: 1.5px solid rgba(255, 157, 66, 0.32);
  border-radius: 6px;
  padding: 12px 16px;
  box-shadow: 0 12px 34px -14px rgba(0,0,0,0.55);
}

.d2-panel__chip { width: 16px; height: 16px; border-radius: 4px; background: linear-gradient(135deg, var(--orange-light), var(--orange)); margin-bottom: 8px; }
.d2-panel__name { display: block; font-family: var(--font-display); font-weight: 600; font-size: clamp(14px, 1.6vw, 17px); color: var(--white); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.d2-panel__role { display: block; font-family: var(--font-mono); font-size: 9.5px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--orange-light); margin-bottom: 2px; }
.d2-panel__location { display: block; font-family: var(--font-mono); font-size: 9.5px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); }

.d2-breaking {
  position: absolute;
  left: var(--edge);
  right: calc(var(--edge) + 8%);
  bottom: calc(var(--ticker-height) + var(--edge) + 78px);
  z-index: 6;
  display: flex;
  align-items: stretch;
  height: 34px;
  background: var(--glass-fill);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-top: 1px solid rgba(255, 106, 0, 0.35);
  clip-path: polygon(0 0, 100% 0, calc(100% - 18px) 100%, 0 100%);
}

.d2-breaking__tag { flex-shrink: 0; display: flex; align-items: center; padding: 0 16px 0 12px; background: linear-gradient(135deg, var(--orange), #E45800); font-family: var(--font-mono); font-weight: 600; font-size: 10px; letter-spacing: 0.12em; color: #0A0603; white-space: nowrap; }
.d2-breaking__headline { display: flex; align-items: center; padding: 0 16px; font-family: var(--font-display); font-weight: 600; font-size: clamp(11.5px, 1.3vw, 14px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.design[data-design="design-02"] .ticker { position: absolute; left: 0; right: 0; bottom: 0; z-index: 6; height: var(--ticker-height); display: flex; align-items: center; overflow: hidden; background: linear-gradient(90deg, rgba(79,124,255,0.16), var(--bg-elevated) 40%); border-top: 1px solid var(--border-soft); }
.design[data-design="design-02"] .ticker__label { flex-shrink: 0; display: flex; align-items: center; gap: 6px; padding: 0 14px; font-family: var(--font-mono); font-weight: 600; font-size: 9.5px; letter-spacing: 0.1em; color: var(--blue); white-space: nowrap; }
.design[data-design="design-02"] .ticker__label .sq { width: 6px; height: 6px; background: var(--blue); border-radius: 1px; flex-shrink: 0; }
.design[data-design="design-02"] .ticker__track { flex: 1; overflow: hidden; position: relative; height: 100%; }
.design[data-design="design-02"] .ticker__track-inner { position: absolute; white-space: nowrap; display: flex; align-items: center; height: 100%; animation: tickerScroll 24s linear infinite; font-family: var(--font-mono); font-size: 11.5px; letter-spacing: 0.02em; color: var(--muted); }
.design[data-design="design-02"] .ticker__track-inner span { padding-right: 60px; }

/* =========================================================================================
   8. DESIGN 03 — "SPLIT BAR"
   Continuous two-tone name bar, stacked breaking-news block, dual-cap ticker.
   ========================================================================================= */
.d3-stack {
  position: absolute;
  left: var(--edge);
  right: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge));
  z-index: 6;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 10px;
}

.d3-bar { align-self: flex-start; display: flex; max-width: min(64%, 520px); border-radius: 2px; overflow: hidden; box-shadow: 0 10px 28px -14px rgba(0,0,0,0.5); }
.d3-bar__name { flex-shrink: 0; display: flex; align-items: center; padding: 9px 16px; background: linear-gradient(135deg, var(--orange-light), var(--orange)); font-family: var(--font-display); font-weight: 700; font-size: clamp(13px, 1.5vw, 16px); color: #150900; white-space: nowrap; }
.d3-bar__meta { display: flex; align-items: center; gap: 7px; padding: 9px 14px; background: var(--glass-fill); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--white); white-space: nowrap; overflow: hidden; }
.d3-bar__meta .loc { color: var(--muted); }
.d3-bar__meta .sep { color: var(--border-soft); }

.d3-breaking { align-self: stretch; display: flex; flex-direction: column; gap: 2px; padding: 9px 14px; border-left: 3px solid var(--orange); background: var(--glass-fill); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); box-shadow: 0 0 26px rgba(255,106,0,0.12); }
.d3-breaking__label { font-family: var(--font-mono); font-weight: 600; font-size: 9.5px; letter-spacing: 0.14em; color: var(--orange-light); }
.d3-breaking__headline { font-family: var(--font-display); font-weight: 600; font-size: clamp(13px, 1.5vw, 16px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.design[data-design="design-03"] .ticker { position: absolute; left: 0; right: 0; bottom: 0; z-index: 6; height: var(--ticker-height); display: flex; align-items: stretch; background: var(--bg-elevated); border-top: 1px solid var(--border-soft); overflow: hidden; }
.design[data-design="design-03"] .ticker__label { flex-shrink: 0; display: flex; align-items: center; padding: 0 14px; background: linear-gradient(135deg, var(--blue), #3A5FE0); font-family: var(--font-mono); font-weight: 600; font-size: 10px; letter-spacing: 0.1em; color: var(--white); white-space: nowrap; }
.design[data-design="design-03"] .ticker__track { flex: 1; overflow: hidden; position: relative; }
.design[data-design="design-03"] .ticker__track-inner { position: absolute; top: 0; white-space: nowrap; display: flex; align-items: center; height: 100%; animation: tickerScroll 24s linear infinite; font-family: var(--font-mono); font-size: 11.5px; color: var(--muted); }
.design[data-design="design-03"] .ticker__track-inner span { padding-right: 60px; }
.design[data-design="design-03"] .ticker__end { flex-shrink: 0; width: 8px; background: linear-gradient(135deg, var(--orange), #E45800); }

/* =========================================================================================
   9. DESIGN 04 — "MINIMAL TAG"
   Pill name tag, slim accent-line breaking strip, ultra-thin mono ticker.
   ========================================================================================= */
.design[data-design="design-04"] { --ticker-height: 24px; }

.d4-pill {
  position: absolute;
  left: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge) + 40px);
  z-index: 6;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 7px 14px 7px 8px;
  background: var(--glass-fill);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid var(--border-soft);
  border-radius: 999px;
  max-width: min(64%, 420px);
}

.d4-pill__dot { width: 6px; height: 6px; border-radius: 50%; background: var(--orange-light); flex-shrink: 0; }
.d4-pill__text { font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.03em; color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.d4-pill__text .role { color: var(--muted); }

.d4-breaking {
  position: absolute;
  left: var(--edge);
  right: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge));
  z-index: 6;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 14px;
  background: var(--glass-fill);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border-radius: 3px;
}

.d4-breaking__bar { width: 2px; align-self: stretch; background: var(--orange); border-radius: 1px; animation: livePulse 1.6s ease-in-out infinite; flex-shrink: 0; }
.d4-breaking__label { font-family: var(--font-mono); font-weight: 600; font-size: 9px; letter-spacing: 0.12em; color: var(--orange-light); flex-shrink: 0; }
.d4-breaking__headline { font-family: var(--font-body); font-weight: 500; font-size: clamp(11.5px, 1.3vw, 13.5px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.design[data-design="design-04"] .ticker { position: absolute; left: 0; right: 0; bottom: 0; z-index: 6; height: var(--ticker-height); display: flex; align-items: center; overflow: hidden; background: rgba(7,9,13,0.7); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }
.design[data-design="design-04"] .ticker__label { flex-shrink: 0; display: flex; align-items: center; gap: 5px; padding: 0 12px; font-family: var(--font-mono); font-size: 8.5px; letter-spacing: 0.08em; color: var(--muted); white-space: nowrap; }
.design[data-design="design-04"] .ticker__label .dot { width: 4px; height: 4px; border-radius: 50%; background: var(--green); }
.design[data-design="design-04"] .ticker__track { flex: 1; overflow: hidden; position: relative; height: 100%; }
.design[data-design="design-04"] .ticker__track-inner { position: absolute; white-space: nowrap; display: flex; align-items: center; height: 100%; animation: tickerScroll 26s linear infinite; font-family: var(--font-mono); font-size: 10px; color: var(--muted); }
.design[data-design="design-04"] .ticker__track-inner span { padding-right: 56px; }

/* =========================================================================================
   10. DESIGN 05 — "NEWS DESK"
   Bold parallelogram-backed lower third, heavy breaking banner, dual-segment ticker.
   ========================================================================================= */
.design[data-design="design-05"] { --ticker-height: 42px; }

.d5-stack {
  position: absolute;
  left: var(--edge);
  right: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge));
  z-index: 6;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 12px;
}

.d5-lower-third { position: relative; align-self: flex-start; max-width: min(60%, 480px); padding: 4px 0; }
.d5-lower-third__shape { position: absolute; left: -6px; top: 2px; bottom: 2px; width: 42px; background: linear-gradient(135deg, var(--orange-light), var(--orange)); clip-path: polygon(14% 0, 100% 0, 86% 100%, 0% 100%); z-index: -1; opacity: 0.9; }
.d5-lower-third__bar { display: flex; flex-direction: column; gap: 3px; padding: 10px 18px 11px 26px; background: var(--glass-fill); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-top: 1px solid var(--border-soft); }
.d5-lower-third__name { font-family: var(--font-display); font-weight: 700; font-size: clamp(17px, 2vw, 22px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.d5-lower-third__rule { width: 28px; height: 2px; background: var(--orange); margin: 2px 0; }
.d5-lower-third__meta { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); white-space: nowrap; }
.d5-lower-third__meta .role { color: var(--orange-light); }

.d5-breaking { align-self: stretch; display: flex; align-items: stretch; border-radius: 2px; overflow: hidden; box-shadow: 0 0 40px rgba(255,106,0,0.22); }
.d5-breaking__tag { flex-shrink: 0; display: flex; align-items: center; padding: 0 20px; background: linear-gradient(135deg, var(--orange), #E45800); }
.d5-breaking__tag span { font-family: var(--font-mono); font-weight: 700; font-size: 12px; letter-spacing: 0.14em; color: #0A0603; white-space: nowrap; }
.d5-breaking__headline { flex: 1; display: flex; align-items: center; padding: 12px 20px; background: var(--glass-fill); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); font-family: var(--font-display); font-weight: 700; font-size: clamp(14px, 1.7vw, 18px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.design[data-design="design-05"] .ticker { position: absolute; left: 0; right: 0; bottom: 0; z-index: 6; height: var(--ticker-height); display: flex; align-items: stretch; background: var(--bg-elevated); border-top: 1px solid var(--border-soft); overflow: hidden; }
.design[data-design="design-05"] .ticker__label { flex-shrink: 0; display: flex; align-items: center; padding: 0 18px; background: linear-gradient(135deg, var(--orange-light), var(--orange)); font-family: var(--font-mono); font-weight: 700; font-size: 11px; letter-spacing: 0.1em; color: #150900; white-space: nowrap; }
.design[data-design="design-05"] .ticker__segment { flex-shrink: 0; display: flex; align-items: center; padding: 0 14px; background: linear-gradient(135deg, var(--blue), #3A5FE0); font-family: var(--font-mono); font-weight: 600; font-size: 9.5px; letter-spacing: 0.1em; color: var(--white); white-space: nowrap; }
.design[data-design="design-05"] .ticker__track { flex: 1; overflow: hidden; position: relative; }
.design[data-design="design-05"] .ticker__track-inner { position: absolute; white-space: nowrap; display: flex; align-items: center; height: 100%; animation: tickerScroll 24s linear infinite; font-family: var(--font-mono); font-size: 12.5px; color: var(--muted); }
.design[data-design="design-05"] .ticker__track-inner span { padding-right: 64px; }

/* =========================================================
   11. RESPONSIVE — overlays stay anchored in every design
   ========================================================= */
@media (max-width: 860px) {
  .d1-lower-third { max-width: 68%; }
  .d3-bar { max-width: 78%; }
  .d5-lower-third { max-width: 74%; }
}

@media (max-width: 620px) {
  .channel-logo .word { display: none; }

  /* Design 01 */
  .d1-lower-third { max-width: none; align-self: stretch; }
  .d1-lower-third__content { padding: 7px 12px 8px; }
  .d1-breaking__headline { padding: 7px 12px 9px; white-space: normal; line-height: 1.28; }

  /* Design 02 */
  .d2-panel { right: var(--edge); max-width: none; }
  .d2-breaking { right: var(--edge); bottom: calc(var(--ticker-height) + var(--edge) + 68px); clip-path: none; }

  /* Design 03 */
  .d3-bar { max-width: none; align-self: stretch; flex-direction: column; }
  .d3-bar__name, .d3-bar__meta { width: 100%; }

  /* Design 04 */
  .d4-pill { max-width: none; right: var(--edge); }
  .d4-breaking__headline { white-space: normal; line-height: 1.3; }

  /* Design 05 */
  .d5-lower-third { max-width: none; align-self: stretch; }
  .d5-breaking { flex-direction: column; }
  .d5-breaking__headline { padding: 9px 18px 11px; white-space: normal; line-height: 1.3; }
}

/* =========================================================
   12. REDUCED MOTION
   ========================================================= */
@media (prefers-reduced-motion: reduce) {
  .live-status .live-dot,
  .d1-breaking__tag .dot,
  .d4-breaking__bar {
    animation: none;
    opacity: 1;
  }

  [class*="__track-inner"] {
    animation-duration: 70s;
  }

  .overlay-toggle {
    transition: none;
  }
}

</style>
</head>
<body>

<!-- ============================================================
     BROADCAST CANVAS — transparent overlay over the live feed
     ============================================================ -->
<div class="broadcast-canvas" id="broadcastCanvas" data-active-design="<?= htmlspecialchars($activeDesign, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Channel logo (persistent) -->
  <div class="channel-logo">
    <span class="mark"></span>
    <div>
      <span class="word">LEAN<span>CAST</span></span>
      <div class="rule"></div>
    </div>
  </div>

  <!-- Live indicator (persistent) -->
  <div class="live-status">
    <span class="live-dot"></span>
    <span>LIVE</span>
  </div>

  <!-- ============================================================
       DESIGN 01 — Classic Strip
       ============================================================ -->
  <div class="design<?= lc_active('design-01', $activeDesign) ?>" data-design="design-01">
    <div class="d1-stack">
      <div class="d1-lower-third stagger-item overlay-toggle<?= $ltHidden ?>" data-overlay="lower-third">
        <div class="d1-lower-third__bar">
          <span class="d1-lower-third__edge"></span>
          <div class="d1-lower-third__content">
            <span class="d1-lower-third__name" data-field="name"><?= $name ?></span>
            <div class="d1-lower-third__meta">
              <span class="d1-lower-third__role" data-field="role"><?= $role ?></span>
              <span class="d1-lower-third__divider"></span>
              <span class="d1-lower-third__location" data-field="location"><?= $location ?></span>
            </div>
          </div>
        </div>
      </div>
      <div class="d1-breaking stagger-item overlay-toggle<?= $bnHidden ?>" data-overlay="breaking-news">
        <div class="d1-breaking__tag"><span class="dot"></span><span>BREAKING NEWS</span></div>
        <div class="d1-breaking__headline" data-field="headline"><?= $headline ?></div>
      </div>
    </div>
    <div class="ticker stagger-item overlay-toggle<?= $tkHidden ?>" data-overlay="ticker">
      <div class="ticker__label" data-field="ticker-label"><?= $tickerLabel ?></div>
      <div class="ticker__track">
        <div class="ticker__track-inner">
          <span data-field="ticker-text"><?= $tickerText ?></span>
          <span data-field="ticker-text"><?= $tickerText ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
       DESIGN 02 — Corner Panel
       ============================================================ -->
  <div class="design<?= lc_active('design-02', $activeDesign) ?>" data-design="design-02">
    <div class="d2-panel stagger-item overlay-toggle<?= $ltHidden ?>" data-overlay="lower-third">
      <div class="d2-panel__chip"></div>
      <span class="d2-panel__name" data-field="name"><?= $name ?></span>
      <span class="d2-panel__role" data-field="role"><?= $role ?></span>
      <span class="d2-panel__location" data-field="location"><?= $location ?></span>
    </div>
    <div class="d2-breaking stagger-item overlay-toggle<?= $bnHidden ?>" data-overlay="breaking-news">
      <div class="d2-breaking__tag">BREAKING</div>
      <div class="d2-breaking__headline" data-field="headline"><?= $headline ?></div>
    </div>
    <div class="ticker stagger-item overlay-toggle<?= $tkHidden ?>" data-overlay="ticker">
      <div class="ticker__label"><span class="sq"></span><span data-field="ticker-label"><?= $tickerLabel ?></span></div>
      <div class="ticker__track">
        <div class="ticker__track-inner">
          <span data-field="ticker-text"><?= $tickerText ?></span>
          <span data-field="ticker-text"><?= $tickerText ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
       DESIGN 03 — Split Bar
       ============================================================ -->
  <div class="design<?= lc_active('design-03', $activeDesign) ?>" data-design="design-03">
    <div class="d3-stack">
      <div class="d3-bar stagger-item overlay-toggle<?= $ltHidden ?>" data-overlay="lower-third">
        <span class="d3-bar__name" data-field="name"><?= $name ?></span>
        <span class="d3-bar__meta"><span class="role" data-field="role"><?= $role ?></span><span class="sep">&nbsp;·&nbsp;</span><span class="loc" data-field="location"><?= $location ?></span></span>
      </div>
      <div class="d3-breaking stagger-item overlay-toggle<?= $bnHidden ?>" data-overlay="breaking-news">
        <span class="d3-breaking__label">BREAKING NEWS</span>
        <span class="d3-breaking__headline" data-field="headline"><?= $headline ?></span>
      </div>
    </div>
    <div class="ticker stagger-item overlay-toggle<?= $tkHidden ?>" data-overlay="ticker">
      <div class="ticker__label" data-field="ticker-label"><?= $tickerLabel ?></div>
      <div class="ticker__track">
        <div class="ticker__track-inner">
          <span data-field="ticker-text"><?= $tickerText ?></span>
          <span data-field="ticker-text"><?= $tickerText ?></span>
        </div>
      </div>
      <div class="ticker__end"></div>
    </div>
  </div>

  <!-- ============================================================
       DESIGN 04 — Minimal Tag
       ============================================================ -->
  <div class="design<?= lc_active('design-04', $activeDesign) ?>" data-design="design-04">
    <div class="d4-pill stagger-item overlay-toggle<?= $ltHidden ?>" data-overlay="lower-third">
      <span class="d4-pill__dot"></span>
      <span class="d4-pill__text"><span data-field="name"><?= $name ?></span> · <span class="role" data-field="role"><?= $role ?></span></span>
    </div>
    <div class="d4-breaking stagger-item overlay-toggle<?= $bnHidden ?>" data-overlay="breaking-news">
      <span class="d4-breaking__bar"></span>
      <span class="d4-breaking__label">BREAKING</span>
      <span class="d4-breaking__headline" data-field="headline"><?= $headline ?></span>
    </div>
    <div class="ticker stagger-item overlay-toggle<?= $tkHidden ?>" data-overlay="ticker">
      <div class="ticker__label"><span class="dot"></span><span data-field="ticker-label"><?= $tickerLabel ?></span></div>
      <div class="ticker__track">
        <div class="ticker__track-inner">
          <span data-field="ticker-text"><?= $tickerText ?></span>
          <span data-field="ticker-text"><?= $tickerText ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
       DESIGN 05 — News Desk
       ============================================================ -->
  <div class="design<?= lc_active('design-05', $activeDesign) ?>" data-design="design-05">
    <div class="d5-stack">
      <div class="d5-lower-third stagger-item overlay-toggle<?= $ltHidden ?>" data-overlay="lower-third">
        <span class="d5-lower-third__shape"></span>
        <div class="d5-lower-third__bar">
          <span class="d5-lower-third__name" data-field="name"><?= $name ?></span>
          <span class="d5-lower-third__rule"></span>
          <span class="d5-lower-third__meta"><span class="role" data-field="role"><?= $role ?></span> &nbsp;·&nbsp; <span data-field="location"><?= $location ?></span></span>
        </div>
      </div>
      <div class="d5-breaking stagger-item overlay-toggle<?= $bnHidden ?>" data-overlay="breaking-news">
        <div class="d5-breaking__tag"><span>BREAKING NEWS</span></div>
        <div class="d5-breaking__headline" data-field="headline"><?= $headline ?></div>
      </div>
    </div>
    <div class="ticker stagger-item overlay-toggle<?= $tkHidden ?>" data-overlay="ticker">
      <div class="ticker__label" data-field="ticker-label"><?= $tickerLabel ?></div>
      <div class="ticker__segment">UPDATE</div>
      <div class="ticker__track">
        <div class="ticker__track-inner">
          <span data-field="ticker-text"><?= $tickerText ?></span>
          <span data-field="ticker-text"><?= $tickerText ?></span>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
/* =========================================================
   1. CONFIG & STATE
   ========================================================= */
const VALID_DESIGNS = ['design-01', 'design-02', 'design-03', 'design-04', 'design-05'];
const POLL_INTERVAL_MS = 5000;

const canvas = document.getElementById('broadcastCanvas');
let currentDesign = canvas.getAttribute('data-active-design') || 'design-01';

/* =========================================================
   2. CONTENT BINDING — shared across all five designs
   ========================================================= */
function setField(name, value, fallback) {
  const text = (value && String(value).trim()) || fallback || '';
  document.querySelectorAll('[data-field="' + name + '"]').forEach((el) => {
    el.textContent = text;
  });
}

function setOverlayVisible(key, enabled) {
  document.querySelectorAll('[data-overlay="' + key + '"]').forEach((el) => {
    el.classList.toggle('is-hidden', !enabled);
  });
}

function applyOverlayData(overlay) {
  if (!overlay) return;
  const lt = overlay.lower_third || {};
  const bn = overlay.breaking_news || {};
  const tk = overlay.ticker || {};

  setField('name', lt.name, 'REPORTER NAME');
  setField('role', lt.role, 'ROLE');
  setField('location', lt.location, 'LOCATION');
  setField('headline', bn.headline, 'Breaking headline');
  setField('ticker-label', tk.label, 'LIVE');
  setField('ticker-text', tk.text, 'Stay tuned for further updates');

  setOverlayVisible('lower-third', !!lt.enabled);
  setOverlayVisible('breaking-news', !!bn.enabled);
  setOverlayVisible('ticker', !!tk.enabled);
}

/* =========================================================
   3. DESIGN SWITCHING — GSAP-driven, falls back to an
      instant swap if GSAP hasn't loaded or motion is reduced
   ========================================================= */
function switchDesign(newDesign) {
  if (!VALID_DESIGNS.includes(newDesign) || newDesign === currentDesign) return;

  const current = canvas.querySelector('.design.is-active');
  const next = canvas.querySelector('.design[data-design="' + newDesign + '"]');
  if (!next) return;

  currentDesign = newDesign;
  canvas.setAttribute('data-active-design', newDesign);

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const hasGsap = typeof gsap !== 'undefined';

  if (!hasGsap || reduceMotion) {
    if (current) current.classList.remove('is-active');
    next.classList.add('is-active');
    return;
  }

  const outItems = current ? current.querySelectorAll('.stagger-item:not(.is-hidden)') : [];
  const inItems = next.querySelectorAll('.stagger-item:not(.is-hidden)');

  const tl = gsap.timeline({
    onComplete: () => {
      if (current) current.classList.remove('is-active');
      if (inItems.length) gsap.set(inItems, { clearProps: 'opacity,transform' });
    },
  });

  if (current && outItems.length) {
    tl.to(outItems, { opacity: 0, y: 10, duration: 0.28, stagger: 0.03, ease: 'power2.in' });
  }

  tl.add(() => { next.classList.add('is-active'); });

  if (inItems.length) {
    tl.fromTo(
      inItems,
      { opacity: 0, y: 12 },
      { opacity: 1, y: 0, duration: 0.45, stagger: 0.05, ease: 'power3.out' },
      current ? '-=0.05' : 0
    );
  }
}

/* =========================================================
   4. STATE APPLICATION
   ========================================================= */
function applyState(fullState) {
  if (!fullState || typeof fullState !== 'object') return;

  const activeOverlayKey = fullState.active_overlay || 'main';
  const overlays = fullState.overlays || {};
  applyOverlayData(overlays[activeOverlayKey]);

  const design = VALID_DESIGNS.includes(fullState.active_design) ? fullState.active_design : 'design-01';
  if (design !== currentDesign) {
    switchDesign(design);
  }
}

/* =========================================================
   5. POLLING — no WebSockets; a lightweight interval fetch
   ========================================================= */
async function pollState() {
  try {
    const res = await fetch('../../api/broadcast-state.php?t=' + Date.now(), { cache: 'no-store' });
    const json = await res.json();
    if (json && json.success) {
      applyState(json.data);
    }
  } catch (err) {
    // Silent by design — the broadcast screen keeps showing the
    // last known good state rather than flashing an error on air.
  }
}

document.addEventListener('DOMContentLoaded', pollState);
setInterval(pollState, POLL_INTERVAL_MS);
</script>

</body>
</html>