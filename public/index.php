<?php
/**
 * LEAN CAST — AI Hackathon Landing Page
 * Al-Khidmat Foundation Pakistan × Alibaba Cloud
 * Single-file PHP/HTML/CSS/JS build. No external assets except GSAP + Google Fonts.
 */
$currentYear = date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lean Cast</title>
<meta name="description" content="Lean Cast is a cloud-based AI streaming concept that turns two smartphones into a lightweight multi-camera live-streaming setup. Built for the AI Hackathon by Al-Khidmat Foundation Pakistan × Alibaba Cloud.">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js" defer></script>

<style>
/* ============================================================
   1. RESET & BASE
   ============================================================ */
*, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
html, body { overflow-x: hidden; width: 100%; }

/* ============================================================
   2. DESIGN TOKENS
   ============================================================ */
:root{
  --bg: #07090D;
  --bg-elevated: #0C0F16;
  --panel: rgba(255,255,255,0.035);
  --panel-border: rgba(255,255,255,0.08);
  --orange: #FF6A00;
  --orange-light: #FF9D42;
  --blue: #4F7CFF;
  --green: #42E8A4;
  --white: #F5F7FB;
  --muted: #9299A8;

  --font-display: 'Space Grotesk', sans-serif;
  --font-body: 'Inter', sans-serif;
  --font-mono: 'JetBrains Mono', monospace;

  --container: 1240px;
  --section-pad: 160px;
  --radius-lg: 28px;
  --radius-md: 18px;
  --radius-sm: 10px;

  --ease: cubic-bezier(.22,1,.36,1);
}

@media (max-width: 768px){
  :root{ --section-pad: 96px; }
}

body{
  background: var(--bg);
  color: var(--white);
  font-family: var(--font-body);
  line-height: 1.5;
  -webkit-font-smoothing: antialiased;
  position: relative;
}

/* ambient background grid + glows, fixed behind everything */
body::before{
  content: '';
  position: fixed;
  inset: 0;
  z-index: 0;
  pointer-events: none;
  background-image:
    linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
  background-size: 64px 64px;
  mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black 0%, transparent 75%);
}

.container{
  max-width: var(--container);
  margin: 0 auto;
  padding: 0 32px;
  position: relative;
  z-index: 1;
}

img, svg { display: block; max-width: 100%; }
a { color: inherit; text-decoration: none; }
button { font-family: inherit; background: none; border: none; color: inherit; cursor: pointer; }
ul { list-style: none; }

h1,h2,h3,h4{ font-family: var(--font-display); font-weight: 600; letter-spacing: -0.02em; }

::selection{ background: var(--orange); color: #07090D; }

/* Focus visibility for accessibility */
a:focus-visible, button:focus-visible{
  outline: 2px solid var(--orange-light);
  outline-offset: 4px;
  border-radius: 4px;
}

.eyebrow{
  font-family: var(--font-mono);
  font-size: 12.5px;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: var(--muted);
  display: inline-flex;
  align-items: center;
  gap: 10px;
}

.section{
  position: relative;
  z-index: 1;
  padding: var(--section-pad) 0;
}

.section-head{
  max-width: 720px;
  margin-bottom: 64px;
}
.section-head h2{
  font-size: clamp(30px, 4vw, 46px);
  line-height: 1.12;
  margin-top: 18px;
  color: var(--white);
}
.section-head p{
  margin-top: 20px;
  color: var(--muted);
  font-size: 16.5px;
  max-width: 560px;
}

/* glass panel base */
.glass{
  background: var(--panel);
  border: 1px solid var(--panel-border);
  border-radius: var(--radius-md);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
}

.btn{
  display: inline-flex;
  align-items: center;
  gap: 10px;
  font-family: var(--font-mono);
  font-size: 13.5px;
  letter-spacing: 0.02em;
  padding: 14px 26px;
  border-radius: 100px;
  transition: transform .35s var(--ease), background .35s var(--ease), border-color .35s var(--ease), color .35s var(--ease);
  cursor: pointer;
  white-space: nowrap;
}
.btn-primary{
  background: linear-gradient(135deg, var(--orange), #E85A00);
  color: #0A0603;
  box-shadow: 0 10px 30px -8px rgba(255,106,0,0.55);
}
.btn-primary:hover{ transform: translateY(-2px); box-shadow: 0 16px 40px -10px rgba(255,106,0,0.7); }
.btn-secondary{
  border: 1px solid var(--panel-border);
  color: var(--white);
  background: rgba(255,255,255,0.02);
}
.btn-secondary:hover{ border-color: rgba(255,255,255,0.25); transform: translateY(-2px); }

/* ============================================================
   3. LOADER
   ============================================================ */
#loader{
  position: fixed;
  inset: 0;
  z-index: 9999;
  background: var(--bg);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 22px;
}
#loader .loader-mark{
  display: flex;
  align-items: center;
  gap: 12px;
  font-family: var(--font-display);
  font-size: 20px;
  letter-spacing: 0.02em;
  opacity: 0;
}
#loader .loader-track{
  width: 220px;
  height: 1px;
  background: rgba(255,255,255,0.12);
  position: relative;
  overflow: hidden;
}
#loader .loader-bar{
  position: absolute;
  left: 0; top: 0;
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, var(--orange), var(--orange-light));
}
#loader .loader-pct{
  font-family: var(--font-mono);
  font-size: 12px;
  color: var(--muted);
  opacity: 0;
}

/* ============================================================
   4. CUSTOM CURSOR
   ============================================================ */
.cursor-dot, .cursor-ring{
  position: fixed;
  top: 0; left: 0;
  pointer-events: none;
  z-index: 9998;
  border-radius: 50%;
  transform: translate(-50%,-50%);
  will-change: transform;
}
.cursor-dot{
  width: 6px; height: 6px;
  background: var(--white);
}
.cursor-ring{
  width: 34px; height: 34px;
  border: 1px solid rgba(255,255,255,0.35);
  transition: width .28s var(--ease), height .28s var(--ease), border-color .28s var(--ease), background .28s var(--ease);
}
.cursor-ring.is-active{
  width: 58px; height: 58px;
  border-color: var(--orange);
  background: rgba(255,106,0,0.08);
}
body.has-custom-cursor, body.has-custom-cursor a, body.has-custom-cursor button{ cursor: none; }

/* ============================================================
   5. NAVIGATION
   ============================================================ */
header.nav{
  position: fixed;
  top: 0; left: 0; right: 0;
  z-index: 500;
  padding: 26px 0;
  transition: padding .4s var(--ease), background .4s var(--ease), border-color .4s var(--ease), backdrop-filter .4s var(--ease);
  border-bottom: 1px solid transparent;
}
header.nav.scrolled{
  padding: 16px 0;
  background: rgba(7,9,13,0.72);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid var(--panel-border);
}
.nav-inner{ display: flex; align-items: center; justify-content: space-between; }
.nav-logo{ display: flex; align-items: center; gap: 10px; font-family: var(--font-display); font-weight: 600; font-size: 15.5px; letter-spacing: 0.04em; }
.nav-logo svg{ width: 26px; height: 26px; }
.nav-links{ display: flex; gap: 40px; }
.nav-links a{
  font-family: var(--font-mono);
  font-size: 13px;
  color: var(--muted);
  letter-spacing: 0.03em;
  position: relative;
  transition: color .3s var(--ease);
}
.nav-links a::after{
  content:'';
  position: absolute;
  left: 0; bottom: -6px;
  width: 0%; height: 1px;
  background: var(--orange-light);
  transition: width .3s var(--ease);
}
.nav-links a:hover{ color: var(--white); }
.nav-links a:hover::after{ width: 100%; }

@media (max-width: 860px){ .nav-links{ display: none; } }

/* ============================================================
   6. HERO
   ============================================================ */
.hero{
  position: relative;
  min-height: 100vh;
  display: flex;
  align-items: center;
  padding-top: 120px;
  padding-bottom: 60px;
  z-index: 1;
}
.hero-glow-orange{
  position: absolute; width: 640px; height: 640px;
  background: radial-gradient(circle, rgba(255,106,0,0.20), transparent 70%);
  top: -180px; left: -180px;
  filter: blur(10px);
  z-index: 0;
  pointer-events: none;
}
.hero-glow-blue{
  position: absolute; width: 560px; height: 560px;
  background: radial-gradient(circle, rgba(79,124,255,0.16), transparent 70%);
  bottom: -220px; right: -120px;
  filter: blur(10px);
  z-index: 0;
  pointer-events: none;
}
.hero-inner{
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 40px;
  align-items: center;
}
.hero-badge{
  display: inline-flex;
  align-items: center;
  gap: 9px;
  font-family: var(--font-mono);
  font-size: 12px;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--orange-light);
  border: 1px solid rgba(255,106,0,0.3);
  background: rgba(255,106,0,0.06);
  padding: 8px 16px;
  border-radius: 100px;
  overflow: hidden;
}
.hero-badge .dot{
  width: 6px; height: 6px; border-radius: 50%;
  background: var(--orange);
  box-shadow: 0 0 10px 2px rgba(255,106,0,0.7);
  animation: pulse-dot 1.8s ease-in-out infinite;
}
@keyframes pulse-dot{ 0%,100%{ opacity: 1; transform: scale(1);} 50%{ opacity: .4; transform: scale(.7);} }

.hero h1{
  margin-top: 26px;
  font-size: clamp(52px, 7.4vw, 100px);
  line-height: 0.98;
  overflow: hidden;
}
.hero h1 .line{ overflow: hidden; }
.hero h1 .line span{ display: inline-block; will-change: transform; }
.hero h1 .accent{
  background: linear-gradient(120deg, var(--orange), var(--orange-light));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.hero-desc{
  margin-top: 30px;
  max-width: 460px;
  color: var(--muted);
  font-size: 17px;
  line-height: 1.65;
  opacity: 0;
}
.hero-actions{
  margin-top: 40px;
  display: flex;
  gap: 18px;
  flex-wrap: wrap;
  opacity: 0;
}

/* ============================================================
   7. HERO VISUAL — Phones + Cloud
   ============================================================ */
.hero-visual{
  position: relative;
  height: 560px;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transform: scale(0.92);
}
#hero-stage{
  position: relative;
  width: 100%;
  height: 100%;
}

.orbit-ring{
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%,-50%);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 50%;
  pointer-events: none;
}
.orbit-ring.r1{ width: 340px; height: 340px; }
.orbit-ring.r2{ width: 440px; height: 440px; border-style: dashed; }

.cloud-node{
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%,-50%);
  width: 118px; height: 118px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at 35% 30%, rgba(255,157,66,0.35), rgba(255,106,0,0.06) 60%, transparent 75%);
  border: 1px solid rgba(255,157,66,0.4);
  box-shadow: 0 0 60px rgba(255,106,0,0.35), inset 0 0 30px rgba(255,106,0,0.15);
  z-index: 3;
}
.cloud-node span{
  font-family: var(--font-mono);
  font-size: 13px;
  letter-spacing: 0.1em;
  color: var(--orange-light);
}

.phone-mock{
  position: absolute;
  width: 132px;
  height: 268px;
  border-radius: 26px;
  background: linear-gradient(160deg, #14171F, #0A0C11);
  border: 1px solid rgba(255,255,255,0.12);
  box-shadow: 0 30px 60px -20px rgba(0,0,0,0.7), inset 0 0 0 1px rgba(255,255,255,0.02);
  z-index: 2;
  padding: 8px;
}
.phone-mock::before{
  content:'';
  position: absolute;
  top: 14px; left: 50%;
  transform: translateX(-50%);
  width: 34px; height: 5px;
  border-radius: 4px;
  background: rgba(255,255,255,0.12);
}
.phone-screen{
  width: 100%; height: 100%;
  border-radius: 18px;
  background:
    linear-gradient(200deg, rgba(79,124,255,0.16), rgba(255,106,0,0.10) 70%),
    #0A0C13;
  position: relative;
  overflow: hidden;
  display: flex;
  align-items: flex-end;
  padding: 12px;
}
.phone-screen .rec{
  display: flex; align-items: center; gap: 6px;
  font-family: var(--font-mono);
  font-size: 9.5px;
  letter-spacing: 0.06em;
  color: var(--green);
}
.phone-screen .rec .rec-dot{
  width: 5px; height: 5px; border-radius: 50%; background: var(--green);
  box-shadow: 0 0 8px 1px var(--green);
  animation: pulse-dot 1.4s ease-in-out infinite;
}
.phone-mock.p1{ top: 8%; left: 4%; transform: rotate(-9deg); }
.phone-mock.p2{ bottom: 6%; right: 2%; transform: rotate(8deg); }

.phone-label{
  position: absolute;
  font-family: var(--font-mono);
  font-size: 10.5px;
  letter-spacing: 0.08em;
  color: var(--muted);
  line-height: 1.5;
}
.phone-label b{ color: var(--white); display: block; font-size: 11px; letter-spacing: 0.1em; }
.phone-label.l1{ top: 2%; left: 0; }
.phone-label.l2{ bottom: -2%; right: 0; text-align: right; }

.connector-svg{
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
}
.connector-svg path{
  fill: none;
  stroke-width: 1.4;
}
.connector-svg .line-a{ stroke: url(#gradOrange); stroke-dasharray: 6 8; animation: dash-flow 2.4s linear infinite; }
.connector-svg .line-b{ stroke: url(#gradBlue); stroke-dasharray: 6 8; animation: dash-flow 2.4s linear infinite; animation-delay: -1.2s; }
@keyframes dash-flow{ to{ stroke-dashoffset: -140; } }

/* ============================================================
   8. STATS STRIP
   ============================================================ */
.stats{
  border-top: 1px solid var(--panel-border);
  border-bottom: 1px solid var(--panel-border);
}
.stats-inner{
  display: grid;
  grid-template-columns: repeat(3, 1fr);
}
.stat{
  padding: 46px 30px;
  text-align: center;
  position: relative;
}
.stat + .stat::before{
  content:'';
  position: absolute;
  left: 0; top: 20%;
  height: 60%;
  width: 1px;
  background: var(--panel-border);
}
.stat .num{
  font-family: var(--font-display);
  font-size: clamp(34px, 4vw, 46px);
  color: var(--white);
}
.stat .label{
  margin-top: 8px;
  font-family: var(--font-mono);
  font-size: 12.5px;
  color: var(--muted);
  letter-spacing: 0.02em;
}

@media (max-width: 700px){
  .stats-inner{ grid-template-columns: 1fr; }
  .stat + .stat::before{ display: none; }
  .stat{ border-top: 1px solid var(--panel-border); }
  .stat:first-child{ border-top: none; }
}

/* ============================================================
   9. CONCEPT SECTION
   ============================================================ */
.concept-body p{
  color: var(--muted);
  font-size: 17px;
  line-height: 1.75;
  max-width: 620px;
}

/* ============================================================
   10. FEATURE CARDS
   ============================================================ */
.feature-grid{
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}
.feature-card{
  padding: 34px;
  position: relative;
  overflow: hidden;
  transition: transform .4s var(--ease), border-color .4s var(--ease), box-shadow .4s var(--ease);
}
.feature-card::before{
  content:'';
  position: absolute;
  inset: 0;
  background: radial-gradient(240px 140px at 20% 0%, rgba(255,106,0,0.0), transparent);
  transition: background .4s var(--ease);
  pointer-events: none;
}
.feature-card:hover{
  transform: translateY(-6px);
  border-color: rgba(255,106,0,0.35);
  box-shadow: 0 24px 50px -22px rgba(255,106,0,0.35);
}
.feature-card:hover::before{
  background: radial-gradient(240px 160px at 20% 0%, rgba(255,106,0,0.16), transparent);
}
.feature-card .icon{
  width: 42px; height: 42px;
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  background: rgba(255,106,0,0.08);
  border: 1px solid rgba(255,106,0,0.25);
  margin-bottom: 22px;
}
.feature-card .icon svg{ width: 20px; height: 20px; stroke: var(--orange-light); }
.feature-card h3{ font-size: 19px; color: var(--white); margin-bottom: 10px; }
.feature-card p{ color: var(--muted); font-size: 14.5px; line-height: 1.65; }

@media (max-width: 760px){ .feature-grid{ grid-template-columns: 1fr; } }

/* ============================================================
   11. ARCHITECTURE
   ============================================================ */
.arch-panel{
  padding: 48px;
  position: relative;
  overflow: hidden;
  background:
    radial-gradient(600px 300px at 50% -10%, rgba(79,124,255,0.10), transparent),
    var(--panel);
}
.arch-grid-bg{
  position: absolute; inset: 0;
  background-image: linear-gradient(rgba(255,255,255,0.035) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.035) 1px, transparent 1px);
  background-size: 34px 34px;
  mask-image: radial-gradient(ellipse 70% 70% at 50% 40%, black, transparent);
  pointer-events: none;
}
.arch-flow{
  position: relative;
  z-index: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}
.arch-node{
  width: min(320px, 90%);
  padding: 20px 24px;
  text-align: center;
}
.arch-node .tag{ font-family: var(--font-mono); font-size: 11px; letter-spacing: 0.08em; color: var(--muted); }
.arch-node .title{ font-family: var(--font-display); font-size: 16px; margin-top: 6px; color: var(--white); }
.arch-node.center{
  border-color: rgba(255,106,0,0.4);
  box-shadow: 0 0 50px -10px rgba(255,106,0,0.3);
}
.arch-node.center .sub-list{
  margin-top: 10px;
  display: flex;
  gap: 8px;
  justify-content: center;
  flex-wrap: wrap;
}
.arch-node.center .sub-list span{
  font-family: var(--font-mono);
  font-size: 10.5px;
  color: var(--orange-light);
  border: 1px solid rgba(255,106,0,0.3);
  padding: 4px 10px;
  border-radius: 100px;
}
.arch-branch{
  display: flex;
  align-items: stretch;
  justify-content: center;
  gap: 60px;
  width: 100%;
  position: relative;
}
.arch-connector{
  width: 100%;
  max-width: 520px;
  height: 46px;
}
.arch-connector path{ fill: none; stroke: rgba(255,255,255,0.18); stroke-width: 1.2; }
.arch-connector .flow{ stroke: var(--orange); stroke-dasharray: 4 8; animation: dash-flow 1.8s linear infinite; }

@media (max-width: 640px){
  .arch-panel{ padding: 28px 18px; }
  .arch-node{ width: 100%; }
}

/* ============================================================
   12. HACKATHON SECTION
   ============================================================ */
.hackathon-grid{
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-top: 20px;
}
.hackathon-card{
  padding: 40px 30px;
  text-align: center;
}
.hackathon-card .big{
  font-family: var(--font-display);
  font-size: clamp(40px, 5vw, 60px);
  background: linear-gradient(120deg, var(--white), var(--orange-light));
  -webkit-background-clip: text; background-clip: text; color: transparent;
}
.hackathon-card .cap{
  margin-top: 10px;
  font-family: var(--font-mono);
  font-size: 12.5px;
  color: var(--muted);
  letter-spacing: 0.04em;
}
@media (max-width: 760px){ .hackathon-grid{ grid-template-columns: 1fr; } }

/* ============================================================
   13. TEAM
   ============================================================ */
.team-grid{
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}
.team-card{
  padding: 34px;
  position: relative;
}
.team-card .index-num{
  font-family: var(--font-mono);
  font-size: 13px;
  color: var(--orange-light);
  border: 1px solid rgba(255,106,0,0.3);
  width: 36px; height: 36px;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 26px;
}
.team-card h3{ font-size: 19px; color: var(--white); }
.team-card p{ margin-top: 6px; font-family: var(--font-mono); font-size: 12.5px; color: var(--muted); }

@media (max-width: 760px){ .team-grid{ grid-template-columns: 1fr; } }

/* ============================================================
   14. FUTURE VISION
   ============================================================ */
.future-pills{
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 30px;
}
.future-pills span{
  font-family: var(--font-mono);
  font-size: 12.5px;
  color: var(--muted);
  border: 1px solid var(--panel-border);
  padding: 10px 18px;
  border-radius: 100px;
  transition: border-color .3s var(--ease), color .3s var(--ease);
}
.future-pills span:hover{ border-color: rgba(255,106,0,0.4); color: var(--orange-light); }
.future-heading{
  margin-top: 40px;
  font-size: clamp(24px, 3vw, 32px);
  color: var(--white);
}

/* ============================================================
   15. FINAL CTA
   ============================================================ */
.final-cta{
  position: relative;
  text-align: center;
  padding: 200px 0;
  overflow: hidden;
}
.final-cta .glow{
  position: absolute;
  width: 900px; height: 900px;
  left: 50%; top: 50%;
  transform: translate(-50%,-50%);
  background: radial-gradient(circle, rgba(255,106,0,0.22), transparent 65%);
  pointer-events: none;
}
.final-cta h2{
  position: relative;
  font-size: clamp(38px, 6vw, 70px);
  line-height: 1.05;
}
.final-cta p{
  position: relative;
  margin-top: 26px;
  color: var(--muted);
  font-size: 17px;
  line-height: 1.6;
}
.final-cta .btn{ position: relative; margin-top: 40px; }

/* ============================================================
   16. FOOTER
   ============================================================ */
footer{
  border-top: 1px solid var(--panel-border);
  padding: 34px 0;
}
.footer-inner{
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  font-family: var(--font-mono);
  font-size: 12px;
  color: var(--muted);
  letter-spacing: 0.04em;
}
.footer-inner .brand{ color: var(--white); font-family: var(--font-display); letter-spacing: 0.06em; }

/* ============================================================
   17. RESPONSIVE — HERO
   ============================================================ */
@media (max-width: 980px){
  .hero-inner{ grid-template-columns: 1fr; }
  .hero-visual{ height: 460px; order: -1; margin-bottom: 10px; }
  .hero{ padding-top: 110px; }
}
@media (max-width: 480px){
  .hero-visual{ height: 380px; }
  .phone-mock{ width: 104px; height: 212px; }
  .cloud-node{ width: 92px; height: 92px; }
  .orbit-ring.r1{ width: 250px; height: 250px; }
  .orbit-ring.r2{ width: 320px; height: 320px; }
}

/* ============================================================
   18. REDUCED MOTION
   ============================================================ */
@media (prefers-reduced-motion: reduce){
  html{ scroll-behavior: auto; }
  .hero-badge .dot, .phone-screen .rec-dot{ animation: none; }
  .connector-svg .line-a, .connector-svg .line-b, .arch-connector .flow{ animation: none; }
  .cursor-dot, .cursor-ring{ display: none; }
  body.has-custom-cursor, body.has-custom-cursor a, body.has-custom-cursor button{ cursor: auto; }
}
</style>
</head>
<body>

<!-- ============================================================
     LOADER
     ============================================================ -->
<div id="loader" aria-hidden="true">
  <div class="loader-mark">
    <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
      <circle cx="13" cy="13" r="11.5" stroke="#FF6A00" stroke-width="1.4"/>
      <circle cx="13" cy="13" r="4" fill="#FF6A00"/>
    </svg>
    LEAN CAST
  </div>
  <div class="loader-track"><div class="loader-bar" id="loaderBar"></div></div>
  <div class="loader-pct" id="loaderPct">0%</div>
</div>

<!-- Custom cursor -->
<div class="cursor-dot" id="cursorDot"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- ============================================================
     NAVIGATION
     ============================================================ -->
<header class="nav" id="siteNav">
  <div class="container nav-inner">
    <a href="#top" class="nav-logo" aria-label="Lean Cast home">
      <svg viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="1" y="6" width="18" height="14" rx="4" stroke="#FF6A00" stroke-width="1.5"/>
        <path d="M19 11L25 8V18L19 15" stroke="#FF9D42" stroke-width="1.5" stroke-linejoin="round"/>
      </svg>
      LEAN CAST
    </a>
    <nav class="nav-links" aria-label="Primary">
      <a href="#concept">Concept</a>
      <a href="#features">Features</a>
      <a href="#architecture">Architecture</a>
      <a href="#team">Team</a>
    </nav>
    <a href="#hackathon" class="btn btn-secondary" data-cursor="hover">Hackathon →</a>
  </div>
</header>

<!-- ============================================================
     HERO
     ============================================================ -->
<section class="hero" id="top">
  <div class="hero-glow-orange"></div>
  <div class="hero-glow-blue"></div>
  <div class="container hero-inner">

    <div class="hero-copy">
      <div class="hero-badge"><span class="dot"></span> AI Streaming Prototype</div>
      <h1>
        <span class="line"><span>Stream</span></span>
        <span class="line"><span class="accent">leaner.</span></span>
        <span class="line"><span>Think</span></span>
        <span class="line"><span class="accent">smarter.</span></span>
      </h1>
      <!-- <p class="hero-desc">
        Lean Cast is a cloud-based AI streaming concept that turns two smartphones
        into a lightweight multi-camera live-streaming setup — without the
        traditional production complexity.
      </p> -->
      <div class="hero-actions">
        <a href="#concept" class="btn btn-primary" data-cursor="hover">Explore the idea ↓</a>
        <a href="#architecture" class="btn btn-secondary" data-cursor="hover">See architecture</a>
      </div>
    </div>

    <div class="hero-visual">
      <div id="hero-stage">
        <div class="orbit-ring r1"></div>
        <div class="orbit-ring r2"></div>

        <svg class="connector-svg" viewBox="0 0 500 500" preserveAspectRatio="none" aria-hidden="true">
          <defs>
            <linearGradient id="gradOrange" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#FF6A00" stop-opacity="0"/>
              <stop offset="50%" stop-color="#FF9D42" stop-opacity="1"/>
              <stop offset="100%" stop-color="#FF6A00" stop-opacity="0"/>
            </linearGradient>
            <linearGradient id="gradBlue" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#4F7CFF" stop-opacity="0"/>
              <stop offset="50%" stop-color="#4F7CFF" stop-opacity="1"/>
              <stop offset="100%" stop-color="#4F7CFF" stop-opacity="0"/>
            </linearGradient>
          </defs>
          <path id="pathA" class="line-a" d="M 110 130 Q 220 150 250 250"/>
          <path id="pathB" class="line-b" d="M 390 370 Q 280 350 250 250"/>
          <circle r="3" fill="#FF9D42"><animateMotion dur="2.2s" repeatCount="indefinite" path="M 110 130 Q 220 150 250 250"/></circle>
          <circle r="3" fill="#4F7CFF"><animateMotion dur="2.2s" repeatCount="indefinite" path="M 390 370 Q 280 350 250 250"/></circle>
        </svg>

        <div class="cloud-node"><span>AI</span></div>

        <span class="phone-label l1"><b>PHONE 01</b>PRIMARY CAMERA</span>
        <div class="phone-mock p1" id="phoneOne">
          <div class="phone-screen"><div class="rec"><span class="rec-dot"></span>LIVE</div></div>
        </div>

        <span class="phone-label l2"><b>PHONE 02</b>Control Panel</span>
        <div class="phone-mock p2" id="phoneTwo">
          <div class="phone-screen"><div class="rec"><span class="rec-dot"></span>LIVE</div></div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- ============================================================
     STATS STRIP
     ============================================================ -->
<section class="stats">
  <div class="container stats-inner">
    <div class="stat ">
      <div class="num">2</div>
      <div class="label">Smartphones</div>
    </div>
    <div class="stat ">
      <div class="num">AI</div>
      <div class="label">Intelligent streaming foundation</div>
    </div>
    <div class="stat ">
      <div class="num">☁</div>
      <div class="label">Cloud-based processing</div>
    </div>
  </div>
</section>

<!-- ============================================================
     CONCEPT
     ============================================================ -->
<section class="section" id="concept">
  <div class="container">
    <div class="section-head ">
      <span class="eyebrow">01 / The Concept</span>
      <h2>Why build a complicated studio when the cameras are already in your pocket?</h2>
    </div>
    <div class="concept-body ">
      <p>
        Most smartphones already carry capable, high-quality cameras. Lean Cast
        explores what happens when two of them are treated as the capture layer
        of a live production, while cloud infrastructure takes on the harder
        problems — coordination, processing, and delivery. Instead of racks of
        dedicated hardware, the setup starts with the devices people already carry.
      </p>
    </div>
  </div>
</section>

<!-- ============================================================
     FEATURES
     ============================================================ -->
<section class="section" id="features">
  <div class="container">
    <div class="section-head ">
      <span class="eyebrow">Capabilities</span>
      <h2>A lean setup with room to grow.</h2>
    </div>
    <div class="feature-grid">

      <div class="glass feature-card ">
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="3" width="12" height="18" rx="3"/><path d="M11 18h2"/></svg></div>
        <h3>one-camera setup</h3>
        <p>one smartphone can provide different viewpoints, creating a flexible production setup without dedicated cameras.</p>
      </div>

      <div class="glass feature-card ">
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M7 18a4 4 0 0 1-1-7.87A5.5 5.5 0 0 1 17 8.5a4.5 4.5 0 0 1 1 8.9"/><path d="M10 18v-4h4v4"/></svg></div>
        <h3>Cloud-powered</h3>
        <p>The cloud acts as the central layer for processing, coordination, and future expansion.</p>
      </div>

      <div class="glass feature-card ">
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 3v2M12 19v2M5 12H3M21 12h-2M6.3 6.3l1.4 1.4M16.3 16.3l1.4 1.4M6.3 17.7l1.4-1.4M16.3 7.7l1.4-1.4"/></svg></div>
        <h3>AI-ready by design</h3>
        <p>The architecture creates room for intelligent features such as automated camera switching, captions, framing, and highlights.</p>
      </div>

      <div class="glass feature-card ">
        <div class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 17l6-6 4 4 8-8"/><path d="M14 7h7v7"/></svg></div>
        <h3>Built to scale</h3>
        <p>Starting as a hackathon prototype, Lean Cast is designed as a foundation that can grow into a broader streaming platform.</p>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     ARCHITECTURE
     ============================================================ -->
<section class="section" id="architecture">
  <div class="container">
    <div class="section-head ">
      <span class="eyebrow">02 / Under The Hood</span>
      <h2>A simple idea. A powerful pipeline.</h2>
    </div>

    <div class="glass arch-panel ">
      <div class="arch-grid-bg"></div>
      <div class="arch-flow">

        <div class="glass arch-node">
          <div class="tag">PHONE 01</div>
          <div class="title">Primary Camera</div>
        </div>

        <svg class="arch-connector" viewBox="0 0 500 46" preserveAspectRatio="none" aria-hidden="true">
          <path d="M250 0 L250 46"/>
          <path class="flow" d="M250 0 L250 46"/>
        </svg>

        <div class="glass arch-node center">
          <div class="tag">LEAN CAST CLOUD</div>
          <div class="title">AI · Processing · Coordination</div>
          <div class="sub-list">
            <span>AI</span><span>Processing</span><span>Coordination</span><span>Streaming Logic</span>
          </div>
        </div>

        <svg class="arch-connector" viewBox="0 0 500 46" preserveAspectRatio="none" aria-hidden="true">
          <path d="M250 0 L250 46"/>
          <path class="flow" d="M250 0 L250 46"/>
        </svg>

        <div class="glass arch-node">
          <div class="tag">PHONE 02</div>
          <div class="title">Control</div>
        </div>

      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     HACKATHON
     ============================================================ -->
<section class="section" id="hackathon">
  <div class="container">
    <div class="section-head ">
      <span class="eyebrow">03 / Built For The Challenge</span>
      <h2>From an idea to an AI hackathon concept.</h2>
      <p>Lean Cast was developed for the AI Hackathon by Al-Khidmat Foundation Pakistan in collaboration with Alibaba Cloud.</p>
    </div>
    <div class="hackathon-grid">
      <div class="glass hackathon-card ">
        <div class="big">16K+</div>
        <div class="cap">Submissions</div>
      </div>
      <div class="glass hackathon-card ">
        <div class="big">3K</div>
        <div class="cap">Selected teams</div>
      </div>
      <div class="glass hackathon-card ">
        <div class="big">01</div>
        <div class="cap">Streaming idea</div>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     TEAM
     ============================================================ -->
<section class="section" id="team">
  <div class="container">
    <div class="section-head ">
      <span class="eyebrow">04 / The Team</span>
      <h2>Two people. One lean idea.</h2>
    </div>
    <div class="team-grid">
      <div class="glass team-card ">
        <div class="index-num">01</div>
        <h3>Sagar Habib</h3>
        <p>Team Lead · Full Stack Developer</p>
      </div>
      <div class="glass team-card ">
        <div class="index-num">02</div>
        <h3>Suleman Memon</h3>
        <p>Team Member</p>
      </div>
    </div>
  </div>
</section>

<!-- ============================================================
     FUTURE VISION
     ============================================================ -->
<section class="section" id="future">
  <div class="container">
    <span class="eyebrow ">What's Next</span>
    <h3 class="future-heading ">This is just the beginning.</h3>
    <div class="future-pills ">
      <span>AI Camera Switching</span>
      <span>Smart Framing</span>
      <span>Auto Captions</span>
      <span>Noise Reduction</span>
      <span>AI Highlights</span>
      <span>Stream Analytics</span>
    </div>
  </div>
</section>

<!-- ============================================================
     FINAL CTA
     ============================================================ -->
<section class="final-cta">
  <div class="glow"></div>
  <div class="container">
    <h2 class="">The future of streaming<br>can be lean.</h2>
    <p class="">
      Two phones.<br>
      Cloud infrastructure.<br>
      AI possibilities.<br>
      Lean Cast is just the beginning.
    </p>
    <a href="#top" class="btn btn-primary " data-cursor="hover">Back to top ↑</a>
  </div>
</section>

<!-- ============================================================
     FOOTER
     ============================================================ -->
<footer>
  <div class="container footer-inner">
    <span class="brand">LEAN CAST</span>
    <span>AI Hackathon · Al-Khidmat Foundation Pakistan × Alibaba Cloud</span>
    <span>© <?php echo $currentYear; ?> Lean Cast</span>
  </div>
</footer>

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script>
document.addEventListener('DOMContentLoaded', function(){

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var isTouch = window.matchMedia('(pointer: coarse)').matches;

  /* ---------- LOADER ---------- */
  var loader = document.getElementById('loader');
  var loaderBar = document.getElementById('loaderBar');
  var loaderPct = document.getElementById('loaderPct');
  var loaderMark = document.querySelector('.loader-mark');

  function runHeroEntrance(){
    if (typeof gsap === 'undefined') return;
    var tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    tl.to('.hero-badge', { opacity: 1, y: 0, duration: .7 }, 0)
      .from('.hero-badge', { opacity: 0, y: 16, duration: .7 }, 0)
      .to('.hero h1 .line span', {
          y: 0, duration: .9, stagger: .08
        }, 0.05)
      .to('.hero-desc', { opacity: 1, y: 0, duration: .8 }, '-=0.5')
      .to('.hero-actions', { opacity: 1, y: 0, duration: .8 }, '-=0.55')
      .to('.hero-visual', { opacity: 1, scale: 1, duration: 1.1 }, '-=0.7');
  }

  gsap && gsap.set('.hero h1 .line span', { y: '110%' });
  gsap && gsap.set('.hero-desc', { y: 16 });
  gsap && gsap.set('.hero-actions', { y: 16 });

  if (reduceMotion) {
    loader.style.display = 'none';
    document.querySelectorAll('.hero-badge, .hero-desc, .hero-actions, .hero-visual').forEach(function(el){
      el.style.opacity = 1; el.style.transform = 'none';
    });
    document.querySelectorAll('.hero h1 .line span').forEach(function(el){ el.style.transform = 'none'; });
  } else {
    var progress = { val: 0 };
    var ltl = gsap.timeline({
      onComplete: function(){
        gsap.to(loader, {
          opacity: 0, duration: .6, ease: 'power2.inOut',
          onComplete: function(){
            loader.style.display = 'none';
            runHeroEntrance();
          }
        });
      }
    });
    ltl.to(loaderMark, { opacity: 1, duration: .5 })
       .to(loaderPct, { opacity: 1, duration: .4 }, '-=0.2')
       .to(progress, {
          val: 100, duration: 1.3, ease: 'power1.inOut',
          onUpdate: function(){
            var v = Math.round(progress.val);
            loaderBar.style.width = v + '%';
            loaderPct.textContent = v + '%';
          }
       })
       .to({}, { duration: .25 });
  }

  /* ---------- NAV SCROLL STATE ---------- */
  var nav = document.getElementById('siteNav');
  function onScrollNav(){
    if (window.scrollY > 40) nav.classList.add('scrolled');
    else nav.classList.remove('scrolled');
  }
  window.addEventListener('scroll', onScrollNav, { passive: true });
  onScrollNav();

  /* ---------- CUSTOM CURSOR ---------- */
  if (!isTouch && !reduceMotion) {
    document.body.classList.add('has-custom-cursor');
    var dot = document.getElementById('cursorDot');
    var ring = document.getElementById('cursorRing');
    var mx = 0, my = 0, rx = 0, ry = 0;

    window.addEventListener('mousemove', function(e){
      mx = e.clientX; my = e.clientY;
      dot.style.transform = 'translate(' + mx + 'px,' + my + 'px) translate(-50%,-50%)';
    });

    function ringLoop(){
      rx += (mx - rx) * 0.18;
      ry += (my - ry) * 0.18;
      ring.style.transform = 'translate(' + rx + 'px,' + ry + 'px) translate(-50%,-50%)';
      requestAnimationFrame(ringLoop);
    }
    ringLoop();

    document.querySelectorAll('a, button, .feature-card, .team-card, .hackathon-card').forEach(function(el){
      el.addEventListener('mouseenter', function(){ ring.classList.add('is-active'); });
      el.addEventListener('mouseleave', function(){ ring.classList.remove('is-active'); });
    });
  }

  /* ---------- HERO MOUSE PARALLAX ---------- */
  if (!isTouch && !reduceMotion && typeof gsap !== 'undefined') {
    var stage = document.getElementById('hero-stage');
    var phone1 = document.getElementById('phoneOne');
    var phone2 = document.getElementById('phoneTwo');
    var cloud = document.querySelector('.cloud-node');
    var glowO = document.querySelector('.hero-glow-orange');
    var glowB = document.querySelector('.hero-glow-blue');

    stage.addEventListener('mousemove', function(e){
      var r = stage.getBoundingClientRect();
      var px = (e.clientX - r.left) / r.width - 0.5;
      var py = (e.clientY - r.top) / r.height - 0.5;

      gsap.to(phone1, { x: px * 18, y: py * 14, rotate: -9 + px * 4, duration: .6, ease: 'power2.out' });
      gsap.to(phone2, { x: px * -14, y: py * -10, rotate: 8 + px * -4, duration: .6, ease: 'power2.out' });
      gsap.to(cloud, { x: px * 10, y: py * 10, duration: .6, ease: 'power2.out' });
      gsap.to(glowO, { x: px * 30, y: py * 30, duration: .8, ease: 'power2.out' });
      gsap.to(glowB, { x: px * -30, y: py * -30, duration: .8, ease: 'power2.out' });
    });
  }

  /* ---------- GENTLE FLOAT ANIMATIONS ---------- */
  if (!reduceMotion && typeof gsap !== 'undefined') {
    gsap.to('#phoneOne', { y: '+=10', duration: 3.4, repeat: -1, yoyo: true, ease: 'sine.inOut' });
    gsap.to('#phoneTwo', { y: '-=10', duration: 3.8, repeat: -1, yoyo: true, ease: 'sine.inOut' });
    gsap.to('.cloud-node', { y: '+=8', duration: 2.6, repeat: -1, yoyo: true, ease: 'sine.inOut' });
    gsap.to('.orbit-ring.r2', { rotate: 360, duration: 40, repeat: -1, ease: 'none', transformOrigin: '50% 50%' });
  }

  /* ---------- SCROLLTRIGGER REVEALS ---------- */
  if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
    gsap.registerPlugin(ScrollTrigger);

    document.querySelectorAll('.').forEach(function(el, i){
      gsap.fromTo(el,
        { opacity: reduceMotion ? 1 : 0, y: reduceMotion ? 0 : 26 },
        {
          opacity: 1, y: 0, duration: .8, ease: 'power3.out',
          scrollTrigger: { trigger: el, start: 'top 88%' }
        }
      );
    });

    gsap.utils.toArray('.feature-grid').forEach(function(grid){
      gsap.from(grid.children, {
        opacity: 0, y: 30, duration: .7, stagger: .12, ease: 'power3.out',
        scrollTrigger: { trigger: grid, start: 'top 85%' }
      });
    });
    gsap.utils.toArray('.team-grid, .hackathon-grid, .stats-inner').forEach(function(grid){
      gsap.from(grid.children, {
        opacity: 0, y: 30, duration: .7, stagger: .12, ease: 'power3.out',
        scrollTrigger: { trigger: grid, start: 'top 88%' }
      });
    });
  }

});
</script>
</body>
</html>