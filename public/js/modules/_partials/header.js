/**
 * Header JavaScript - Sistema de Gestão de Eventos UNIFIO
 *
 * Este arquivo contém toda a funcionalidade JavaScript relacionada ao cabeçalho da aplicação.
 * Inclui controle de dropdowns, animações e interações dos menus de navegação.
 *
 * Funcionalidades:
 * - Controle de dropdowns via hover (mouse enter/leave)
 * - Prevenção de conflitos com o Bootstrap
 * - Animações suaves nos menus
 *
 * @author Sistema SGE UNIFIO
 * @version 2.1
 */

let __hoverReady = false;
window.addEventListener('pointermove', () => { __hoverReady = true; }, { once: true });
window.addEventListener('load', () => { setTimeout(() => { __hoverReady = true; }, 400); }, { once: true });
window.__hoverReady = () => __hoverReady;

/* ---------- Estabiliza scroll em reload/bfcache ---------- */
try { if ('scrollRestoration' in history) history.scrollRestoration = 'manual'; } catch (e) {}
window.addEventListener('load', () => {
  if (!location.hash) window.scrollTo(0, 0);
}, { once: true });

(function () {
  document.addEventListener('DOMContentLoaded', function () {
    const mqHoverFine = window.matchMedia('(hover:hover) and (pointer:fine)');
    const isDesktop = () => window.innerWidth > 991.98;

    /* ---------- Utils ---------- */
    function getToggleFromMenu(menuEl) {
      const item = menuEl.closest('.nav-item.dropdown');
      return item ? item.querySelector('[data-bs-toggle="dropdown"]') : null;
    }

    function closeAllDropdowns(exceptEl = null) {
      document.querySelectorAll('.nav-item.dropdown .dropdown-menu.show').forEach(menu => {
        if (menu !== exceptEl && !menu.classList.contains('notification-dropdown')) {
          const t = getToggleFromMenu(menu);
          if (t) t.setAttribute('aria-expanded', 'false');
          if (menu.classList.contains('notification-dropdown')) {
            menu.style.display = 'none';
          }
          menu.classList.remove('show');
        }
      });
    }

    function ensureA11y(dropdown, toggle, menu) {
      toggle.setAttribute('aria-haspopup', 'true');
      toggle.setAttribute('aria-expanded', 'false');
      if (!toggle.id) toggle.id = `dd-toggle-${Math.random().toString(36).slice(2, 8)}`;
      menu.setAttribute('role', 'menu');
      menu.setAttribute('aria-labelledby', toggle.id);
      menu.querySelectorAll('.dropdown-item').forEach((it) => {
        if (!it.hasAttribute('tabindex') && it.tagName !== 'A' && it.tagName !== 'BUTTON') {
          it.setAttribute('tabindex', '0');
        }
        if (!it.getAttribute('role')) it.setAttribute('role', 'menuitem');
      });
    }

    function openDropdown(dropdown, { viaHover = false } = {}) {
      const menu = dropdown.querySelector('.dropdown-menu');
      const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
      if (!menu || !toggle) return;

      // Fecha outros
      document.querySelectorAll('.nav-item.dropdown .dropdown-menu.show').forEach(m => {
        if (m !== menu && !m.classList.contains('notification-dropdown')) {
          const t = getToggleFromMenu(m);
          if (t) t.setAttribute('aria-expanded', 'false');
          m.classList.remove('show');
        }
      });

      toggle.setAttribute('aria-expanded', 'true');
      if (menu.classList.contains('notification-dropdown')) {
        menu.style.display = 'block';
      }
      menu.classList.add('show');

      if (!viaHover) {
        const firstItem = menu.querySelector('.dropdown-item, [role="menuitem"]');
        if (firstItem) firstItem.focus({ preventScroll: true });
      }
    }

    function closeDropdown(dropdown) {
      const menu = dropdown.querySelector('.dropdown-menu');
      const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
      if (!menu || !toggle) return;

      toggle.setAttribute('aria-expanded', 'false');
      if (menu.classList.contains('notification-dropdown')) {
        menu.style.display = 'none';
      }
      menu.classList.remove('show');
    }

    /* ---------- 1) Hover/Click em dropdowns ---------- */
    function initDropdownHover() {
      const dropdowns = document.querySelectorAll('.nav-item.dropdown');

      dropdowns.forEach(dropdown => {
        if (dropdown.classList.contains('notifications')) return; // gerenciado em notifications.js

        const menu = dropdown.querySelector('.dropdown-menu');
        const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
        if (!menu || !toggle) return;

        ensureA11y(dropdown, toggle, menu);

        let hoverTimeout;

        // HOVER (só desktop + ponteiro fino + após 1ª interação)
        if (mqHoverFine.matches) {
          dropdown.addEventListener('mouseenter', function () {
            if (!(typeof window.__hoverReady === 'function' ? window.__hoverReady() : true)) return;
            if (!isDesktop()) return;

            clearTimeout(hoverTimeout);
            menu.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');
          });

          dropdown.addEventListener('mouseleave', function () {
            if (!isDesktop()) return;

            hoverTimeout = setTimeout(() => {
              menu.classList.remove('show');
              toggle.setAttribute('aria-expanded', 'false');
            }, 120);
          });
        }

        // CLICK: toggle estado
        toggle.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          const isOpen = menu.classList.contains('show');
          if (isOpen) closeDropdown(dropdown);
          else openDropdown(dropdown);
        }, false);

        // TECLADO no toggle
        toggle.addEventListener('keydown', (e) => {
          if (e.key === 'ArrowDown' || e.key === ' ' || e.key === 'Enter') {
            e.preventDefault();
            if (!menu.classList.contains('show')) openDropdown(dropdown);
          }
          if (e.key === 'Escape') {
            e.preventDefault();
            closeDropdown(dropdown);
            toggle.focus({ preventScroll: true });
          }
        });

        // TECLADO dentro do menu
        menu.addEventListener('keydown', (e) => {
          const items = Array.from(menu.querySelectorAll('.dropdown-item, [role="menuitem"]'))
            .filter(el => !el.hasAttribute('disabled'));
          if (!items.length) return;

          const currentIndex = items.indexOf(document.activeElement);
          if (e.key === 'ArrowDown') {
            e.preventDefault();
            const next = items[(currentIndex + 1) % items.length];
            next.focus({ preventScroll: true });
          } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            const prev = items[(currentIndex - 1 + items.length) % items.length];
            prev.focus({ preventScroll: true });
          } else if (e.key === 'Home') {
            e.preventDefault();
            items[0].focus({ preventScroll: true });
          } else if (e.key === 'End') {
            e.preventDefault();
            items[items.length - 1].focus({ preventScroll: true });
          } else if (e.key === 'Escape') {
            e.preventDefault();
            closeDropdown(dropdown);
            toggle.focus({ preventScroll: true });
          }
        });
      });

      // CLICK OUTSIDE — fecha todos (exceto notificações)
      document.addEventListener('click', function (e) {
        if (!e.target.closest('.nav-item.dropdown')) {
          closeAllDropdowns();
        }
      });

      // Fecha em resize/scroll/ESC
      window.addEventListener('resize', () => closeAllDropdowns());
      window.addEventListener('scroll', () => { if (isDesktop()) closeAllDropdowns(); }, { passive: true });
      document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAllDropdowns(); });
    }

    /* ---------- 2) Proteção do menu mobile (collapse) ---------- */
    function initMobileMenuProtection() {
      const navbarCollapse = document.querySelector('.navbar-collapse');
      const navbarToggler = document.querySelector('.navbar-toggler');
      if (!navbarCollapse || !navbarToggler) return;

      // Liga/desliga classe de fallback para bloquear cliques no conteúdo
      navbarCollapse.addEventListener('show.bs.collapse', () => document.body.classList.add('menu-open'));
      navbarCollapse.addEventListener('hide.bs.collapse', () => document.body.classList.remove('menu-open'));

      // Fecha popup de evento ao abrir menu
      navbarCollapse.addEventListener('show.bs.collapse', function () {
        const eventPopup = document.querySelector('.event-popup-overlay');
        if (eventPopup && eventPopup.classList.contains('active')) {
          eventPopup.classList.remove('active');
        }
      });

      // Previne vazamento de clique dos links normais
      const menuLinks = navbarCollapse.querySelectorAll('a.nav-link:not(.dropdown-toggle), a.dropdown-item');
      menuLinks.forEach(link => {
        link.addEventListener('click', function (e) {
          e.stopPropagation();
        }, true);
      });

      // Fecha ao clicar fora (exceto em dropdowns internos)
      document.addEventListener('click', function (e) {
        if (navbarCollapse.classList.contains('show')) {
          const isDropdownClick = e.target.closest('.nav-item.dropdown');
          if (!navbarCollapse.contains(e.target) &&
              !navbarToggler.contains(e.target) &&
              !isDropdownClick) {
            navbarToggler.click();
          }
        }
      });
    }

    /* ---------- 3) Init ---------- */
    function initHeader() {
      initDropdownHover();
      initMobileMenuProtection();
      // Notificações seguem em notifications.js
    }

    initHeader();
  });
})();