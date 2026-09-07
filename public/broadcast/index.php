<?php
/**
 * Lean Cast — Broadcast Screen (Phone 1)
 * Transparent overlay canvas rendered on top of the live camera feed.
 * Hosts three original, bold "flagship bulletin" channel identities —
 * Zaviya News, Fanoos News, Sitara News — switchable from Control.
 * The LeanCast platform mark + LIVE indicator stay constant across all
 * three; only the channel package (colours, marks, composition) changes.
 * Display only — no controls. Reads initial state server-side from
 * data/broadcast.json so the first paint already reflects reality,
 * then stays in sync via polling.
 */

$validDesigns = ['zaviya', 'fanoos', 'sitara'];

$defaultState = [
    'active_overlay' => 'main',
    'active_design'  => 'zaviya',
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
    $state['active_design'] = 'zaviya';
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
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
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
   2. PLATFORM DESIGN TOKENS (LeanCast chrome — unchanged
   across every channel package)
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

  /* consistent safe-area spacing — unchanged across every channel */
  --edge: clamp(16px, 2.2vw, 32px);
  --ticker-height: 38px;
  --stack-gap: 14px;
}

/* =========================================================
   3. BROADCAST CANVAS — fully transparent overlay layer
   (size/behaviour preserved exactly; only the channel package
   inside changes)
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
   4. LEANCAST PLATFORM MARK / LIVE STATUS — persistent,
   identical in every channel package
   ========================================================= */
.channel-logo {
  position: absolute;
  top: var(--edge);
  left: var(--edge);
  z-index: 9;
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

.live-status {
  position: absolute;
  top: var(--edge);
  right: var(--edge);
  z-index: 9;
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
   5. DESIGN SWITCHER SHELL
   ========================================================= */
.design {
  position: absolute;
  inset: 0;
  z-index: 2;
  display: none;
}

.design.is-active { display: block; }

.overlay-toggle {
  transition: opacity 0.3s var(--ease-out), transform 0.35s var(--ease-out);
}

.overlay-toggle.is-hidden {
  opacity: 0;
  pointer-events: none;
}

[data-overlay="lower-third"].is-hidden,
[data-overlay="breaking-news"].is-hidden,
[data-overlay="channel-bug"].is-hidden {
  transform: translateY(10px);
}

[data-overlay="ticker"].is-hidden {
  transform: translateY(100%);
}

@keyframes tickerScroll {
  from { transform: translateX(0%); }
  to   { transform: translateX(-50%); }
}

/* =========================================================
   6. CHANNEL BUG — shared shell, per-channel colour via
   --ch-* custom properties set on each .design
   ========================================================= */
.channel-bug {
  position: absolute;
  right: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge));
  z-index: 7;
  display: flex;
  align-items: center;
  gap: 7px;
  opacity: 0.94;
}

.channel-bug__badge {
  width: 22px;
  height: 22px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 11px;
  color: var(--ch-text-on-a);
  background: linear-gradient(135deg, var(--ch-a), var(--ch-a-dark));
  box-shadow: 0 0 14px var(--ch-glow);
}

.channel-bug__badge--square { border-radius: 5px; }
.channel-bug__badge--circle { border-radius: 50%; }
.channel-bug__badge--diamond { border-radius: 4px; transform: rotate(45deg); }
.channel-bug__badge--diamond span { display: block; transform: rotate(-45deg); }

.channel-bug__word {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 9px;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--white);
  text-shadow: 0 1px 6px rgba(0,0,0,0.7);
  white-space: nowrap;
}

/* Ticker leading brand chip — shared shell */
.ticker__brand {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  gap: 6px;
  height: 100%;
  padding: 0 13px;
  background: linear-gradient(135deg, var(--ch-a), var(--ch-a-dark));
}

.ticker__brand .badge {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-display);
  font-weight: 700;
  font-size: 8px;
  color: var(--ch-text-on-a);
  background: rgba(255, 255, 255, 0.2);
  border-radius: 3px;
}

.ticker__brand .name {
  font-family: var(--font-mono);
  font-weight: 700;
  font-size: 9.5px;
  letter-spacing: 0.1em;
  color: var(--ch-text-on-a);
  white-space: nowrap;
}

.ticker__loc {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  padding: 0 12px;
  background: rgba(255, 255, 255, 0.05);
  border-right: 1px solid var(--border-soft);
  font-family: var(--font-mono);
  font-weight: 600;
  font-size: 9.5px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--muted);
  white-space: nowrap;
}

/* =========================================================================================
   7. ZAVIYA NEWS — hard-hitting flagship register
   Crimson + gold, parallelogram-backed lower third, heavy banner,
   dual-tone ticker with a bold end-cap.
   ========================================================================================= */
.design[data-design="zaviya"] {
  --ch-a: #E1233D;
  --ch-a-dark: #8C0F22;
  --ch-b: #F2B705;
  --ch-text-on-a: #1A0402;
  --ch-glow: rgba(225, 35, 61, 0.45);
  --ticker-height: 44px;
}

.zv-stack {
  position: absolute; left: var(--edge); right: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge)); z-index: 6;
  display: flex; flex-direction: column; align-items: flex-start; gap: 12px;
}

.zv-lower-third { position: relative; align-self: flex-start; max-width: min(60%, 480px); padding: 4px 0; }
.zv-lower-third__shape { position: absolute; left: -6px; top: 2px; bottom: 2px; width: 42px; background: linear-gradient(135deg, var(--ch-b), var(--ch-a)); clip-path: polygon(14% 0, 100% 0, 86% 100%, 0% 100%); z-index: -1; }
.zv-lower-third__bar { display: flex; flex-direction: column; gap: 3px; padding: 10px 18px 11px 26px; background: var(--glass-fill); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); border-top: 1px solid var(--border-soft); border-left: 3px solid var(--ch-a); }
.zv-lower-third__name { font-family: var(--font-display); font-weight: 800; font-size: clamp(18px, 2.1vw, 23px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.zv-lower-third__rule { width: 30px; height: 2px; background: var(--ch-a); margin: 2px 0; }
.zv-lower-third__meta { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--muted); white-space: nowrap; }
.zv-lower-third__meta .role { color: var(--ch-b); }

.zv-breaking { align-self: stretch; display: flex; align-items: stretch; border-radius: 2px; overflow: hidden; box-shadow: 0 0 44px var(--ch-glow); }
.zv-breaking__tag { flex-shrink: 0; display: flex; align-items: center; gap: 8px; padding: 0 20px; background: linear-gradient(135deg, var(--ch-a), var(--ch-a-dark)); }
.zv-breaking__tag .dot { width: 6px; height: 6px; border-radius: 50%; background: #fff; animation: livePulse 1.2s ease-in-out infinite; }
.zv-breaking__tag span { font-family: var(--font-mono); font-weight: 800; font-size: 12.5px; letter-spacing: 0.14em; color: #fff; white-space: nowrap; }
.zv-breaking__headline { flex: 1; display: flex; align-items: center; padding: 13px 20px; background: var(--glass-fill); backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px); font-family: var(--font-display); font-weight: 700; font-size: clamp(15px, 1.8vw, 19px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.design[data-design="zaviya"] .ticker { position: absolute; left: 0; right: 0; bottom: 0; z-index: 6; height: var(--ticker-height); display: flex; align-items: stretch; background: var(--bg-elevated); border-top: 2px solid var(--ch-a); overflow: hidden; }
.design[data-design="zaviya"] .ticker__track { flex: 1; overflow: hidden; position: relative; }
.design[data-design="zaviya"] .ticker__track-inner { position: absolute; white-space: nowrap; display: flex; align-items: center; height: 100%; animation: tickerScroll 22s linear infinite; font-family: var(--font-mono); font-weight: 500; font-size: 13px; color: var(--white); }
.design[data-design="zaviya"] .ticker__track-inner span { padding-right: 64px; }
.design[data-design="zaviya"] .ticker__end { flex-shrink: 0; width: 10px; background: linear-gradient(135deg, var(--ch-b), var(--ch-a)); }

/* =========================================================================================
   8. FANOOS NEWS — warm, trusted, analytical register
   Amber + maroon, classic accent-edge lower third, restrained
   ribbon breaking news, calm two-tone ticker.
   ========================================================================================= */
.design[data-design="fanoos"] {
  --ch-a: #D89B3C;
  --ch-a-dark: #8A5A18;
  --ch-b: #7C2E2E;
  --ch-text-on-a: #1D1002;
  --ch-glow: rgba(216, 155, 60, 0.38);
  --ticker-height: 38px;
}

.fn-stack {
  position: absolute; left: var(--edge); right: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge)); z-index: 6;
  display: flex; flex-direction: column; align-items: flex-start; gap: var(--stack-gap);
}

.fn-lower-third { align-self: flex-start; max-width: min(54%, 440px); }
.fn-lower-third__bar { display: flex; align-items: stretch; background: var(--glass-fill); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-top: 1px solid var(--border-soft); border-bottom: 1px solid var(--border-softer); border-radius: 2px; }
.fn-lower-third__edge { width: 3px; flex-shrink: 0; background: linear-gradient(180deg, var(--ch-b), var(--ch-a)); }
.fn-lower-third__content { padding: 9px 15px 10px; display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.fn-lower-third__name { font-family: var(--font-display); font-weight: 600; font-size: clamp(14px, 1.7vw, 18px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.fn-lower-third__meta { display: flex; align-items: center; gap: 7px; }
.fn-lower-third__role { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.09em; text-transform: uppercase; color: var(--ch-a); white-space: nowrap; }
.fn-lower-third__divider { width: 1px; height: 9px; background: var(--border-soft); flex-shrink: 0; }
.fn-lower-third__location { font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.09em; text-transform: uppercase; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.fn-breaking { align-self: stretch; display: flex; align-items: stretch; border-radius: 2px; overflow: hidden; border-top: 1px solid var(--ch-a); background: var(--glass-fill); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); box-shadow: 0 0 30px var(--ch-glow); }
.fn-breaking__tag { flex-shrink: 0; display: flex; align-items: center; padding: 0 15px; background: linear-gradient(135deg, var(--ch-a), var(--ch-a-dark)); font-family: var(--font-mono); font-weight: 600; font-size: 10.5px; letter-spacing: 0.12em; color: var(--ch-text-on-a); white-space: nowrap; }
.fn-breaking__headline { display: flex; align-items: center; padding: 10px 17px; font-family: var(--font-display); font-weight: 600; font-size: clamp(13px, 1.4vw, 15.5px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.design[data-design="fanoos"] .ticker { position: absolute; left: 0; right: 0; bottom: 0; z-index: 6; height: var(--ticker-height); display: flex; align-items: center; background: var(--bg-elevated); border-top: 1px solid var(--border-soft); overflow: hidden; }
.design[data-design="fanoos"] .ticker__track { flex: 1; overflow: hidden; position: relative; height: 100%; }
.design[data-design="fanoos"] .ticker__track-inner { position: absolute; white-space: nowrap; display: flex; align-items: center; height: 100%; animation: tickerScroll 24s linear infinite; font-family: var(--font-mono); font-size: 12px; letter-spacing: 0.02em; color: var(--muted); }
.design[data-design="fanoos"] .ticker__track-inner span { padding-right: 64px; }

/* =========================================================================================
   9. SITARA NEWS — modern, digital-forward register
   Blue + cyan, continuous two-tone name bar, stacked breaking
   block, sleek geometric ticker.
   ========================================================================================= */
.design[data-design="sitara"] {
  --ch-a: #2F6FED;
  --ch-a-dark: #173E96;
  --ch-b: #33D1C9;
  --ch-text-on-a: #04121F;
  --ch-glow: rgba(47, 111, 237, 0.4);
  --ticker-height: 36px;
}

.st-stack {
  position: absolute; left: var(--edge); right: var(--edge);
  bottom: calc(var(--ticker-height) + var(--edge)); z-index: 6;
  display: flex; flex-direction: column; align-items: flex-start; gap: 10px;
}

.st-bar { align-self: flex-start; display: flex; max-width: min(62%, 500px); border-radius: 2px; overflow: hidden; box-shadow: 0 10px 28px -14px rgba(0,0,0,0.5); }
.st-bar__name { flex-shrink: 0; display: flex; align-items: center; padding: 9px 16px; background: linear-gradient(135deg, var(--ch-b), var(--ch-a)); font-family: var(--font-display); font-weight: 700; font-size: clamp(13px, 1.5vw, 16px); color: var(--ch-text-on-a); white-space: nowrap; }
.st-bar__meta { display: flex; align-items: center; gap: 7px; padding: 9px 14px; background: var(--glass-fill); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); font-family: var(--font-mono); font-size: 10px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--white); white-space: nowrap; overflow: hidden; }
.st-bar__meta .loc { color: var(--muted); }
.st-bar__meta .sep { color: var(--border-soft); }

.st-breaking { align-self: stretch; display: flex; flex-direction: column; gap: 2px; padding: 9px 14px; border-left: 3px solid var(--ch-a); background: var(--glass-fill); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); box-shadow: 0 0 26px var(--ch-glow); }
.st-breaking__label { font-family: var(--font-mono); font-weight: 600; font-size: 9.5px; letter-spacing: 0.14em; color: var(--ch-b); }
.st-breaking__headline { font-family: var(--font-display); font-weight: 600; font-size: clamp(13px, 1.5vw, 16px); color: var(--white); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.design[data-design="sitara"] .ticker { position: absolute; left: 0; right: 0; bottom: 0; z-index: 6; height: var(--ticker-height); display: flex; align-items: stretch; background: var(--bg-elevated); border-top: 1px solid var(--border-soft); overflow: hidden; }
.design[data-design="sitara"] .ticker__track { flex: 1; overflow: hidden; position: relative; }
.design[data-design="sitara"] .ticker__track-inner { position: absolute; white-space: nowrap; display: flex; align-items: center; height: 100%; animation: tickerScroll 24s linear infinite; font-family: var(--font-mono); font-size: 11.5px; color: var(--muted); }
.design[data-design="sitara"] .ticker__track-inner span { padding-right: 60px; }
.design[data-design="sitara"] .ticker__end { flex-shrink: 0; width: 8px; background: linear-gradient(135deg, var(--ch-a), var(--ch-b)); }

/* =========================================================
   10. RESPONSIVE — overlays stay anchored in every channel
   ========================================================= */
@media (max-width: 860px) {
  .zv-lower-third { max-width: 74%; }
  .fn-lower-third { max-width: 68%; }
  .st-bar { max-width: 78%; }
}

@media (max-width: 620px) {
  .channel-logo .word { display: none; }
  .channel-bug__word { display: none; }
  .channel-bug { gap: 0; }

  /* Zaviya */
  .zv-lower-third { max-width: none; align-self: stretch; }
  .zv-breaking__headline { padding: 10px 16px 12px; white-space: normal; line-height: 1.3; }

  /* Fanoos */
  .fn-lower-third { max-width: none; align-self: stretch; }
  .fn-lower-third__content { padding: 7px 12px 8px; }
  .fn-breaking__headline { padding: 7px 12px 9px; white-space: normal; line-height: 1.28; }

  /* Sitara */
  .st-bar { max-width: none; align-self: stretch; flex-direction: column; }
  .st-bar__name, .st-bar__meta { width: 100%; }
}

/* =========================================================
   11. REDUCED MOTION
   ========================================================= */
@media (prefers-reduced-motion: reduce) {
  .live-status .live-dot,
  .zv-breaking__tag .dot {
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

  <!-- LeanCast platform mark (persistent, identical in every channel) -->
  <div class="channel-logo">
    <span class="mark"></span>
    <span class="word">LEAN<span>CAST</span></span>
  </div>

  <!-- Live indicator (persistent) -->
  <div class="live-status">
    <span class="live-dot"></span>
    <span>LIVE</span>
  </div>

  <!-- ============================================================
       ZAVIYA NEWS
       ============================================================ -->
  <div class="design<?= lc_active('zaviya', $activeDesign) ?>" data-design="zaviya">
    <div class="zv-stack">
      <div class="zv-lower-third stagger-item overlay-toggle<?= $ltHidden ?>" data-overlay="lower-third">
        <span class="zv-lower-third__shape"></span>
        <div class="zv-lower-third__bar">
          <span class="zv-lower-third__name" data-field="name"><?= $name ?></span>
          <span class="zv-lower-third__rule"></span>
          <span class="zv-lower-third__meta"><span class="role" data-field="role"><?= $role ?></span> &nbsp;·&nbsp; <span data-field="location"><?= $location ?></span></span>
        </div>
      </div>
      <div class="zv-breaking stagger-item overlay-toggle<?= $bnHidden ?>" data-overlay="breaking-news">
        <div class="zv-breaking__tag"><span class="dot"></span><span>BREAKING NEWS</span></div>
        <div class="zv-breaking__headline" data-field="headline"><?= $headline ?></div>
      </div>
    </div>
    <div class="channel-bug stagger-item overlay-toggle" data-overlay="channel-bug">
      <span class="channel-bug__badge channel-bug__badge--square"><span>Z</span></span>
      <span class="channel-bug__word">Zaviya News</span>
    </div>
    <div class="ticker stagger-item overlay-toggle<?= $tkHidden ?>" data-overlay="ticker">
      <div class="ticker__brand"><span class="badge">Z</span><span class="name">ZAVIYA</span></div>
      <div class="ticker__loc" data-field="ticker-label"><?= $tickerLabel ?></div>
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
       FANOOS NEWS
       ============================================================ -->
  <div class="design<?= lc_active('fanoos', $activeDesign) ?>" data-design="fanoos">
    <div class="fn-stack">
      <div class="fn-lower-third stagger-item overlay-toggle<?= $ltHidden ?>" data-overlay="lower-third">
        <div class="fn-lower-third__bar">
          <span class="fn-lower-third__edge"></span>
          <div class="fn-lower-third__content">
            <span class="fn-lower-third__name" data-field="name"><?= $name ?></span>
            <div class="fn-lower-third__meta">
              <span class="fn-lower-third__role" data-field="role"><?= $role ?></span>
              <span class="fn-lower-third__divider"></span>
              <span class="fn-lower-third__location" data-field="location"><?= $location ?></span>
            </div>
          </div>
        </div>
      </div>
      <div class="fn-breaking stagger-item overlay-toggle<?= $bnHidden ?>" data-overlay="breaking-news">
        <div class="fn-breaking__tag">BREAKING NEWS</div>
        <div class="fn-breaking__headline" data-field="headline"><?= $headline ?></div>
      </div>
    </div>
    <div class="channel-bug stagger-item overlay-toggle" data-overlay="channel-bug">
      <span class="channel-bug__badge channel-bug__badge--circle"><span>F</span></span>
      <span class="channel-bug__word">Fanoos News</span>
    </div>
    <div class="ticker stagger-item overlay-toggle<?= $tkHidden ?>" data-overlay="ticker">
      <div class="ticker__brand"><span class="badge">F</span><span class="name">FANOOS</span></div>
      <div class="ticker__loc" data-field="ticker-label"><?= $tickerLabel ?></div>
      <div class="ticker__track">
        <div class="ticker__track-inner">
          <span data-field="ticker-text"><?= $tickerText ?></span>
          <span data-field="ticker-text"><?= $tickerText ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================
       SITARA NEWS
       ============================================================ -->
  <div class="design<?= lc_active('sitara', $activeDesign) ?>" data-design="sitara">
    <div class="st-stack">
      <div class="st-bar stagger-item overlay-toggle<?= $ltHidden ?>" data-overlay="lower-third">
        <span class="st-bar__name" data-field="name"><?= $name ?></span>
        <span class="st-bar__meta"><span class="role" data-field="role"><?= $role ?></span><span class="sep">&nbsp;·&nbsp;</span><span class="loc" data-field="location"><?= $location ?></span></span>
      </div>
      <div class="st-breaking stagger-item overlay-toggle<?= $bnHidden ?>" data-overlay="breaking-news">
        <span class="st-breaking__label">BREAKING NEWS</span>
        <span class="st-breaking__headline" data-field="headline"><?= $headline ?></span>
      </div>
    </div>
    <div class="channel-bug stagger-item overlay-toggle" data-overlay="channel-bug">
      <span class="channel-bug__badge channel-bug__badge--diamond"><span>S</span></span>
      <span class="channel-bug__word">Sitara News</span>
    </div>
    <div class="ticker stagger-item overlay-toggle<?= $tkHidden ?>" data-overlay="ticker">
      <div class="ticker__brand"><span class="badge">S</span><span class="name">SITARA</span></div>
      <div class="ticker__loc" data-field="ticker-label"><?= $tickerLabel ?></div>
      <div class="ticker__track">
        <div class="ticker__track-inner">
          <span data-field="ticker-text"><?= $tickerText ?></span>
          <span data-field="ticker-text"><?= $tickerText ?></span>
        </div>
      </div>
      <div class="ticker__end"></div>
    </div>
  </div>

</div>

<script>
/* =========================================================
   1. CONFIG & STATE
   ========================================================= */
const VALID_DESIGNS = ['zaviya', 'fanoos', 'sitara'];
const POLL_INTERVAL_MS = 5000;

const canvas = document.getElementById('broadcastCanvas');
let currentDesign = canvas.getAttribute('data-active-design') || 'zaviya';

/* =========================================================
   2. CONTENT BINDING — shared across all three channels
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

  const design = VALID_DESIGNS.includes(fullState.active_design) ? fullState.active_design : 'zaviya';
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