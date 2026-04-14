*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
    --bg:#060612;--surface:#0d0d1f;--card:#11112a;
    --border:rgba(255,255,255,0.07);--border2:rgba(255,255,255,0.13);
    --accent:#6c63ff;--accent2:#ff6584;--accent3:#43e8a0;
    --text:#f0f0ff;--muted:#6b6b9a;--muted2:#9494c0;
}
body{font-family:'Outfit',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;overflow-x:hidden}
.bg-mesh{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
.bg-mesh::before{content:'';position:absolute;width:900px;height:900px;top:-250px;left:-200px;background:radial-gradient(circle,rgba(108,99,255,0.18) 0%,transparent 65%);animation:drift1 20s ease-in-out infinite alternate}
.bg-mesh::after{content:'';position:absolute;width:700px;height:700px;bottom:-150px;right:-150px;background:radial-gradient(circle,rgba(255,101,132,0.13) 0%,transparent 65%);animation:drift2 25s ease-in-out infinite alternate}
.bg-orb3{position:absolute;width:500px;height:500px;top:45%;left:55%;transform:translate(-50%,-50%);background:radial-gradient(circle,rgba(67,232,160,0.08) 0%,transparent 65%);animation:drift3 16s ease-in-out infinite alternate}
@keyframes drift1{from{transform:translate(0,0) scale(1)}to{transform:translate(100px,70px) scale(1.2)}}
@keyframes drift2{from{transform:translate(0,0) scale(1)}to{transform:translate(-70px,-90px) scale(1.25)}}
@keyframes drift3{from{transform:translate(-50%,-50%) scale(1)}to{transform:translate(-50%,-50%) scale(1.4)}}
.bg-grid{position:fixed;inset:0;z-index:0;pointer-events:none;background-image:linear-gradient(rgba(255,255,255,0.025) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.025) 1px,transparent 1px);background-size:64px 64px;mask-image:radial-gradient(ellipse at 50% 50%,black 20%,transparent 75%);-webkit-mask-image:radial-gradient(ellipse at 50% 50%,black 20%,transparent 75%)}
.particle{position:fixed;border-radius:50%;pointer-events:none;z-index:0;animation:floatUp linear infinite;opacity:0}
@keyframes floatUp{0%{transform:translateY(100vh) scale(0);opacity:0}10%{opacity:1}90%{opacity:.5}100%{transform:translateY(-10vh) scale(1);opacity:0}}
.navbar{position:fixed;top:0;left:0;right:0;z-index:200;height:64px;display:flex;align-items:center;padding:0 1.5rem;gap:14px;background:rgba(6,6,18,0.78);backdrop-filter:blur(24px);border-bottom:1px solid var(--border);animation:slideDown .5s ease}
@keyframes slideDown{from{transform:translateY(-100%);opacity:0}to{transform:translateY(0);opacity:1}}
.logo-icon{width:34px;height:34px;border-radius:9px;flex-shrink:0;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;box-shadow:0 4px 18px rgba(108,99,255,.45);animation:iconPulse 3s ease-in-out infinite}
@keyframes iconPulse{0%,100%{box-shadow:0 4px 18px rgba(108,99,255,.45)}50%{box-shadow:0 4px 30px rgba(108,99,255,.75)}}
.nav-avatar{width:34px;height:34px;border-radius:50%;margin-left:auto;background:linear-gradient(135deg,rgba(108,99,255,.38),rgba(255,101,132,.28));border:1px solid rgba(108,99,255,.4);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#a09aff}
.back-btn{display:inline-flex;align-items:center;gap:7px;padding:.45rem 1rem;border-radius:9px;text-decoration:none;font-size:13px;font-weight:500;background:rgba(255,255,255,.06);border:1px solid var(--border);color:var(--muted2);transition:all .2s}
.back-btn:hover{background:rgba(255,255,255,.1);color:var(--text)}
.layout{display:flex;padding-top:64px;min-height:100vh;position:relative;z-index:10}
.sidebar{width:240px;flex-shrink:0;background:rgba(8,8,20,.9);backdrop-filter:blur(24px);border-right:1px solid var(--border);padding:1.5rem 1rem;min-height:calc(100vh - 64px);position:sticky;top:64px;height:calc(100vh - 64px);animation:sideIn .4s ease .15s both}
@keyframes sideIn{from{opacity:0;transform:translateX(-24px)}to{opacity:1;transform:translateX(0)}}
.sidebar-label{font-size:10px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--muted);padding:0 .75rem;margin-bottom:.5rem}
.sidebar-link{display:flex;align-items:center;gap:10px;padding:.6rem .85rem;border-radius:10px;font-family:'Outfit',sans-serif;font-size:14px;font-weight:400;margin-bottom:3px;text-decoration:none;background:transparent;color:var(--muted2);transition:all .2s}
.sidebar-link:hover{background:rgba(108,99,255,.12);color:var(--text)}
.sidebar-link.active{background:rgba(108,99,255,.15);border:1px solid rgba(108,99,255,.28);color:#a09aff;font-weight:500}
.main{flex:1;padding:2rem 1.75rem;min-width:0}
.page-eyebrow{font-size:11px;color:var(--muted);font-weight:600;letter-spacing:.12em;text-transform:uppercase;margin-bottom:6px}
.page-title{font-size:30px;font-weight:800;letter-spacing:-1px;margin-bottom:2rem;background:linear-gradient(135deg,var(--text) 40%,var(--muted2));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.alert{padding:.85rem 1.25rem;border-radius:12px;margin-bottom:1.5rem;font-size:13px;font-weight:500;display:flex;align-items:center;gap:9px;animation:cardIn .35s ease}
.alert-success{background:rgba(67,232,160,.12);border:1px solid rgba(67,232,160,.3);color:var(--accent3)}
@keyframes cardIn{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.form-card{background:rgba(255,255,255,.03);border:1px solid var(--border);border-radius:18px;padding:2rem;animation:cardIn .4s ease}
.errors-box{background:rgba(255,101,132,.1);border:1px solid rgba(255,101,132,.28);border-radius:10px;padding:1rem 1.2rem;margin-bottom:1.5rem}
.errors-box p{font-size:12px;color:#ff8fab;margin-bottom:3px}
.errors-box p:last-child{margin-bottom:0}
.form-label{display:block;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);margin-bottom:6px}
.form-input{width:100%;background:rgba(255,255,255,.05);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:'Outfit',sans-serif;font-size:14px;padding:.65rem 1rem;outline:none;transition:border-color .2s,background .2s,box-shadow .2s;-webkit-appearance:none;appearance:none}
.form-input::placeholder{color:var(--muted)}
.form-input:focus{border-color:rgba(108,99,255,.5);background:rgba(108,99,255,.07);box-shadow:0 0 0 3px rgba(108,99,255,.12)}
.form-input option{background:#0d0d1f}
.form-group{margin-bottom:1.2rem}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:1.2rem}
.divider{height:1px;background:var(--border);margin:1.5rem 0}
.submit-btn{width:100%;padding:.78rem;border-radius:10px;border:none;cursor:pointer;font-family:'Outfit',sans-serif;font-size:15px;font-weight:700;background:linear-gradient(135deg,var(--accent),#8a83ff);color:#fff;box-shadow:0 4px 22px rgba(108,99,255,.42);transition:all .22s;display:block}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(108,99,255,.6)}
.submit-btn:active{transform:scale(.98)}
::-webkit-scrollbar{width:5px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:3px}
