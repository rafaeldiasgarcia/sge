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
(function(){
  document.addEventListener('DOMContentLoaded',function(){
    const mqHoverFine=window.matchMedia('(hover:hover) and (pointer:fine)');
    const isDesktop=()=>window.innerWidth>991.98;
    const isMobile=()=>!isDesktop();
    const canHover=()=>typeof window.__hoverReady==='function'?window.__hoverReady():true;

    function closeDropdown(dropdown){
      const menu=dropdown.querySelector('.dropdown-menu');
      const toggle=dropdown.querySelector('.dropdown-toggle, [data-manual-dropdown="true"]');
      if(!menu||!toggle)return;
      dropdown.classList.remove('show');
      toggle.setAttribute('aria-expanded','false');
      menu.style.display='';
      menu.classList.remove('show');
    }

    function closeAllDropdowns(exceptMenu=null){
      document.querySelectorAll('.nav-item.dropdown .dropdown-menu.show').forEach(m=>{
        if(m!==exceptMenu){
          const dd=m.closest('.nav-item.dropdown');
          if(dd)closeDropdown(dd);
        }
      });
    }

    function ensureA11y(dropdown,toggle,menu){
      toggle.setAttribute('aria-haspopup','true');
      toggle.setAttribute('aria-expanded','false');
      toggle.setAttribute('role','button');
      toggle.setAttribute('tabindex','0');
      if(!toggle.id)toggle.id=`dd-toggle-${Math.random().toString(36).slice(2,8)}`;
      menu.setAttribute('role','menu');
      menu.setAttribute('aria-labelledby',toggle.id);
      menu.querySelectorAll('.dropdown-item').forEach(it=>{
        if(!it.hasAttribute('tabindex')&&it.tagName!=='A'&&it.tagName!=='BUTTON')it.setAttribute('tabindex','0');
        if(!it.getAttribute('role'))it.setAttribute('role','menuitem');
      });
    }

    function takeOverFromBootstrap(toggle){
      if(toggle.hasAttribute('data-bs-toggle')){
        toggle.removeAttribute('data-bs-toggle');
      }
      if(toggle.hasAttribute('data-bs-auto-close')){
        toggle.removeAttribute('data-bs-auto-close');
      }
      toggle.setAttribute('data-manual-dropdown','true');
    }

    function openDropdown(dropdown,{viaHover=false}={}){
      const menu=dropdown.querySelector('.dropdown-menu');
      const toggle=dropdown.querySelector('.dropdown-toggle, [data-manual-dropdown="true"]');
      if(!menu||!toggle)return;
      closeAllDropdowns(menu);
      dropdown.classList.add('show');
      toggle.setAttribute('aria-expanded','true');
      if(isMobile())menu.style.display='block';
      menu.classList.add('show');
      if(!viaHover){
        const firstItem=menu.querySelector('.dropdown-item,[role="menuitem"]');
        if(firstItem)firstItem.focus({preventScroll:true});
      }
    }

    function initDropdowns(){
      document.querySelectorAll('.nav-item.dropdown').forEach(dropdown=>{
        const menu=dropdown.querySelector('.dropdown-menu');
        const toggle=dropdown.querySelector('.dropdown-toggle, [data-bs-toggle="dropdown"]');
        if(!menu||!toggle)return;

        takeOverFromBootstrap(toggle);
        ensureA11y(dropdown,toggle,menu);

        let hoverTimeout;
        if(mqHoverFine.matches){
          dropdown.addEventListener('mouseenter',function(){
            if(!isDesktop()||!canHover())return;
            clearTimeout(hoverTimeout);
            openDropdown(dropdown,{viaHover:true});
          });
          dropdown.addEventListener('mouseleave',function(){
            if(!isDesktop())return;
            hoverTimeout=setTimeout(()=>{closeDropdown(dropdown);},160);
          });
        }

        toggle.addEventListener('click',e=>{
          e.preventDefault();
          e.stopPropagation();
          const isOpen=menu.classList.contains('show');
          if(isOpen)closeDropdown(dropdown);else openDropdown(dropdown);
        },true); // capture=true para “pegar” antes de qq handler do Bootstrap remanescente

        toggle.addEventListener('keydown',e=>{
          if(e.key==='ArrowDown'||e.key===' '||e.key==='Enter'){
            e.preventDefault();
            if(!menu.classList.contains('show'))openDropdown(dropdown);
          }
          if(e.key==='Escape'){
            e.preventDefault();closeDropdown(dropdown);toggle.focus({preventScroll:true});
          }
        });

        menu.addEventListener('keydown',e=>{
          const items=Array.from(menu.querySelectorAll('.dropdown-item,[role="menuitem"]')).filter(el=>!el.hasAttribute('disabled'));
          if(!items.length)return;
          const i=items.indexOf(document.activeElement);
          if(e.key==='ArrowDown'){e.preventDefault();items[(i+1)%items.length].focus({preventScroll:true});}
          else if(e.key==='ArrowUp'){e.preventDefault();items[(i-1+items.length)%items.length].focus({preventScroll:true});}
          else if(e.key==='Home'){e.preventDefault();items[0].focus({preventScroll:true});}
          else if(e.key==='End'){e.preventDefault();items[items.length-1].focus({preventScroll:true});}
          else if(e.key==='Escape'){e.preventDefault();closeDropdown(dropdown);toggle.focus({preventScroll:true});}
        });
      });

      document.addEventListener('click',e=>{if(!e.target.closest('.nav-item.dropdown'))closeAllDropdowns();});
      window.addEventListener('resize',()=>closeAllDropdowns());
      window.addEventListener('scroll',()=>{if(isDesktop())closeAllDropdowns();},{passive:true});
      document.addEventListener('keydown',e=>{if(e.key==='Escape')closeAllDropdowns();});
    }

    function initMobileMenu(){
      const navbarCollapse=document.querySelector('.navbar-collapse');
      const navbarToggler=document.querySelector('.navbar-toggler');
      if(!navbarCollapse||!navbarToggler)return;
      navbarCollapse.addEventListener('show.bs.collapse',()=>document.body.classList.add('menu-open'));
      navbarCollapse.addEventListener('hide.bs.collapse',()=>document.body.classList.remove('menu-open'));
      navbarCollapse.addEventListener('show.bs.collapse',function(){
        const eventPopup=document.querySelector('.event-popup-overlay');
        if(eventPopup&&eventPopup.classList.contains('active'))eventPopup.classList.remove('active');
      });
      navbarCollapse.querySelectorAll('a.nav-link:not(.dropdown-toggle),a.dropdown-item').forEach(link=>{
        link.addEventListener('click',e=>{e.stopPropagation();},true);
      });
      document.addEventListener('click',function(e){
        if(navbarCollapse.classList.contains('show')){
          const isDropdownClick=e.target.closest('.nav-item.dropdown');
          if(!navbarCollapse.contains(e.target)&&!navbarToggler.contains(e.target)&&!isDropdownClick){navbarToggler.click();}
        }
      });
    }

    initDropdowns();
    initMobileMenu();
  });
})();