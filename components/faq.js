/*! @nrk/core-input v1.3.0 - Copyright (c) 2017-2019 NRK */
!function(e,t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define(t):(e=e||self).coreInput=t()}(this,function(){"use strict";var e="undefined"!=typeof window,a=(e&&/(android)/i.test(navigator.userAgent),e&&/iPad|iPhone|iPod/.test(String(navigator.platform))),i=function(e){void 0===e&&(e=!1);try{window.addEventListener("test",null,{get passive(){e=!0}})}catch(e){}return e}();function t(e,t,n,r){(void 0===r&&(r=!1),"undefined"==typeof window||window[e=e+"-"+t])||(i||"object"!=typeof r||(r=Boolean(r.capture)),("resize"===t||"load"===t?window:document).addEventListener(window[e]=t,n,r))}var n={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","/":"&#x2F;","'":"&#x27;"};function r(e){return String(e||"").replace(/[&<>"'/]/g,function(e){return n[e]})}var u="prevent_recursive_dispatch_maximum_callstack";function c(e,t,n){void 0===n&&(n={});var r,i=""+u+t;if(e[i])return!0;e[i]=!0,"function"==typeof window.CustomEvent?r=new window.CustomEvent(t,{bubbles:!0,cancelable:!0,detail:n}):(r=document.createEvent("CustomEvent")).initCustomEvent(t,!0,!0,n);var o=e.dispatchEvent(r);return e[i]=null,o}function d(e){(window.requestAnimationFrame||window.setTimeout)(e)}function l(e,t){if(void 0===t&&(t=document),e){if(e.nodeType)return[e];if("string"==typeof e)return[].slice.call(t.querySelectorAll(e));if(e.length)return[].slice.call(e)}return[]}var f="data-@nrk/core-input-1.3.0".replace(/\W+/g,"-"),s=13,p=27,v=33,m=34,b=35,g=36,y=38,w=40,x='[tabindex="-1"]',o=500;function h(e,t){var i="object"==typeof t?t:{content:t},o="string"==typeof i.content;return l(e).map(function(e){var t=e.nextElementSibling,n=void 0===i.ajax?e.getAttribute(f):i.ajax,r=void 0===i.open?e===document.activeElement:i.open;return e.setAttribute(f,n||""),e.setAttribute(a?"data-role":"role","combobox"),e.setAttribute("aria-autocomplete","list"),e.setAttribute("autocomplete","off"),o&&(t.innerHTML=i.content),l("a,button",t).forEach(k),C(e,r),e})}function E(a){a.ctrlKey||a.altKey||a.metaKey||a.defaultPrevented||l("["+f+"]").forEach(function(e){var t,n,r=e.nextElementSibling,i=e===a.target||r.contains(a.target),o="click"===a.type&&i&&l(x,r).filter(function(e){return e.contains(a.target)})[0];o?(t=e,n={relatedTarget:r,currentTarget:o,value:o.value||o.textContent.trim()},c(t,"input.select",n)&&(t.value=n.value,t.focus(),d(function(){return C(t,!1)}))):C(e,i)})}function A(e,t){var n=e.nextElementSibling,r=[e].concat(l(x+":not([hidden])",n)),i=t.keyCode===p&&"true"===e.getAttribute("aria-expanded"),o=r.indexOf(document.activeElement),a=!1;t.keyCode===w?a=r[o+1]||r[0]:t.keyCode===y?a=r[o-1]||r.pop():n.contains(t.target)&&(t.keyCode===b||t.keyCode===m?a=r.pop():t.keyCode===g||t.keyCode===v?a=r[1]:t.keyCode!==s&&e.focus()),C(e,t.keyCode!==p),(!1!==a||i)&&t.preventDefault(),a&&a.focus()}function C(e,t){void 0===t&&(t="true"===e.getAttribute("aria-expanded")),d(function(){e.nextElementSibling[t?"removeAttribute":"setAttribute"]("hidden",""),e.setAttribute("aria-expanded",t)})}function k(e,t,n){e.setAttribute("aria-label",e.textContent.trim()+", "+(t+1)+" av "+n.length),e.setAttribute("tabindex","-1"),e.setAttribute("type","button")}function S(e){var t=e.getAttribute(f),n=S.xhr=S.xhr||new window.XMLHttpRequest;if(!t)return!1;clearTimeout(S.timer),n.abort(),n.onload=function(){try{n.responseJSON=JSON.parse(n.responseText)}catch(e){n.responseJSON=!1}c(e,"input.ajax",n)},S.timer=setTimeout(function(){e.value&&c(e,"input.ajax.beforeSend",n)&&(n.open("GET",t.replace("{{value}}",window.encodeURIComponent(e.value)),!0),n.setRequestHeader("X-Requested-With","XMLHttpRequest"),n.send())},o)}return h.escapeHTML=r,h.highlight=function(e,t){var n=t.replace(/[-/\\^$*+?.()|[\]{}]/g,"\\$&");return r(e).replace(new RegExp(n||".^","gi"),"<mark>$&</mark>")},t(f,"click",E),t(f,"focus",E,!0),t(f,"input",function(e){var o,t,n=e.target;n.hasAttribute(f)&&(t={relatedTarget:(o=n).nextElementSibling},c(o,"input.filter",t)&&!1===S(o)&&l(x,o.nextElementSibling).reduce(function(e,t){var n="LI"===t.parentElement.nodeName&&t.parentElement,r=-1!==t.textContent.toLowerCase().indexOf(o.value.toLowerCase()),i=r?"removeAttribute":"setAttribute";return n&&n[i]("hidden",""),t[i]("hidden",""),r?e.concat(t):e},[]).forEach(k))}),t(f,"keydown",function(e){if(!(e.ctrlKey||e.altKey||e.metaKey)){if(e.target.hasAttribute&&e.target.hasAttribute(f))return A(e.target,e);for(var t=e.target,n=void 0;t;t=t.parentElement)if((n=t.previousElementSibling)&&n.hasAttribute(f))return A(n,e)}},!0),h});

wp.blocks.registerBlockType('nrk/faq', {
  title: 'Løsning',
  icon: 'lightbulb',
  category: 'common',
  attributes: { faq: { type: 'string' } },
  supports: { customClassName: false, html: false, reusable: false },
  edit: function (props) {
    var el = wp.element.createElement
    var faq = props.attributes.faq

    return faq ? el(wp.components.ServerSideRender, {
      block: 'nrk/faq',
      attributes: props.attributes
    }) : el(wp.components.Placeholder, {
      icon: 'lightbulb',
      label: 'Løsning'
    },
    el('div', null,
      el('input', {
        type: 'text',
        className: 'nrk-faq-input components-text-control__input',
        placeholder: 'Søk etter løsning...',
        ref: nrkFaqInput,
        onClick: function (event) {
          if (event.target.faq) props.setAttributes({ faq: event.target.faq })
        }
      }),
      el('ul', { className: 'nrk-faq-hits' }),
      el('div', null, 'eller'),
      el(wp.components.Button, {
        isLarge: true,
        isDefault: true,
        href: 'post-new.php?post_type=faq',
        target: '_blank'
      }, 'Opprett ny løsning')
    ))
  },
  save: function () {
    return null; // Rendered in PHP
  }
})

document.addEventListener('input.filter', nrkFaqFilter)
document.addEventListener('input.select', nrkFaqSelect)
document.addEventListener('input.ajax.beforeSend', nrkFaqFetch)

function nrkFaqFilter (event) {
  if (!event.target.classList.contains('nrk-faq-input')) return
  coreInput(event.target, event.target.value && '<li>Søker...</li>')
}

function nrkFaqSelect (event) {
  if (!event.target.classList.contains('nrk-faq-input')) return
  event.target.faq = event.detail.currentTarget.getAttribute('data-faq')
  event.target.click() // Trigger setAttributes
}

function nrkFaqFetch (event) {
  if (!event.target.classList.contains('nrk-faq-input')) return
  var search = encodeURIComponent(event.target.value)
  event.preventDefault()
  wp.apiFetch({ path: '/wp/v2/search?subtype=faq&per_page=10&search=' + search + '&ts=' + Date.now() })
    .then(function (posts) { coreInput(event.target, posts.map(nrkFaqHit).join('') || '<li>Ingen treff</li>') })
}

function nrkFaqInput (input) {
  if (input) coreInput(input, { ajax: true })
}

function nrkFaqHit (post) {
  return '<li><button data-faq="' + coreInput.escapeHTML(post.id) + '">' + post.title + '</button></li>'
}
