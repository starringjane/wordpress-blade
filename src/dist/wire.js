/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/scripts/wire/Component.js":
/*!***************************************!*\
  !*** ./src/scripts/wire/Component.js ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Component": () => (/* binding */ Component)
/* harmony export */ });
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils.js */ "./src/scripts/wire/utils.js");
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }

var Component = /*#__PURE__*/function () {
  function Component(el) {
    _classCallCheck(this, Component);
    this.el = el;
    this.updateId();
  }
  _createClass(Component, [{
    key: "getEl",
    value: function getEl() {
      return document.querySelector("[x-wire=\"".concat(this.id, "\"]")) || this.el;
    }
  }, {
    key: "updateId",
    value: function updateId() {
      this.id = this.el.getAttribute('x-wire');
    }
  }, {
    key: "updateData",
    value: function updateData() {
      var _this = this;
      var el = this.getEl();
      if (!el.getAttribute('x-wire-data')) {
        return;
      }
      this.data = this.data || window.Alpine.reactive({});
      var data = JSON.parse(el.getAttribute('x-wire-data'));
      Object.entries(data).forEach(function (_ref) {
        var _ref2 = _slicedToArray(_ref, 2),
          key = _ref2[0],
          value = _ref2[1];
        _this.data[key] = value;
      });
      el.removeAttribute('x-wire-data');
      this.onDataUpdated();
    }
  }, {
    key: "onDataUpdated",
    value: function onDataUpdated() {
      this.logErrors();
    }
  }, {
    key: "logErrors",
    value: function logErrors() {
      this.data.serverMemo.errors.forEach(function (error) {
        console.error(error);
      });
    }
  }, {
    key: "$wire",
    get: function get() {
      var $this = this;
      return new Proxy({}, {
        get: function get(_, prop) {
          return $this.get(prop);
        },
        set: function set(_, prop, value) {
          return $this.set(prop, value);
        }
      });
    }
  }, {
    key: "hasPropertyValue",
    value: function hasPropertyValue(prop) {
      return prop in this.data.serverMemo.data;
    }
  }, {
    key: "getPropertyValue",
    value: function getPropertyValue(prop) {
      return this.data.serverMemo.data[prop];
    }
  }, {
    key: "call",
    value: function call(method) {
      var $this = this;
      return function () {
        var payload = {
          fingerprint: $this.data.fingerprint,
          serverMemo: $this.data.serverMemo,
          path: window.location.href,
          call: {
            method: method,
            arguments: Array.prototype.slice.call(arguments)
          }
        };
        fetch('/wp-json/wire/v1/wire', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(payload)
        }).then(function (response) {
          return response.json();
        }).then(function (response) {
          if (response && response.path) {
            (0,_utils_js__WEBPACK_IMPORTED_MODULE_0__.updatePathFromUrl)(response.path);
          }
          if (response && response.html) {
            window.Alpine.morph($this.getEl(), response.html);
          }
        });
      };
    }
  }, {
    key: "get",
    value: function get(prop) {
      // Check of public property exists
      // Example: x-text="$wire.count"
      if (this.hasPropertyValue(prop)) {
        return this.getPropertyValue(prop);
      }

      // Asume method call
      // Example: @click="$wire.increaseCounter()"
      return this.call(prop);
    }
  }, {
    key: "set",
    value: function set(prop, value) {
      if (prop in this.data.serverMemo.data) {
        this.data.serverMemo.data[prop] = value;
      }
      return true;
    }
  }]);
  return Component;
}();
;

/***/ }),

/***/ "./src/scripts/wire/Livewire.js":
/*!**************************************!*\
  !*** ./src/scripts/wire/Livewire.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Livewire": () => (/* binding */ Livewire)
/* harmony export */ });
/* harmony import */ var _Component_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Component.js */ "./src/scripts/wire/Component.js");
/* harmony import */ var _utils_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./utils.js */ "./src/scripts/wire/utils.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }


var Livewire = /*#__PURE__*/function () {
  function Livewire() {
    var _this = this;
    _classCallCheck(this, Livewire);
    this.components = new Map();
    this.forceDataDirectiveToBody();
    this.updatePath();
    document.addEventListener('alpine:init', function () {
      _this.registerWireDirective();
      _this.registerWireDataDirective();
      _this.registerWireMacicProperty();
      _this.validate();
    });
  }
  _createClass(Livewire, [{
    key: "updatePath",
    value: function updatePath() {
      if (window.LIVEWIRE_PATH) {
        (0,_utils_js__WEBPACK_IMPORTED_MODULE_1__.updatePathFromUrl)(window.LIVEWIRE_PATH);
      }
    }
  }, {
    key: "validate",
    value: function validate() {
      setTimeout(function () {
        if (!window.Alpine.morph) {
          console.error('Plugin "morph" is not included. Find out more here: https://alpinejs.dev/plugins/morph');
        }
      }, 1);
    }
  }, {
    key: "registerWireDirective",
    value: function registerWireDirective() {
      var _this2 = this;
      window.Alpine.directive('wire', function (el, _ref) {
        var expression = _ref.expression;
        var id = expression;
        if (!_this2.components.has(id)) {
          _this2.components.set(id, new _Component_js__WEBPACK_IMPORTED_MODULE_0__.Component(el));
        }
      });
    }
  }, {
    key: "registerWireDataDirective",
    value: function registerWireDataDirective() {
      var _this3 = this;
      window.Alpine.directive('wire-data', function (el) {
        var updateData = function updateData() {
          var id = el.getAttribute('x-wire');
          if (_this3.components.has(id)) {
            _this3.components.get(id).updateData();
          }
        };
        updateData();

        // Try a second time, because sometimes the directive was not loaded yet
        setTimeout(function () {
          updateData();
        }, 10);
      });
    }
  }, {
    key: "registerWireMacicProperty",
    value: function registerWireMacicProperty() {
      var _this4 = this;
      window.Alpine.magic('wire', function (el) {
        var wireEl = el.closest('[x-wire]');
        var id = wireEl.getAttribute('x-wire');
        if (!wireEl) {
          console.error('Alpine: Cannot reference "$wire" outside a Livewire component.');
          return null;
        }
        ;
        if (!_this4.components.has(id)) {
          console.error("Alpine: Cannot reference \"$wire\" for Livewire component with id ".concat(id, "."));
          return null;
        }
        ;
        return _this4.components.get(id).$wire;
      });
    }
  }, {
    key: "forceDataDirectiveToBody",
    value: function forceDataDirectiveToBody() {
      var _this5 = this;
      if (!document.body) {
        setTimeout(function () {
          _this5.forceDataDirectiveToBody();
        });
        return;
      }
      if (!document.body.hasAttribute('x-data')) {
        document.body.setAttribute('x-data', '');
      }
    }
  }], [{
    key: "create",
    value: function create() {
      return new Livewire();
    }
  }]);
  return Livewire;
}();

/***/ }),

/***/ "./src/scripts/wire/utils.js":
/*!***********************************!*\
  !*** ./src/scripts/wire/utils.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "getPathFromUrl": () => (/* binding */ getPathFromUrl),
/* harmony export */   "updatePathFromUrl": () => (/* binding */ updatePathFromUrl)
/* harmony export */ });
function getPathFromUrl(url) {
  var urlObject = new URL(url);
  return urlObject.pathname + urlObject.search;
}
function updatePathFromUrl(url) {
  var currentPath = getPathFromUrl(window.location.href);
  var newPath = getPathFromUrl(url);
  if (currentPath !== newPath) {
    window.history.replaceState({}, '', newPath);
  }
}

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!*****************************!*\
  !*** ./src/scripts/wire.js ***!
  \*****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wire_Livewire_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./wire/Livewire.js */ "./src/scripts/wire/Livewire.js");

_wire_Livewire_js__WEBPACK_IMPORTED_MODULE_0__.Livewire.create();
})();

/******/ })()
;