/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/App.vue?vue&type=script&setup=true&lang=js":
/*!*************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/App.vue?vue&type=script&setup=true&lang=js ***!
  \*************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _views_TabMenu_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/views/TabMenu.vue */ \"./src/views/TabMenu.vue\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  __name: 'App',\n\n  setup(__props, {\n    expose: __expose\n  }) {\n    __expose();\n\n    const __returned__ = {\n      TabMenu: _views_TabMenu_vue__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n    };\n    Object.defineProperty(__returned__, '__isScriptSetup', {\n      enumerable: false,\n      value: true\n    });\n    return __returned__;\n  }\n\n});\n\n//# sourceURL=webpack://gugrades_ui/./src/App.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/TabsNav.vue?vue&type=script&setup=true&lang=js":
/*!****************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/TabsNav.vue?vue&type=script&setup=true&lang=js ***!
  \****************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _vue_runtime_core__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @vue/runtime-core */ \"./node_modules/@vue/reactivity/dist/reactivity.esm-bundler.js\");\n/* harmony import */ var _vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @vue/runtime-core */ \"./node_modules/@vue/runtime-core/dist/runtime-core.esm-bundler.js\");\n/* harmony import */ var vue_toastification__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-toastification */ \"./node_modules/vue-toastification/dist/index.mjs\");\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  __name: 'TabsNav',\n  emits: ['tabchange'],\n\n  setup(__props, {\n    expose: __expose,\n    emit\n  }) {\n    __expose();\n\n    const activetab = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_1__.ref)('capture');\n    const settingscapability = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_1__.ref)(false);\n    const mstrings = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.inject)('mstrings');\n    const toast = (0,vue_toastification__WEBPACK_IMPORTED_MODULE_0__.useToast)();\n    /**\n     * Detect change of tab and emit result to parent\n     * @param {} item\n     */\n\n    function clickTab(item) {\n      activetab.value = item;\n      emit('tabchange', item);\n    }\n    /**\n     * Check capability\n     */\n\n\n    (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.onMounted)(() => {\n      const GU = window.GU;\n      const courseid = GU.courseid;\n      const fetchMany = GU.fetchMany;\n      fetchMany([{\n        methodname: 'local_gugrades_has_capability',\n        args: {\n          courseid: courseid,\n          capability: 'local/gugrades:changesettings'\n        }\n      }])[0].then(result => {\n        settingscapability.value = result['hascapability'];\n      }).catch(error => {\n        window.console.error(error);\n        toast.error('Error communicating with server (see console)');\n      });\n    });\n    const __returned__ = {\n      activetab,\n      settingscapability,\n      mstrings,\n      toast,\n      emit,\n      clickTab,\n\n      get ref() {\n        return _vue_runtime_core__WEBPACK_IMPORTED_MODULE_1__.ref;\n      },\n\n      get defineEmits() {\n        return _vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.defineEmits;\n      },\n\n      get inject() {\n        return _vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.inject;\n      },\n\n      get onMounted() {\n        return _vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.onMounted;\n      },\n\n      get useToast() {\n        return vue_toastification__WEBPACK_IMPORTED_MODULE_0__.useToast;\n      }\n\n    };\n    Object.defineProperty(__returned__, '__isScriptSetup', {\n      enumerable: false,\n      value: true\n    });\n    return __returned__;\n  }\n\n});\n\n//# sourceURL=webpack://gugrades_ui/./src/components/TabsNav.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/TabMenu.vue?vue&type=script&setup=true&lang=js":
/*!***********************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/TabMenu.vue?vue&type=script&setup=true&lang=js ***!
  \***********************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @vue/runtime-core */ \"./node_modules/@vue/reactivity/dist/reactivity.esm-bundler.js\");\n/* harmony import */ var _vue_runtime_core__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @vue/runtime-core */ \"./node_modules/@vue/runtime-core/dist/runtime-core.esm-bundler.js\");\n/* harmony import */ var _components_TabsNav_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/components/TabsNav.vue */ \"./src/components/TabsNav.vue\");\n/* harmony import */ var vue_toastification__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue-toastification */ \"./node_modules/vue-toastification/dist/index.mjs\");\n\n //import CaptureTable from '@/components/CaptureTable.vue';\n//import SettingsPage from '@/views/SettingsPage.vue';\n//import AuditPage from '@/views/AuditPage.vue';\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  __name: 'TabMenu',\n\n  setup(__props, {\n    expose: __expose\n  }) {\n    __expose();\n\n    const currenttab = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.ref)('capture');\n    const level1category = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.ref)(0);\n    const showactivityselect = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.ref)(false);\n    const itemid = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.ref)(0);\n    const enabledashboard = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.ref)(false);\n    const toast = (0,vue_toastification__WEBPACK_IMPORTED_MODULE_1__.useToast)();\n    /**\n     * Capture change to capture/aggregate tab\n     * @param {*} tab\n     */\n\n    function tabChange(tab) {\n      currenttab.value = tab;\n      level1category.value = 0;\n      showactivityselect.value = false;\n      itemid.value = 0;\n    }\n    /**\n     * get enable dashboard and set logo\n     */\n\n\n    (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_3__.onMounted)(() => {\n      const GU = window.GU;\n      const courseid = GU.courseid;\n      const fetchMany = GU.fetchMany;\n      fetchMany([{\n        methodname: 'local_gugrades_get_settings',\n        args: {\n          courseid: courseid,\n          gradeitemid: 0\n        }\n      }])[0].then(settings => {\n        settings.forEach(setting => {\n          // TODO: Something a bit cleverer than this\n          if (setting.name == 'enabledashboard') {\n            enabledashboard.value = setting.value ? true : false;\n          }\n        }); // Bodge to get jQuery needed for Bootstrap JS.\n\n        const $ = window.jQuery;\n\n        if (enabledashboard.value) {\n          $('#mygradeslogo').css('filter', 'grayscale(0)');\n        } else {\n          $('#mygradeslogo').css('filter', 'grayscale(1)');\n        }\n      }).catch(error => {\n        window.console.error(error);\n        toast.error('Error communicating with server (see console)');\n      });\n    });\n    const __returned__ = {\n      currenttab,\n      level1category,\n      showactivityselect,\n      itemid,\n      enabledashboard,\n      toast,\n      tabChange,\n\n      get ref() {\n        return _vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.ref;\n      },\n\n      get onMounted() {\n        return _vue_runtime_core__WEBPACK_IMPORTED_MODULE_3__.onMounted;\n      },\n\n      TabsNav: _components_TabsNav_vue__WEBPACK_IMPORTED_MODULE_0__[\"default\"],\n\n      get useToast() {\n        return vue_toastification__WEBPACK_IMPORTED_MODULE_1__.useToast;\n      }\n\n    };\n    Object.defineProperty(__returned__, '__isScriptSetup', {\n      enumerable: false,\n      value: true\n    });\n    return __returned__;\n  }\n\n});\n\n//# sourceURL=webpack://gugrades_ui/./src/views/TabMenu.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/App.vue?vue&type=template&id=7ba5bd90":
/*!******************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/App.vue?vue&type=template&id=7ba5bd90 ***!
  \******************************************************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* binding */ render; }\n/* harmony export */ });\n/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ \"./node_modules/vue/dist/vue.runtime.esm-bundler.js\");\n\nfunction render(_ctx, _cache, $props, $setup, $data, $options) {\n  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)($setup[\"TabMenu\"]);\n}\n\n//# sourceURL=webpack://gugrades_ui/./src/App.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/templateLoader.js??ruleSet%5B1%5D.rules%5B3%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/TabsNav.vue?vue&type=template&id=5802a776":
/*!*********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/TabsNav.vue?vue&type=template&id=5802a776 ***!
  \*********************************************************************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* binding */ render; }\n/* harmony export */ });\n/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ \"./node_modules/vue/dist/vue.runtime.esm-bundler.js\");\n\nconst _hoisted_1 = {\n  class: \"nav nav-pills mb-4 border-bottom\"\n};\nconst _hoisted_2 = {\n  class: \"nav-item\"\n};\n\nconst _hoisted_3 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"i\", {\n  class: \"fa fa-download\",\n  \"aria-hidden\": \"true\"\n}, null, -1\n/* HOISTED */\n);\n\nconst _hoisted_4 = {\n  class: \"nav-item\"\n};\n\nconst _hoisted_5 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"i\", {\n  class: \"fa fa-exchange\",\n  \"aria-hidden\": \"true\"\n}, null, -1\n/* HOISTED */\n);\n\nconst _hoisted_6 = {\n  class: \"nav-item\"\n};\n\nconst _hoisted_7 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"i\", {\n  class: \"fa fa-compress\",\n  \"aria-hidden\": \"true\"\n}, null, -1\n/* HOISTED */\n);\n\nconst _hoisted_8 = {\n  class: \"nav-item\"\n};\n\nconst _hoisted_9 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"i\", {\n  class: \"fa fa-history\",\n  \"aria-hidden\": \"true\"\n}, null, -1\n/* HOISTED */\n);\n\nconst _hoisted_10 = {\n  key: 0,\n  class: \"nav-item\"\n};\n\nconst _hoisted_11 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"i\", {\n  class: \"fa fa-cog\",\n  \"aria-hidden\": \"true\"\n}, null, -1\n/* HOISTED */\n);\n\nfunction render(_ctx, _cache, $props, $setup, $data, $options) {\n  const _component_router_link = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)(\"router-link\");\n\n  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(\"ul\", _hoisted_1, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"li\", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_router_link, {\n    to: \"/\",\n    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)([\"nav-link btn btn-secondary\", {\n      active: $setup.activetab == 'capture'\n    }]),\n    onClick: _cache[0] || (_cache[0] = $event => $setup.clickTab('capture'))\n  }, {\n    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [_hoisted_3, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(\"  \" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.mstrings.assessmentgradecapture), 1\n    /* TEXT */\n    )]),\n    _: 1\n    /* STABLE */\n\n  }, 8\n  /* PROPS */\n  , [\"class\"])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"li\", _hoisted_4, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_router_link, {\n    to: \"/conversion\",\n    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)([\"nav-link btn btn-secondary\", {\n      active: $setup.activetab == 'conversion'\n    }]),\n    onClick: _cache[1] || (_cache[1] = $event => $setup.clickTab('conversion'))\n  }, {\n    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [_hoisted_5, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(\"  \" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.mstrings.conversion), 1\n    /* TEXT */\n    )]),\n    _: 1\n    /* STABLE */\n\n  }, 8\n  /* PROPS */\n  , [\"class\"])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"li\", _hoisted_6, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_router_link, {\n    to: \"/aggregation\",\n    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)([\"nav-link btn btn-secondary\", {\n      active: $setup.activetab == 'aggregate'\n    }]),\n    onClick: _cache[2] || (_cache[2] = $event => $setup.clickTab('aggregate'))\n  }, {\n    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [_hoisted_7, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(\"  \" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.mstrings.coursegradeaggregation), 1\n    /* TEXT */\n    )]),\n    _: 1\n    /* STABLE */\n\n  }, 8\n  /* PROPS */\n  , [\"class\"])]), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"li\", _hoisted_8, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_router_link, {\n    to: \"/audit\",\n    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)([\"nav-link btn btn-secondary\", {\n      active: $setup.activetab == 'audit'\n    }]),\n    onClick: _cache[3] || (_cache[3] = $event => $setup.clickTab('audit'))\n  }, {\n    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [_hoisted_9, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(\"  \" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.mstrings.auditlog), 1\n    /* TEXT */\n    )]),\n    _: 1\n    /* STABLE */\n\n  }, 8\n  /* PROPS */\n  , [\"class\"])]), $setup.settingscapability ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(\"li\", _hoisted_10, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_router_link, {\n    to: \"/settings\",\n    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)([\"nav-link btn btn-secondary\", {\n      active: $setup.activetab == 'settings'\n    }]),\n    onClick: _cache[4] || (_cache[4] = $event => $setup.clickTab('settings'))\n  }, {\n    default: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [_hoisted_11, (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(\"  \" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.mstrings.settings), 1\n    /* TEXT */\n    )]),\n    _: 1\n    /* STABLE */\n\n  }, 8\n  /* PROPS */\n  , [\"class\"])])) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(\"v-if\", true)]);\n}\n\n//# sourceURL=webpack://gugrades_ui/./src/components/TabsNav.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/templateLoader.js??ruleSet%5B1%5D.rules%5B3%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/TabMenu.vue?vue&type=template&id=3784634a":
/*!****************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/TabMenu.vue?vue&type=template&id=3784634a ***!
  \****************************************************************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* binding */ render; }\n/* harmony export */ });\n/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ \"./node_modules/vue/dist/vue.runtime.esm-bundler.js\");\n\nfunction render(_ctx, _cache, $props, $setup, $data, $options) {\n  const _component_router_view = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)(\"router-view\");\n\n  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(\"div\", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup[\"TabsNav\"], {\n    onTabchange: $setup.tabChange\n  }), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(\"\\n        <div v-if=\\\"currenttab == 'capture'\\\">\\n            <CaptureTable></CaptureTable>\\n        </div>\\n\\n        <div v-if=\\\"currenttab == 'settings'\\\">\\n            <SettingsPage></SettingsPage>\\n        </div>\\n\\n        <div v-if=\\\"currenttab == 'audit'\\\">\\n            <AuditPage></AuditPage>\\n        </div>\\n        \"), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)(_component_router_view)]);\n}\n\n//# sourceURL=webpack://gugrades_ui/./src/views/TabMenu.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/templateLoader.js??ruleSet%5B1%5D.rules%5B3%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./src/js/formkit.config.js":
/*!**********************************!*\
  !*** ./src/js/formkit.config.js ***!
  \**********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _formkit_themes__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @formkit/themes */ \"./node_modules/@formkit/themes/dist/index.mjs\");\n/**\n * Styling config for FormKit\n * See,  https://formkit.com/essentials/styling\n */\n\nconst config = {\n  config: {\n    classes: (0,_formkit_themes__WEBPACK_IMPORTED_MODULE_0__.generateClasses)({\n      global: {\n        // classes\n        outer: '$reset my-1',\n        input: 'form-control',\n        label: '$reset mb-0',\n        legend: '$reset mb-0 fs-1',\n        help: 'form-text',\n        messages: 'list-unstyled mt-1',\n        message: 'text-danger'\n      },\n      form: {\n        form: \"mt-5 mx-auto p-5 border rounded\"\n      },\n      range: {\n        input: '$reset form-range'\n      },\n      submit: {\n        outer: '$reset mt-3',\n        input: '$reset btn btn-primary'\n      },\n      checkbox: {\n        outer: '$reset form-check',\n        input: '$reset form-check-input'\n      },\n      radio: {\n        outer: '$reset form-check form-check-inline',\n        input: '$reset form-check-input',\n        options: '$reset list-unstyled list-inline',\n        option: '$reset list-inline-item pr-3'\n      }\n    })\n  }\n};\n/* harmony default export */ __webpack_exports__[\"default\"] = (config);\n\n//# sourceURL=webpack://gugrades_ui/./src/js/formkit.config.js?");

/***/ }),

/***/ "./src/main.js":
/*!*********************!*\
  !*** ./src/main.js ***!
  \*********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var core_js_modules_es_error_cause_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.error.cause.js */ \"./node_modules/core-js/modules/es.error.cause.js\");\n/* harmony import */ var core_js_modules_es_error_cause_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_error_cause_js__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! vue */ \"./node_modules/vue/dist/vue.runtime.esm-bundler.js\");\n/* harmony import */ var _App_vue__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./App.vue */ \"./src/App.vue\");\n/* harmony import */ var _router__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./router */ \"./src/router/index.js\");\n/* harmony import */ var vue_toastification__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! vue-toastification */ \"./node_modules/vue-toastification/dist/index.mjs\");\n/* harmony import */ var vue_toastification_dist_index_css__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! vue-toastification/dist/index.css */ \"./node_modules/vue-toastification/dist/index.css\");\n/* harmony import */ var vue_toastification_dist_index_css__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(vue_toastification_dist_index_css__WEBPACK_IMPORTED_MODULE_5__);\n/* harmony import */ var vue3_easy_data_table__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! vue3-easy-data-table */ \"./node_modules/vue3-easy-data-table/dist/vue3-easy-data-table.es.js\");\n/* harmony import */ var vue3_easy_data_table_dist_style_css__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! vue3-easy-data-table/dist/style.css */ \"./node_modules/vue3-easy-data-table/dist/style.css\");\n/* harmony import */ var vue3_easy_data_table_dist_style_css__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(vue3_easy_data_table_dist_style_css__WEBPACK_IMPORTED_MODULE_7__);\n/* harmony import */ var _formkit_vue__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @formkit/vue */ \"./node_modules/@formkit/vue/dist/index.mjs\");\n/* harmony import */ var _kouts_vue_modal__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! @kouts/vue-modal */ \"./node_modules/@kouts/vue-modal/dist/vue-modal.es.js\");\n/* harmony import */ var _src_assets_VueModal_css__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ../src/assets/VueModal.css */ \"./src/assets/VueModal.css\");\n/* harmony import */ var _src_assets_VueModal_css__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_src_assets_VueModal_css__WEBPACK_IMPORTED_MODULE_10__);\n/* harmony import */ var _js_formkit_config_js__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @/js/formkit.config.js */ \"./src/js/formkit.config.js\");\n\n\n\n\n\n\n\n\n\n\n\n // This stuff makes sure that the window.GU variable\n// exists.\n// This can take some time as Moodle runs this once the page\n// has loaded\n\nvar timeout = 1000000;\n\nfunction ensureGUIsSet(timeout) {\n  var start = Date.now();\n  return new Promise(waitForGU);\n\n  function waitForGU(resolve, reject) {\n    if (window.GU) {\n      resolve(window.GU);\n    } else if (timeout && Date.now() - start >= timeout) {\n      reject(new Error(\"timeout\"));\n    } else {\n      setTimeout(waitForGU.bind(this, resolve, reject), 30);\n    }\n  }\n} // Toast defaults\n\n\nconst toastoptions = {\n  position: 'top-center',\n  timeout: 5000\n};\nensureGUIsSet(timeout).then(() => {\n  const app = (0,vue__WEBPACK_IMPORTED_MODULE_1__.createApp)(_App_vue__WEBPACK_IMPORTED_MODULE_2__[\"default\"]);\n  const mstrings = (0,vue__WEBPACK_IMPORTED_MODULE_1__.reactive)([]);\n  app.provide('mstrings', mstrings);\n  app.use(_router__WEBPACK_IMPORTED_MODULE_3__[\"default\"]);\n  app.use(vue_toastification__WEBPACK_IMPORTED_MODULE_4__[\"default\"], toastoptions);\n  app.use(_formkit_vue__WEBPACK_IMPORTED_MODULE_8__.plugin, (0,_formkit_vue__WEBPACK_IMPORTED_MODULE_8__.defaultConfig)({\n    config: _js_formkit_config_js__WEBPACK_IMPORTED_MODULE_11__[\"default\"].config\n  }));\n  app.component('EasyDataTable', vue3_easy_data_table__WEBPACK_IMPORTED_MODULE_6__[\"default\"]);\n  app.component('VueModal', _kouts_vue_modal__WEBPACK_IMPORTED_MODULE_9__.Modal);\n  app.mount('#app'); // Read strings\n  // Strings are pushed to individual components using provide() / inject()\n\n  const GU = window.GU;\n  const fetchMany = GU.fetchMany;\n  fetchMany([{\n    methodname: 'local_gugrades_get_all_strings',\n    args: {}\n  }])[0].then(result => {\n    const strings = result;\n    strings.forEach(string => {\n      mstrings[string.tag] = string.stringvalue;\n    });\n  }).catch(error => {\n    window.console.error(error);\n  });\n});\n\n//# sourceURL=webpack://gugrades_ui/./src/main.js?");

/***/ }),

/***/ "./src/router/index.js":
/*!*****************************!*\
  !*** ./src/router/index.js ***!
  \*****************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var vue_router__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-router */ \"./node_modules/vue-router/dist/vue-router.mjs\");\n\nconst routes = [{\n  path: '/',\n  name: 'capture',\n  component: () => __webpack_require__.e(/*! import() | capture */ \"capture\").then(__webpack_require__.bind(__webpack_require__, /*! ../views/CaptureTable.vue */ \"./src/views/CaptureTable.vue\"))\n}, {\n  path: '/conversion',\n  name: 'conversion',\n  component: () => __webpack_require__.e(/*! import() | conversion */ \"conversion\").then(__webpack_require__.bind(__webpack_require__, /*! ../views/ConversionPage.vue */ \"./src/views/ConversionPage.vue\"))\n}, {\n  path: '/aggregation',\n  name: 'aggregation',\n  component: () => __webpack_require__.e(/*! import() | aggregation */ \"aggregation\").then(__webpack_require__.bind(__webpack_require__, /*! ../views/AggregationTable.vue */ \"./src/views/AggregationTable.vue\"))\n}, {\n  path: '/settings',\n  name: 'settings',\n  component: () => __webpack_require__.e(/*! import() | settings */ \"settings\").then(__webpack_require__.bind(__webpack_require__, /*! ../views/SettingsPage.vue */ \"./src/views/SettingsPage.vue\"))\n}, {\n  path: '/audit',\n  name: 'audit',\n  component: () => __webpack_require__.e(/*! import() | audit */ \"audit\").then(__webpack_require__.bind(__webpack_require__, /*! ../views/AuditPage.vue */ \"./src/views/AuditPage.vue\"))\n}];\nconst router = (0,vue_router__WEBPACK_IMPORTED_MODULE_0__.createRouter)({\n  history: (0,vue_router__WEBPACK_IMPORTED_MODULE_0__.createWebHashHistory)(),\n  //history: createWebHistory(process.env.BASE_URL),\n  routes\n});\n/* harmony default export */ __webpack_exports__[\"default\"] = (router);\n\n//# sourceURL=webpack://gugrades_ui/./src/router/index.js?");

/***/ }),

/***/ "./node_modules/css-loader/dist/cjs.js??clonedRuleSet-14.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-14.use[2]!./src/assets/VueModal.css":
/*!*******************************************************************************************************************************************************************!*\
  !*** ./node_modules/css-loader/dist/cjs.js??clonedRuleSet-14.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-14.use[2]!./src/assets/VueModal.css ***!
  \*******************************************************************************************************************************************************************/
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../node_modules/css-loader/dist/runtime/noSourceMaps.js */ \"./node_modules/css-loader/dist/runtime/noSourceMaps.js\");\n/* harmony import */ var _node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../node_modules/css-loader/dist/runtime/api.js */ \"./node_modules/css-loader/dist/runtime/api.js\");\n/* harmony import */ var _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1__);\n// Imports\n\n\nvar ___CSS_LOADER_EXPORT___ = _node_modules_css_loader_dist_runtime_api_js__WEBPACK_IMPORTED_MODULE_1___default()((_node_modules_css_loader_dist_runtime_noSourceMaps_js__WEBPACK_IMPORTED_MODULE_0___default()));\n// Module\n___CSS_LOADER_EXPORT___.push([module.id, \".vm-backdrop {\\n    position: fixed;\\n    top: 0;\\n    right: 0;\\n    bottom: 0;\\n    left: 0;\\n    background-color: rgba(0, 0, 0, 0.5);\\n  }\\n  .vm-wrapper {\\n    position: fixed;\\n    top: 0;\\n    right: 0;\\n    bottom: 0;\\n    left: 0;\\n    overflow-x: hidden;\\n    overflow-y: auto;\\n    outline: 0;\\n  }\\n  .vm {\\n    position: relative;\\n    margin: 0px auto;\\n    width: calc(100% - 20px);\\n    min-width: 110px;\\n    /* max-width: 500px; */\\n    background-color: #fff;\\n    /* top: 30px; */\\n    top: 10%;\\n    cursor: default;\\n    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);\\n  }\\n  .vm-titlebar {\\n    padding: 10px 15px 10px 15px;\\n    overflow: auto;\\n    border-bottom: 1px solid #e5e5e5;\\n  }\\n  .vm-title {\\n    margin-top: 2px;\\n    margin-bottom: 0px;\\n    display: inline-block;\\n    font-size: 18px;\\n    font-weight: normal;\\n  }\\n  .vm-btn-close {\\n    color: #ccc;\\n    padding: 0px;\\n    cursor: pointer;\\n    background: 0 0;\\n    border: 0;\\n    float: right;\\n    font-size: 24px;\\n    line-height: 1em;\\n  }\\n  .vm-btn-close:before {\\n    content: '×';\\n    font-family: Arial;\\n  }\\n  .vm-btn-close:hover,\\n  .vm-btn-close:focus,\\n  .vm-btn-close:focus:hover {\\n    color: #bbb;\\n    border-color: transparent;\\n    background-color: transparent;\\n  }\\n  .vm-content {\\n    padding: 10px 15px 15px 15px;\\n  }\\n  .vm-content .full-hr {\\n    width: auto;\\n    border: 0;\\n    border-top: 1px solid #e5e5e5;\\n    margin-top: 15px;\\n    margin-bottom: 15px;\\n    margin-left: -14px;\\n    margin-right: -14px;\\n  }\\n  .vm-fadeIn {\\n    animation-name: vm-fadeIn;\\n  }\\n  @keyframes vm-fadeIn {\\n    0% {\\n      opacity: 0;\\n    }\\n    100% {\\n      opacity: 1;\\n    }\\n  }\\n  .vm-fadeOut {\\n    animation-name: vm-fadeOut;\\n  }\\n  @keyframes vm-fadeOut {\\n    0% {\\n      opacity: 1;\\n    }\\n    100% {\\n      opacity: 0;\\n    }\\n  }\\n  .vm-fadeIn,\\n  .vm-fadeOut {\\n    animation-duration: 0.25s;\\n    animation-fill-mode: both;\\n  }\", \"\"]);\n// Exports\n/* harmony default export */ __webpack_exports__[\"default\"] = (___CSS_LOADER_EXPORT___);\n\n\n//# sourceURL=webpack://gugrades_ui/./src/assets/VueModal.css?./node_modules/css-loader/dist/cjs.js??clonedRuleSet-14.use%5B1%5D!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-14.use%5B2%5D");

/***/ }),

/***/ "./src/App.vue":
/*!*********************!*\
  !*** ./src/App.vue ***!
  \*********************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _App_vue_vue_type_template_id_7ba5bd90__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./App.vue?vue&type=template&id=7ba5bd90 */ \"./src/App.vue?vue&type=template&id=7ba5bd90\");\n/* harmony import */ var _App_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./App.vue?vue&type=script&setup=true&lang=js */ \"./src/App.vue?vue&type=script&setup=true&lang=js\");\n/* harmony import */ var _home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ \"./node_modules/vue-loader/dist/exportHelper.js\");\n\n\n\n\n;\nconst __exports__ = /*#__PURE__*/(0,_home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(_App_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__[\"default\"], [['render',_App_vue_vue_type_template_id_7ba5bd90__WEBPACK_IMPORTED_MODULE_0__.render],['__file',\"src/App.vue\"]])\n/* hot reload */\nif (false) {}\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (__exports__);\n\n//# sourceURL=webpack://gugrades_ui/./src/App.vue?");

/***/ }),

/***/ "./src/components/TabsNav.vue":
/*!************************************!*\
  !*** ./src/components/TabsNav.vue ***!
  \************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _TabsNav_vue_vue_type_template_id_5802a776__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TabsNav.vue?vue&type=template&id=5802a776 */ \"./src/components/TabsNav.vue?vue&type=template&id=5802a776\");\n/* harmony import */ var _TabsNav_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TabsNav.vue?vue&type=script&setup=true&lang=js */ \"./src/components/TabsNav.vue?vue&type=script&setup=true&lang=js\");\n/* harmony import */ var _home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ \"./node_modules/vue-loader/dist/exportHelper.js\");\n\n\n\n\n;\nconst __exports__ = /*#__PURE__*/(0,_home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(_TabsNav_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__[\"default\"], [['render',_TabsNav_vue_vue_type_template_id_5802a776__WEBPACK_IMPORTED_MODULE_0__.render],['__file',\"src/components/TabsNav.vue\"]])\n/* hot reload */\nif (false) {}\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (__exports__);\n\n//# sourceURL=webpack://gugrades_ui/./src/components/TabsNav.vue?");

/***/ }),

/***/ "./src/views/TabMenu.vue":
/*!*******************************!*\
  !*** ./src/views/TabMenu.vue ***!
  \*******************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _TabMenu_vue_vue_type_template_id_3784634a__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./TabMenu.vue?vue&type=template&id=3784634a */ \"./src/views/TabMenu.vue?vue&type=template&id=3784634a\");\n/* harmony import */ var _TabMenu_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./TabMenu.vue?vue&type=script&setup=true&lang=js */ \"./src/views/TabMenu.vue?vue&type=script&setup=true&lang=js\");\n/* harmony import */ var _home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ \"./node_modules/vue-loader/dist/exportHelper.js\");\n\n\n\n\n;\nconst __exports__ = /*#__PURE__*/(0,_home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(_TabMenu_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__[\"default\"], [['render',_TabMenu_vue_vue_type_template_id_3784634a__WEBPACK_IMPORTED_MODULE_0__.render],['__file',\"src/views/TabMenu.vue\"]])\n/* hot reload */\nif (false) {}\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (__exports__);\n\n//# sourceURL=webpack://gugrades_ui/./src/views/TabMenu.vue?");

/***/ }),

/***/ "./src/App.vue?vue&type=script&setup=true&lang=js":
/*!********************************************************!*\
  !*** ./src/App.vue?vue&type=script&setup=true&lang=js ***!
  \********************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_App_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"]; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_App_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./App.vue?vue&type=script&setup=true&lang=js */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/App.vue?vue&type=script&setup=true&lang=js\");\n \n\n//# sourceURL=webpack://gugrades_ui/./src/App.vue?");

/***/ }),

/***/ "./src/components/TabsNav.vue?vue&type=script&setup=true&lang=js":
/*!***********************************************************************!*\
  !*** ./src/components/TabsNav.vue?vue&type=script&setup=true&lang=js ***!
  \***********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_TabsNav_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"]; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_TabsNav_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./TabsNav.vue?vue&type=script&setup=true&lang=js */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/TabsNav.vue?vue&type=script&setup=true&lang=js\");\n \n\n//# sourceURL=webpack://gugrades_ui/./src/components/TabsNav.vue?");

/***/ }),

/***/ "./src/views/TabMenu.vue?vue&type=script&setup=true&lang=js":
/*!******************************************************************!*\
  !*** ./src/views/TabMenu.vue?vue&type=script&setup=true&lang=js ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_TabMenu_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"]; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_TabMenu_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./TabMenu.vue?vue&type=script&setup=true&lang=js */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/TabMenu.vue?vue&type=script&setup=true&lang=js\");\n \n\n//# sourceURL=webpack://gugrades_ui/./src/views/TabMenu.vue?");

/***/ }),

/***/ "./src/App.vue?vue&type=template&id=7ba5bd90":
/*!***************************************************!*\
  !*** ./src/App.vue?vue&type=template&id=7ba5bd90 ***!
  \***************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_App_vue_vue_type_template_id_7ba5bd90__WEBPACK_IMPORTED_MODULE_0__.render; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_App_vue_vue_type_template_id_7ba5bd90__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./App.vue?vue&type=template&id=7ba5bd90 */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/App.vue?vue&type=template&id=7ba5bd90\");\n\n\n//# sourceURL=webpack://gugrades_ui/./src/App.vue?");

/***/ }),

/***/ "./src/components/TabsNav.vue?vue&type=template&id=5802a776":
/*!******************************************************************!*\
  !*** ./src/components/TabsNav.vue?vue&type=template&id=5802a776 ***!
  \******************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_TabsNav_vue_vue_type_template_id_5802a776__WEBPACK_IMPORTED_MODULE_0__.render; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_TabsNav_vue_vue_type_template_id_5802a776__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./TabsNav.vue?vue&type=template&id=5802a776 */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/TabsNav.vue?vue&type=template&id=5802a776\");\n\n\n//# sourceURL=webpack://gugrades_ui/./src/components/TabsNav.vue?");

/***/ }),

/***/ "./src/views/TabMenu.vue?vue&type=template&id=3784634a":
/*!*************************************************************!*\
  !*** ./src/views/TabMenu.vue?vue&type=template&id=3784634a ***!
  \*************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_TabMenu_vue_vue_type_template_id_3784634a__WEBPACK_IMPORTED_MODULE_0__.render; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_TabMenu_vue_vue_type_template_id_3784634a__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./TabMenu.vue?vue&type=template&id=3784634a */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/TabMenu.vue?vue&type=template&id=3784634a\");\n\n\n//# sourceURL=webpack://gugrades_ui/./src/views/TabMenu.vue?");

/***/ }),

/***/ "./src/assets/VueModal.css":
/*!*********************************!*\
  !*** ./src/assets/VueModal.css ***!
  \*********************************/
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

eval("// style-loader: Adds some css to the DOM by adding a <style> tag\n\n// load the styles\nvar content = __webpack_require__(/*! !!../../node_modules/css-loader/dist/cjs.js??clonedRuleSet-14.use[1]!../../node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-14.use[2]!./VueModal.css */ \"./node_modules/css-loader/dist/cjs.js??clonedRuleSet-14.use[1]!./node_modules/postcss-loader/dist/cjs.js??clonedRuleSet-14.use[2]!./src/assets/VueModal.css\");\nif(content.__esModule) content = content.default;\nif(typeof content === 'string') content = [[module.id, content, '']];\nif(content.locals) module.exports = content.locals;\n// add the styles to the DOM\nvar add = (__webpack_require__(/*! !../../node_modules/vue-style-loader/lib/addStylesClient.js */ \"./node_modules/vue-style-loader/lib/addStylesClient.js\")[\"default\"])\nvar update = add(\"51796536\", content, false, {\"sourceMap\":false,\"shadowMode\":false});\n// Hot Module Replacement\nif(false) {}\n\n//# sourceURL=webpack://gugrades_ui/./src/assets/VueModal.css?");

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
/******/ 			id: moduleId,
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	!function() {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = function(result, chunkIds, fn, priority) {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every(function(key) { return __webpack_require__.O[key](chunkIds[j]); })) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/ensure chunk */
/******/ 	!function() {
/******/ 		__webpack_require__.f = {};
/******/ 		// This file contains only the entry chunk.
/******/ 		// The chunk loading function for additional chunks
/******/ 		__webpack_require__.e = function(chunkId) {
/******/ 			return Promise.all(Object.keys(__webpack_require__.f).reduce(function(promises, key) {
/******/ 				__webpack_require__.f[key](chunkId, promises);
/******/ 				return promises;
/******/ 			}, []));
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/get javascript chunk filename */
/******/ 	!function() {
/******/ 		// This function allow to reference async chunks
/******/ 		__webpack_require__.u = function(chunkId) {
/******/ 			// return url for filenames based on template
/******/ 			return "js/" + chunkId + ".js";
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	!function() {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/load script */
/******/ 	!function() {
/******/ 		var inProgress = {};
/******/ 		var dataWebpackPrefix = "gugrades_ui:";
/******/ 		// loadScript function to load a script via script tag
/******/ 		__webpack_require__.l = function(url, done, key, chunkId) {
/******/ 			if(inProgress[url]) { inProgress[url].push(done); return; }
/******/ 			var script, needAttach;
/******/ 			if(key !== undefined) {
/******/ 				var scripts = document.getElementsByTagName("script");
/******/ 				for(var i = 0; i < scripts.length; i++) {
/******/ 					var s = scripts[i];
/******/ 					if(s.getAttribute("src") == url || s.getAttribute("data-webpack") == dataWebpackPrefix + key) { script = s; break; }
/******/ 				}
/******/ 			}
/******/ 			if(!script) {
/******/ 				needAttach = true;
/******/ 				script = document.createElement('script');
/******/ 		
/******/ 				script.charset = 'utf-8';
/******/ 				script.timeout = 120;
/******/ 				if (__webpack_require__.nc) {
/******/ 					script.setAttribute("nonce", __webpack_require__.nc);
/******/ 				}
/******/ 				script.setAttribute("data-webpack", dataWebpackPrefix + key);
/******/ 				script.src = url;
/******/ 			}
/******/ 			inProgress[url] = [done];
/******/ 			var onScriptComplete = function(prev, event) {
/******/ 				// avoid mem leaks in IE.
/******/ 				script.onerror = script.onload = null;
/******/ 				clearTimeout(timeout);
/******/ 				var doneFns = inProgress[url];
/******/ 				delete inProgress[url];
/******/ 				script.parentNode && script.parentNode.removeChild(script);
/******/ 				doneFns && doneFns.forEach(function(fn) { return fn(event); });
/******/ 				if(prev) return prev(event);
/******/ 			}
/******/ 			;
/******/ 			var timeout = setTimeout(onScriptComplete.bind(null, undefined, { type: 'timeout', target: script }), 120000);
/******/ 			script.onerror = onScriptComplete.bind(null, script.onerror);
/******/ 			script.onload = onScriptComplete.bind(null, script.onload);
/******/ 			needAttach && document.head.appendChild(script);
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/publicPath */
/******/ 	!function() {
/******/ 		__webpack_require__.p = "/local/gugrades/ui/dist/";
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	!function() {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"app": 0
/******/ 		};
/******/ 		
/******/ 		__webpack_require__.f.j = function(chunkId, promises) {
/******/ 				// JSONP chunk loading for javascript
/******/ 				var installedChunkData = __webpack_require__.o(installedChunks, chunkId) ? installedChunks[chunkId] : undefined;
/******/ 				if(installedChunkData !== 0) { // 0 means "already installed".
/******/ 		
/******/ 					// a Promise means "currently loading".
/******/ 					if(installedChunkData) {
/******/ 						promises.push(installedChunkData[2]);
/******/ 					} else {
/******/ 						if(true) { // all chunks have JS
/******/ 							// setup Promise in chunk cache
/******/ 							var promise = new Promise(function(resolve, reject) { installedChunkData = installedChunks[chunkId] = [resolve, reject]; });
/******/ 							promises.push(installedChunkData[2] = promise);
/******/ 		
/******/ 							// start chunk loading
/******/ 							var url = __webpack_require__.p + __webpack_require__.u(chunkId);
/******/ 							// create error before stack unwound to get useful stacktrace later
/******/ 							var error = new Error();
/******/ 							var loadingEnded = function(event) {
/******/ 								if(__webpack_require__.o(installedChunks, chunkId)) {
/******/ 									installedChunkData = installedChunks[chunkId];
/******/ 									if(installedChunkData !== 0) installedChunks[chunkId] = undefined;
/******/ 									if(installedChunkData) {
/******/ 										var errorType = event && (event.type === 'load' ? 'missing' : event.type);
/******/ 										var realSrc = event && event.target && event.target.src;
/******/ 										error.message = 'Loading chunk ' + chunkId + ' failed.\n(' + errorType + ': ' + realSrc + ')';
/******/ 										error.name = 'ChunkLoadError';
/******/ 										error.type = errorType;
/******/ 										error.request = realSrc;
/******/ 										installedChunkData[1](error);
/******/ 									}
/******/ 								}
/******/ 							};
/******/ 							__webpack_require__.l(url, loadingEnded, "chunk-" + chunkId, chunkId);
/******/ 						} else installedChunks[chunkId] = 0;
/******/ 					}
/******/ 				}
/******/ 		};
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = function(chunkId) { return installedChunks[chunkId] === 0; };
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = function(parentChunkLoadingFunction, data) {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some(function(id) { return installedChunks[id] !== 0; })) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkgugrades_ui"] = self["webpackChunkgugrades_ui"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["chunk-vendors"], function() { return __webpack_require__("./src/main.js"); })
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;