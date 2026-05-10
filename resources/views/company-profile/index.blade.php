<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="{{ __('company.meta_description') }}">
<title>{{ __('company.page_title') }}</title>
<link rel="icon" href="{{ asset('assets/images/favicon.ico') }}">

{{-- Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

{{-- AOS --}}
<link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

<style>
/* ─── CSS Variables / Themes ─────────────────────────────────────── */
:root {
  --accent:        #dc2626;
  --accent-light:  #ef4444;
  --accent-dark:   #b91c1c;
  --accent-glow:   rgba(220,38,38,.25);
  --radius:        14px;
  --radius-sm:     8px;
  --transition:    .3s ease;
}
[data-theme="dark"] {
  --bg:        #0a0a0a;
  --bg2:       #111111;
  --bg3:       #1a1a1a;
  --card:      #1e1e1e;
  --border:    #2a2a2a;
  --text:      #f0f0f0;
  --text-sub:  #9ca3af;
  --nav-bg:    rgba(10,10,10,.96);
  --shadow:    0 8px 32px rgba(0,0,0,.6);
}
[data-theme="light"] {
  --bg:        #ffffff;
  --bg2:       #f9f9f9;
  --bg3:       #f0f0f0;
  --card:      #ffffff;
  --border:    #e5e5e5;
  --text:      #111111;
  --text-sub:  #6b7280;
  --nav-bg:    rgba(255,255,255,.96);
  --shadow:    0 8px 32px rgba(0,0,0,.1);
}

/* ─── Base ───────────────────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html {
  scroll-behavior: smooth;
  font-size: 16px;
  width: 100%;
  max-width: 100%;
  overflow-x: clip;
}
body {
  font-family: 'Inter', sans-serif;
  background: var(--bg);
  color: var(--text);
  transition: background var(--transition), color var(--transition);
  overflow-x: hidden;
  width: 100%;
  max-width: 100%;
}
img { max-width: 100%; display: block; }
a { text-decoration: none; color: inherit; }
ul { list-style: none; }

/* ─── Utility ────────────────────────────────────────────────────── */
.section { padding: 100px 0; }
.section-alt { background: var(--bg2); }
.container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
.container,
.hero-content,
.about-grid,
.svc-grid,
.fleet-grid,
.charts-grid,
.why-grid,
.contact-grid,
.footer-grid,
.form-row,
.hero-img-row,
.stats-inner,
.kpi-row {
  min-width: 0;
  max-width: 100%;
}
[data-aos] { max-width: 100%; }
.label-chip {
  display: inline-flex; align-items: center; gap: 6px;
  background: var(--accent-glow); color: var(--accent);
  font-size: .75rem; font-weight: 700; letter-spacing: .08em;
  text-transform: uppercase; padding: 6px 14px;
  border-radius: 999px; border: 1px solid rgba(220,38,38,.3);
  margin-bottom: 16px;
}
.section-title {
  font-size: clamp(2rem,4vw,2.8rem);
  font-weight: 800; line-height: 1.15;
  margin-bottom: 16px;
}
.section-desc {
  color: var(--text-sub); font-size: 1.05rem;
  line-height: 1.75; max-width: 640px;
  overflow-wrap: anywhere;
}
.accent { color: var(--accent); }
.btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 14px 28px; border-radius: var(--radius-sm);
  font-size: .95rem; font-weight: 600; cursor: pointer;
  border: none; transition: all var(--transition);
  white-space: normal;
  text-align: center;
}
.btn-primary {
  background: var(--accent); color: #fff;
  box-shadow: 0 4px 20px var(--accent-glow);
}
.btn-primary:hover { background: var(--accent-dark); transform: translateY(-2px); box-shadow: 0 8px 30px var(--accent-glow); }
.btn-outline {
  background: transparent;
  color: #fff;
  border: 1.5px solid rgba(255,255,255,.22);
}
.btn-outline i {
  color: #fff;
}
.btn-outline:hover {
  border-color: rgba(255,255,255,.4);
  color: #fff;
  transform: translateY(-2px);
}
.btn-outline:hover i {
  color: #fff;
}
.grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 32px; }
.grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 28px; }
.grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }

/* ─── Navbar ─────────────────────────────────────────────────────── */
.navbar {
  position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
  background: var(--nav-bg);
  backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid var(--border);
  transition: all var(--transition);
  width: 100%;
  max-width: 100%;
  overflow-x: clip;
}
.navbar.scrolled { box-shadow: var(--shadow); }
.navbar-inner {
  display: flex; align-items: center; justify-content: space-between;
  height: 72px; padding: 0 24px; max-width: 1280px; margin: 0 auto;
  width: 100%;
}
.nav-logo {
  display: flex; align-items: center; gap: 12px; text-decoration: none;
  min-width: 0;
}
.nav-logo img { height: 44px; width: auto; }
.nav-logo-text { display: flex; flex-direction: column; line-height: 1.2; min-width: 0; }
.nav-logo-text strong {
  font-size: .8rem; font-weight: 800; color: var(--accent);
  letter-spacing: .04em;
}
.nav-logo-text span {
  font-size: .65rem; color: var(--text-sub); font-weight: 500;
  overflow-wrap: anywhere;
}
.nav-menu {
  display: flex; align-items: center; gap: 4px;
  min-width: 0;
}
.nav-link {
  padding: 8px 14px; border-radius: var(--radius-sm);
  font-size: .88rem; font-weight: 500; color: var(--text-sub);
  transition: all var(--transition);
}
.nav-link:hover, .nav-link.active { color: var(--accent); background: var(--accent-glow); }
.nav-actions {
  display: flex; align-items: center; gap: 10px;
  min-width: 0;
  flex-shrink: 0;
}
.lang-switcher {
  display: flex; align-items: center;
  background: var(--bg3); border-radius: 999px;
  padding: 3px; border: 1px solid var(--border);
  flex-wrap: wrap;
}
.lang-btn {
  padding: 5px 12px; border-radius: 999px; font-size: .78rem;
  font-weight: 600; color: var(--text-sub); cursor: pointer;
  transition: all var(--transition); letter-spacing: .04em;
}
.lang-btn.active {
  background: var(--accent); color: #fff;
  box-shadow: 0 2px 8px var(--accent-glow);
}
.theme-btn {
  width: 40px; height: 40px; border-radius: 50%;
  background: var(--bg3); border: 1px solid var(--border);
  color: var(--text-sub); cursor: pointer; font-size: 1.1rem;
  display: flex; align-items: center; justify-content: center;
  transition: all var(--transition);
}
.theme-btn:hover { color: var(--accent); border-color: var(--accent); }
.btn-erp {
  padding: 9px 18px; background: var(--accent); color: #fff;
  border-radius: var(--radius-sm); font-size: .82rem; font-weight: 600;
  transition: all var(--transition);
  white-space: nowrap;
}
.btn-erp:hover { background: var(--accent-dark); transform: translateY(-1px); }
.hamburger {
  display: none; flex-direction: column; gap: 5px;
  background: none; border: none; cursor: pointer; padding: 4px;
}
.hamburger span {
  display: block; width: 24px; height: 2px;
  background: var(--text); border-radius: 2px; transition: all .3s;
}
.mobile-menu {
  display: none; flex-direction: column;
  background: var(--nav-bg); border-top: 1px solid var(--border);
  padding: 16px 24px 20px;
  width: 100%;
  max-width: 100%;
  overflow-x: clip;
}
.mobile-menu.open { display: flex; }
.mobile-menu .nav-link { padding: 12px 8px; border-bottom: 1px solid var(--border); }
.mobile-menu > div { flex-wrap: wrap; }

/* ─── Hero ───────────────────────────────────────────────────────── */
.hero {
  position: relative; min-height: 100vh;
  display: flex; align-items: center; overflow: hidden;
  background: #050505;
  width: 100%;
  max-width: 100%;
}
.hero-bg {
  position: absolute; inset: 0; z-index: 0;
  background: linear-gradient(135deg, #0a0a0a 0%, #1a0505 40%, #0a0a0a 100%);
}
.hero-grid {
  position: absolute; inset: 0; z-index: 0; opacity: .06;
  background-image:
    linear-gradient(var(--border) 1px, transparent 1px),
    linear-gradient(90deg, var(--border) 1px, transparent 1px);
  background-size: 60px 60px;
}
.hero-glow {
  position: absolute; top: 20%; left: 50%; transform: translateX(-50%);
  width: 700px; height: 700px; border-radius: 50%;
  background: radial-gradient(circle, rgba(220,38,38,.15) 0%, transparent 70%);
  z-index: 0; pointer-events: none;
}
.hero-particles { position: absolute; inset: 0; z-index: 0; overflow: hidden; }
.hero-particles span {
  position: absolute; width: 2px; height: 2px;
  background: var(--accent); border-radius: 50%; opacity: .4;
  animation: float-up linear infinite;
}
@keyframes float-up {
  0% { transform: translateY(100vh) scale(0); opacity: 0; }
  10% { opacity: .4; }
  90% { opacity: .4; }
  100% { transform: translateY(-100px) scale(1.5); opacity: 0; }
}
.hero-content {
  position: relative; z-index: 2;
  max-width: 1200px; margin: 0 auto; padding: 0 24px;
  padding-top: 80px; width: 100%;
  display: grid; grid-template-columns: 1fr 1fr; gap: 60px;
  align-items: center;
}
.hero-content > * { min-width: 0; }
.hero-badge {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(220,38,38,.12); border: 1px solid rgba(220,38,38,.3);
  color: var(--accent); padding: 8px 16px; border-radius: 999px;
  font-size: .78rem; font-weight: 700; letter-spacing: .06em;
  text-transform: uppercase; margin-bottom: 24px;
  flex-wrap: wrap;
}
.hero-badge i { font-size: 1rem; }
.hero-title {
  font-size: clamp(2.4rem,5vw,3.8rem);
  font-weight: 900; line-height: 1.1; margin-bottom: 20px;
  color: #fff;
}
.hero-title .line-accent {
  display: block; color: var(--accent);
  text-shadow: 0 0 40px rgba(220,38,38,.4);
}
.hero-desc {
  font-size: 1.05rem; color: #a0a0a0; line-height: 1.8;
  margin-bottom: 36px; max-width: 480px;
  overflow-wrap: anywhere;
}
.hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 48px; }
.hero-stats {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 1px; background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.08); border-radius: var(--radius);
  overflow: hidden;
}
.hero-stat {
  padding: 18px 16px; background: rgba(255,255,255,.03);
  text-align: center;
}
.hero-stat strong {
  display: block; font-size: 1.6rem; font-weight: 800; color: var(--accent);
}
.hero-stat span { font-size: .75rem; color: #777; margin-top: 2px; display: block; }
.hero-visual {
  display: flex; flex-direction: column; gap: 16px;
}
.hero-img-main {
  border-radius: var(--radius); overflow: hidden;
  height: 300px; position: relative;
  background: linear-gradient(135deg, #1a1a1a, #2a0a0a);
  border: 1px solid rgba(220,38,38,.2);
  box-shadow: 0 20px 60px rgba(0,0,0,.6), 0 0 0 1px rgba(220,38,38,.1);
}
.hero-img-main img {
  width: 100%; height: 100%; object-fit: cover; opacity: .75;
}
.hero-img-main .play-btn {
  position: absolute; inset: 0; display: flex; align-items: center;
  justify-content: center; cursor: pointer; transition: all var(--transition);
}
.play-circle {
  width: 72px; height: 72px; border-radius: 50%;
  background: rgba(220,38,38,.9); display: flex;
  align-items: center; justify-content: center;
  font-size: 1.6rem; color: #fff;
  box-shadow: 0 0 0 12px rgba(220,38,38,.2), 0 0 0 24px rgba(220,38,38,.08);
  transition: all var(--transition);
}
.play-circle:hover { transform: scale(1.1); background: var(--accent-dark); }
.hero-img-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.hero-img-sm {
  border-radius: var(--radius-sm); overflow: hidden;
  height: 140px; border: 1px solid rgba(255,255,255,.06);
  position: relative;
}
.hero-img-sm img { width: 100%; height: 100%; object-fit: cover; opacity: .7; }
.img-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,.7) 0%, transparent 60%);
  display: flex; align-items: flex-end; padding: 12px;
}
.img-overlay span { font-size: .75rem; font-weight: 600; color: #fff; }
.scroll-hint {
  position: absolute; bottom: 32px; left: 50%; transform: translateX(-50%);
  z-index: 2; display: flex; flex-direction: column; align-items: center;
  gap: 8px; color: #555; font-size: .75rem; letter-spacing: .08em;
  text-transform: uppercase;
}
.scroll-dot {
  width: 24px; height: 40px; border: 2px solid #333; border-radius: 12px;
  position: relative;
}
.scroll-dot::after {
  content: ''; position: absolute; top: 6px; left: 50%; transform: translateX(-50%);
  width: 4px; height: 8px; background: var(--accent); border-radius: 2px;
  animation: scroll-anim 2s ease infinite;
}
@keyframes scroll-anim {
  0% { top: 6px; opacity: 1; } 100% { top: 22px; opacity: 0; }
}

/* ─── Stats Bar ──────────────────────────────────────────────────── */
.stats-bar { background: var(--accent); padding: 0; overflow: hidden; }
.stats-bar,
.section,
.footer {
  width: 100%;
  max-width: 100%;
  overflow-x: clip;
}
.stats-inner {
  display: grid; grid-template-columns: repeat(4, 1fr);
  max-width: 1200px; margin: 0 auto;
}
.stat-item {
  padding: 32px 24px; text-align: center;
  border-right: 1px solid rgba(255,255,255,.15);
  transition: background var(--transition);
}
.stat-item:last-child { border-right: none; }
.stat-item:hover { background: rgba(0,0,0,.1); }
.stat-number {
  font-size: clamp(2rem,3.5vw,2.8rem);
  font-weight: 900; color: #fff; display: block; line-height: 1;
}
.stat-label { font-size: .82rem; color: rgba(255,255,255,.75); margin-top: 6px; font-weight: 500; }

/* ─── About ──────────────────────────────────────────────────────── */
.about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; }
.about-img-wrap {
  position: relative; border-radius: var(--radius);
  overflow: hidden;
}
.about-img-main {
  width: 100%; height: 420px; object-fit: cover;
  border-radius: var(--radius);
  border: 1px solid var(--border);
}
.about-img-badge {
  position: absolute; bottom: 24px; left: 24px;
  background: var(--accent); color: #fff; padding: 12px 20px;
  border-radius: var(--radius-sm); font-size: .85rem; font-weight: 600;
  box-shadow: 0 8px 24px rgba(220,38,38,.4);
  display: flex; align-items: center; gap: 10px;
}
.about-img-badge i { font-size: 1.4rem; }
.about-certif {
  position: absolute; top: 20px; right: 20px;
  display: flex; flex-direction: column; gap: 8px;
}
.certif-chip {
  background: rgba(0,0,0,.75); backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,.1); color: #fff;
  padding: 6px 12px; border-radius: 999px;
  font-size: .72rem; font-weight: 600;
  display: flex; align-items: center; gap: 6px;
}
.certif-chip i { color: var(--accent); }
.about-text p { color: var(--text-sub); line-height: 1.8; margin-bottom: 16px; }
.about-badges { display: flex; gap: 12px; flex-wrap: wrap; margin: 28px 0; }
.badge-item {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 16px; border-radius: var(--radius-sm);
  border: 1px solid var(--border); background: var(--card);
  font-size: .83rem; font-weight: 600; color: var(--text);
}
.badge-item i { color: var(--accent); font-size: 1.1rem; }
.vm-tabs { display: flex; gap: 8px; margin-bottom: 20px; margin-top: 32px; }
.vm-tab {
  padding: 8px 20px; border-radius: var(--radius-sm);
  font-size: .88rem; font-weight: 600; cursor: pointer;
  border: 1.5px solid var(--border); background: transparent;
  color: var(--text-sub); transition: all var(--transition);
}
.vm-tab.active {
  background: var(--accent); color: #fff; border-color: var(--accent);
  box-shadow: 0 4px 16px var(--accent-glow);
}
.vm-panel { display: none; }
.vm-panel.active { display: block; }
.vm-box {
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 24px;
}
.vm-box p { color: var(--text-sub); line-height: 1.8; font-size: .95rem; }
.mission-list { display: flex; flex-direction: column; gap: 12px; }
.mission-item {
  display: flex; gap: 12px; align-items: flex-start;
  padding: 10px 0; border-bottom: 1px solid var(--border);
}
.mission-item:last-child { border-bottom: none; }
.mission-num {
  min-width: 28px; height: 28px; border-radius: 50%;
  background: var(--accent); color: #fff;
  font-size: .75rem; font-weight: 700;
  display: flex; align-items: center; justify-content: center;
}
.mission-item p { color: var(--text-sub); font-size: .88rem; line-height: 1.6; }

/* ─── Services ───────────────────────────────────────────────────── */
.svc-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 56px; }
.svc-card {
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 32px;
  transition: all var(--transition); position: relative; overflow: hidden;
}
.svc-card::before {
  content: ''; position: absolute; inset: 0;
  background: linear-gradient(135deg, var(--accent-glow), transparent);
  opacity: 0; transition: opacity var(--transition);
}
.svc-card:hover { border-color: var(--accent); transform: translateY(-4px); box-shadow: var(--shadow); }
.svc-card:hover::before { opacity: 1; }
.svc-icon {
  width: 56px; height: 56px; border-radius: var(--radius-sm);
  background: var(--accent-glow); display: flex;
  align-items: center; justify-content: center;
  font-size: 1.6rem; color: var(--accent);
  margin-bottom: 20px; border: 1px solid rgba(220,38,38,.2);
  transition: all var(--transition);
}
.svc-card:hover .svc-icon {
  background: var(--accent); color: #fff;
  box-shadow: 0 6px 20px var(--accent-glow);
}
.svc-card h3 { font-size: 1.05rem; font-weight: 700; margin-bottom: 10px; }
.svc-card p { color: var(--text-sub); font-size: .88rem; line-height: 1.7; }

/* ─── Fleet ──────────────────────────────────────────────────────── */
.fleet-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px; margin-top: 56px;
}
.fleet-card {
  border-radius: var(--radius); overflow: hidden;
  border: 1px solid var(--border); position: relative;
  height: 240px; cursor: pointer;
  transition: all var(--transition);
}
.fleet-card:first-child { grid-column: span 2; }
.fleet-card:hover { transform: scale(1.02); box-shadow: var(--shadow); border-color: var(--accent); }
.fleet-img {
  width: 100%; height: 100%; object-fit: cover;
  transition: transform .5s ease;
}
.fleet-card:hover .fleet-img { transform: scale(1.06); }
.fleet-overlay {
  position: absolute; inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,.85) 0%, rgba(0,0,0,.1) 60%, transparent 100%);
  display: flex; flex-direction: column; justify-content: flex-end;
  padding: 20px;
}
.fleet-name { font-size: 1rem; font-weight: 700; color: #fff; }
.fleet-sub  { font-size: .78rem; color: rgba(255,255,255,.6); margin-top: 4px; }
.fleet-tag {
  display: inline-block; margin-top: 8px;
  background: var(--accent); color: #fff;
  font-size: .68rem; font-weight: 700;
  padding: 3px 10px; border-radius: 999px; letter-spacing: .04em;
}

/* ─── Performance / Charts ───────────────────────────────────────── */
.perf-header { display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 24px; margin-bottom: 48px; }
.kpi-row {
  display: grid; grid-template-columns: repeat(4, 1fr);
  gap: 20px; margin-bottom: 40px;
}
.kpi-card {
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 24px 20px; text-align: center;
  transition: all var(--transition);
}
.kpi-card:hover { border-color: var(--accent); transform: translateY(-2px); }
.kpi-value {
  font-size: 1.8rem; font-weight: 800; color: var(--accent);
  line-height: 1; margin-bottom: 6px;
}
.kpi-label { font-size: .78rem; color: var(--text-sub); font-weight: 500; }
.charts-grid {
  display: grid;
  grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
  gap: 24px;
  align-items: stretch;
}
.chart-card {
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 28px;
  min-width: 0;
  overflow: hidden;
}
.chart-card-full { grid-column: span 2; }
.chart-title { font-size: .95rem; font-weight: 700; margin-bottom: 6px; }
.chart-sub { font-size: .78rem; color: var(--text-sub); margin-bottom: 24px; }
.chart-wrap {
  position: relative;
  width: 100%;
  min-width: 0;
  overflow: hidden;
}
.chart-wrap canvas {
  width: 100% !important;
  height: 100% !important;
  display: block;
}
.chart-wrap.chart-tall { height: 260px; }
.chart-wrap.chart-wide { height: 220px; }

/* ─── Why Us ─────────────────────────────────────────────────────── */
.why-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 56px; }
.why-card {
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 28px;
  display: flex; gap: 18px; align-items: flex-start;
  transition: all var(--transition);
}
.why-card:hover { border-color: var(--accent); transform: translateY(-3px); box-shadow: var(--shadow); }
.why-icon {
  min-width: 48px; height: 48px; border-radius: var(--radius-sm);
  background: var(--accent-glow); display: flex; align-items: center;
  justify-content: center; font-size: 1.4rem; color: var(--accent);
  border: 1px solid rgba(220,38,38,.2);
}
.why-card h3 { font-size: .95rem; font-weight: 700; margin-bottom: 6px; }
.why-card p { font-size: .85rem; color: var(--text-sub); line-height: 1.7; }

/* ─── Video Modal ────────────────────────────────────────────────── */
.modal-overlay {
  position: fixed; inset: 0; z-index: 2000;
  background: rgba(0,0,0,.9); display: none;
  align-items: center; justify-content: center;
  backdrop-filter: blur(8px);
}
.modal-overlay.open { display: flex; }
.modal-box {
  position: relative; width: 90%; max-width: 900px;
  aspect-ratio: 16/9; border-radius: var(--radius);
  overflow: hidden; border: 1px solid rgba(255,255,255,.1);
}
.modal-close {
  position: absolute; top: -48px; right: 0;
  background: none; border: none; color: #fff;
  font-size: 2rem; cursor: pointer; line-height: 1;
}

/* ─── Contact ────────────────────────────────────────────────────── */
.contact-grid { display: grid; grid-template-columns: 1fr 1.4fr; gap: 56px; align-items: start; }
.contact-info { display: flex; flex-direction: column; gap: 28px; }
.contact-item { display: flex; gap: 16px; align-items: flex-start; }
.contact-item > div:last-child { min-width: 0; }
.contact-icon {
  min-width: 48px; height: 48px; border-radius: var(--radius-sm);
  background: var(--accent-glow); display: flex; align-items: center;
  justify-content: center; font-size: 1.3rem; color: var(--accent);
  border: 1px solid rgba(220,38,38,.2);
}
.contact-item h4 { font-size: .82rem; font-weight: 600; color: var(--text-sub); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 4px; }
.contact-item p { font-size: .93rem; color: var(--text); line-height: 1.6; }
.contact-map {
  margin-top: 28px; border-radius: var(--radius); overflow: hidden;
  border: 1px solid var(--border); height: 200px; background: var(--bg3);
  display: flex; align-items: center; justify-content: center;
  color: var(--text-sub); font-size: .88rem; gap: 8px;
  padding: 18px;
  text-align: center;
  line-height: 1.6;
}
.contact-form {
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 36px;
}
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px; }
.form-group label { font-size: .82rem; font-weight: 600; color: var(--text); }
.form-control {
  padding: 12px 16px; border-radius: var(--radius-sm);
  border: 1.5px solid var(--border); background: var(--bg2);
  color: var(--text); font-size: .9rem; font-family: inherit;
  transition: all var(--transition); outline: none;
}
.form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px var(--accent-glow); }
.form-control::placeholder { color: var(--text-sub); }
textarea.form-control { resize: vertical; min-height: 120px; }
.btn-submit {
  width: 100%; padding: 14px; background: var(--accent);
  color: #fff; border: none; border-radius: var(--radius-sm);
  font-size: .95rem; font-weight: 600; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: all var(--transition);
}
.btn-submit:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 8px 24px var(--accent-glow); }

/* ─── Footer ─────────────────────────────────────────────────────── */
.footer {
  background: #080808; border-top: 1px solid #1e1e1e;
  padding: 64px 0 0;
}
[data-theme="light"] .footer {
  background: #111; color: #ccc;
}
.footer-grid { display: grid; grid-template-columns: 1.6fr 1fr 1fr 1fr; gap: 40px; }
.footer-brand .nav-logo { margin-bottom: 16px; }
.footer-brand p { font-size: .85rem; color: #666; line-height: 1.7; max-width: 260px; }
.footer-socials { display: flex; gap: 10px; margin-top: 20px; }
.social-btn {
  width: 38px; height: 38px; border-radius: 50%;
  background: #1e1e1e; border: 1px solid #2a2a2a;
  color: #666; font-size: 1rem;
  display: flex; align-items: center; justify-content: center;
  transition: all var(--transition);
}
.social-btn:hover { background: var(--accent); color: #fff; border-color: var(--accent); transform: translateY(-2px); }
.footer-col h4 { font-size: .82rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #888; margin-bottom: 16px; }
.footer-col ul { display: flex; flex-direction: column; gap: 10px; }
.footer-col ul li a { font-size: .88rem; color: #555; transition: color var(--transition); }
.footer-col,
.footer-col ul,
.footer-col li,
.footer-col a,
.footer-brand,
.footer-brand p,
.footer-bottom p {
  min-width: 0;
  overflow-wrap: anywhere;
}
.footer-col ul li a:hover { color: var(--accent); }
.footer-bottom {
  margin-top: 48px; padding: 20px 24px;
  border-top: 1px solid #1a1a1a;
  display: flex; justify-content: space-between; align-items: center;
  flex-wrap: wrap; gap: 12px;
}
.footer-bottom p { font-size: .8rem; color: #444; }
.footer-bottom a { color: var(--accent); }

/* ─── Back to Top ────────────────────────────────────────────────── */
.back-top {
  position: fixed; bottom: 28px; right: 28px; z-index: 500;
  width: 44px; height: 44px; border-radius: 50%;
  background: var(--accent); color: #fff; border: none;
  font-size: 1.1rem; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  box-shadow: 0 4px 16px var(--accent-glow);
  opacity: 0; transform: translateY(16px);
  transition: all var(--transition);
}
.back-top.visible { opacity: 1; transform: translateY(0); }
.back-top:hover { background: var(--accent-dark); transform: translateY(-4px) !important; }

/* ─── Responsive ─────────────────────────────────────────────────── */
@media (max-width: 1180px) {
  .navbar-inner { padding-left: 20px; padding-right: 20px; }
  .nav-link { padding: 8px 10px; font-size: .82rem; }
  .lang-btn { padding: 5px 10px; font-size: .74rem; }
  .btn-erp { padding: 9px 14px; font-size: .78rem; }
  .nav-logo-text strong { font-size: .74rem; }
  .nav-logo-text span { font-size: .58rem; }
}
@media (max-width: 1024px) {
  .container, .hero-content { padding-left: 20px; padding-right: 20px; }
  .hero-content { grid-template-columns: 1fr; gap: 40px; }
  .hero-visual { display: none; }
  .about-grid, .contact-grid { grid-template-columns: 1fr; }
  .svc-grid, .fleet-grid, .why-grid { grid-template-columns: repeat(2, 1fr); }
  .kpi-row { grid-template-columns: repeat(2, 1fr); }
  .charts-grid { grid-template-columns: 1fr; }
  .chart-card-full { grid-column: span 1; }
  .chart-wrap.chart-tall { height: 240px; }
  .chart-wrap.chart-wide { height: 220px; }
  .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
}
@media (max-width: 900px) {
  .nav-menu, .nav-actions { display: none; }
  .hamburger { display: flex; }
  .navbar-inner { height: 68px; }
  .hero-content { padding-top: 112px; }
  .svc-grid, .why-grid, .fleet-grid { grid-template-columns: 1fr; }
  .fleet-card:first-child { grid-column: span 1; }
}
@media (max-width: 768px) {
  .section { padding: 72px 0; }
  .container, .hero-content, .navbar-inner { padding-left: 16px; padding-right: 16px; }
  .navbar-inner { height: 68px; }
  .mobile-menu { padding: 12px 16px 18px; }
  .mobile-menu > div {
    flex-direction: column;
    align-items: stretch;
    gap: 10px !important;
  }
  .mobile-menu .lang-btn,
  .mobile-menu .btn-erp {
    width: 100%;
    text-align: center;
    justify-content: center;
    margin-left: 0 !important;
  }
  .nav-logo img { height: 38px; }
  .nav-logo-text strong { font-size: .72rem; }
  .nav-logo-text span { font-size: .58rem; }
  .hero { min-height: auto; }
  .hero-content { padding-top: 112px; gap: 28px; }
  .hero-title { font-size: clamp(2rem, 10vw, 3rem); }
  .hero-desc { max-width: none; font-size: .98rem; }
  .hero-actions { flex-direction: column; align-items: stretch; }
  .hero-actions .btn { justify-content: center; width: 100%; }
  .svc-grid, .why-grid, .fleet-grid { grid-template-columns: 1fr; }
  .fleet-card:first-child { grid-column: span 1; }
  .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
  .kpi-row { grid-template-columns: repeat(2, 1fr); }
  .stats-inner { grid-template-columns: repeat(2, 1fr); }
  .about-grid { gap: 32px; }
  .about-img-wrap {
    display: flex;
    flex-direction: column;
    gap: 12px;
    overflow: visible;
  }
  .about-img-main { height: 320px; }
  .about-certif {
    position: static;
    order: 2;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 8px;
  }
  .about-img-badge {
    position: static;
    order: 3;
    left: auto;
    right: auto;
    bottom: auto;
    width: 100%;
    max-width: none;
    box-shadow: 0 6px 18px rgba(220,38,38,.28);
  }
  .section-title { font-size: clamp(1.75rem, 7vw, 2.4rem); }
  .chart-wrap.chart-tall { height: 220px; }
  .chart-wrap.chart-wide { height: 200px; }
  .contact-form { padding: 24px; }
  .stat-item:nth-child(2) { border-right: none; }
  .form-row { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr; }
  .footer-brand p { max-width: none; }
  .footer-bottom { flex-direction: column; text-align: center; }
  .hero-stats { grid-template-columns: 1fr; }
  .scroll-hint { display: none; }
  .back-top { right: 16px; bottom: 16px; }
}
@media (max-width: 600px) {
  .section { padding: 64px 0; }
  .label-chip { font-size: .68rem; padding: 6px 12px; letter-spacing: .05em; }
  .section-desc { font-size: .95rem; }
  .hero-badge { width: fit-content; max-width: 100%; }
  .hero-desc { font-size: .94rem; line-height: 1.7; }
  .about-img-main { height: 280px; }
  .about-img-badge {
    padding: 12px 14px;
    font-size: .8rem;
  }
  .about-img-badge i { font-size: 1.2rem; }
  .about-certif { margin-top: 0; }
  .certif-chip { font-size: .68rem; padding: 6px 10px; }
  .vm-tabs { display: grid; grid-template-columns: 1fr 1fr; }
  .vm-tab { width: 100%; padding: 10px 12px; }
  .mission-item { gap: 10px; }
  .svc-card, .why-card, .chart-card, .contact-form, .vm-box { padding: 18px; }
  .why-card { gap: 14px; }
  .why-icon { min-width: 42px; height: 42px; font-size: 1.15rem; }
  .kpi-value { font-size: 1.55rem; }
  .chart-wrap.chart-tall { height: 200px; }
  .chart-wrap.chart-wide { height: 190px; }
  .footer-bottom { padding-left: 16px; padding-right: 16px; }
}
@media (max-width: 480px) {
  .container, .hero-content, .navbar-inner { padding-left: 14px; padding-right: 14px; }
  .nav-logo { gap: 10px; max-width: calc(100% - 44px); }
  .nav-logo-text strong { font-size: .64rem; line-height: 1.25; }
  .nav-logo-text span { display: none; }
  .mobile-menu .nav-link { font-size: .95rem; }
  .hero-content { padding-top: 104px; }
  .hero-title { font-size: clamp(1.8rem, 11vw, 2.4rem); }
  .hero-badge { font-size: .68rem; padding: 7px 12px; }
  .hero-stat { padding: 16px 12px; }
  .hero-stat strong { font-size: 1.35rem; }
  .kpi-row { grid-template-columns: 1fr; }
  .stats-inner { grid-template-columns: 1fr; }
  .kpi-card, .chart-card, .svc-card, .why-card, .contact-form { padding: 20px; }
  .about-grid { gap: 24px; }
  .about-img-main { height: 240px; }
  .about-img-badge {
    padding: 10px 12px;
    gap: 8px;
  }
  .about-img-badge > div div:last-child { font-size: 1rem !important; }
  .about-badges { gap: 10px; margin: 22px 0; }
  .badge-item { width: 100%; justify-content: flex-start; }
  .hero-img-row { grid-template-columns: 1fr; }
  .play-circle { width: 58px; height: 58px; font-size: 1.2rem; box-shadow: 0 0 0 10px rgba(220,38,38,.18), 0 0 0 18px rgba(220,38,38,.08); }
  .charts-grid { gap: 16px; }
  .chart-card { padding: 18px; }
  .chart-wrap.chart-tall { height: 180px; }
  .chart-wrap.chart-wide { height: 170px; }
  .contact-item { gap: 12px; }
  .contact-icon { min-width: 42px; height: 42px; font-size: 1.1rem; }
  .contact-map { height: auto; min-height: 120px; }
  .footer { padding-top: 48px; }
  .stat-item { border-right: none; border-bottom: 1px solid rgba(255,255,255,.1); }
  .stat-item:last-child { border-bottom: none; }
}
@media (max-width: 360px) {
  html { font-size: 15px; }
  .container, .hero-content, .navbar-inner { padding-left: 12px; padding-right: 12px; }
  .mobile-menu { padding-left: 12px; padding-right: 12px; }
  .mobile-menu > div { gap: 8px !important; }
  .mobile-menu .lang-btn,
  .mobile-menu .btn-erp {
    width: 100%;
    text-align: center;
    justify-content: center;
    margin-left: 0 !important;
  }
  .hero-content { padding-top: 96px; }
  .hero-title { font-size: 1.7rem; }
  .hero-badge { padding: 6px 10px; }
  .hero-stat span,
  .stat-label,
  .kpi-label,
  .chart-sub,
  .footer-bottom p { font-size: .74rem; }
  .about-img-main { height: 210px; }
  .about-img-badge {
    padding: 10px;
    border-radius: 12px;
  }
  .about-certif { gap: 6px; }
  .certif-chip {
    width: 100%;
    justify-content: center;
  }
  .vm-tabs { grid-template-columns: 1fr; }
  .chart-wrap.chart-tall { height: 165px; }
  .chart-wrap.chart-wide { height: 155px; }
}
</style>
</head>
<body>

{{-- ─── Navbar ──────────────────────────────────────────────────────── --}}
<nav class="navbar" id="navbar">
  <div class="navbar-inner">
    <a href="{{ route('company.profile') }}" class="nav-logo">
      <img src="{{ asset('assets/images/logo.png') }}" alt="ASH Logo">
      <div class="nav-logo-text">
        <strong>{{ __('company.company_name') }}</strong>
        <span>{{ __('company.company_subtitle') }}</span>
      </div>
    </a>

    <ul class="nav-menu">
      <li><a class="nav-link" href="#about">{{ __('company.nav_about') }}</a></li>
      <li><a class="nav-link" href="#services">{{ __('company.nav_services') }}</a></li>
      <li><a class="nav-link" href="#fleet">{{ __('company.nav_fleet') }}</a></li>
      <li><a class="nav-link" href="#performance">{{ __('company.nav_performance') }}</a></li>
      <li><a class="nav-link" href="#contact">{{ __('company.nav_contact') }}</a></li>
    </ul>

    <div class="nav-actions">
      {{-- Language Switcher --}}
      <div class="lang-switcher">
        <a href="{{ route('lang.switch', 'id') }}" class="lang-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}">ID</a>
        <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
        <a href="{{ route('lang.switch', 'zh') }}" class="lang-btn {{ app()->getLocale() === 'zh' ? 'active' : '' }}">ZH</a>
      </div>
      {{-- Theme Toggle --}}
      <button class="theme-btn" id="themeBtn" title="{{ __('company.theme_dark') }}">
        <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
      </button>
      {{-- ERP Login --}}
      <a href="{{ route('login') }}" class="btn-erp">
        <i class="bi bi-grid-3x3-gap-fill me-1"></i> {{ __('company.nav_login') }}
      </a>
    </div>

    <button class="hamburger" id="hamburger" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>

  <div class="mobile-menu" id="mobileMenu">
    <a class="nav-link" href="#about">{{ __('company.nav_about') }}</a>
    <a class="nav-link" href="#services">{{ __('company.nav_services') }}</a>
    <a class="nav-link" href="#fleet">{{ __('company.nav_fleet') }}</a>
    <a class="nav-link" href="#performance">{{ __('company.nav_performance') }}</a>
    <a class="nav-link" href="#contact">{{ __('company.nav_contact') }}</a>
    <div style="display:flex;gap:10px;padding:12px 8px;">
      <a href="{{ route('lang.switch', 'id') }}" class="lang-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}" style="border:1px solid var(--border);border-radius:999px;padding:6px 14px;">ID</a>
      <a href="{{ route('lang.switch', 'en') }}" class="lang-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}" style="border:1px solid var(--border);border-radius:999px;padding:6px 14px;">EN</a>
      <a href="{{ route('lang.switch', 'zh') }}" class="lang-btn {{ app()->getLocale() === 'zh' ? 'active' : '' }}" style="border:1px solid var(--border);border-radius:999px;padding:6px 14px;">ZH</a>
      <button type="button" class="theme-btn" id="themeBtnMobile" title="{{ __('company.theme_dark') }}" style="width:100%;border-radius:12px;">
        <i class="bi bi-moon-stars-fill" id="themeIconMobile"></i>
      </button>
      <a href="{{ route('login') }}" class="btn-erp" style="margin-left:auto;">ERP</a>
    </div>
  </div>
</nav>

{{-- ─── Hero ─────────────────────────────────────────────────────────── --}}
<section class="hero" id="home">
  <div class="hero-bg"></div>
  <div class="hero-grid"></div>
  <div class="hero-glow"></div>
  <div class="hero-particles" id="heroParticles"></div>

  <div class="hero-content">
    {{-- Left --}}
    <div>
      <div class="hero-badge">
        <i class="bi bi-truck"></i>
        {{ __('company.hero_badge') }}
      </div>
      <h1 class="hero-title">
        {{ __('company.company_short') }}
        <span class="line-accent">{{ __('company.company_accent') }}</span>
      </h1>
      <p class="hero-desc">{{ __('company.hero_desc') }}</p>
      <div class="hero-actions">
        <a href="#about" class="btn btn-primary">
          <i class="bi bi-arrow-down-circle"></i> {{ __('company.hero_cta_primary') }}
        </a>
        <button class="btn btn-outline" onclick="openVideoModal()">
          <i class="bi bi-play-circle"></i> {{ __('company.hero_cta_secondary') }}
        </button>
      </div>
      <div class="hero-stats">
        <div class="hero-stat">
          <strong>85+</strong>
          <span>{{ __('company.stats_units') }}</span>
        </div>
        <div class="hero-stat">
          <strong>12</strong>
          <span>{{ __('company.stats_years') }}</span>
        </div>
        <div class="hero-stat">
          <strong>15M+</strong>
          <span>{{ __('company.stats_tonnage') }}</span>
        </div>
      </div>
    </div>

    {{-- Right Visual --}}
    <div class="hero-visual">
      <div class="hero-img-main">
        <img src="https://images.unsplash.com/photo-1545558014-8692077e9b5c?w=900&q=80&fit=crop"
             alt="Coal Hauling Operations"
             onerror="this.style.display='none'">
        <div class="play-btn" onclick="openVideoModal()">
          <div class="play-circle"><i class="bi bi-play-fill"></i></div>
        </div>
      </div>
      <div class="hero-img-row">
        <div class="hero-img-sm">
          <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500&q=80&fit=crop"
               alt="Dump Truck" onerror="this.parentElement.style.background='#1a1a1a'">
          <div class="img-overlay"><span>{{ __('company.hero_image_1') }}</span></div>
        </div>
        <div class="hero-img-sm">
          <img src="https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=500&q=80&fit=crop"
               alt="Mining Site" onerror="this.parentElement.style.background='#1a1a1a'">
          <div class="img-overlay"><span>{{ __('company.hero_image_2') }}</span></div>
        </div>
      </div>
    </div>
  </div>

  <div class="scroll-hint">
    <div class="scroll-dot"></div>
    <span>{{ __('company.hero_scroll') }}</span>
  </div>
</section>

{{-- ─── Stats Bar ────────────────────────────────────────────────────── --}}
<div class="stats-bar">
  <div class="stats-inner">
    <div class="stat-item" data-aos="fade-up" data-aos-delay="0">
      <span class="stat-number" data-count="{{ $stats['units'] }}">0</span>
      <div class="stat-label"><i class="bi bi-truck me-1"></i> {{ __('company.stats_units') }}</div>
    </div>
    <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
      <span class="stat-number" data-count="{{ $stats['years'] }}">0</span>
      <div class="stat-label"><i class="bi bi-calendar-check me-1"></i> {{ __('company.stats_years') }}</div>
    </div>
    <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
      <span class="stat-number" data-count-text="{{ $stats['tonnage'] }}">0</span>
      <div class="stat-label"><i class="bi bi-box-seam me-1"></i> {{ __('company.stats_tonnage') }}</div>
    </div>
    <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
      <span class="stat-number" data-count="{{ $stats['clients'] }}">0</span>
      <div class="stat-label"><i class="bi bi-handshake me-1"></i> {{ __('company.stats_clients') }}</div>
    </div>
  </div>
</div>

{{-- ─── About ────────────────────────────────────────────────────────── --}}
<section class="section" id="about">
  <div class="container">
    <div class="about-grid">
      {{-- Image --}}
      <div data-aos="fade-right">
        <div class="about-img-wrap">
          <img class="about-img-main"
               src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=800&q=80&fit=crop"
               alt="About ASH"
               onerror="this.style.background='linear-gradient(135deg,#1a1a1a,#2a0a0a)'">
          <div class="about-certif">
            <div class="certif-chip"><i class="bi bi-patch-check-fill"></i> ISO 9001:2015</div>
            <div class="certif-chip"><i class="bi bi-shield-check"></i> SMK3 Gold</div>
            <div class="certif-chip"><i class="bi bi-award"></i> PROPER Biru</div>
          </div>
          <div class="about-img-badge">
            <i class="bi bi-calendar-heart"></i>
            <div>
              <div style="font-size:.72rem;opacity:.8">{{ __('company.about_since') }}</div>
              <div style="font-size:1.1rem;font-weight:800;">2012</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Text --}}
      <div data-aos="fade-left">
        <div class="label-chip"><i class="bi bi-building"></i> {{ __('company.about_label') }}</div>
        <h2 class="section-title">{{ __('company.about_title') }}</h2>
        <p style="color:var(--text-sub);line-height:1.8;margin-bottom:12px;">{{ __('company.about_p1') }}</p>
        <p style="color:var(--text-sub);line-height:1.8;margin-bottom:4px;">{{ __('company.about_p2') }}</p>

        <div class="about-badges">
          <div class="badge-item"><i class="bi bi-patch-check-fill"></i> {{ __('company.about_licensed') }}</div>
          <div class="badge-item"><i class="bi bi-shield-check"></i> {{ __('company.about_iso') }}</div>
          <div class="badge-item"><i class="bi bi-geo-alt-fill"></i> {{ __('company.about_region') }}</div>
        </div>

        {{-- Vision / Mission Tabs --}}
        <div class="vm-tabs">
          <button class="vm-tab active" onclick="switchTab(this,'vision')">{{ __('company.vision_label') }}</button>
          <button class="vm-tab" onclick="switchTab(this,'mission')">{{ __('company.mission_label') }}</button>
        </div>
        <div class="vm-box">
          <div class="vm-panel active" id="panel-vision">
            <p>{{ __('company.vision_text') }}</p>
          </div>
          <div class="vm-panel" id="panel-mission">
            <div class="mission-list">
              @foreach([1,2,3,4] as $n)
              <div class="mission-item">
                <div class="mission-num">{{ $n }}</div>
                <p>{{ __("company.mission_{$n}") }}</p>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ─── Services ─────────────────────────────────────────────────────── --}}
<section class="section section-alt" id="services">
  <div class="container">
    <div style="text-align:center" data-aos="fade-up">
      <div class="label-chip" style="margin:0 auto 16px;"><i class="bi bi-gear"></i> {{ __('company.services_label') }}</div>
      <h2 class="section-title">{{ __('company.services_title') }}</h2>
      <p class="section-desc" style="margin:0 auto;">{{ __('company.services_desc') }}</p>
    </div>

    <div class="svc-grid">
      @php
        $services = [
          ['icon'=>'bi-truck',           'title'=>'svc1_title','desc'=>'svc1_desc','delay'=>0],
          ['icon'=>'bi-diagram-3',       'title'=>'svc2_title','desc'=>'svc2_desc','delay'=>100],
          ['icon'=>'bi-tools',           'title'=>'svc3_title','desc'=>'svc3_desc','delay'=>200],
          ['icon'=>'bi-shield-check',    'title'=>'svc4_title','desc'=>'svc4_desc','delay'=>0],
          ['icon'=>'bi-geo-alt',         'title'=>'svc5_title','desc'=>'svc5_desc','delay'=>100],
          ['icon'=>'bi-graph-up-arrow',  'title'=>'svc6_title','desc'=>'svc6_desc','delay'=>200],
        ];
      @endphp
      @foreach($services as $s)
      <div class="svc-card" data-aos="fade-up" data-aos-delay="{{ $s['delay'] }}">
        <div class="svc-icon"><i class="bi {{ $s['icon'] }}"></i></div>
        <h3>{{ __('company.'.$s['title']) }}</h3>
        <p>{{ __('company.'.$s['desc']) }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ─── Fleet ────────────────────────────────────────────────────────── --}}
<section class="section" id="fleet">
  <div class="container">
    <div data-aos="fade-up">
      <div class="label-chip"><i class="bi bi-truck-flatbed"></i> {{ __('company.fleet_label') }}</div>
      <div style="display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;margin-bottom:12px;">
        <h2 class="section-title" style="margin-bottom:0;">{{ __('company.fleet_title') }}</h2>
        <a href="#contact" class="btn btn-outline" style="font-size:.85rem;">
          <i class="bi bi-info-circle"></i> {{ __('company.fleet_view_all') }}
        </a>
      </div>
      <p class="section-desc">{{ __('company.fleet_desc') }}</p>
    </div>

    @php
      $fleets = [
        ['key'=>'hd785',  'img'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=900&q=80&fit=crop', 'tag'=>'fleet_tag_hd785'],
        ['key'=>'cat777', 'img'=>'https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?w=600&q=80&fit=crop', 'tag'=>'fleet_tag_cat777'],
        ['key'=>'hino500','img'=>'https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=600&q=80&fit=crop', 'tag'=>'fleet_tag_hino500'],
        ['key'=>'d155',   'img'=>'https://images.unsplash.com/photo-1545558014-8692077e9b5c?w=600&q=80&fit=crop', 'tag'=>'fleet_tag_d155'],
        ['key'=>'pc400',  'img'=>'https://images.unsplash.com/photo-1485083269755-a7b559a4fe5e?w=600&q=80&fit=crop', 'tag'=>'fleet_tag_pc400'],
        ['key'=>'grader', 'img'=>'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=600&q=80&fit=crop', 'tag'=>'fleet_tag_grader'],
      ];
    @endphp

    <div class="fleet-grid" style="margin-top:48px;">
      @foreach($fleets as $i => $f)
      <div class="fleet-card" data-aos="zoom-in" data-aos-delay="{{ $i * 80 }}">
        <img class="fleet-img"
             src="{{ $f['img'] }}"
             alt="{{ __('company.fleet_'.$f['key']) }}"
             onerror="this.style.display='none';this.parentElement.style.background='linear-gradient(135deg,#1a1a1a,#2a0808)'">
        <div class="fleet-overlay">
          <div>
            <div class="fleet-name">{{ __('company.fleet_'.$f['key']) }}</div>
            <div class="fleet-sub">{{ __('company.fleet_'.$f['key'].'_sub') }}</div>
            <span class="fleet-tag">{{ __('company.'.$f['tag']) }}</span>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ─── Performance / Charts ─────────────────────────────────────────── --}}
<section class="section section-alt" id="performance">
  <div class="container">
    <div class="perf-header" data-aos="fade-up">
      <div>
        <div class="label-chip"><i class="bi bi-bar-chart-line"></i> {{ __('company.perf_label') }}</div>
        <h2 class="section-title" style="margin-bottom:8px;">{{ __('company.perf_title') }}</h2>
        <p class="section-desc">{{ __('company.perf_desc') }}</p>
      </div>
    </div>

    {{-- KPI Row --}}
    <div class="kpi-row">
      @php $kpis = [
        ['val'=>'93.4%','label'=>'kpi1_label','icon'=>'bi-speedometer2'],
        ['val'=>'98.2%','label'=>'kpi2_label','icon'=>'bi-check-circle'],
        ['val'=>'0','label'=>'kpi3_label','icon'=>'bi-shield-fill-check'],
        ['val'=>'4.8/5','label'=>'kpi4_label','icon'=>'bi-star-fill'],
      ]; @endphp
      @foreach($kpis as $i => $k)
      <div class="kpi-card" data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
        <div style="font-size:1.6rem;color:var(--accent);margin-bottom:8px;"><i class="bi {{ $k['icon'] }}"></i></div>
        <div class="kpi-value">{{ $k['val'] }}</div>
        <div class="kpi-label">{{ __('company.'.$k['label']) }}</div>
      </div>
      @endforeach
    </div>

    {{-- Charts --}}
    <div class="charts-grid">
      <div class="chart-card" data-aos="fade-right">
        <div class="chart-title">{{ __('company.chart_tonnage') }}</div>
        <div class="chart-sub">{{ __('company.chart_monthly') }} - {{ __('company.chart_year') }}</div>
        <div class="chart-wrap chart-tall">
          <canvas id="chartTonnage"></canvas>
        </div>
      </div>

      <div class="chart-card" data-aos="fade-left">
        <div class="chart-title">{{ __('company.chart_avail') }}</div>
        <div class="chart-sub">{{ __('company.chart_monthly') }} - {{ __('company.chart_year') }}</div>
        <div class="chart-wrap chart-tall">
          <canvas id="chartAvail"></canvas>
        </div>
      </div>

      <div class="chart-card chart-card-full" data-aos="fade-up">
        <div class="chart-title">{{ __('company.chart_distance') }}</div>
        <div class="chart-sub">{{ __('company.chart_monthly') }} - {{ __('company.chart_year') }}</div>
        <div class="chart-wrap chart-wide">
          <canvas id="chartDistance"></canvas>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ─── Why Us ───────────────────────────────────────────────────────── --}}
<section class="section" id="why">
  <div class="container">
    <div style="text-align:center;" data-aos="fade-up">
      <div class="label-chip" style="margin:0 auto 16px;"><i class="bi bi-stars"></i> {{ __('company.why_label') }}</div>
      <h2 class="section-title">{{ __('company.why_title') }}</h2>
    </div>
    <div class="why-grid">
      @php
        $whys = [
          ['icon'=>'bi-clock-history',     'n'=>1,'delay'=>0],
          ['icon'=>'bi-truck',             'n'=>2,'delay'=>100],
          ['icon'=>'bi-shield-exclamation','n'=>3,'delay'=>200],
          ['icon'=>'bi-cpu',               'n'=>4,'delay'=>0],
          ['icon'=>'bi-people',            'n'=>5,'delay'=>100],
          ['icon'=>'bi-lightning-charge',  'n'=>6,'delay'=>200],
        ];
      @endphp
      @foreach($whys as $w)
      <div class="why-card" data-aos="fade-up" data-aos-delay="{{ $w['delay'] }}">
        <div class="why-icon"><i class="bi {{ $w['icon'] }}"></i></div>
        <div>
          <h3>{{ __('company.why'.$w['n'].'_title') }}</h3>
          <p>{{ __('company.why'.$w['n'].'_desc') }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ─── Contact ──────────────────────────────────────────────────────── --}}
<section class="section section-alt" id="contact">
  <div class="container">
    <div data-aos="fade-up" style="margin-bottom:48px;">
      <div class="label-chip"><i class="bi bi-envelope"></i> {{ __('company.contact_label') }}</div>
      <h2 class="section-title">{{ __('company.contact_title') }}</h2>
      <p class="section-desc">{{ __('company.contact_desc') }}</p>
    </div>

    <div class="contact-grid">
      {{-- Info --}}
      <div data-aos="fade-right">
        <div class="contact-info">
          <div class="contact-item">
            <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <div>
              <h4>{{ __('company.contact_address') }}</h4>
              <p>{!! __('company.contact_address_val') !!}</p>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
            <div>
              <h4>{{ __('company.contact_phone') }}</h4>
              <p>{!! __('company.contact_phone_val') !!}</p>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
            <div>
              <h4>{{ __('company.contact_email') }}</h4>
              <p>{!! __('company.contact_email_val') !!}</p>
            </div>
          </div>
          <div class="contact-item">
            <div class="contact-icon"><i class="bi bi-clock-fill"></i></div>
            <div>
              <h4>{{ __('company.contact_hours') }}</h4>
              <p>{{ __('company.contact_hours_val') }}</p>
            </div>
          </div>
        </div>
        <a class="contact-map" href="https://www.google.com/maps/place/PT+ANUGERAH+SARANA+HIKMAH/data=!4m2!3m1!1s0x0:0x77a9d51651fca5ee?sa=X&ved=1t:2428&ictx=111" target="_blank" rel="noopener noreferrer">
          <i class="bi bi-map"></i>
          <span>{{ __('company.contact_map_label') }} - {{ __('company.contact_map_open') }}</span>
        </a>
      </div>

      {{-- Form --}}
      <div class="contact-form" data-aos="fade-left">
        <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:24px;">
          <i class="bi bi-send" style="color:var(--accent);margin-right:8px;"></i>
          {{ __('company.form_send') }}
        </h3>
        <form onsubmit="handleForm(event)">
          <div class="form-row">
            <div class="form-group">
              <label>{{ __('company.form_name') }}</label>
              <input type="text" class="form-control" placeholder="{{ __('company.form_placeholder_name') }}" required>
            </div>
            <div class="form-group">
              <label>{{ __('company.form_company') }}</label>
              <input type="text" class="form-control" placeholder="{{ __('company.form_placeholder_company') }}">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label>{{ __('company.form_email') }}</label>
              <input type="email" class="form-control" placeholder="{{ __('company.form_placeholder_email') }}" required>
            </div>
            <div class="form-group">
              <label>{{ __('company.form_phone') }}</label>
              <input type="tel" class="form-control" placeholder="{{ __('company.form_placeholder_phone') }}">
            </div>
          </div>
          <div class="form-group">
            <label>{{ __('company.form_message') }}</label>
            <textarea class="form-control" placeholder="{{ __('company.form_placeholder_msg') }}" required></textarea>
          </div>
          <button type="submit" class="btn-submit">
            <i class="bi bi-send-fill"></i> {{ __('company.form_send') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

{{-- ─── Footer ───────────────────────────────────────────────────────── --}}
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <a href="{{ route('company.profile') }}" class="nav-logo" style="margin-bottom:16px;display:inline-flex;">
          <img src="{{ asset('assets/images/logo.png') }}" alt="ASH" style="height:40px;">
          <div class="nav-logo-text" style="margin-left:10px;">
            <strong style="color:#dc2626;">{{ __('company.company_name') }}</strong>
            <span style="color:#555;">{{ __('company.company_subtitle') }}</span>
          </div>
        </a>
        <p>{{ __('company.footer_about') }}</p>
        <div class="footer-socials">
          <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
          <a href="#" class="social-btn"><i class="bi bi-linkedin"></i></a>
          <a href="#" class="social-btn"><i class="bi bi-facebook"></i></a>
          <a href="#" class="social-btn"><i class="bi bi-youtube"></i></a>
        </div>
      </div>

      <div class="footer-col">
        <h4>{{ __('company.footer_links') }}</h4>
        <ul>
          <li><a href="#about">{{ __('company.nav_about') }}</a></li>
          <li><a href="#services">{{ __('company.nav_services') }}</a></li>
          <li><a href="#fleet">{{ __('company.nav_fleet') }}</a></li>
          <li><a href="#performance">{{ __('company.nav_performance') }}</a></li>
          <li><a href="#contact">{{ __('company.nav_contact') }}</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>{{ __('company.footer_services') }}</h4>
        <ul>
          <li><a href="#">{{ __('company.svc1_title') }}</a></li>
          <li><a href="#">{{ __('company.svc2_title') }}</a></li>
          <li><a href="#">{{ __('company.svc3_title') }}</a></li>
          <li><a href="#">{{ __('company.svc4_title') }}</a></li>
          <li><a href="#">{{ __('company.svc5_title') }}</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>{{ __('company.footer_contact') }}</h4>
        <ul>
          <li><a href="tel:+62541000000"><i class="bi bi-telephone me-1"></i>+62 541 xxxxxx</a></li>
          <li><a href="mailto:info@ash-hauling.co.id"><i class="bi bi-envelope me-1"></i> info@ash-hauling.co.id</a></li>
          <li><a href="https://www.google.com/maps/place/PT+ANUGERAH+SARANA+HIKMAH/data=!4m2!3m1!1s0x0:0x77a9d51651fca5ee?sa=X&ved=1t:2428&ictx=111" target="_blank" rel="noopener noreferrer"><i class="bi bi-geo-alt me-1"></i> {{ __('company.footer_location') }}</a></li>
          <li style="margin-top:16px;">
            <a href="{{ route('login') }}" style="display:inline-flex;align-items:center;gap:8px;background:var(--accent);color:#fff;padding:9px 16px;border-radius:8px;font-size:.82rem;font-weight:600;">
              <i class="bi bi-grid-3x3-gap-fill"></i> {{ __('company.footer_erp') }}
            </a>
          </li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>{{ str_replace(':year', date('Y'), __('company.footer_copy')) }}</p>
      <p>{{ __('company.footer_dev') }} <a href="#">{{ __('company.footer_dev_company') }}</a></p>
    </div>
  </div>
</footer>

{{-- Back to Top --}}
<button class="back-top" id="backTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="bi bi-arrow-up"></i>
</button>

{{-- Video Modal --}}
<div class="modal-overlay" id="videoModal" onclick="closeVideoModal(event)">
  <div class="modal-box">
    <button class="modal-close" onclick="closeVideoModal()">
      <i class="bi bi-x-lg"></i>
    </button>
    <iframe id="videoFrame"
      src=""
      width="100%" height="100%"
      frameborder="0"
      allow="autoplay; fullscreen"
      allowfullscreen
      style="border:none;"></iframe>
  </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
{{-- AOS --}}
<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>
/* ── Data from PHP ── */
const months   = @json($chartMonths);
const tonnage  = @json($chartTonnage);
const avail    = @json($chartAvailability);
const distance = @json($chartDistance);

/* ── Init AOS ── */
AOS.init({ duration: 700, once: true, offset: 60 });

/* ── Theme ── */
const html = document.documentElement;
const themeBtn  = document.getElementById('themeBtn');
const themeIcon = document.getElementById('themeIcon');
const themeBtnMobile = document.getElementById('themeBtnMobile');
const themeIconMobile = document.getElementById('themeIconMobile');
let tonChart = null;
let availChart = null;
let distChart = null;

function applyTheme(t) {
  html.setAttribute('data-theme', t);
  localStorage.setItem('ash-theme', t);
  const iconClass = t === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
  themeIcon.className = iconClass;
  if (themeIconMobile) {
    themeIconMobile.className = iconClass;
  }
  if (tonChart && availChart && distChart) {
    updateChartTheme();
  }
}
themeBtn.addEventListener('click', () => {
  applyTheme(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
});
if (themeBtnMobile) {
  themeBtnMobile.addEventListener('click', () => {
    applyTheme(html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
  });
}
applyTheme(localStorage.getItem('ash-theme') || 'dark');

/* ── Navbar scroll ── */
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
  navbar.classList.toggle('scrolled', window.scrollY > 40);
  document.getElementById('backTop').classList.toggle('visible', window.scrollY > 400);

  // Active nav link
  const sections = ['home','about','services','fleet','performance','contact'];
  let current = '';
  sections.forEach(id => {
    const el = document.getElementById(id);
    if (el && window.scrollY >= el.offsetTop - 100) current = id;
  });
  document.querySelectorAll('.nav-link').forEach(l => {
    const href = l.getAttribute('href');
    l.classList.toggle('active', href && href.includes(current) && current !== '');
  });
});

/* ── Hamburger ── */
document.getElementById('hamburger').addEventListener('click', function() {
  const menu = document.getElementById('mobileMenu');
  menu.classList.toggle('open');
  const spans = this.querySelectorAll('span');
  if (menu.classList.contains('open')) {
    spans[0].style.transform = 'rotate(45deg) translate(5px,5px)';
    spans[1].style.opacity = '0';
    spans[2].style.transform = 'rotate(-45deg) translate(5px,-5px)';
  } else {
    spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
  }
});
document.querySelectorAll('.mobile-menu .nav-link').forEach(l => {
  l.addEventListener('click', () => {
    document.getElementById('mobileMenu').classList.remove('open');
  });
});

/* ── Hero Particles ── */
const container = document.getElementById('heroParticles');
for (let i = 0; i < 25; i++) {
  const s = document.createElement('span');
  s.style.cssText = `
    left:${Math.random()*100}%;
    width:${1+Math.random()*2}px;height:${1+Math.random()*2}px;
    animation-duration:${6+Math.random()*12}s;
    animation-delay:-${Math.random()*10}s;
  `;
  container.appendChild(s);
}

/* ── Counter Animation ── */
function animateCounters() {
  document.querySelectorAll('[data-count],[data-count-text]').forEach(el => {
    if (el.dataset.animated) return;
    const rect = el.getBoundingClientRect();
    if (rect.top < window.innerHeight - 50) {
      el.dataset.animated = '1';
      if (el.dataset.countText) { el.textContent = el.dataset.countText; return; }
      const target = +el.dataset.count;
      let current = 0;
      const step = target / 60;
      const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = Math.floor(current) + (target >= 10 ? '+' : '');
        if (current >= target) clearInterval(timer);
      }, 25);
    }
  });
}
window.addEventListener('scroll', animateCounters);
animateCounters();

/* ── Vision / Mission Tabs ── */
function switchTab(btn, panel) {
  document.querySelectorAll('.vm-tab').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.vm-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('panel-' + panel).classList.add('active');
}

/* ── Video Modal ── */
const VIDEO_URL = 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1';
function openVideoModal() {
  document.getElementById('videoFrame').src = VIDEO_URL;
  document.getElementById('videoModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeVideoModal(e) {
  if (e && e.target !== document.getElementById('videoModal') && !e.target.closest('.modal-close')) return;
  document.getElementById('videoFrame').src = '';
  document.getElementById('videoModal').classList.remove('open');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeVideoModal({target: document.getElementById('videoModal')}); });

/* ── Contact Form ── */
function handleForm(e) {
  e.preventDefault();
  const btn = e.target.querySelector('.btn-submit');
  btn.innerHTML = '<i class="bi bi-check-circle-fill"></i> {{ __("company.form_sent") }}';
  btn.style.background = '#16a34a';
  setTimeout(() => {
    btn.innerHTML = '<i class="bi bi-send-fill"></i> {{ __("company.form_send") }}';
    btn.style.background = '';
    e.target.reset();
  }, 3000);
}

/* ── Charts ── */
const RED        = '#dc2626';
const RED_LIGHT  = '#ef4444';
const RED_ALPHA  = 'rgba(220,38,38,.15)';
const RED_ALPHA2 = 'rgba(220,38,38,.05)';

function getGridColor() {
  return html.getAttribute('data-theme') === 'dark'
    ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';
}
function getTextColor() {
  return html.getAttribute('data-theme') === 'dark' ? '#888' : '#666';
}

Chart.defaults.font.family = 'Inter, sans-serif';

tonChart = new Chart(document.getElementById('chartTonnage'), {
  type: 'bar',
  data: {
    labels: months,
    datasets: [{
      label: '{{ __("company.chart_tonnage") }}',
      data: tonnage,
      backgroundColor: RED_ALPHA,
      borderColor: RED,
      borderWidth: 2,
      borderRadius: 6,
      borderSkipped: false,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: getGridColor() }, ticks: { color: getTextColor(), font: { size: 11 } } },
      y: {
        grid: { color: getGridColor() }, ticks: { color: getTextColor(), font: { size: 11 },
          callback: v => (v/1000000).toFixed(1)+'M'
        }
      }
    }
  }
});

availChart = new Chart(document.getElementById('chartAvail'), {
  type: 'line',
  data: {
    labels: months,
    datasets: [{
      label: '{{ __("company.chart_avail") }}',
      data: avail,
      borderColor: RED,
      backgroundColor: RED_ALPHA,
      fill: true,
      tension: 0.4,
      pointBackgroundColor: RED,
      pointRadius: 5,
      pointHoverRadius: 7,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: getGridColor() }, ticks: { color: getTextColor(), font: { size: 11 } } },
      y: {
        min: 80, max: 100,
        grid: { color: getGridColor() }, ticks: { color: getTextColor(), font: { size: 11 },
          callback: v => v+'%'
        }
      }
    }
  }
});

distChart = new Chart(document.getElementById('chartDistance'), {
  type: 'line',
  data: {
    labels: months,
    datasets: [{
      label: '{{ __("company.chart_distance") }}',
      data: distance,
      borderColor: RED_LIGHT,
      backgroundColor: RED_ALPHA2,
      fill: true,
      tension: 0.4,
      pointBackgroundColor: RED_LIGHT,
      pointRadius: 4,
      pointHoverRadius: 7,
      borderWidth: 2.5,
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      x: { grid: { color: getGridColor() }, ticks: { color: getTextColor(), font: { size: 11 } } },
      y: {
        grid: { color: getGridColor() }, ticks: { color: getTextColor(), font: { size: 11 },
          callback: v => (v/1000).toFixed(0)+'K km'
        }
      }
    }
  }
});

function updateChartTheme() {
  const gc = getGridColor(), tc = getTextColor();
  [tonChart, availChart, distChart].forEach(chart => {
    chart.options.scales.x.grid.color = gc;
    chart.options.scales.y.grid.color = gc;
    chart.options.scales.x.ticks.color = tc;
    chart.options.scales.y.ticks.color = tc;
    chart.update();
  });
}

updateChartTheme();

/* ── Smooth anchor scroll (offset for navbar) ── */
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', e => {
    const target = document.querySelector(a.getAttribute('href'));
    if (!target) return;
    e.preventDefault();
    window.scrollTo({ top: target.offsetTop - 72, behavior: 'smooth' });
  });
});
</script>
</body>
</html>
