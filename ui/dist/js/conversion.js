"use strict";
/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkgugrades_ui"] = self["webpackChunkgugrades_ui"] || []).push([["conversion"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/Conversion/ManageMaps.vue?vue&type=script&setup=true&lang=js":
/*!******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/Conversion/ManageMaps.vue?vue&type=script&setup=true&lang=js ***!
  \******************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _vue_runtime_core__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @vue/runtime-core */ \"./node_modules/@vue/reactivity/dist/reactivity.esm-bundler.js\");\n/* harmony import */ var _vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @vue/runtime-core */ \"./node_modules/@vue/runtime-core/dist/runtime-core.esm-bundler.js\");\n/* harmony import */ var vue_toastification__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue-toastification */ \"./node_modules/vue-toastification/dist/index.mjs\");\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  __name: 'ManageMaps',\n\n  setup(__props, {\n    expose: __expose\n  }) {\n    __expose();\n\n    const maps = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_1__.ref)([]);\n    const mstrings = (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.inject)('mstrings');\n    const toast = (0,vue_toastification__WEBPACK_IMPORTED_MODULE_0__.useToast)();\n    /**\n     * Get all the maps for this course\n     */\n\n    (0,_vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.onMounted)(() => {\n      const GU = window.GU;\n      const courseid = GU.courseid;\n      const fetchMany = GU.fetchMany;\n      fetchMany([{\n        methodname: 'local_gugrades_get_conversion_maps',\n        args: {\n          courseid: courseid\n        }\n      }])[0].then(result => {\n        maps.value = result;\n      }).catch(error => {\n        window.console.error(error);\n        toast.error('Error communicating with server (see console)');\n      });\n    });\n    const __returned__ = {\n      maps,\n      mstrings,\n      toast,\n\n      get ref() {\n        return _vue_runtime_core__WEBPACK_IMPORTED_MODULE_1__.ref;\n      },\n\n      get inject() {\n        return _vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.inject;\n      },\n\n      get onMounted() {\n        return _vue_runtime_core__WEBPACK_IMPORTED_MODULE_2__.onMounted;\n      },\n\n      get useToast() {\n        return vue_toastification__WEBPACK_IMPORTED_MODULE_0__.useToast;\n      }\n\n    };\n    Object.defineProperty(__returned__, '__isScriptSetup', {\n      enumerable: false,\n      value: true\n    });\n    return __returned__;\n  }\n\n});\n\n//# sourceURL=webpack://gugrades_ui/./src/components/Conversion/ManageMaps.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/ConversionPage.vue?vue&type=script&setup=true&lang=js":
/*!******************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/ConversionPage.vue?vue&type=script&setup=true&lang=js ***!
  \******************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _components_Conversion_ManageMaps_vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @/components/Conversion/ManageMaps.vue */ \"./src/components/Conversion/ManageMaps.vue\");\n\n/* harmony default export */ __webpack_exports__[\"default\"] = ({\n  __name: 'ConversionPage',\n\n  setup(__props, {\n    expose: __expose\n  }) {\n    __expose();\n\n    const __returned__ = {\n      ManageMaps: _components_Conversion_ManageMaps_vue__WEBPACK_IMPORTED_MODULE_0__[\"default\"]\n    };\n    Object.defineProperty(__returned__, '__isScriptSetup', {\n      enumerable: false,\n      value: true\n    });\n    return __returned__;\n  }\n\n});\n\n//# sourceURL=webpack://gugrades_ui/./src/views/ConversionPage.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/Conversion/ManageMaps.vue?vue&type=template&id=b33b7456":
/*!***********************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/Conversion/ManageMaps.vue?vue&type=template&id=b33b7456 ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* binding */ render; }\n/* harmony export */ });\n/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ \"./node_modules/vue/dist/vue.runtime.esm-bundler.js\");\n\nconst _hoisted_1 = {\n  key: 0,\n  class: \"alert alert-warning\"\n};\nconst _hoisted_2 = {\n  class: \"mt-2\"\n};\nconst _hoisted_3 = {\n  class: \"btn btn-primary\"\n};\nfunction render(_ctx, _cache, $props, $setup, $data, $options) {\n  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(\"div\", null, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"h2\", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.mstrings.conversionmaps), 1\n  /* TEXT */\n  ), !$setup.maps.length ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(\"div\", _hoisted_1, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.mstrings.noconversionmaps), 1\n  /* TEXT */\n  )) : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)(\"v-if\", true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"div\", _hoisted_2, [(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)(\"button\", _hoisted_3, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.mstrings.addconversionmap), 1\n  /* TEXT */\n  )])]);\n}\n\n//# sourceURL=webpack://gugrades_ui/./src/components/Conversion/ManageMaps.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/templateLoader.js??ruleSet%5B1%5D.rules%5B3%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/ConversionPage.vue?vue&type=template&id=16a8f202":
/*!***********************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/ConversionPage.vue?vue&type=template&id=16a8f202 ***!
  \***********************************************************************************************************************************************************************************************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* binding */ render; }\n/* harmony export */ });\n/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ \"./node_modules/vue/dist/vue.runtime.esm-bundler.js\");\n\nfunction render(_ctx, _cache, $props, $setup, $data, $options) {\n  return (0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)($setup[\"ManageMaps\"]);\n}\n\n//# sourceURL=webpack://gugrades_ui/./src/views/ConversionPage.vue?./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use%5B0%5D!./node_modules/vue-loader/dist/templateLoader.js??ruleSet%5B1%5D.rules%5B3%5D!./node_modules/vue-loader/dist/index.js??ruleSet%5B0%5D.use%5B0%5D");

/***/ }),

/***/ "./src/components/Conversion/ManageMaps.vue":
/*!**************************************************!*\
  !*** ./src/components/Conversion/ManageMaps.vue ***!
  \**************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ManageMaps_vue_vue_type_template_id_b33b7456__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ManageMaps.vue?vue&type=template&id=b33b7456 */ \"./src/components/Conversion/ManageMaps.vue?vue&type=template&id=b33b7456\");\n/* harmony import */ var _ManageMaps_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ManageMaps.vue?vue&type=script&setup=true&lang=js */ \"./src/components/Conversion/ManageMaps.vue?vue&type=script&setup=true&lang=js\");\n/* harmony import */ var _home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ \"./node_modules/vue-loader/dist/exportHelper.js\");\n\n\n\n\n;\nconst __exports__ = /*#__PURE__*/(0,_home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(_ManageMaps_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__[\"default\"], [['render',_ManageMaps_vue_vue_type_template_id_b33b7456__WEBPACK_IMPORTED_MODULE_0__.render],['__file',\"src/components/Conversion/ManageMaps.vue\"]])\n/* hot reload */\nif (false) {}\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (__exports__);\n\n//# sourceURL=webpack://gugrades_ui/./src/components/Conversion/ManageMaps.vue?");

/***/ }),

/***/ "./src/views/ConversionPage.vue":
/*!**************************************!*\
  !*** ./src/views/ConversionPage.vue ***!
  \**************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _ConversionPage_vue_vue_type_template_id_16a8f202__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ConversionPage.vue?vue&type=template&id=16a8f202 */ \"./src/views/ConversionPage.vue?vue&type=template&id=16a8f202\");\n/* harmony import */ var _ConversionPage_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ConversionPage.vue?vue&type=script&setup=true&lang=js */ \"./src/views/ConversionPage.vue?vue&type=script&setup=true&lang=js\");\n/* harmony import */ var _home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ \"./node_modules/vue-loader/dist/exportHelper.js\");\n\n\n\n\n;\nconst __exports__ = /*#__PURE__*/(0,_home_howard_Projects_Moodle41_app_public_local_gugrades_ui_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__[\"default\"])(_ConversionPage_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__[\"default\"], [['render',_ConversionPage_vue_vue_type_template_id_16a8f202__WEBPACK_IMPORTED_MODULE_0__.render],['__file',\"src/views/ConversionPage.vue\"]])\n/* hot reload */\nif (false) {}\n\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (__exports__);\n\n//# sourceURL=webpack://gugrades_ui/./src/views/ConversionPage.vue?");

/***/ }),

/***/ "./src/components/Conversion/ManageMaps.vue?vue&type=script&setup=true&lang=js":
/*!*************************************************************************************!*\
  !*** ./src/components/Conversion/ManageMaps.vue?vue&type=script&setup=true&lang=js ***!
  \*************************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_ManageMaps_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"]; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_ManageMaps_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./ManageMaps.vue?vue&type=script&setup=true&lang=js */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/Conversion/ManageMaps.vue?vue&type=script&setup=true&lang=js\");\n \n\n//# sourceURL=webpack://gugrades_ui/./src/components/Conversion/ManageMaps.vue?");

/***/ }),

/***/ "./src/views/ConversionPage.vue?vue&type=script&setup=true&lang=js":
/*!*************************************************************************!*\
  !*** ./src/views/ConversionPage.vue?vue&type=script&setup=true&lang=js ***!
  \*************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_ConversionPage_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__[\"default\"]; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_ConversionPage_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./ConversionPage.vue?vue&type=script&setup=true&lang=js */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/ConversionPage.vue?vue&type=script&setup=true&lang=js\");\n \n\n//# sourceURL=webpack://gugrades_ui/./src/views/ConversionPage.vue?");

/***/ }),

/***/ "./src/components/Conversion/ManageMaps.vue?vue&type=template&id=b33b7456":
/*!********************************************************************************!*\
  !*** ./src/components/Conversion/ManageMaps.vue?vue&type=template&id=b33b7456 ***!
  \********************************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_ManageMaps_vue_vue_type_template_id_b33b7456__WEBPACK_IMPORTED_MODULE_0__.render; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_ManageMaps_vue_vue_type_template_id_b33b7456__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./ManageMaps.vue?vue&type=template&id=b33b7456 */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/components/Conversion/ManageMaps.vue?vue&type=template&id=b33b7456\");\n\n\n//# sourceURL=webpack://gugrades_ui/./src/components/Conversion/ManageMaps.vue?");

/***/ }),

/***/ "./src/views/ConversionPage.vue?vue&type=template&id=16a8f202":
/*!********************************************************************!*\
  !*** ./src/views/ConversionPage.vue?vue&type=template&id=16a8f202 ***!
  \********************************************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"render\": function() { return /* reexport safe */ _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_ConversionPage_vue_vue_type_template_id_16a8f202__WEBPACK_IMPORTED_MODULE_0__.render; }\n/* harmony export */ });\n/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_40_use_0_node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_3_node_modules_vue_loader_dist_index_js_ruleSet_0_use_0_ConversionPage_vue_vue_type_template_id_16a8f202__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!../../node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./ConversionPage.vue?vue&type=template&id=16a8f202 */ \"./node_modules/babel-loader/lib/index.js??clonedRuleSet-40.use[0]!./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[3]!./node_modules/vue-loader/dist/index.js??ruleSet[0].use[0]!./src/views/ConversionPage.vue?vue&type=template&id=16a8f202\");\n\n\n//# sourceURL=webpack://gugrades_ui/./src/views/ConversionPage.vue?");

/***/ })

}]);