try { if ('scrollRestoration' in history) history.scrollRestoration = 'manual'; } catch(e){}
const toTop = () => { if (!location.hash) window.scrollTo(0, 0); };
window.addEventListener('load', toTop, { once:true });
window.addEventListener('pageshow', (e) => { if (e.persisted && !location.hash) toTop(); });
window.addEventListener('load', () => {
  document.querySelectorAll('[autofocus]').forEach(el => {
    try { el.focus({ preventScroll: true }); } catch(_) { el.focus(); }
  });
}, { once:true });

let __hoverReady = false;
window.addEventListener('pointermove', () => { __hoverReady = true; }, { once:true });
window.__hoverReady = () => __hoverReady;
