!function(){var e={705:function(e,t,n){var o=n(639).Symbol;e.exports=o},239:function(e,t,n){var o=n(705),r=n(607),a=n(333),i=o?o.toStringTag:void 0;e.exports=function(e){return null==e?void 0===e?"[object Undefined]":"[object Null]":i&&i in Object(e)?r(e):a(e)}},561:function(e,t,n){var o=n(990),r=/^\s+/;e.exports=function(e){return e?e.slice(0,o(e)+1).replace(r,""):e}},957:function(e,t,n){var o="object"==typeof n.g&&n.g&&n.g.Object===Object&&n.g;e.exports=o},607:function(e,t,n){var o=n(705),r=Object.prototype,a=r.hasOwnProperty,i=r.toString,c=o?o.toStringTag:void 0;e.exports=function(e){var t=a.call(e,c),n=e[c];try{e[c]=void 0;var o=!0}catch(s){}var r=i.call(e);return o&&(t?e[c]=n:delete e[c]),r}},333:function(e){var t=Object.prototype.toString;e.exports=function(e){return t.call(e)}},639:function(e,t,n){var o=n(957),r="object"==typeof self&&self&&self.Object===Object&&self,a=o||r||Function("return this")();e.exports=a},990:function(e){var t=/\s/;e.exports=function(e){for(var n=e.length;n--&&t.test(e.charAt(n)););return n}},279:function(e,t,n){var o=n(218),r=n(771),a=n(841),i=Math.max,c=Math.min;e.exports=function(e,t,n){var s,l,d,u,f,v,b=0,g=!1,h=!1,p=!0;if("function"!=typeof e)throw new TypeError("Expected a function");function m(t){var n=s,o=l;return s=l=void 0,b=t,u=e.apply(o,n)}function y(e){return b=e,f=setTimeout(x,t),g?m(e):u}function w(e){var n=e-v;return void 0===v||n>=t||n<0||h&&e-b>=d}function x(){var e=r();if(w(e))return S(e);f=setTimeout(x,function(e){var n=t-(e-v);return h?c(n,d-(e-b)):n}(e))}function S(e){return f=void 0,p&&s?m(e):(s=l=void 0,u)}function k(){var e=r(),n=w(e);if(s=arguments,l=this,v=e,n){if(void 0===f)return y(v);if(h)return clearTimeout(f),f=setTimeout(x,t),m(v)}return void 0===f&&(f=setTimeout(x,t)),u}return t=a(t)||0,o(n)&&(g=!!n.leading,d=(h="maxWait"in n)?i(a(n.maxWait)||0,t):d,p="trailing"in n?!!n.trailing:p),k.cancel=function(){void 0!==f&&clearTimeout(f),b=0,s=v=l=f=void 0},k.flush=function(){return void 0===f?u:S(r())},k}},218:function(e){e.exports=function(e){var t=typeof e;return null!=e&&("object"==t||"function"==t)}},5:function(e){e.exports=function(e){return null!=e&&"object"==typeof e}},448:function(e,t,n){var o=n(239),r=n(5);e.exports=function(e){return"symbol"==typeof e||r(e)&&"[object Symbol]"==o(e)}},771:function(e,t,n){var o=n(639);e.exports=function(){return o.Date.now()}},493:function(e,t,n){var o=n(279),r=n(218);e.exports=function(e,t,n){var a=!0,i=!0;if("function"!=typeof e)throw new TypeError("Expected a function");return r(n)&&(a="leading"in n?!!n.leading:a,i="trailing"in n?!!n.trailing:i),o(e,t,{leading:a,maxWait:t,trailing:i})}},841:function(e,t,n){var o=n(561),r=n(218),a=n(448),i=/^[-+]0x[0-9a-f]+$/i,c=/^0b[01]+$/i,s=/^0o[0-7]+$/i,l=parseInt;e.exports=function(e){if("number"==typeof e)return e;if(a(e))return NaN;if(r(e)){var t="function"==typeof e.valueOf?e.valueOf():e;e=r(t)?t+"":t}if("string"!=typeof e)return 0===e?e:+e;e=o(e);var n=c.test(e);return n||s.test(e)?l(e.slice(2),n?2:8):i.test(e)?NaN:+e}}},t={};function n(o){var r=t[o];if(void 0!==r)return r.exports;var a=t[o]={exports:{}};return e[o](a,a.exports,n),a.exports}n.g=function(){if("object"===typeof globalThis)return globalThis;try{return this||new Function("return this")()}catch(e){if("object"===typeof window)return window}}(),function(){"use strict";function e(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,o=new Array(t);n<t;n++)o[n]=e[n];return o}function t(t){return function(t){if(Array.isArray(t))return e(t)}(t)||function(e){if("undefined"!==typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(t)||function(t,n){if(t){if("string"===typeof t)return e(t,n);var o=Object.prototype.toString.call(t).slice(8,-1);return"Object"===o&&t.constructor&&(o=t.constructor.name),"Map"===o||"Set"===o?Array.from(t):"Arguments"===o||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(o)?e(t,n):void 0}}(t)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}var o=function(e){e.preventDefault();var t=e.target.closest(".accordion-item"),n=e.target.closest(".accordion-wrapper"),o=t.getAttribute("aria-expanded");r(n),t.toggleAttribute("aria-expanded",""!==o),t.scrollIntoView({block:"center"})},r=function(e){t(e.querySelectorAll(".accordion-item")).forEach((function(e){return e.removeAttribute("aria-expanded")}))};document.addEventListener("DOMContentLoaded",(function(){t(document.querySelectorAll(".accordion-item")).forEach((function(e){return function(e){e.querySelector(".accordion-item__title").addEventListener("click",o)}(e)}))}));var a=function(e){var t=e.currentTarget;t.closest(".collapsible-text").classList.toggle("expanded")||t.scrollIntoView({block:"center"})};document.addEventListener("DOMContentLoaded",(function(){t(document.querySelectorAll(".collapsible-text__toggle")).forEach((function(e){return e.addEventListener("click",a)}))}));var i=3e3,c=t(document.querySelectorAll(".hero-home")),s=t(document.querySelectorAll(".hero-home__rotator")),l=c.filter((function(e){return!e.closest(".hero-home__rotator")})),d=function(e){var n=t(e.querySelectorAll(".hero-home")),o=e.querySelector(".hero-home__controls");n.length&&(n.forEach((function(e){return e.classList.remove("hero-home--current")})),n[0].classList.add("hero-home--current"),n.length>1&&(o&&o.classList.add("hero-home__controls--active"),o.addEventListener("click",(function(){return u(e)}))))},u=function(e){var t=e.querySelector(".hero-home--current"),n=e.querySelectorAll(".hero-home:not(.hero-home--current)"),o=n[Math.floor(Math.random()*n.length)];t.rotateHeadingsTimeout&&clearTimeout(t.rotateHeadingsTimeout),t.classList.remove("hero-home--current"),o.classList.add("hero-home--current"),o.rotateHeadingsTimeout=setTimeout((function(){return f(o)}),i)},f=function e(n){var o=n.querySelector(".hero-home__link");if(o&&o===document.activeElement)n.rotateHeadingsTimeout=setTimeout((function(){return e(n)}),i);else{var r,a=t(n.querySelectorAll(".hero-home__heading")),c=a.findIndex((function(e){return!e.classList.contains("hero-home__heading--transparent")}));if(r=c,c=++c%a.length,Object.assign(n,{headings:a,previousHeadingIndex:r,currentHeadingIndex:c,rotateHeadingsTimeout:null}),o){var s=o.querySelector(".screen-reader-text");s&&(s.textContent=a[c].textContent)}!function(e){var t=e.headings,n=e.previousHeadingIndex;t[n].classList.add("hero-home__heading--transparent"),e.rotateHeadingsTimeout=setTimeout((function(){return v(e)}),750)}(n)}};function v(e){var t=e.headings,n=e.previousHeadingIndex,o=e.currentHeadingIndex;t[n].classList.add("hero-home__heading--hidden"),t[o].classList.remove("hero-home__heading--hidden"),setTimeout((function(){t[o].classList.remove("hero-home__heading--transparent")}),20),e.rotateHeadingsTimeout=setTimeout((function(){return f(e)}),i)}s.length&&s.forEach(d),l.length&&l.forEach(f);var b=[];function g(e){var t,n=null!==(t=e.dataset.clock)&&void 0!==t&&t,o=!!e.dataset.stop&&"true"===e.dataset.stop,r=e.querySelector(".clock__contents__count-count"),a=e.dataset.display,i=e.dataset.displaypadding;if(!1!==n){var c=new Date(n).getTime(),s=6e4,l=36e5,d=864e5;if(o)if(Date.now()>c){var u="";switch(a){case"d-nolabel":u="0".padStart(parseInt(i),"0");break;case"d":u="0 Days";break;case"dh":u="0 Days 0 Hours";break;case"dhm":u="0 Days 0 Hours 0 Minutes";break;default:u="0 Days 0 Hours 0 Minutes 0 Seconds"}return void(r.innerHTML=p(u))}b.push(setInterval((function(){var e=Date.now(),t=Math.abs(e-c),n=Math.floor(t/d),o=Math.floor(t%d/l),u=Math.floor(t%d%l/s),f=Math.floor(t%d%l%s/1e3),v="";switch(a){case"d-nolabel":v=""+n.toString().padStart(parseInt(i),"0");break;case"d":v=n+" Days";break;case"dh":v=n+" Days "+o+" Hours";break;case"dhm":v=n+" Days "+o+" Hours "+u+" Minutes";break;default:v=n+" Days "+o+" Hours "+u+" Minutes "+f+" Seconds"}r.innerHTML=p(v)}),1e3))}}var h=function(){b.forEach((function(e){clearInterval(e)})),b=[],t(document.querySelectorAll("[data-clock]")).map(g)},p=function(e){var t=(e=e.replace(/(<([^>]+)>)/gi,"")).split("");return(t=t.map((function(e){return"<span>"+e+"</span>"}))).join("")},m=0;function y(){var e=.01*window.innerHeight;document.documentElement.style.setProperty("--vh","".concat(e,"px"));var t=window.innerWidth-document.documentElement.clientWidth;document.documentElement.style.setProperty("--scrollbar","".concat(t,"px"))}function w(){clearTimeout(m),m=setTimeout(y,250)}function x(){y(),window.addEventListener("resize",w)}var S=function(){x()};var k=function(e,t){return function(){e()}},E=document.querySelector("[data-dropdown-backdrop]"),L=t(document.querySelectorAll("[data-dropdown]")),_={attributeFilter:["data-visible","data-backdrop","data-trap","data-toggleable"],attributeOldValue:!0};function q(e){var t=e.target;if(t.dropdown){var n=t.dropdown.handlers,o=n.visibleChange,r=n.backdropChange,a=n.trapChange,i=n.toggleableChange;switch(e.attributeName){case"data-visible":o(e.target);break;case"data-backdrop":r(e.target);break;case"data-trap":a(e.target);break;case"data-toggleable":i(e.target)}}}function T(e){e.forEach(q)}function A(e){var t=e.dropdown,n=t.content,o=t.toggle,r=e.dataset,a=r.toggleable,i="yes"===r.visible;i?n.removeAttribute("hidden"):n.hidden=!0,"yes"===a&&o.setAttribute("aria-expanded",i?"true":"false"),e.dataset.trap=i&&"yes"===a?"active":"inactive",e.dataset.backdrop=i&&"yes"===a?"active":"inactive"}function H(e){if(E){var t=L.filter((function(e){return"active"===e.dataset.backdrop})).map((function(e){return e.dataset.dropdown}));E.dataset.activeDropdowns=t.join(" "),E.dataset.dropdownBackdrop=t.length<1?"inactive":"active"}}function C(e){"active"===e.dataset.trap?(!function(e){var t=e.dropdown.getFocusable().skip;t.length>0&&t.forEach(F)}(e),e.addEventListener("keydown",e.dropdown.handlers.keydown)):(!function(e){var t=e.dropdown.getFocusable().skip;t.length>0&&t.forEach(P)}(e),e.removeEventListener("keydown",e.dropdown.handlers.keydown))}function j(e){if("no"===e.dataset.toggleable)if(e.dropdown.toggle.disabled=!0,e.classList.contains("menu-item")){var t=e.classList.contains("current-menu-item")||e.classList.contains("current-menu-ancestor");e.dataset.visible=t?"yes":"no"}else e.dataset.visible="yes";else e.dropdown.toggle.removeAttribute("disabled")}function I(){L.filter((function(e){return"active"===e.dataset.backdrop})).map((function(e){return e.dataset.visible="no"}))}function M(e){return function(t){e.dataset.visible="yes"===e.dataset.visible?"no":"yes"}}function O(e){return function(t){var n=e.dropdown.toggle,o=e.dropdown.getFocusable(),r=o.first,a=o.last,i="Tab"===t.key||9===t.keyCode,c="Escape"===t.key||27===t.keyCode;if(i||c)return c?(e.dataset.visible="no",void(n&&n.focus())):void(t.shiftKey?document.activeElement===r&&(a.focus(),t.preventDefault()):document.activeElement===a&&(r.focus(),t.preventDefault()))}}function D(e){return Array.from(e.querySelectorAll('a:not([disabled]), button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), details:not([disabled]), [tabindex]:not([tabindex="-1"]:not([disabled]))'))}function N(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:[],n=e.filter((function(e){return!t.includes(e)}));return{first:n[0],last:n[n.length-1],all:e,allowed:n,skip:t}}function F(e){e.hasAttribute("tabindex")&&(e.dataset.tabindex=e.tabindex),e.tabindex=-1}function P(e){e.dataset.tabindex?e.tabindex=e.dataset.tabindex:e.removeAttribute("tabindex")}function B(e){var t=e.dataset,n=t.dropdownContent,o=t.dropdownToggle,r=e.querySelector(n),a=e.querySelector(o);"yes"===e.dataset.toggleable&&a&&a.hasAttribute("hidden")&&a.removeAttribute("hidden");var i=function(e){var t=new MutationObserver(T);return t.observe(e,_),t}(e);e.dropdown={content:r,toggle:a,observer:i,getFocusable:function(){return N(D(e))},handlers:{backdropChange:H,toggleableChange:j,trapChange:C,visibleChange:A,keydown:O(e),toggleClick:M(e)}},a&&a.addEventListener("click",e.dropdown.handlers.toggleClick),E&&E.addEventListener("click",I),e.dataset.dropdownStatus="initialized"}var V=k((function(){L.map(B)}),(function(){L.forEach((function(e){var t=e.dropdown;t.observer.disconnect(),t.toggle.removeEventListener("click",t.handlers.toggleClick),delete e.dropdown})),E&&(E.removeEventListener("click",I),E.dataset.dropdownBackdrop="inactive",E.dataset.activeDropdowns="")})),W=document.querySelector('[data-dropdown="primary-nav"]'),R=document.querySelector('[data-dropdown="language-switcher"]'),$=W.querySelectorAll(".menu-item[data-dropdown]");function z(e){var t=e.isIntersecting,n=e.target;n===W.dropdown.toggle?t?(W.dataset.visible="no",W.dataset.toggleable="yes",$.forEach((function(e){e.dataset.toggleable="yes",e.dropdown.toggle.removeAttribute("hidden")}))):(W.dataset.visible="yes",W.dataset.toggleable="no",W.dataset.backdrop="inactive",W.dataset.trap="inactive",$.forEach((function(e){e.dataset.toggleable="no",e.dropdown.toggle.hidden=!0}))):n.classList.contains("sub-menu")&&(n.closest("[data-dropdown]").dataset.trap="inactive")}function U(e){e.forEach(z)}function K(e){A(e);var t="yes"===e.dataset.visible,n=null!=e.dropdown.toggle.offsetParent;t&&n?(document.body.classList.add("disable-body-scrolling"),R&&(R.dataset.visible="no")):(document.body.classList.remove("disable-body-scrolling"),$.forEach((function(e){e.dataset.backdrop="inactive","yes"===e.dataset.visible&&"no"===W.dataset.toggleable&&(W.dataset.subnavVisible="yes",W.style.setProperty("--subnav-margin-bottom",e.dropdown.content.offsetHeight),W.querySelectorAll(".current-menu-item, .current-menu-ancestor").forEach((function(e){e.offsetTop+e.offsetHeight>=e.closest("ul").offsetHeight-e.offsetHeight?e.classList.add("menu-item-bottom-line"):e.classList.remove("menu-item-bottom-line")})))})))}function G(e){if(A(e),W){var t="yes"===e.dataset.visible,n=W.dataset,o=n.visible,r=n.toggleable;t&&o&&"yes"===r&&(W.dataset.visible="no")}}function J(){if(W){K(W),W.dropdown.handlers.visibleChange=K;var e=W.querySelector(".header-content"),n=W.querySelector(".translation-bar"),o=[].concat(t(n?D(n):[]),t(e?D(e):[]));W.dropdown.getFocusable=function(){return N(D(W),o)},W.observer=new IntersectionObserver(U),W.observer.observe(W.dropdown.toggle),$.forEach((function(e){W.observer.observe(e.dropdown.content)}))}R&&(G(R),R.dropdown.handlers.visibleChange=G)}var Q=k((function(){J()}),(function(){W&&W.observer&&W.observer.disconnect(),W.dropdown.handlers.visibleChange=A})),X="site-header",Y="".concat(X,"--pinned"),Z=document.querySelector(".".concat(X));function ee(){document.documentElement.scrollTop>0?Z.classList.add(Y):Z.classList.remove(Y)}var te,ne=k((function(){var e=n(493);window.addEventListener("scroll",e(ee,100,{trailing:!0}))}),(function(){window.removeEventListener("scroll",ee),Z&&Z.classList.remove(Y)})),oe=document.querySelector('[data-dropdown="toc-nav"]'),re=null===oe||void 0===oe?void 0:oe.querySelector(".toc__title"),ae=null===oe||void 0===oe?void 0:oe.querySelector(".toc"),ie=null===oe||void 0===oe||null===(te=oe.closest(".toc__section"))||void 0===te?void 0:te.querySelector(".toc__content"),ce=document.querySelector(".site-header").getBoundingClientRect().height;function se(e){return new IntersectionObserver(de,e)}function le(e){var t=e.intersectionRatio,n=e.isIntersecting,o=e.target;if(o===ae){var r=o.parentElement;n&&1===t&&"no"===r.dataset.toggleable&&(r.dataset.sticky="yes"),e.rootBounds.height<e.boundingClientRect.height&&(r.dataset.sticky="no")}else if(o===re){var a=o.parentElement;n?(a.dataset.visible="no",a.dataset.toggleable="yes",a.dataset.sticky="no"):(a.dataset.visible="yes",a.dataset.toggleable="no",a.dataset.sticky="yes",a.dataset.backdrop="inactive",a.dataset.trap="inactive")}else if(["H2","P","LI"].includes(o.tagName)&&(n&&t>=.5&&"H2"===o.tagName?o.dataset.visible="yes":"H2"===o.tagName&&(o.dataset.visible="no"),"yes"===oe.dataset.observeScroll)){var i=Object.values(ie.querySelectorAll("h2[id]")),c=i.filter((function(e){return"yes"===e.dataset.visible}))[0];if(!c)return;var s=i.indexOf(c),l=c.getBoundingClientRect().top,d=window.innerHeight/4>l?c:i[s-1],u=d?function(e){var t=e.id;return document.querySelector('.toc a.toc__link[href="#'.concat(t,'"]'))}(d):null;u&&fe(u)}}function de(e){e.forEach(le)}function ue(e){A(e);var t="yes"===e.dataset.visible,n=null!=e.dropdown.toggle.offsetParent;t&&n?(be(e),document.body.classList.add("disable-body-scrolling")):document.body.classList.remove("disable-body-scrolling")}function fe(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:e.getAttribute("href"),n=arguments.length>2&&void 0!==arguments[2]&&arguments[2],o=e.closest(".toc__nested"),r=!!o&&o.previousElementSibling,a=r?r.innerText:e.innerText;a=a.length>0?a:oe.dropdown.toggle.querySelector(".btn-label-a11y").innerText,oe.querySelectorAll(".toc__link").forEach((function(e){return e.classList.remove("toc__link--active")})),e.classList.add("toc__link--active"),oe.dropdown.toggle.querySelector(".btn-label-active-item").textContent=a;var i=ve(e);i&&n?be(i,t):history.replaceState(null,null,t)}function ve(e){var t=e.getAttribute("href");return document.querySelector('h2[id="'.concat(t.replace("#",""),'"]'))}function be(e){var t,n=arguments.length>1&&void 0!==arguments[1]&&arguments[1];function o(){clearTimeout(t),t=setTimeout((function(){oe.dataset.observeScroll="yes",removeEventListener("scroll",o)}),100)}oe.dataset.observeScroll="no",n&&history.replaceState(null,null,n),e.scrollIntoView({behavior:"smooth"}),addEventListener("scroll",o)}function ge(e){e.preventDefault();var t=e.target,n=t.getAttribute("href");fe(t,n,!0),"yes"===oe.dataset.toggleable&&"yes"===oe.dataset.visible&&(oe.dataset.visible="no")}var he=k((function(){!function(){if(oe){if(ue(oe),oe.dropdown.handlers.visibleChange=ue,oe.observer=se({root:null,rootMargin:ce+"px 0px 0px 0px",threshold:[0,.25,.5,.75,1]}),oe.observer.observe(re),oe.observer.observe(ae),oe.querySelectorAll('.toc__link[href^="#"]').forEach((function(e){e.addEventListener("click",ge)})),ie&&(ie.observer=se({root:null,rootMargin:ce+"px 0px 0px 0px",threshold:[0,.25,.5,.75,1]}),ie.querySelectorAll("h2, p, li").forEach((function(e){ie.observer.observe(e)}))),location.hash){var e=location.hash,t=oe.querySelector('a[href="'.concat(e,'"]'));t&&fe(t,e),"no"===oe.dataset.toggleable&&(oe.dataset.sticky="yes")}window.onload=function(e){oe.dataset.observeScroll="yes"}}}()}),(function(){oe&&oe.observer&&oe.observer.disconnect(),oe.dropdown.handlers.visibleChange=A,ie&&ie.observer&&ie.observer.disconnect()}));h(),V(),Q(),S(),ne(),he()}()}();