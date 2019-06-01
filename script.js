/*! @nrk/core-toggle v2.2.0 - Copyright (c) 2017-2019 NRK */
!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e=e||self).coreToggle=t()}(this,function(){"use strict";var e="undefined"!=typeof window,t=e&&/(android)/i.test(navigator.userAgent),n=e&&/iPad|iPhone|iPod/.test(String(navigator.platform)),o=function(e){void 0===e&&(e=!1);try{window.addEventListener("test",null,{get passive(){e=!0}})}catch(e){}return e}();function r(e,t,n,r){(void 0===r&&(r=!1),"undefined"==typeof window||window[e=e+"-"+t])||(o||"object"!=typeof r||(r=Boolean(r.capture)),("resize"===t||"load"===t?window:document).addEventListener(window[e]=t,n,r))}var u="prevent_recursive_dispatch_maximum_callstack";function a(e,t,n){void 0===n&&(n={});var r,o=""+u+t;if(e[o])return!0;e[o]=!0,"function"==typeof window.CustomEvent?r=new window.CustomEvent(t,{bubbles:!0,cancelable:!0,detail:n}):(r=document.createEvent("CustomEvent")).initCustomEvent(t,!0,!0,n);var i=e.dispatchEvent(r);return e[o]=null,i}function d(e){return Date.now().toString(36)+Math.random().toString(36).slice(2,5)}function l(e,t){if(void 0===t&&(t=document),e){if(e.nodeType)return[e];if("string"==typeof e)return[].slice.call(t.querySelectorAll(e));if(e.length)return[].slice.call(e)}return[]}var c="data-@nrk/core-toggle-2.2.0".replace(/\W+/g,"-"),f=t?"data":"aria",s="aria-expanded",i=27;function p(e){return document.getElementById(e.getAttribute("aria-controls"))||e.nextElementSibling}function g(e,t){var n=p(e),r="true"===e.getAttribute(s),o="boolean"==typeof t?t:"toggle"===t?!r:r,i=r===o||a(e,"toggle",{relatedTarget:n,isOpen:r,willOpen:o})?o:"true"===e.getAttribute(s),u=!r&&i&&n.querySelector("[autofocus]");u&&setTimeout(function(){return u&&u.focus()}),e.setAttribute(s,i),n[i?"removeAttribute":"setAttribute"]("hidden","")}return r(c,"keydown",function(e){if(e.keyCode===i)for(var t=e.target;t;t=t.parentElement){var n=t.id&&document.querySelector('[aria-controls="'+t.id+'"]')||t;if("false"!==n.getAttribute(c)&&"true"===n.getAttribute(s))return e.preventDefault(),n.focus(),g(n,!1)}},!0),r(c,"click",function(e){var o=e.target;if(e.defaultPrevented)return!1;for(var t=o,n=void 0;t;t=t.parentElement){var r=n&&t.id&&document.querySelector("["+c+'][aria-controls="'+t.id+'"]');if("BUTTON"!==t.nodeName&&"A"!==t.nodeName||t.hasAttribute(c)||(n=t),r){a(r,"toggle.select",{relatedTarget:p(r),currentTarget:n,value:n.textContent.trim()});break}}l("["+c+"]").forEach(function(e){var t="true"===e.getAttribute(s),n="false"!==e.getAttribute(c),r=p(e);e.contains(o)?g(e,!t):n&&t&&g(e,r.contains(o))})}),function(e,t){var i="object"==typeof t?t:{open:t};return n&&(document.documentElement.style.cursor="pointer"),l(e).map(function(e){var t="true"===e.getAttribute(s),n="boolean"==typeof i.open?i.open:"toggle"===i.open?!t:t,r=String((i.hasOwnProperty("popup")?i.popup:e.getAttribute(c))||!1),o=p(e);return i.value&&(e.innerHTML=i.value),e.setAttribute(c,r),e.setAttribute("aria-label",e.textContent+", "+r.replace(/^true|false$/,"")),e.setAttribute("aria-controls",o.id=o.id||d()),o.setAttribute(f+"-labelledby",e.id=e.id||d()),g(e,n),e})}});

/*! @nrk/core-input v1.3.0 - Copyright (c) 2017-2019 NRK */
!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e=e||self).coreInput=t()}(this,function(){"use strict";var e="undefined"!=typeof window,a=(e&&/(android)/i.test(navigator.userAgent),e&&/iPad|iPhone|iPod/.test(String(navigator.platform))),i=function(e){void 0===e&&(e=!1);try{window.addEventListener("test",null,{get passive(){e=!0}})}catch(e){}return e}();function t(e,t,n,r){(void 0===r&&(r=!1),"undefined"==typeof window||window[e=e+"-"+t])||(i||"object"!=typeof r||(r=Boolean(r.capture)),("resize"===t||"load"===t?window:document).addEventListener(window[e]=t,n,r))}var n={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","/":"&#x2F;","'":"&#x27;"};function r(e){return String(e||"").replace(/[&<>"'/]/g,function(e){return n[e]})}var u="prevent_recursive_dispatch_maximum_callstack";function c(e,t,n){void 0===n&&(n={});var r,i=""+u+t;if(e[i])return!0;e[i]=!0,"function"==typeof window.CustomEvent?r=new window.CustomEvent(t,{bubbles:!0,cancelable:!0,detail:n}):(r=document.createEvent("CustomEvent")).initCustomEvent(t,!0,!0,n);var o=e.dispatchEvent(r);return e[i]=null,o}function d(e,t){if(void 0===t&&(t=document),e){if(e.nodeType)return[e];if("string"==typeof e)return[].slice.call(t.querySelectorAll(e));if(e.length)return[].slice.call(e)}return[]}var l="data-@nrk/core-input-1.3.0".replace(/\W+/g,"-"),s=13,f=27,p=33,v=34,m=35,b=36,g=38,y=40,w='[tabindex="-1"]',o=500;function x(e,t){var i="object"==typeof t?t:{content:t},o="string"==typeof i.content;return d(e).map(function(e){var t=e.nextElementSibling,n=void 0===i.ajax?e.getAttribute(l):i.ajax,r=void 0===i.open?e===document.activeElement:i.open;return e.setAttribute(l,n||""),e.setAttribute(a?"data-role":"role","combobox"),e.setAttribute("aria-autocomplete","list"),e.setAttribute("autocomplete","off"),o&&(t.innerHTML=i.content),d("a,button",t).forEach(C),A(e,r),e})}function h(a){a.ctrlKey||a.altKey||a.metaKey||a.defaultPrevented||d("["+l+"]").forEach(function(e){var t,n,r=e.nextElementSibling,i=e===a.target||r.contains(a.target),o="click"===a.type&&i&&d(w,r).filter(function(e){return e.contains(a.target)})[0];o?(t=e,n={relatedTarget:r,currentTarget:o,value:o.value||o.textContent.trim()},c(t,"input.select",n)&&(t.value=n.value,t.focus(),A(t,!1))):A(e,i)})}function E(e,t){var n=e.nextElementSibling,r=[e].concat(d(w+":not([hidden])",n)),i=t.keyCode===f&&"true"===e.getAttribute("aria-expanded"),o=r.indexOf(document.activeElement),a=!1;t.keyCode===y?a=r[o+1]||r[0]:t.keyCode===g?a=r[o-1]||r.pop():n.contains(t.target)&&(t.keyCode===m||t.keyCode===v?a=r.pop():t.keyCode===b||t.keyCode===p?a=r[1]:t.keyCode!==s&&e.focus()),A(e,t.keyCode!==f),(!1!==a||i)&&t.preventDefault(),a&&a.focus()}function A(e,t){var n;void 0===t&&(t="true"===e.getAttribute("aria-expanded")),n=function(){e.nextElementSibling[t?"removeAttribute":"setAttribute"]("hidden",""),e.setAttribute("aria-expanded",t)},(window.requestAnimationFrame||window.setTimeout)(n)}function C(e,t,n){e.setAttribute("aria-label",e.textContent.trim()+", "+(t+1)+" av "+n.length),e.setAttribute("tabindex","-1"),e.setAttribute("type","button")}function k(e){var t=e.getAttribute(l),n=k.xhr=k.xhr||new window.XMLHttpRequest;if(!t)return!1;clearTimeout(k.timer),n.abort(),n.onload=function(){try{n.responseJSON=JSON.parse(n.responseText)}catch(e){n.responseJSON=!1}c(e,"input.ajax",n)},k.timer=setTimeout(function(){e.value&&c(e,"input.ajax.beforeSend",n)&&(n.open("GET",t.replace("{{value}}",window.encodeURIComponent(e.value)),!0),n.setRequestHeader("X-Requested-With","XMLHttpRequest"),n.send())},o)}return x.escapeHTML=r,x.highlight=function(e,t){var n=t.replace(/[-/\\^$*+?.()|[\]{}]/g,"\\$&");return r(e).replace(new RegExp(n||".^","gi"),"<mark>$&</mark>")},t(l,"click",h),t(l,"focus",h,!0),t(l,"input",function(e){var o,t,n=e.target;n.hasAttribute(l)&&(t={relatedTarget:(o=n).nextElementSibling},c(o,"input.filter",t)&&!1===k(o)&&d(w,o.nextElementSibling).reduce(function(e,t){var n="LI"===t.parentElement.nodeName&&t.parentElement,r=-1!==t.textContent.toLowerCase().indexOf(o.value.toLowerCase()),i=r?"removeAttribute":"setAttribute";return n&&n[i]("hidden",""),t[i]("hidden",""),r?e.concat(t):e},[]).forEach(C))}),t(l,"keydown",function(e){if(!(e.ctrlKey||e.altKey||e.metaKey)){if(e.target.hasAttribute&&e.target.hasAttribute(l))return E(e.target,e);for(var t=e.target,n=void 0;t;t=t.parentElement)if((n=t.previousElementSibling)&&n.hasAttribute(l))return E(n,e)}},!0),x});

/* https://github.com/GoogleChromeLabs/quicklink - v1.0.0 */
if (window.IntersectionObserver) !function(e,n){"object"==typeof exports&&"undefined"!=typeof module?module.exports=n():"function"==typeof define&&define.amd?define(n):e.quicklink=n()}(this,function(){var e={};function n(e){return new Promise(function(n,t){var r=new XMLHttpRequest;r.open("GET",e,r.withCredentials=!0),r.onload=function(){200===r.status?n():t()},r.send()})}var t,r,i=(t="prefetch",((r=document.createElement("link")).relList||{}).supports&&r.relList.supports(t)?function(e){return new Promise(function(n,t){var r=document.createElement("link");r.rel="prefetch",r.href=e,r.onload=n,r.onerror=t,document.head.appendChild(r)})}:n);function o(t,r,o){if(!(e[t]||(o=navigator.connection)&&((o.effectiveType||"").includes("2g")||o.saveData)))return(r?function(e){return null==self.fetch?n(e):fetch(e,{credentials:"include"})}:i)(t).then(function(){e[t]=!0})}var u=u||function(e){var n=Date.now();return setTimeout(function(){e({didTimeout:!1,timeRemaining:function(){return Math.max(0,50-(Date.now()-n))}})},1)},c=new Set,f=new IntersectionObserver(function(e){e.forEach(function(e){if(e.isIntersecting){var n=e.target.href;c.has(n)&&a(n)}})});function a(e){c.delete(e),o(new URL(e,location.href).toString(),f.priority)}return function(e){e=Object.assign({timeout:2e3,priority:!1,timeoutFn:u,el:document},e),f.priority=e.priority;var n=e.origins||[location.hostname],t=e.ignores||[];e.timeoutFn(function(){e.urls?e.urls.forEach(a):Array.from(e.el.querySelectorAll("a"),function(e){f.observe(e),n.length&&!n.includes(e.hostname)||function e(n,t){return Array.isArray(t)?t.some(function(t){return e(n,t)}):(t.test||t).call(t,n.href,n)}(e,t)||c.add(e.href)})},{timeout:e.timeout})}});
if (window.IntersectionObserver) window.quicklink({ ignores: function (uri, el) { return el.rel === 'bookmark' } })

//External links in new window, Animate internal
document.addEventListener('click', function (event) {
  for (var el = event.target; el; el = el.parentElement) if (el.hostname) break
  if (event.metaKey || event.ctrlKey || event.defaultPrevented || !el || el.target || el.hash) return
  if(el.hostname !== location.hostname || el.pathname.indexOf('.') > 0){
    if (window.coreAnalytics) window.coreAnalytics('event', { ga: ['Click', 'Outbound', el.href] })
    window.open(el.href)
    return event.preventDefault()
  }
});


/* FAQ
------------------------------------------------------------------------ */
coreToggle('.wp-block-nrk-faq > a')

// Scroll into view on load
window.addEventListener('load', function () {
  var el = document.querySelector('.wp-block-nrk-faq > a[href="' + window.location.href + '"]')
  if (el) el.scrollIntoView()
})

// Forward spacebar to click so FAQ toggles behave like buttons
document.addEventListener('keydown', function (event) {
  if (event.keyCode === 32 && event.target.getAttribute && event.target.getAttribute('role') === 'button') {
    event.preventDefault() // Prevent scroll
    event.target.click()
  }
})

// Close FAQ on back
window.addEventListener('popstate', function (event) {
  if (window._faq) coreToggle(window._faq.pop(), 'toggle')
  if (window.coreAnalytics) window.coreAnalytics('pageview')
})

// Do not navigate away when purely a bookmark link
document.addEventListener('click', function (event) {
  for (var el = event.target; el; el = el.parentElement) if (el.rel === 'bookmark') {
    event.preventDefault()
    var href = event.target.href
    var text = event.target.textContent
    var path = window._faq = window._faq || [] // Store order of opened faqs
    if (href !== window.location.href) window.history.pushState(path.push(event.target), text, href)
    if (window.coreAnalytics) window.coreAnalytics('pageview')
  }
})


/* Search
------------------------------------------------------------------------ */
;[].forEach.call(document.querySelectorAll('.search-field'), function (input) {
  input.insertAdjacentHTML('afterend', '<div class="search-hits" hidden></div>')
  input.form.addEventListener('submit', function (event) { event.preventDefault(); coreInput(input, { open: true }) })
  coreInput(input, { ajax: input.form.action + '?s={{value}}&ts=' + Date.now() })
});

document.addEventListener('input.filter', function (event) {
  var value = event.target.value.trim()
  coreInput(event.target, value ? '<ul><li>Søker etter <strong>' + coreInput.escapeHTML(value) + '</strong>&hellip;</li></ul>' : '')
})

document.addEventListener('input.select', function (event) {
  event.preventDefault()
  coreInput(event.target, { open: false })
})

document.addEventListener('input.ajax', function (event) {
  coreInput(event.target, event.detail.responseText)
  trackMissing(event.target.form)
  if (window.coreAnalytics) window.coreAnalytics('pageview', {
    title: 'Søk etter "' + event.target.value + '"',
    page: '/?s=' +  encodeURIComponent(event.target.value)
  })
})

function trackMissing (context) {
  var el = (context || document).querySelector('[data-missing]')
  if (window.coreAnalytics && el) window.coreAnalytics('event', { ga: ['Search', 'Missing', el.getAttribute('data-missing')] })
}

trackMissing()


/* Grid
------------------------------------------------------------------------ */
coreToggle('.wp-block-nrk-grid button', { popup: true })
document.addEventListener('toggle', function (event) {
  if (!event.target.hasAttribute('data-grid-popup')) return
  var pop = event.detail.relatedTarget
  var btn = event.target.getBoundingClientRect()
  var minX = -btn.left + 10
  var midX = (btn.width - pop.offsetWidth) / 2
  var maxX = window.innerWidth - (btn.left + pop.offsetWidth) - 10
  var isOver = btn.top - pop.offsetHeight > 0
  var x = Math.max(minX, Math.min(midX, maxX))
  var y = isOver ? -btn.height - pop.offsetHeight : 0

  pop.style.left = 'auto' // Reset position
  pop.style.margin = Math.round(y) + 'px ' + Math.round(x) + 'px'
  pop.style.transformOrigin = Math.round((x - (btn.width / 2)) * -1) + 'px ' + (isOver ? 100 : 0) + '%'
})
