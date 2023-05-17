(()=>{"use strict";function e(e){var t=new URL(e);return t.pathname+t.search}function t(t){var r=e(window.location.href),n=e(t);r!==n&&window.history.replaceState({},"",n)}function r(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){var r=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null==r)return;var n,i,o=[],a=!0,u=!1;try{for(r=r.call(e);!(a=(n=r.next()).done)&&(o.push(n.value),!t||o.length!==t);a=!0);}catch(e){u=!0,i=e}finally{try{a||null==r.return||r.return()}finally{if(u)throw i}}return o}(e,t)||function(e,t){if(!e)return;if("string"==typeof e)return n(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);"Object"===r&&e.constructor&&(r=e.constructor.name);if("Map"===r||"Set"===r)return Array.from(e);if("Arguments"===r||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return n(e,t)}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function n(e,t){(null==t||t>e.length)&&(t=e.length);for(var r=0,n=new Array(t);r<t;r++)n[r]=e[r];return n}function i(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}var o=function(){function e(t){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.el=t,this.updateId()}var n,o,a;return n=e,o=[{key:"getEl",value:function(){return document.querySelector('[x-wire="'.concat(this.id,'"]'))||this.el}},{key:"updateId",value:function(){this.id=this.el.getAttribute("x-wire")}},{key:"updateData",value:function(){var e=this,t=this.getEl();if(t.getAttribute("x-wire-data")){this.data=this.data||window.Alpine.reactive({});var n=JSON.parse(t.getAttribute("x-wire-data"));Object.entries(n).forEach((function(t){var n=r(t,2),i=n[0],o=n[1];e.data[i]=o})),t.removeAttribute("x-wire-data"),this.onDataUpdated()}}},{key:"onDataUpdated",value:function(){this.logErrors()}},{key:"logErrors",value:function(){this.data.serverMemo.errors.forEach((function(e){console.error(e)}))}},{key:"$wire",get:function(){var e=this;return new Proxy({},{get:function(t,r){return e.get(r)},set:function(t,r,n){return e.set(r,n)}})}},{key:"hasPropertyValue",value:function(e){return e in this.data.serverMemo.data}},{key:"getPropertyValue",value:function(e){return this.data.serverMemo.data[e]}},{key:"hasPublicMethod",value:function(e){return this.data.serverMemo.methods.includes(e)}},{key:"call",value:function(e){var r=this;return function(){r.data.loading=e;var n={fingerprint:r.data.fingerprint,serverMemo:r.data.serverMemo,path:window.location.href,call:{method:e,arguments:Array.prototype.slice.call(arguments)}};fetch("/wp-json/wire/v1/wire",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify(n)}).then((function(e){return e.json()})).then((function(e){e&&e.path&&t(e.path),e&&e.html&&window.Alpine.morph(r.getEl(),e.html)}))}}},{key:"get",value:function(e){return this.hasPropertyValue(e)?this.getPropertyValue(e):this.hasPublicMethod(e)?this.call(e):"$refresh"===e?this.call("dollarRefresh"):"$set"===e?this.call("dollarSet"):"$loading"===e?this.data.loading:null}},{key:"set",value:function(e,t){return e in this.data.serverMemo.data&&(this.data.serverMemo.data[e]=t),!0}}],o&&i(n.prototype,o),a&&i(n,a),Object.defineProperty(n,"prototype",{writable:!1}),e}(),a=new Proxy({},{get:function(){return function(){return a}}});function u(e,t){for(var r=0;r<t.length;r++){var n=t[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}(function(){function e(){var t=this;!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),this.components=new Map,this.forceDataDirectiveToBody(),this.updatePath(),window.Alpine?this.register():document.addEventListener("alpine:init",(function(){t.register()}))}var r,n,i;return r=e,i=[{key:"create",value:function(){return new e}}],(n=[{key:"register",value:function(){this.registerWireDirective(),this.registerWireDataDirective(),this.registerWireMacicProperty(),this.validate()}},{key:"updatePath",value:function(){window.LIVEWIRE_PATH&&t(window.LIVEWIRE_PATH)}},{key:"validate",value:function(){setTimeout((function(){window.Alpine.morph||console.error('Plugin "morph" is not included. Find out more here: https://alpinejs.dev/plugins/morph')}),1)}},{key:"createComponent",value:function(e,t){this.components.has(e)||this.components.set(e,new o(t))}},{key:"registerWireDirective",value:function(){var e=this;window.Alpine.directive("wire",(function(t,r){var n=r.expression;e.createComponent(n,t)}))}},{key:"registerWireDataDirective",value:function(){var e=this;window.Alpine.directive("wire-data",(function(t){var r=function(){var r=t.getAttribute("x-wire");e.components.has(r)||e.createComponent(r,t),e.components.get(r).updateData()};r(),setTimeout((function(){r()}),10)}))}},{key:"registerWireMacicProperty",value:function(){var e=this;window.Alpine.magic("wire",(function(t){if(t.__$wire)return t.__$wire;var r=t.closest("[x-wire]");if(!r)return console.error('Alpine: Cannot reference "$wire" outside a Livewire component.'),a;var n=r.getAttribute("x-wire");return e.components.has(n)?t.__$wire=e.components.get(n).$wire:(console.error('Alpine: Cannot reference "$wire" for Livewire component with id '.concat(n,".")),a)}))}},{key:"forceDataDirectiveToBody",value:function(){var e=this;document.body?document.body.hasAttribute("x-data")||document.body.setAttribute("x-data",""):setTimeout((function(){e.forceDataDirectiveToBody()}))}}])&&u(r.prototype,n),i&&u(r,i),Object.defineProperty(r,"prototype",{writable:!1}),e})().create()})();