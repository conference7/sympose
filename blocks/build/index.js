/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/components/list.js":
/*!********************************!*\
  !*** ./src/components/list.js ***!
  \********************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./../index.js */ "./src/index.js");

// src/index.js






Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__["registerBlockType"])('sympose/list', {
  title: 'Sympose List',
  icon: 'list-view',
  category: 'sympose',
  attributes: {
    type: {
      type: 'string',
      default: 'all'
    },
    category: {
      type: 'string',
      default: 'all'
    },
    align: {
      type: 'string',
      default: 'left'
    },
    name: {
      type: 'boolean',
      default: true
    }
  },
  edit: function edit(props) {
    var categoryList = [];
    categoryList.push({
      label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Select a category'),
      value: 'all'
    });
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5___default()({
      path: '/wp/v2/' + props.attributes.type + '-category?parent=0'
    }).then(function (data) {
      data.map(function (item) {
        categoryList.push({
          label: item.name,
          value: item.slug
        });
        _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5___default()({
          path: '/wp/v2/' + props.attributes.type + '-category?parent=' + item.id
        }).then(function (children) {
          children.map(function (child) {
            categoryList.push({
              label: '-- ' + child.name,
              value: child.slug
            });
          });
        });
      });
    });
    var List = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__["withState"])({
      size: '50%'
    })(function (_ref) {
      var size = _ref.size,
          setState = _ref.setState;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
        className: "sympose-block sympose-list"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
        className: "logo"
      }, _index_js__WEBPACK_IMPORTED_MODULE_6__["Icon"]), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", {
        className: "title"
      }, "Sympose List"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SelectControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Post Type'),
        options: [{
          'label': Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('People'),
          'value': 'person'
        }, {
          'label': Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Organisations'),
          'value': 'organisation'
        }],
        value: props.attributes.type,
        onChange: function onChange(value) {
          return props.setAttributes({
            type: value
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SelectControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Category'),
        options: categoryList,
        value: props.attributes.category,
        onChange: function onChange(value) {
          return props.setAttributes({
            category: value
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SelectControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Alignment'),
        options: [{
          'label': Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Left'),
          'value': 'left'
        }, {
          'label': Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Center'),
          'value': 'center'
        }, {
          'label': Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Right'),
          'value': 'right'
        }],
        value: props.attributes.align,
        onChange: function onChange(value) {
          return props.setAttributes({
            align: value
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["CheckboxControl"], {
        label: "Show name",
        checked: props.attributes.name,
        onChange: function onChange(value) {
          return props.setAttributes({
            name: value
          });
        }
      }));
    });
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(List, null);
  },
  save: function save() {
    return null;
  }
});

/***/ }),

/***/ "./src/components/schedule.js":
/*!************************************!*\
  !*** ./src/components/schedule.js ***!
  \************************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/blocks */ "@wordpress/blocks");
/* harmony import */ var _wordpress_blocks__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _index_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./../index.js */ "./src/index.js");

// src/index.js







var eventList = [];
eventList.push({
  label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Select an event'),
  value: 'all'
});
_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5___default()({
  path: '/wp/v2/event?parent=0'
}).then(function (data) {
  data.map(function (item) {
    eventList.push({
      label: item.name,
      value: item.slug
    });
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_5___default()({
      path: '/wp/v2/event?parent=' + item.id
    }).then(function (children) {
      children.map(function (child) {
        eventList.push({
          label: '-- ' + child.name,
          value: child.slug
        });
      });
    });
  });
});
Object(_wordpress_blocks__WEBPACK_IMPORTED_MODULE_1__["registerBlockType"])('sympose/schedule', {
  title: 'Sympose Schedule',
  icon: 'calendar-alt',
  category: 'sympose',
  attributes: {
    event: {
      type: 'string',
      default: 'all'
    },
    read_more: {
      type: 'boolean',
      default: true
    },
    show_people: {
      type: 'boolean',
      default: true
    },
    show_organisations: {
      type: 'boolean',
      default: true
    },
    hide_title: {
      type: 'boolean',
      default: false
    }
  },
  edit: function edit(props) {
    var Schedule = Object(_wordpress_compose__WEBPACK_IMPORTED_MODULE_3__["withState"])({
      size: '50%'
    })(function (_ref) {
      var size = _ref.size,
          setState = _ref.setState;
      return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
        className: "sympose-block sympose-schedule-block"
      }, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("div", {
        className: "logo"
      }, _index_js__WEBPACK_IMPORTED_MODULE_6__["Icon"]), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])("p", {
        className: "title"
      }, "Sympose Schedule"), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["SelectControl"], {
        label: Object(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__["__"])('Select an event'),
        options: eventList,
        value: props.attributes.event,
        onChange: function onChange(value) {
          return props.setAttributes({
            event: value
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["CheckboxControl"], {
        label: "Show people",
        checked: props.attributes.show_people,
        onChange: function onChange(value) {
          return props.setAttributes({
            show_people: value
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["CheckboxControl"], {
        label: "Show organisations",
        checked: props.attributes.show_organisations,
        onChange: function onChange(value) {
          return props.setAttributes({
            show_organisations: value
          });
        }
      }), Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_components__WEBPACK_IMPORTED_MODULE_2__["CheckboxControl"], {
        label: "Hide schedule title",
        checked: props.attributes.hide_title,
        onChange: function onChange(value) {
          return props.setAttributes({
            hide_title: value
          });
        }
      }));
    });
    return Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(Schedule, null);
  },
  save: function save() {
    return null;
  }
});

/***/ }),

/***/ "./src/index.js":
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
/*! exports provided: Icon */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "Icon", function() { return Icon; });
/* harmony import */ var _components_schedule_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./components/schedule.js */ "./src/components/schedule.js");
/* harmony import */ var _components_list_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/list.js */ "./src/components/list.js");


var Icon = wp.element.createElement('svg', {
  width: 20,
  height: 20
}, wp.element.createElement('path', {
  d: "M17.07,2.93c-3.9-3.9-10.25-3.9-14.15,0c-3.43,3.43-3.84,8.74-1.25,12.63l1.96-3.25c0.41,0.86,0.96,1.67,1.68,2.38 c0.71,0.71,1.52,1.27,2.38,1.68l-3.25,1.96c3.89,2.59,9.2,2.18,12.63-1.25C20.98,13.17,20.98,6.83,17.07,2.93z M14.82,11.39 c-0.04,0.03-0.08,0.06-0.12,0.09c-0.07,0.05-0.13,0.1-0.2,0.15c-0.05,0.04-0.11,0.07-0.16,0.1c-0.06,0.03-0.11,0.07-0.17,0.1 c-0.06,0.04-0.13,0.07-0.2,0.1c-0.04,0.02-0.08,0.04-0.12,0.06c-0.4,0.19-0.83,0.31-1.27,0.37l0,0c-1.32,0.17-2.69-0.24-3.7-1.25 s-1.42-2.39-1.25-3.7l0,0C7.69,6.98,7.81,6.55,8,6.15c0.02-0.04,0.04-0.08,0.06-0.12c0.03-0.07,0.07-0.13,0.1-0.2 c0.03-0.06,0.07-0.11,0.1-0.17c0.03-0.05,0.07-0.11,0.1-0.16c0.05-0.07,0.1-0.14,0.15-0.2c0.03-0.04,0.06-0.08,0.09-0.12 c0.09-0.11,0.18-0.21,0.28-0.31c1.72-1.72,4.52-1.72,6.25,0s1.72,4.52,0,6.25C15.03,11.21,14.93,11.31,14.82,11.39z M9.22,15.43 c-1.06-0.33-2.06-0.91-2.91-1.75s-1.42-1.84-1.75-2.91l1.18-1.96L5.76,8.8c0.17,1.34,0.76,2.63,1.79,3.65s2.32,1.62,3.65,1.79 l-0.02,0.01L9.22,15.43z"
}));


(function () {
  wp.blocks.updateCategory('sympose', {
    icon: Icon
  });
})();

/***/ }),

/***/ "@wordpress/api-fetch":
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["apiFetch"]; }());

/***/ }),

/***/ "@wordpress/blocks":
/*!********************************!*\
  !*** external ["wp","blocks"] ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ })

/******/ });
//# sourceMappingURL=index.js.map