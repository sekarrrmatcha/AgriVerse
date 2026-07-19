<style>
  :root{
    --c-soil:#20261d; --c-moss:#37472f; --c-moss-light:#46583a; --c-canopy:#7d9c5e;
    --c-husk:#f2ede0; --c-paper:#fbf8f0; --c-paper-dim:#e9e1c9;
    --c-marigold:#dd9c33; --c-marigold-dark:#b87d20; --c-clay:#9c5f3c; --c-steel:#4c6b76;
    --c-ink:#20261d; --c-ink-soft:#5c5a4a; --c-border:#d9cfae;
    --radius-sm:6px; --radius-md:12px; --radius-lg:20px;
    --font-display:'Fraunces',serif; --font-body:'Inter',sans-serif; --font-mono:'IBM Plex Mono',monospace;
  }
  *{box-sizing:border-box;}
  html,body{margin:0;padding:0;}
  body{font-family:var(--font-body);background:var(--c-husk);color:var(--c-ink);-webkit-font-smoothing:antialiased;}
  a{color:inherit;text-decoration:none;}
  button{font-family:inherit;cursor:pointer;}

  .app{display:flex;min-height:100vh;}
  .sidebar{width:280px;flex-shrink:0;background:var(--c-soil);color:#e7e3d3;display:flex;flex-direction:column;padding:22px 16px 20px;position:sticky;top:0;height:100vh;overflow-y:auto;}
  .brand{display:flex;align-items:center;gap:10px;padding:4px 8px 20px;border-bottom:1px solid rgba(255,255,255,0.09);margin-bottom:16px;}
  .brand__mark{width:34px;height:34px;border-radius:9px;background:linear-gradient(155deg,var(--c-canopy),var(--c-moss-light));display:flex;align-items:center;justify-content:center;flex-shrink:0;}
  .brand__mark svg{width:19px;height:19px;color:#f2ede0;}
  .brand__name{font-family:var(--font-display);font-weight:700;font-size:1.18rem;}
  .brand__tag{font-family:var(--font-mono);font-size:.62rem;letter-spacing:.09em;text-transform:uppercase;color:#a9a690;}
  .nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:var(--radius-sm);font-size:.92rem;font-weight:500;color:#e7e3d3;background:transparent;border:none;width:100%;text-align:left;margin-bottom:4px;}
  .nav-item svg{width:17px;height:17px;flex-shrink:0;}
  .nav-item.active,.nav-item:hover{background:rgba(255,255,255,0.08);}
  .nav-section-label{font-family:var(--font-mono);font-size:.64rem;letter-spacing:.12em;text-transform:uppercase;color:#8c8a75;padding:10px 12px 8px;}
  .prodi-block{margin-bottom:10px;padding:10px 12px;border-radius:var(--radius-sm);background:rgba(255,255,255,0.04);}
  .prodi-block__head{display:flex;align-items:center;gap:8px;margin-bottom:6px;}
  .prodi-block__dot{width:8px;height:8px;border-radius:50%;background:var(--dot);flex-shrink:0;}
  .prodi-block__kode{font-family:var(--font-mono);font-size:.68rem;letter-spacing:.06em;color:#cfccb8;}
  .prodi-block__links{display:flex;flex-direction:column;gap:2px;padding-left:16px;}
  .prodi-block__links a{font-size:.82rem;color:#c7c4b0;padding:5px 6px;border-radius:6px;}
  .prodi-block__links a:hover,.prodi-block__links a.active{background:rgba(255,255,255,0.08);color:#fff;}
  .prodi-block__nama{font-size:.82rem;color:#c7c4b0;padding-left:16px;}
  .sidebar__foot{margin-top:auto;padding:14px 10px 4px;border-top:1px solid rgba(255,255,255,0.09);font-family:var(--font-mono);font-size:.66rem;color:#8c8a75;}
  .sidebar__user{display:flex;align-items:center;gap:10px;padding:10px;border-radius:var(--radius-sm);background:rgba(255,255,255,0.05);margin-bottom:10px;}
  .sidebar__user-avatar{width:32px;height:32px;border-radius:50%;background:var(--c-marigold);color:#3a2a0d;display:flex;align-items:center;justify-content:center;font-family:var(--font-mono);font-weight:700;font-size:.8rem;flex-shrink:0;}
  .sidebar__user-name{font-size:.82rem;font-weight:600;line-height:1.2;}
  .sidebar__user-nim{font-family:var(--font-mono);font-size:.65rem;color:#a9a690;}
.sidebar__logout {
    width: 100%;
    background: transparent;
    border: 1px solid rgba(255, 255, 255, .15);
    color: #e7e3d3;
    padding: 10px 14px;
    border-radius: var(--radius-sm);
    font-size: .9rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: .2s ease;
}

.sidebar__logout:hover {
    background: rgba(255,255,255,.08);
}

.sidebar__logout svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

/* Tablet */
@media (max-width: 768px) {
    .sidebar__logout {
        padding: 9px 12px;
        font-size: .85rem;
        gap: 6px;
    }

    .sidebar__logout svg {
        width: 16px;
        height: 16px;
    }
}

/* HP */
@media (max-width: 480px) {
    .sidebar__logout {
        padding: 8px 10px;
        font-size: .8rem;
        border-radius: 8px;
    }

    .sidebar__logout svg {
        width: 15px;
        height: 15px;
    }
}

  .main{flex:1;min-width:0;display:flex;flex-direction:column;}
  .topbar{display:flex;align-items:center;justify-content:space-between;padding:16px 32px;border-bottom:1px solid var(--c-border);background:var(--c-paper);position:sticky;top:0;z-index:5;}
  .breadcrumb{font-family:var(--font-mono);font-size:.75rem;color:var(--c-ink-soft);}
  .breadcrumb b{color:var(--c-ink);font-weight:600;}
  .topbar__progress{display:flex;align-items:center;gap:10px;}
  .topbar__progress-label{font-family:var(--font-mono);font-size:.7rem;color:var(--c-ink-soft);text-transform:uppercase;letter-spacing:.08em;}
  .pct-pill{font-family:var(--font-mono);font-weight:700;font-size:.78rem;background:var(--c-paper-dim);color:var(--c-ink);padding:6px 12px;border-radius:999px;}

  .view{padding:32px 40px 64px;max-width:1180px;}

  .hero{position:relative;overflow:hidden;border-radius:var(--radius-lg);background:var(--c-moss);color:#f2ede0;padding:44px 40px;margin-bottom:34px;}
  .hero::before{content:"";position:absolute;inset:0;opacity:.16;background:repeating-linear-gradient(100deg,transparent 0 26px,rgba(255,255,255,.5) 26px 27px);pointer-events:none;}
  .hero__eyebrow{font-family:var(--font-mono);font-size:.72rem;letter-spacing:.14em;text-transform:uppercase;color:#c7dcae;position:relative;}
  .hero__title{font-family:var(--font-display);font-weight:700;font-size:clamp(1.8rem,3.2vw,2.7rem);margin:10px 0 12px;max-width:640px;position:relative;line-height:1.1;}
  .hero__desc{font-size:.96rem;color:#d9e3c9;max-width:560px;line-height:1.6;position:relative;}

  .stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:34px;}
  .stat-card{background:var(--c-paper);border:1px solid var(--c-border);border-radius:var(--radius-md);padding:16px 18px;}
  .stat-card__num{font-family:var(--font-mono);font-size:1.5rem;font-weight:600;}
  .stat-card__label{font-size:.76rem;color:var(--c-ink-soft);margin-top:2px;}

  .section-heading{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:16px;}
  .section-heading h2{font-family:var(--font-display);font-size:1.4rem;font-weight:600;margin:0;}
  .section-heading__note{font-family:var(--font-mono);font-size:.7rem;color:var(--c-ink-soft);text-transform:uppercase;}

  .dashboard-grid{display:grid;grid-template-columns:1.2fr .8fr;gap:18px;margin-bottom:24px;}
  .dashboard-panel{background:var(--c-paper);border:1px solid var(--c-border);border-radius:var(--radius-lg);padding:22px 24px;}
  .summary-stack{display:grid;gap:10px;margin-bottom:16px;}
  .summary-card{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-radius:var(--radius-sm);background:var(--c-husk);border:1px solid var(--c-border);}
  .summary-card--accent{background:linear-gradient(90deg, rgba(125,156,94,.16), rgba(125,156,94,.04));}
  .summary-card__label{font-family:var(--font-mono);font-size:.68rem;text-transform:uppercase;letter-spacing:.06em;color:var(--c-ink-soft);}
  .summary-card__value{font-weight:600;font-size:.95rem;}
  .progress-shell{height:10px;border-radius:999px;background:var(--c-paper-dim);overflow:hidden;margin-bottom:8px;}
  .progress-shell__bar{height:100%;border-radius:inherit;background:linear-gradient(90deg,var(--c-canopy),var(--c-marigold));}
  .summary-caption{font-size:.8rem;color:var(--c-ink-soft);margin:0;}
  .next-item{display:block;padding:12px 14px;border:1px solid var(--c-border);border-radius:var(--radius-sm);background:var(--c-husk);margin-bottom:10px;transition:border-color .15s ease, transform .15s ease;}
  .next-item:hover{border-color:var(--c-canopy);transform:translateY(-1px);}
  .next-item__pill{display:inline-block;font-family:var(--font-mono);font-size:.62rem;text-transform:uppercase;letter-spacing:.08em;border-radius:999px;padding:3px 8px;background:var(--c-paper-dim);color:var(--c-ink-soft);margin-bottom:6px;}
  .next-item strong{display:block;font-size:.95rem;margin-bottom:3px;}
  .next-item span{font-size:.8rem;color:var(--c-ink-soft);}
  .activity-list{display:grid;gap:10px;}
  .activity-item{padding:12px 14px;border:1px solid var(--c-border);border-radius:var(--radius-sm);background:var(--c-husk);}
  .activity-item__pill{display:inline-block;font-family:var(--font-mono);font-size:.62rem;text-transform:uppercase;letter-spacing:.08em;border-radius:999px;padding:3px 8px;background:var(--c-paper-dim);color:var(--c-ink-soft);margin-bottom:6px;}
  .activity-item strong{display:block;font-size:.95rem;}
  .plot-card__bar{position:absolute;left:0;top:0;bottom:0;width:5px;background:var(--accent);}
  .plot-card__label{font-family:var(--font-mono);font-size:.66rem;letter-spacing:.1em;text-transform:uppercase;color:var(--accent);font-weight:600;}
  .plot-card__nama{font-family:var(--font-display);font-size:1.15rem;font-weight:600;margin:8px 0;}
  .plot-card__desc{font-size:.82rem;color:var(--c-ink-soft);line-height:1.5;margin-bottom:14px;min-height:52px;}
  .plot-card__links{display:flex;gap:8px;}
  .plot-card__links a{flex:1;text-align:center;font-size:.78rem;font-weight:600;padding:9px 8px;border-radius:8px;border:1px solid var(--c-border);color:var(--accent);}
  .plot-card__links a:hover{background:var(--accent);color:#fff;}

  .list-header{display:flex;align-items:flex-end;justify-content:space-between;gap:20px;margin-bottom:22px;flex-wrap:wrap;}
  .list-header__title{font-family:var(--font-mono);font-size:.7rem;letter-spacing:.1em;text-transform:uppercase;color:var(--c-ink-soft);margin-bottom:4px;}
  .list-header h1{font-family:var(--font-display);font-size:1.8rem;font-weight:600;margin:0;}
  .list-header__desc{font-size:.86rem;color:var(--c-ink-soft);margin-top:6px;max-width:560px;}

  .back-link{display:inline-flex;align-items:center;gap:6px;font-family:var(--font-mono);font-size:.72rem;text-transform:uppercase;color:var(--c-ink-soft);margin-bottom:18px;}
  .back-link:hover{color:var(--c-ink);}

  .item-card{--accent:#7d9c5e;display:flex;align-items:center;gap:16px;background:var(--c-paper);border:1px solid var(--c-border);border-radius:var(--radius-md);padding:16px 18px;margin-bottom:10px;transition:border-color .15s ease;}
  .item-card:hover{border-color:var(--accent);}
  .item-card__num{font-family:var(--font-mono);font-size:.78rem;font-weight:600;color:var(--accent);background:var(--c-paper-dim);border-radius:8px;padding:8px 10px;flex-shrink:0;min-width:64px;text-align:center;}
  .item-card__body{flex:1;min-width:0;}
  .item-card__title{font-family:var(--font-display);font-weight:600;font-size:1rem;margin:0 0 3px;}
  .item-card__meta{display:flex;align-items:center;gap:12px;font-size:.78rem;color:var(--c-ink-soft);flex-wrap:wrap;}

  .item-card--kosong{opacity:0.55;cursor:not-allowed;pointer-events:none;}
  .item-card__meta--muted{font-style:italic;color:var(--c-ink-soft, #9a9a9a);}

  .badge{font-family:var(--font-mono);font-size:.65rem;text-transform:uppercase;padding:3px 8px;border-radius:6px;font-weight:600;}
  .badge--dasar{background:#e4ecdc;color:#4a6a2f;}
  .badge--menengah{background:#f4e3c6;color:#93641c;}
  .badge--lanjut{background:#f1dad0;color:#94402a;}
  .item-card__check{width:26px;height:26px;border-radius:50%;flex-shrink:0;display:flex;align-items:center;justify-content:center;border:1.5px solid var(--c-border);color:transparent;font-size:.7rem;}
  .item-card__check.done{background:var(--c-canopy);border-color:var(--c-canopy);color:#fff;}

  .detail-card{background:var(--c-paper);border:1px solid var(--c-border);border-radius:var(--radius-lg);padding:30px 32px;--accent:#7d9c5e;}
  .detail-card__eyebrow{font-family:var(--font-mono);font-size:.72rem;text-transform:uppercase;color:var(--accent);font-weight:600;}
  .detail-card__title{font-family:var(--font-display);font-size:1.6rem;font-weight:700;margin:8px 0 18px;}
  .detail-block{margin-bottom:24px;}
  .detail-block h4{font-family:var(--font-mono);font-size:.72rem;text-transform:uppercase;color:var(--c-ink-soft);margin:0 0 10px;}
  .detail-block p{font-size:.92rem;line-height:1.65;margin:0;}
  .tag-list{display:flex;flex-wrap:wrap;gap:8px;margin:0;padding:0;}
  .tag-list li{list-style:none;font-size:.83rem;background:var(--c-husk);border:1px solid var(--c-border);padding:6px 12px;border-radius:8px;}
  .outcome-list{margin:0;padding-left:0;list-style:none;}
  .outcome-list li{display:flex;gap:10px;font-size:.92rem;line-height:1.55;margin-bottom:9px;}
  .outcome-list li::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--accent);margin-top:7px;flex-shrink:0;}

  .step-list{margin:0;padding:0;list-style:none;}
  .step-item{display:flex;align-items:flex-start;gap:12px;padding:12px 0;border-bottom:1px solid var(--c-paper-dim);}
  .step-item:last-child{border-bottom:none;}
  .step-item__box{width:22px;height:22px;border-radius:6px;border:1.5px solid var(--c-border);flex-shrink:0;display:flex;align-items:center;justify-content:center;color:transparent;margin-top:1px;background:none;font-size:.7rem;}
  .step-item__box.checked,.step-item__box:has(input:checked){background:var(--accent);border-color:var(--accent);color:#fff;}
  .step-item__text{font-size:.92rem;line-height:1.55;}
  .step-item__text.done{color:var(--c-ink-soft);text-decoration:line-through;text-decoration-color:var(--c-border);}
  .step-item:has(input:checked) .step-item__text{color:var(--c-ink-soft);text-decoration:line-through;text-decoration-color:var(--c-border);}

  .toggle-btn{display:inline-flex;align-items:center;gap:8px;background:var(--c-husk);border:1px solid var(--c-border);color:var(--c-ink);padding:10px 16px;border-radius:999px;font-size:.83rem;font-weight:600;}
  .toggle-btn.done{background:var(--c-canopy);border-color:var(--c-canopy);color:#fff;}

  .quiz-box{background:var(--c-husk);border:1px solid var(--c-border);border-radius:var(--radius-md);padding:20px 22px;margin-top:6px;}
  .quiz-box__q{font-size:.94rem;font-weight:600;margin-bottom:14px;line-height:1.5;}
  .quiz-answer{font-size:.85rem;line-height:1.55;padding:12px 14px;border-radius:8px;background:#e4ecdc;color:#3f5c26;margin-top:4px;}
  .quiz-opt-static{display:block;width:100%;background:var(--c-paper);border:1px solid var(--c-border);border-radius:var(--radius-sm);padding:11px 14px;margin-bottom:8px;font-size:.87rem;}
  .quiz-opt-static.correct{border-color:#4a6a2f;background:#e4ecdc;font-weight:600;}

  .auth-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--c-husk);padding:24px;}
  .auth-card{width:100%;max-width:420px;background:var(--c-paper);border:1px solid var(--c-border);border-radius:var(--radius-lg);padding:36px 34px;}
  .auth-card__brand{display:flex;align-items:center;gap:10px;margin-bottom:22px;}
  .auth-card h1{font-family:var(--font-display);font-size:1.5rem;margin:0 0 6px;}
  .auth-card p.sub{font-size:.86rem;color:var(--c-ink-soft);margin:0 0 24px;}
  .field{margin-bottom:16px;}
  .field label{display:block;font-family:var(--font-mono);font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;color:var(--c-ink-soft);margin-bottom:6px;}
  .field input,.field select{width:100%;padding:11px 13px;border-radius:var(--radius-sm);border:1px solid var(--c-border);background:var(--c-husk);font-family:var(--font-body);font-size:.92rem;color:var(--c-ink);}
  .field input:focus,.field select:focus{outline:2px solid var(--c-canopy);outline-offset:1px;}
  .field-error{color:#94402a;font-size:.78rem;margin-top:5px;}
  .field-check{display:flex;align-items:center;gap:8px;font-size:.85rem;color:var(--c-ink-soft);margin-bottom:18px;}
  .btn-primary{width:100%;background:var(--c-moss);color:#f2ede0;border:none;padding:12px 16px;border-radius:999px;font-size:.9rem;font-weight:600;}
  .btn-primary:hover{background:var(--c-moss-light);}
  .auth-alt{text-align:center;font-size:.84rem;color:var(--c-ink-soft);margin-top:18px;}
  .auth-alt a{color:var(--c-marigold-dark);font-weight:600;}
  .alert-error{background:#f1dad0;color:#7d3a25;border-radius:8px;padding:12px 14px;font-size:.84rem;margin-bottom:18px;}
  .alert-success{background:#e4ecdc;color:#3f5c26;border-radius:8px;padding:12px 14px;font-size:.84rem;margin-bottom:18px;}

  .site-nav{display:flex;align-items:center;justify-content:space-between;padding:20px 40px;max-width:1180px;margin:0 auto;}
  .site-nav__links{display:flex;align-items:center;gap:10px;}
  .btn-ghost{padding:9px 16px;border-radius:999px;font-size:.85rem;font-weight:600;color:var(--c-ink);}
  .btn-ghost:hover{background:var(--c-paper-dim);}
  .btn-solid{padding:9px 18px;border-radius:999px;font-size:.85rem;font-weight:600;background:var(--c-moss);color:#f2ede0;}
  .btn-solid:hover{background:var(--c-moss-light);}

  @media (max-width:920px){
    .app{flex-direction:column;}
    .sidebar{width:100%;height:auto;position:relative;}
    .plot-grid{grid-template-columns:1fr;}
    .stats-row{grid-template-columns:repeat(2,1fr);}
    .view{padding:22px 18px 48px;}
    .site-nav{padding:18px 20px;}
  }
</style>
