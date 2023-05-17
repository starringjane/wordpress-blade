/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*****************************!*\
  !*** ./src/scripts/wire.js ***!
  \*****************************/
function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }
function _iterableToArrayLimit(arr, i) { var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"]; if (_i == null) return; var _arr = []; var _n = true; var _d = false; var _s, _e; try { for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }
function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
var components = new Map();
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
            updatePathFromUrl(response.path);
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
var Livewire = /*#__PURE__*/function () {
  function Livewire() {
    var _this2 = this;
    _classCallCheck(this, Livewire);
    this.forceDataDirectiveToBody();
    this.updatePath();
    document.addEventListener('alpine:init', function () {
      _this2.registerWireDirective();
      _this2.registerWireMacicProperty();
      _this2.validate();
    });
  }
  _createClass(Livewire, [{
    key: "updatePath",
    value: function updatePath() {
      if (window.LIVEWIRE_PATH) {
        updatePathFromUrl(window.LIVEWIRE_PATH);
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
      window.Alpine.directive('wire', function (el, _ref3) {
        var expression = _ref3.expression;
        var id = expression;
        if (!components.has(id)) {
          components.set(id, new Component(el));
        }
      });
      window.Alpine.directive('wire-data', function (el, _ref4) {
        var expression = _ref4.expression;
        var id = el.getAttribute('x-wire');
        if (components.has(id)) {
          components.get(id).updateData();

          // Try a second time, because sometimes the directive was not loaded yet
          setTimeout(function () {
            components.get(id).updateData();
          }, 10);
        }
      });
    }
  }, {
    key: "registerWireMacicProperty",
    value: function registerWireMacicProperty() {
      window.Alpine.magic('wire', function (el) {
        var wireEl = el.closest('[x-wire]');
        var id = wireEl.getAttribute('x-wire');
        if (!wireEl) {
          console.error('Alpine: Cannot reference "$wire" outside a Livewire component.');
          return null;
        }
        ;
        if (!components.has(id)) {
          console.error("Alpine: Cannot reference \"$wire\" for Livewire component with id ".concat(id, "."));
          return null;
        }
        ;
        return components.get(id).$wire;
      });
    }
  }, {
    key: "forceDataDirectiveToBody",
    value: function forceDataDirectiveToBody() {
      var _this3 = this;
      if (!document.body) {
        setTimeout(function () {
          _this3.forceDataDirectiveToBody();
        });
        return;
      }
      if (!document.body.hasAttribute('x-data')) {
        document.body.setAttribute('x-data', '');
      }
    }
  }]);
  return Livewire;
}();
new Livewire();
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
/******/ })()
;