webpackJsonp(["main"],{

/***/ "./src/$$_lazy_route_resource lazy recursive":
/***/ (function(module, exports) {

function webpackEmptyAsyncContext(req) {
	// Here Promise.resolve().then() is used instead of new Promise() to prevent
	// uncatched exception popping up in devtools
	return Promise.resolve().then(function() {
		throw new Error("Cannot find module '" + req + "'.");
	});
}
webpackEmptyAsyncContext.keys = function() { return []; };
webpackEmptyAsyncContext.resolve = webpackEmptyAsyncContext;
module.exports = webpackEmptyAsyncContext;
webpackEmptyAsyncContext.id = "./src/$$_lazy_route_resource lazy recursive";

/***/ }),

/***/ "./src/app/_components/app.component.html":
/***/ (function(module, exports) {

module.exports = "<!-- main app container -->\n<div class=\"jumbotron\"> \n    <div class=\"container\">\n        <div class=\"col-sm-8 col-sm-offset-2\">\n            <div *ngIf=\"showMenu\" class=\"menu\">\n                <mat-toolbar class=\"toolbar\">\n                    <button mat-icon-button [matMenuTriggerFor]=\"menu\">\n                        <mat-icon>menu</mat-icon>\n                    </button>\n                    <!-- This fills the remaining space of the current row -->\n                    <!-- <span class=\"fill-remaining-space\"></span> -->\n                </mat-toolbar>\n                <mat-menu #menu=\"matMenu\">\n                    <br />\n                    <a mat-menu-item routerLink=\"\">Home</a>\n                    <a mat-menu-item routerLink=\"profile\">Profile</a>\n                    <a mat-menu-item routerLink=\"login\">Logout</a>\n                </mat-menu>\n            </div>\n            <router-outlet></router-outlet>\n        </div>\n    </div>\n</div>"

/***/ }),

/***/ "./src/app/_components/app.component.scss":
/***/ (function(module, exports) {

module.exports = "h1 {\n  color: #369;\n  font-family: Arial, Helvetica, sans-serif;\n  font-size: 250%; }\n\n.toolbar {\n  background-color: transparent; }\n"

/***/ }),

/***/ "./src/app/_components/app.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AppComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_filter__ = __webpack_require__("./node_modules/rxjs/_esm5/add/operator/filter.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};



var AppComponent = (function () {
    function AppComponent(router, activatedRoute) {
        var _this = this;
        this.router = router;
        this.activatedRoute = activatedRoute;
        this.showMenu = this.isLoggedIn();
        router.events
            .filter(function (event) { return event instanceof __WEBPACK_IMPORTED_MODULE_1__angular_router__["b" /* NavigationEnd */]; })
            .map(function () { return activatedRoute; })
            .subscribe(function (event) {
            if (router.url == "/login") {
                _this.showMenu = false;
            }
            else {
                _this.showMenu = _this.isLoggedIn();
            }
        });
    }
    AppComponent.prototype.isLoggedIn = function () {
        return localStorage.getItem('currentUser') ? true : false;
    };
    ;
    AppComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            moduleId: module.i,
            selector: 'app-root',
            template: __webpack_require__("./src/app/_components/app.component.html"),
            styles: [__webpack_require__("./src/app/_components/app.component.scss")]
        }),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */],
            __WEBPACK_IMPORTED_MODULE_1__angular_router__["a" /* ActivatedRoute */]])
    ], AppComponent);
    return AppComponent;
}());



/***/ }),

/***/ "./src/app/_components/home/home.component.html":
/***/ (function(module, exports) {

module.exports = "<div *ngIf=\"topicsTree.length\" class=\"topics\">\n  <div *ngFor=\"let level of topicsTree; let levelIndex = 'index'\" >\n    <mat-card *ngIf=\"level.units.length\" class=\"level\">\n      <h3 class=\"level-title\">{{level.title}}</h3>\n      <mat-card class=\"unit\" *ngFor=\"let unit of level.units; let unitIndex = 'index'\">\n        <h4 class=\"unit-title\">{{unit.title}}</h4>\n        <div class=\"topics-container\">\n          <div *ngFor=\"let topic of unit.topics; let topicIndex = 'index'\" class=\"topic\">\n            <a [routerLink]=\"topic.status == 0 ? '' : '/topic/'+topic.id\" routerLinkActive=\"active\" [class.disabled]=\"topic.status == 0 ? true : null\" class=\"topic-picture\">\n              <div [ngClass]=\"topic.image_id ? topic.image_id : 'cb0-img'\">\n                <div *ngIf=\"topic.status != 2\" class=\"topicButton\"  [ngClass]=\"topic.status == 1 ? 'greenout': 'redout'\" >\n                </div>\n                <div *ngIf=\"topic.status == 2\" class=\"topicButton\"  [ngClass]=\"'yellowout'\" >\n                  <div [ngStyle]=\"{'height.%' : topic.progress.percent}\" class=\"bottom_progress\" [ngClass]=\"'greenout'\"></div>\n                </div>\n              </div>\n            </a>\n            <div class=\"topic-progress\"> {{topic.progress.done}} of {{topic.progress.total}} done</div>\n            <div>\n              <div *ngIf=\"!topic.short_name\" class=\"topic-title\">{{topic.title}}</div>\n              <div *ngIf=\"topic.short_name\" class=\"topic-title\">{{topic.short_name}}</div>  \n            </div>\n          </div>\n        </div>\n      </mat-card>\n    </mat-card>\n  </div>\n</div>\n\n<!-- Old template (without Angular Material)\n<div class=\"text-center\">\n    <div ng-if=\"topicsTree.length\">\n        <div *ngFor=\"let level of topicsTree; let levelIndex = index\">\n            <div *ngIf=\"level.units.length\" class=\"shadow\">\n                <h3 style=\"font-weight: bold;\">{{level.title}}</h3>\n                <div *ngFor=\"let unit of level.units; let unitIndex = index\"  class=\"shadow\">\n                    <h4>{{unit.title}}</h4>\n                    <div *ngFor=\"let topic of unit.topics; let topicIndex = index\" style=\"display: inline-block;width:33%;vertical-align:top;\">\n                        <span class=\"fa-stack fa-4x\" >\n                            <a [routerLink]=\"topic.status == 0 ? '' : '/topic/'+topic.id\" routerLinkActive=\"active\" [class.disabled]=\"topic.status == 0 ? true : null\">\n                                <div [ngClass]=\"topic.image_id ? topic.image_id : 'cb0-img'\">\n                                    <div *ngIf=\"topic.status != 2\" class=\"topicButton\"  [ngClass]=\"topic.status == 1 ? 'greenout': 'redout'\" ></div>\n                                    <div *ngIf=\"topic.status == 2\" class=\"topicButton\"  [ngClass]=\"'yellowout'\" >\n                                        <div [ngStyle]=\"{'height.%' : topic.progress.percent}\" class=\"bottom_progress\" [ngClass]=\"'greenout'\"></div>\n                                    </div>\n                                </div>\n                            </a>\n                        </span>\n                        <div> {{topic.progress.done}} of {{topic.progress.total}} done</div>\n                        <div *ngIf=\"!topic.short_name\">{{topic.title}}</div>\n                        <div *ngIf=\"topic.short_name\">{{topic.short_name}}</div>\n                    </div>\n                    <div style=\"width:1%\"></div>\n                    <div style=\"clear:both\"><br /></div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n-->"

/***/ }),

/***/ "./src/app/_components/home/home.component.scss":
/***/ (function(module, exports) {

module.exports = ".level {\n  margin: 10px;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n      -ms-flex-direction: column;\n          flex-direction: column;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n  background-color: #f3f3f3; }\n\n.level-title, .unit-title {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center; }\n\n.level-title {\n  font-weight: bold; }\n\n.unit {\n  margin: 10px;\n  width: 100%;\n  background-color: #f9f9f9; }\n\n.topics-container {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  -webkit-box-orient: horizontal;\n  -webkit-box-direction: normal;\n      -ms-flex-direction: row;\n          flex-direction: row;\n  -ms-flex-wrap: wrap;\n      flex-wrap: wrap;\n  -webkit-box-align: start;\n      -ms-flex-align: start;\n          align-items: flex-start; }\n\n.topic {\n  margin: 10px;\n  width: 120px;\n  padding: 0;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n      -ms-flex-direction: column;\n          flex-direction: column;\n  -ms-flex-wrap: wrap;\n      flex-wrap: wrap;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center; }\n\n.topic-title, .topic-progress, .topic-picture {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  padding: 0;\n  margin: 0; }\n\n.topic-title, .topic-progress {\n  text-align: center; }\n"

/***/ }),

/***/ "./src/app/_components/home/home.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return HomeComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__services_index__ = __webpack_require__("./src/app/_services/index.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};


var HomeComponent = (function () {
    function HomeComponent(topicService) {
        this.topicService = topicService;
        this.topicsTree = [];
    }
    HomeComponent.prototype.ngOnInit = function () {
        var _this = this;
        // get topics tree from API
        this.topicService.getTopics()
            .subscribe(function (topicsTree) {
            _this.topicsTree = topicsTree;
        });
    };
    HomeComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            moduleId: module.i,
            template: __webpack_require__("./src/app/_components/home/home.component.html"),
            providers: [__WEBPACK_IMPORTED_MODULE_1__services_index__["c" /* TopicService */]],
            styles: [__webpack_require__("./src/app/_components/home/home.component.scss")]
        }),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__services_index__["c" /* TopicService */]])
    ], HomeComponent);
    return HomeComponent;
}());



/***/ }),

/***/ "./src/app/_components/home/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__home_component__ = __webpack_require__("./src/app/_components/home/home.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__home_component__["a"]; });



/***/ }),

/***/ "./src/app/_components/home/topic/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__topic_component__ = __webpack_require__("./src/app/_components/home/topic/topic.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__topic_component__["a"]; });



/***/ }),

/***/ "./src/app/_components/home/topic/lesson/bad-dialog/bad-dialog.component.html":
/***/ (function(module, exports) {

module.exports = "<h2 mat-dialog-title>Incorrect :(</h2>\n<mat-dialog-content>\n    <div *ngIf=\"(answers.length == 1) && showAnswer\">\n        Correct answer is: {{answers[0].value}}\n    </div>\n    <div *ngIf=\"(answers.length != 1) && showAnswer\">\n        Correct answers are: \n        <ul>\n            <li *ngFor=\"let answer of answers; let answerIndex = 'index'\">\n                {{answer.value}}\n            </li>\n        </ul>\n    </div>\n    <div *ngIf=\"explanation!=''\">\n        {{explanation}}\n    </div>\n</mat-dialog-content>\n<mat-dialog-actions>\n    <button mat-button [mat-dialog-close]=\"false\" class='continue-button'>Continue</button>\n    <button mat-button [mat-dialog-close]=\"true\" class='error-button'>Report Error!</button>\n</mat-dialog-actions>"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/bad-dialog/bad-dialog.component.scss":
/***/ (function(module, exports) {

module.exports = "div {\n  min-height: 40px; }\n\n.continue-button {\n  background-color: #fef65b; }\n\n.error-button {\n  background-color: #ff4444; }\n"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/bad-dialog/bad-dialog.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return BadDialogComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_material__ = __webpack_require__("./node_modules/@angular/material/esm5/material.es5.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
var __param = (this && this.__param) || function (paramIndex, decorator) {
    return function (target, key) { decorator(target, key, paramIndex); }
};


var BadDialogComponent = (function () {
    function BadDialogComponent(dialogRef, data) {
        this.dialogRef = dialogRef;
        this.data = data;
        this.answers = data.data;
        this.explanation = data.explanation;
        this.showAnswer = data.showAnswers;
        setTimeout(function () {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
        }, 50);
    }
    BadDialogComponent.prototype.onNoClick = function () {
        this.dialogRef.close();
    };
    BadDialogComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            selector: 'bad-dialog',
            template: __webpack_require__("./src/app/_components/home/topic/lesson/bad-dialog/bad-dialog.component.html"),
            styles: [__webpack_require__("./src/app/_components/home/topic/lesson/bad-dialog/bad-dialog.component.scss")]
        }),
        __param(1, Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Inject"])(__WEBPACK_IMPORTED_MODULE_1__angular_material__["a" /* MAT_DIALOG_DATA */])),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_material__["g" /* MatDialogRef */], Object])
    ], BadDialogComponent);
    return BadDialogComponent;
}());



/***/ }),

/***/ "./src/app/_components/home/topic/lesson/chart/chart.component.html":
/***/ (function(module, exports) {

module.exports = "<h2 id=\"chart-container\"></h2>\n<div id=\"controls\" *ngIf=\"chartControl > 0\" >\n  <p>Value</p>\n  <span *ngIf=\"chartControl == 1\">\n    <mat-form-field (change)=\"ngOnChanges()\">\n      <input matInput  *ngIf=\"chartType != 4\"\n        [(ngModel)]=\"chartValue\" type=\"number\" \n        [step]=\"chartStep\" [max]=\"chartMaxValue\" min=\"0\"/>\n      <input *ngIf=\"chartType == 4\" id='inputValue'\n        matInput [(ngModel)]=\"chartValue\" type=\"number\" \n        [step]=\"chartStep\" [max]=\"chartMaxValue\" [min]=\"chartStartValue\"/>\n      <mat-progress-bar class='progress' value={{percentValue}}>\n      </mat-progress-bar>\n    </mat-form-field> \n    <span *ngIf=\"chartType != 4\">\n      <span class='max-value' *ngIf=\"chartMaxValue != 1\" >/{{chartMaxValue}}</span>\n    </span>\n  </span>\n  <span *ngIf=\"chartControl == 2\">\n    <span *ngIf=\"chartType != 4\" >\n      <mat-slider (change)=\"ngOnChanges()\" [(ngModel)]=\"chartValue\"\n        [step]=\"chartStep\" [max]=\"chartMaxValue\" min=\"0\" \n        color=\"primary\" class='slider-control'>\n      </mat-slider>\n      <div class=\"value-label-container\">\n        <label *ngIf=\"chartType != 3\" >\n          <span *ngIf=\"!isSteepInteger\"> {{chartValue.toFixed(2)}} </span>\n          <span *ngIf=\"0\" > / {{chartMaxValue}} </span>\n          <span *ngIf=\"isSteepInteger\">\n            {{chartValue}}/{{chartMaxValue}}\n          </span>\n        </label>\n        <label *ngIf=\"chartType == 3\" >\n          <span> {{chartValue}} </span>\n          <span *ngIf=\"chartMaxValue != 1\" > / {{chartMaxValue}} </span>\n        </label>\n      </div> \n    </span>\n    <span *ngIf=\"chartType == 4\" >\n      <mat-slider (change)=\"ngOnChanges()\" [(ngModel)]=\"chartValue\"\n        [step]=\"chartStep\" [max]=\"endValue\" [min]=\"startValue\" \n        color=\"primary\" class='slider-control' id='inputValue'>\n      </mat-slider>\n      <div class=\"value-label-container\">\n        <label *ngIf=\"!isSteepInteger\">{{chartValue.toFixed(2)}}</label>\n        <label *ngIf=\"isSteepInteger\">{{chartValue}}</label>\n      </div> \n    </span>\n  </span>\n</div>"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/chart/chart.component.scss":
/***/ (function(module, exports) {

module.exports = "input {\n  text-align: center;\n  font-weight: bold; }\n\n#chart-container {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n      -ms-flex-direction: column;\n          flex-direction: column;\n  margin: 0;\n  padding: 0; }\n\n#controls {\n  margin-top: 8px;\n  padding: 0; }\n\n.slider-control {\n  min-width: 250px; }\n\n.center {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center; }\n\n.value-label-container, .value-label-container label,\nmat-form-field, #controls > * {\n  margin: 0;\n  padding: 0; }\n\n.progress {\n  width: 100%;\n  height: 4px;\n  z-index: 2;\n  position: absolute;\n  top: 25px;\n  left: 0; }\n\n.max-value {\n  font-weight: bold; }\n"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/chart/chart.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ChartComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};

var ChartComponent = (function () {
    function ChartComponent() {
        this.mainColor = "#f7f7f7";
        this.selectedColor = "#ff4444";
        this.strokeColor = "#111";
        this.strokeWidth = 1;
        this.dotRadius = 4;
        this.chartType = 1;
        this.chartControl = 0;
        this.chartValue = 0.50;
        this.chartMaxValue = 0;
        this.startValue = 0;
        this.endValue = 1;
        this.chartStep = 0.5;
        this.chartMarksList = [0, 0.5, 1];
        this.initialized = false;
        this.isSteepInteger = false;
        this.setClickPositionEventId = false;
        this.dots = [];
        if (!this.chartHeight)
            this.chartHeight = 250;
    }
    ChartComponent.prototype.ngOnInit = function () {
    };
    ChartComponent.prototype.ngOnDestroy = function () {
        this.destroyDotsChart();
        this.removeSetClickPositionEvent();
    };
    ChartComponent.prototype.ngOnChanges = function (changes) {
        if (this.oldQuestion != this.question) {
            this.oldQuestion = this.question;
            this.initialized = false;
        }
        this.destroyDotsChart();
        this.removeSetClickPositionEvent();
        this.buildChart();
        this.percentValue = Math.round(this.chartValue / this.chartMaxValue * 100);
    };
    // function to build charts
    ChartComponent.prototype.buildChart = function () {
        var _this = this;
        if (!this.initialized) {
            var chart = this.question
                .match(new RegExp(/[^{}]+(?=\}%%)/g));
            if (chart['0'].indexOf('type:') >= 0)
                this.chartType = parseFloat(chart['0']
                    .match(new RegExp(/type:([^;]*)(?=(;|$))/g))['0']
                    .replace('type:', ''));
            if (chart['0'].indexOf('value:') >= 0)
                this.chartValue = parseFloat(chart['0']
                    .match(new RegExp(/value:([^;]*)(?=(;|$))/g))['0']
                    .replace('value:', ''));
            if (chart['0'].indexOf('max:') >= 0) {
                this.chartMaxValue = parseFloat(chart['0']
                    .match(new RegExp(/max:([^;]*)(?=(;|$))/g))['0']
                    .replace('max:', ''));
            }
            if (chart['0'].indexOf('marks:') >= 0) {
                this.chartMarksList = chart['0']
                    .match(new RegExp(/marks:([^;]*)(?=(;|$))/g))['0']
                    .replace('marks:', '').split(',').map(Number);
                this.chartMaxValue = this.chartMarksList[this.chartMarksList.length - 1];
            }
            if (chart['0'].indexOf('step:') >= 0) {
                this.chartStep = parseFloat(chart['0']
                    .match(new RegExp(/step:([^;]*)(?=(;|$))/g))['0']
                    .replace('step:', ''));
            }
            if (chart['0'].indexOf('main-color:') >= 0) {
                this.mainColor = chart['0']
                    .match(new RegExp(/main-color:([^;]*)(?=(;|$))/g))['0']
                    .replace('main-color:', '');
            }
            if (chart['0'].indexOf('selected-color:') >= 0) {
                this.selectedColor = chart['0']
                    .match(new RegExp(/selected-color:([^;]*)(?=(;|$))/g))['0']
                    .replace('selected-color:', '');
            }
            if (chart['0'].indexOf('stroke-color:') >= 0) {
                this.strokeColor = chart['0']
                    .match(new RegExp(/stroke-color:([^;]*)(?=(;|$))/g))['0']
                    .replace('stroke-color:', '');
            }
            if (chart['0'].indexOf('stroke-width:') >= 0) {
                this.strokeWidth = chart['0']
                    .match(new RegExp(/stroke-width:([^;]*)(?=(;|$))/g))['0']
                    .replace('stroke-width:', '');
            }
            if (chart['0'].indexOf('control:') >= 0)
                this.chartControl = parseFloat(chart['0']
                    .match(new RegExp(/control:([^;]*)(?=(;|$))/g))['0']
                    .replace('control:', ''));
            this.initialized = true;
        }
        var chartHtml = '';
        if (this.chartType == 3) {
            this.chartStep >= 1
                ? this.chartStep = Math.round(this.chartStep)
                : this.chartStep = 1;
        }
        this.isSteepInteger = Number.isInteger(this.chartStep);
        var chartValuePercent = this.chartValue / this.chartMaxValue;
        var chartContainer = document.getElementById('chart-container');
        switch (this.chartType) {
            default:
            case 1:
                // Chart (type 1 - rectangle)
                chartHtml += '<svg style="height: '
                    + this.chartHeight + '; width:' + this.chartHeight + ';">';
                chartHtml += '<rect id="rect2" style="height:'
                    + this.chartHeight + ' !important; width: 100%;';
                chartHtml += ' fill: ' + this.mainColor + '; stroke: ' +
                    this.strokeColor + '; stroke-width: ' + this.strokeWidth + '"';
                chartHtml += '></rect>';
                chartHtml += '<rect id="rect1" style="y: ' +
                    (1 - chartValuePercent) * this.chartHeight + '; height:' +
                    chartValuePercent * this.chartHeight + ' !important; width: 100%;';
                chartHtml += ' fill: ' + this.selectedColor + '; stroke: ' +
                    this.strokeColor + '; stroke-width: ' + this.strokeWidth + '"';
                chartHtml += '></rect>';
                chartHtml += '</svg>';
                chartContainer.innerHTML = chartHtml;
                break;
            case 2:
                // Chart (type 2 - circle)
                var radius = this.chartHeight / 2;
                var angle = 2 * Math.PI * chartValuePercent;
                var x = radius + radius * Math.sin(angle);
                var y = radius - radius * Math.cos(angle);
                chartHtml += '<svg style="height: '
                    + this.chartHeight + '; width:' + this.chartHeight + ';">';
                if (chartValuePercent <= 0.999) {
                    chartHtml += '<circle id="circle2" style="r: ' + radius
                        + ' !important; cx: ' + radius + ' !important; cy: '
                        + radius + ' !important;';
                    chartHtml += ' fill: ' + this.mainColor + '; stroke: ' +
                        this.strokeColor + '; stroke-width: ' + this.strokeWidth + '" />';
                    chartHtml += '<path id="circle1" d="M' + radius + ',' + radius
                        + ' L' + radius + ',0 A' + radius + ',' + radius;
                    if (chartValuePercent <= 0.5) {
                        chartHtml += ' 1 0,1';
                    }
                    else {
                        chartHtml += ' 1 1,1';
                    }
                    chartHtml += ' ' + x + ', ' + y + ' z"';
                    chartHtml += 'style="fill: ' + this.selectedColor + '; stroke: ' +
                        this.strokeColor + '; stroke-width: ' + this.strokeWidth + '"';
                    chartHtml += '></path>';
                }
                else {
                    chartHtml += '<circle id="circle1" style="r: ' + radius
                        + ' !important; cx: ' + radius + ' !important; cy: '
                        + radius + ' !important;';
                    chartHtml += 'fill: ' + this.selectedColor + '; stroke: ' +
                        this.strokeColor + '; stroke-width: ' + this.strokeWidth + '"/>';
                }
                chartHtml += '</svg>';
                chartContainer.innerHTML = chartHtml;
                break;
            case 3:
                // Chart (type 3 - dots)
                var canvas_1 = document.createElement("canvas");
                requestAnimationFrame(function () {
                    chartContainer.innerHTML = chartHtml;
                    chartContainer.appendChild(canvas_1);
                });
                canvas_1.style.height = this.chartHeight + 'px';
                canvas_1.style.width = chartContainer.style.width;
                var ctx_1 = canvas_1.getContext("2d");
                for (var i = 0; i < this.chartMaxValue; i++) {
                    if (this.dots[i] == undefined) {
                        this.dots[i] = {
                            x: Math.random() * (canvas_1.width - this.dotRadius * 2),
                            y: Math.random() * (canvas_1.height - this.dotRadius * 2),
                            radius: this.dotRadius
                        };
                    }
                }
                this.dotsChartRebuildFunctionId = setInterval(function () {
                    ctx_1.clearRect(0, 0, canvas_1.width, canvas_1.height);
                    _this.drawDotsChart(_this.chartValue, _this.chartMaxValue, ctx_1, canvas_1);
                }, 80);
                break;
            case 4:
                // Chart (type 4 - slider)
                var circleDiameter = 2 * this.dotRadius;
                var width = chartContainer.offsetWidth;
                var indentation = circleDiameter + 5;
                this.startValue = Math.min.apply(null, this.chartMarksList);
                this.endValue = Math.max.apply(null, this.chartMarksList);
                chartHtml += '<svg style="width:' + width + 'px; height: 50px;">';
                chartHtml += '<line x1="' + indentation + '" y1="10" x2="'
                    + (width - indentation) + '" y2="10" style="stroke:'
                    + this.mainColor + '; stroke-width:'
                    + this.strokeWidth + '" />';
                width -= indentation * 2;
                for (var i = 0; i < (this.endValue - this.startValue); i += this.chartStep) {
                    var position = (i * width / (this.endValue - this.startValue)) + indentation;
                    chartHtml += '<circle cx="' + position + '" cy="10" r="'
                        + (circleDiameter / 2) + '" fill="' + this.strokeColor + '" />';
                }
                chartHtml += '<circle cx="' + (width + indentation) + '" cy="10" r="'
                    + (circleDiameter / 2) + '" fill="' + this.strokeColor + '" />';
                for (var i = 0; i < this.chartMarksList.length; i++) {
                    var position = ((this.chartMarksList[i] - this.startValue) / (this.endValue
                        - this.startValue) * width + indentation);
                    chartHtml += '<text x="' + position
                        + '" y="35" fill="' + this.strokeColor
                        + '" font-size="16" text-anchor="middle">'
                        + this.chartMarksList[i] + '</text>';
                }
                var currentPointX = (this.chartValue - this.startValue) / (this.endValue
                    - this.startValue) * width + indentation;
                chartHtml += '<circle cx="' + currentPointX + '" cy="10" r="'
                    + circleDiameter + '" fill="' + this.selectedColor + '" />';
                chartHtml += '</svg>';
                chartContainer.innerHTML = chartHtml;
                if (!this.setClickPositionEventId) {
                    chartContainer.addEventListener('click', this.setClickPosition.bind(this));
                    this.setClickPositionEventId = true;
                }
                break;
        }
    };
    // function to set value by clicking on top slider
    ChartComponent.prototype.setClickPosition = function (event) {
        var chartContainer = document.getElementById('chart-container');
        var pos = getAbsolutePosition(chartContainer);
        var x = event.pageX - pos.x;
        var circleDiameter = 2 * this.dotRadius;
        var width = chartContainer.offsetWidth;
        var indentation = circleDiameter + 5;
        this.chartValue = (x - indentation) * (this.endValue
            - this.startValue) / width + this.startValue;
        this.chartValue = Math.round(this.chartValue * 105) / 100;
        if (this.chartValue < this.startValue)
            this.chartValue = this.startValue;
        else if (this.chartValue > this.endValue)
            this.chartValue = this.endValue;
        this.buildChart();
        if (this.chartControl > 0) {
            document.getElementById('inputValue').focus();
        }
    };
    // function to draw Dots Chart
    ChartComponent.prototype.drawDotsChart = function (dotsNum, maxDotsNum, ctx, canvas) {
        for (var i = 0; i < dotsNum; i++) {
            this.dots[i] = this.drawDot(2, ctx, canvas, this.dots[i]);
        }
        for (var i = dotsNum; i < maxDotsNum; i++) {
            this.dots[i] = this.drawDot(1, ctx, canvas, this.dots[i]);
        }
    };
    // function to draw one Dot
    ChartComponent.prototype.drawDot = function (type, ctx, canvas, dot) {
        ctx.strokeStyle = this.strokeColor;
        ctx.lineWidth = this.strokeWidth;
        if (type == 1) {
            ctx.fillStyle = this.mainColor;
        }
        if (type == 2) {
            ctx.fillStyle = this.selectedColor;
        }
        dot.x += Math.random() * 2 - 1;
        dot.y += Math.random() * 2 - 1;
        // Check if dot goes beyond the field
        var dotDiameter = dot.radius * 2;
        if (dot.x > canvas.width - dotDiameter)
            dot.x = canvas.width - dotDiameter;
        if (dot.x < dotDiameter)
            dot.x = dotDiameter;
        if (dot.y > canvas.height - dotDiameter)
            dot.y = canvas.height - dotDiameter;
        if (dot.y < dotDiameter)
            dot.y = dotDiameter;
        ctx.beginPath();
        ctx.arc(dot.x, dot.y, dot.radius, 0, Math.PI * 2, true);
        ctx.fill();
        ctx.stroke();
        return dot;
    };
    // remove dot chart rebuild function if it exists
    ChartComponent.prototype.destroyDotsChart = function () {
        if (this.dotsChartRebuildFunctionId)
            clearInterval(this.dotsChartRebuildFunctionId);
    };
    // remove Set Click Position Event if it exists
    ChartComponent.prototype.removeSetClickPositionEvent = function () {
        if (this.setClickPositionEventId) {
            document.getElementById('chart-container')
                .removeEventListener('click', this.setClickPosition);
            this.setClickPositionEventId = false;
        }
    };
    __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
        __metadata("design:type", String)
    ], ChartComponent.prototype, "question", void 0);
    __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Input"])(),
        __metadata("design:type", Number)
    ], ChartComponent.prototype, "chartHeight", void 0);
    ChartComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            selector: 'chart',
            template: __webpack_require__("./src/app/_components/home/topic/lesson/chart/chart.component.html"),
            styles: [__webpack_require__("./src/app/_components/home/topic/lesson/chart/chart.component.scss")],
            changeDetection: __WEBPACK_IMPORTED_MODULE_0__angular_core__["ChangeDetectionStrategy"].OnPush
        }),
        __metadata("design:paramtypes", [])
    ], ChartComponent);
    return ChartComponent;
}());

// function to get absolute position of HTML element
function getAbsolutePosition(element) {
    var r = { x: element.offsetLeft, y: element.offsetTop };
    if (element.offsetParent) {
        var tmp = getAbsolutePosition(element.offsetParent);
        r.x += tmp.x;
        r.y += tmp.y;
    }
    return r;
}
;


/***/ }),

/***/ "./src/app/_components/home/topic/lesson/good-dialog/good-dialog.component.html":
/***/ (function(module, exports) {

module.exports = "<h2 mat-dialog-title>Correct!</h2>\n<mat-dialog-content></mat-dialog-content>\n<mat-dialog-actions>\n\t<button mat-button [mat-dialog-close]=\"true\" class='continue-button'>Continue</button>\n</mat-dialog-actions>"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/good-dialog/good-dialog.component.scss":
/***/ (function(module, exports) {

module.exports = ".continue-button {\n  background-color: #fef65b; }\n"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/good-dialog/good-dialog.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return GoodDialogComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_material__ = __webpack_require__("./node_modules/@angular/material/esm5/material.es5.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
var __param = (this && this.__param) || function (paramIndex, decorator) {
    return function (target, key) { decorator(target, key, paramIndex); }
};


var GoodDialogComponent = (function () {
    function GoodDialogComponent(dialogRef, data) {
        this.dialogRef = dialogRef;
        this.data = data;
    }
    GoodDialogComponent.prototype.onNoClick = function () {
        this.dialogRef.close();
    };
    GoodDialogComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            selector: 'good-dialog',
            template: __webpack_require__("./src/app/_components/home/topic/lesson/good-dialog/good-dialog.component.html"),
            styles: [__webpack_require__("./src/app/_components/home/topic/lesson/good-dialog/good-dialog.component.scss")]
        }),
        __param(1, Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Inject"])(__WEBPACK_IMPORTED_MODULE_1__angular_material__["a" /* MAT_DIALOG_DATA */])),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_material__["g" /* MatDialogRef */], Object])
    ], GoodDialogComponent);
    return GoodDialogComponent;
}());



/***/ }),

/***/ "./src/app/_components/home/topic/lesson/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__lesson_component__ = __webpack_require__("./src/app/_components/home/topic/lesson/lesson.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "d", function() { return __WEBPACK_IMPORTED_MODULE_0__lesson_component__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__good_dialog_good_dialog_component__ = __webpack_require__("./src/app/_components/home/topic/lesson/good-dialog/good-dialog.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "c", function() { return __WEBPACK_IMPORTED_MODULE_1__good_dialog_good_dialog_component__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__bad_dialog_bad_dialog_component__ = __webpack_require__("./src/app/_components/home/topic/lesson/bad-dialog/bad-dialog.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_2__bad_dialog_bad_dialog_component__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__report_dialog_report_dialog_component__ = __webpack_require__("./src/app/_components/home/topic/lesson/report-dialog/report-dialog.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "e", function() { return __WEBPACK_IMPORTED_MODULE_3__report_dialog_report_dialog_component__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__chart_chart_component__ = __webpack_require__("./src/app/_components/home/topic/lesson/chart/chart.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "b", function() { return __WEBPACK_IMPORTED_MODULE_4__chart_chart_component__["a"]; });







/***/ }),

/***/ "./src/app/_components/home/topic/lesson/lesson.component.html":
/***/ (function(module, exports) {

module.exports = "<span class=\"grey-theme\">\n <a routerLink=\"/topic/{{topic_id}}\" routerLinkActive=\"active\" class=\"backButton left\"><-Back</a>\n<mat-progress-bar color=\"accent\" *ngIf=\"question_num > 0\" mode=determinate value={{complete_percent}} ></mat-progress-bar>\n<label *ngIf=\"question_num > 0\" style=\"display: flex; justify-content: center;\">{{correct_answers}}/{{question_num}}</label>\n<div class=\"text-center\">\n    <div *ngIf=\"question !== null\">\n        <h2 [innerHtml]=\"question.question\" *ngIf=\"!is_chart\" class=\"question\"></h2>\n        <span  *ngIf=\"is_chart\" >\n            <h2 [innerHtml]=\"questionForChart\" *ngIf=\"is_chart\" class=\"question\"></h2>\n            <chart [question]=\"question['question']\" chartHeight=\"250\" ></chart>\n        </span>\n        <span class=\"answer\">\n            <div *ngIf=\"question.answer_mode=='order'\" [sortablejs]=\"answers\" class=\"order-container\">\n                <div *ngFor=\"let answer of answers\" class=\"order-item\">{{answer}}</div>\n            </div>\n            <div *ngIf=\"question.answer_mode=='radio'\">\n                <mat-radio-group class=\"radio-group\" [(ngModel)]=\"answers[0]\" >\n                    <mat-radio-button class=\"radio-button\" *ngFor=\"let answer of question.answers; let answerIndex = index\" value=\"{{answerIndex}}\" color=\"primary\">\n                        {{answer.value}}\n                    </mat-radio-button>\n                </mat-radio-group>\n            </div>\n            <div *ngIf=\"question.answer_mode=='TF'\">\n                <mat-radio-group class=\"radio-group\" [(ngModel)]=\"answers[0]\" >\n                    <mat-radio-button class=\"radio-button\" value=\"False\" color=\"primary\">\n                        false\n                    </mat-radio-button>\n                    <mat-radio-button class=\"radio-button\" value=\"True\" color=\"primary\">\n                        true\n                    </mat-radio-button>\n                </mat-radio-group>\n            </div>\n            <div *ngIf=\"question.answer_mode=='checkbox'\">\n                <div *ngFor=\"let answer of question.answers; let answerIndex = 'index'\">\n                    <mat-checkbox [(ngModel)]=\"answers[answerIndex]\" color=\"primary\"> \n                        {{answer.value}} \n                    </mat-checkbox>\n                </div>\n            </div>\n            <div  *ngIf=\"question.answer_mode=='input'\">\n                <input *ngFor=\"let answer of question.answers; let answerIndex = 'index'\" [(ngModel)]=\"answers[answerIndex]\" name=\"'answers[{{answerIndex}}]'\"\n                (keyup.enter) = \"checkAnswer()\" class=\"input\">\n            </div>\n        </span>\n        <br />\n        <button (click)=\"checkAnswer()\"\n            mat-raised-button\n            style=\"color: #000; background-color: #f5f5f5; \">\n            <span>Continue</span>\n        </button>\n    </div>\n    <div *ngIf=\"question === null\">\n        <div *ngIf=\"initial_loading == 1\">\n            <h2>Loading....!</h2>\n        </div>\n        <div *ngIf=\"initial_loading == 0 && lesson_id != -1\">\n            <h2>Congratulations!</h2>\n            <h3>You have finished this lesson.</h3>\n            <a\n                class=\"button-container\"\n                routerLink=\"/topic/{{topic_id}}/lesson/{{next}}\"\n                routerLinkActive=\"active\"\n                *ngIf=\"next != 0\">\n                <button\n                    mat-raised-button\n                    style=\"margin: 16px; color: #000; background-color: #f5f5f5;\">\n                    <mat-icon>done all</mat-icon>\n                    <span>Go to next lesson</span>\n                </button>\n            </a>\n            <a\n                class=\"button-container\"\n                routerLink=\"/topic/{{topic_id}}\"\n                routerLinkActive=\"active\"\n                *ngIf=\"next == 0\">\n                <button\n                    mat-raised-button\n                    style=\"margin: 16px; color: #000; background-color: #f5f5f5;\">\n                    <mat-icon>done all</mat-icon>\n                    <span>Go back to topic</span>\n                </button>\n            </a>\n        </div>\n        <div *ngIf=\"initial_loading == 0 && lesson_id == -1\">\n            <h2>Congratulations!</h2>\n            <h3>You have finished this topic.</h3>\n            <a\n                class=\"button-container\"\n                routerLink=\"/topic/{{next}}\"\n                routerLinkActive=\"active\"\n                *ngIf=\"next != 0\">\n                <button\n                    mat-raised-button\n                    style=\"margin: 16px; color: #000; background-color: #f5f5f5;\">\n                    <mat-icon>done all</mat-icon>\n                    <span>Go to next topic</span>\n                </button>\n            </a>\n        </div>\n    </div>\n</div>   \n</span>"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/lesson.component.scss":
/***/ (function(module, exports) {

module.exports = ".order-container {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n      -ms-flex-direction: column;\n          flex-direction: column; }\n\n.order-item {\n  background-color: rgba(17, 17, 17, 0.5);\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  min-width: 50px;\n  width: 80%;\n  line-height: 35px;\n  margin: 4px;\n  color: #fff; }\n\n.radio-button {\n  text-align: left; }\n\n.input {\n  text-align: center; }\n"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/lesson.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return LessonComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("./src/app/_services/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__angular_material__ = __webpack_require__("./node_modules/@angular/material/esm5/material.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__good_dialog_good_dialog_component__ = __webpack_require__("./src/app/_components/home/topic/lesson/good-dialog/good-dialog.component.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__bad_dialog_bad_dialog_component__ = __webpack_require__("./src/app/_components/home/topic/lesson/bad-dialog/bad-dialog.component.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__report_dialog_report_dialog_component__ = __webpack_require__("./src/app/_components/home/topic/lesson/report-dialog/report-dialog.component.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};









var LessonComponent = (function () {
    function LessonComponent(router, topicService, trackingService, route, dialog) {
        this.router = router;
        this.topicService = topicService;
        this.trackingService = trackingService;
        this.route = route;
        this.dialog = dialog;
        this.lessonTree = [];
        this.question = null;
        this.answer = '';
        this.weak_questions = [];
        this.start_time = '';
        this.initial_loading = 1;
        this.next = 0;
        this.questionForChart = '';
        this.max_incorrect_answers = 1;
        if (localStorage.getItem('question_num') != undefined) {
            this.question_num = Number(localStorage.getItem('question_num'));
        }
        else {
            this.question_num = 4;
        }
        this.is_chart = false;
    }
    LessonComponent.prototype.ngOnInit = function () {
        var _this = this;
        this.question_num = +localStorage.getItem('question_num');
        this.incorrect_answers = 0;
        this.sub = this.route.params.subscribe(function (params) {
            _this.topic_id = +params['topic_id']; // (+) converts string 'id' to a number
            _this.lesson_id = (params['lesson_id'] == "testout") ? -1 :
                +params['lesson_id']; // (+) converts string 'id' to a number
            if (_this.lesson_id == -1) {
                _this.question_num = 0;
            }
            // get lesson tree from API
            _this.topicService.getLesson(_this.topic_id, _this.lesson_id)
                .subscribe(function (lessonTree) {
                _this.lessonTree = lessonTree;
                _this.initial_loading = 0;
                if (lessonTree['questions'].length) {
                    if (_this.question_num >= _this.lessonTree['questions'].length)
                        _this.question_num = _this.lessonTree['questions'].length;
                    _this.nextQuestion();
                    _this.trackingService.startLesson(_this.lesson_id)
                        .subscribe(function (start_time) {
                        _this.start_time = start_time;
                    });
                }
                if (_this.lesson_id == -1) {
                    _this.next = lessonTree['next_topic_id'];
                }
                else {
                    _this.next = lessonTree['next_lesson_id'];
                }
                _this.correct_answers = _this.complete_percent = 0;
            });
        });
    };
    LessonComponent.prototype.nextQuestion = function () {
        this.answers = [];
        this.question = this.lessonTree['questions'].shift();
        this.is_chart = false;
        if (this.question['question'].indexOf('%%chart{') >= 0) {
            this.is_chart = true;
            this.questionForChart = this.question['question']
                .replace(new RegExp(/%%chart(.*)(?=%)%/g), "");
        }
        if (['mcqms'].indexOf(this.question.reply_mode) >= 0) {
            for (var i = 0; i < this.question.answers.length; i++) {
                this.answers.push('');
            }
            this.question.answer_mode = 'checkbox';
        }
        else if (['mcq'].indexOf(this.question.reply_mode) >= 0) {
            this.answers.push('');
            this.question.answer_mode = 'radio';
        }
        else if (['TF'].indexOf(this.question.reply_mode) >= 0) {
            this.answers.push('');
            this.question.answer_mode = 'TF';
        }
        else if (['order'].indexOf(this.question.reply_mode) >= 0) {
            for (var i = 0; i < this.question.answers.length; i++) {
                this.answers.push(this.question.answers[i].value);
            }
            this.answers = this.shuffle(this.answers);
            this.question.answer_mode = 'order';
        }
        else {
            for (var i = 0; i < this.question.answers.length; i++) {
                this.answers.push('');
            }
            this.question.answer_mode = 'input';
        }
        setTimeout(function () {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
        }, 50);
    };
    LessonComponent.prototype.checkAnswer = function () {
        var _this = this;
        // sort question answers
        if (this.question.question_order) {
            this.question.answers.sort(function (a, b) {
                return a.value - b.value;
            });
            // check if all answers are numbers
            var isNumbers = true;
            for (var i = 0; i < this.answers.length; i++) {
                var answer = this.answers[i].replace(",", ".");
                if (isNaN(+answer)) {
                    isNumbers = false;
                    break;
                }
            }
            if (isNumbers) {
                for (var i = 0; i < this.answers.length; i++)
                    this.answers[i] = this.answers[i].replace(",", ".");
                this.answers.sort(function (a, b) {
                    return +a - +b;
                });
                //console.log("NUM: "+this.answers);
            }
            else {
                this.answers.sort();
                //console.log("STR: "+this.answers);
            }
        }
        if (this.isCorrect()) {
            this.correct_answers++;
            this.complete_percent = (this.correct_answers == 0) ? 0
                : this.correct_answers / this.question_num * 100;
            //if we have enough correct responces just remove rest of the questions
            if (this.correct_answers == this.question_num
                && this.question_num != 0) {
                this.lessonTree['questions'] = [];
            }
            var dialogRef = this.dialog.open(__WEBPACK_IMPORTED_MODULE_4__good_dialog_good_dialog_component__["a" /* GoodDialogComponent */], {
                width: '300px',
                data: {}
            });
            dialogRef.afterClosed().subscribe(function (result) {
                if (_this.lessonTree['questions'].length) {
                    _this.nextQuestion();
                }
                else {
                    _this.question = null;
                    _this.trackingService.doneLesson(_this.topic_id, _this.lesson_id, _this.start_time, _this.weak_questions).subscribe();
                }
            });
        }
        else {
            if (this.weak_questions.indexOf(this.question.id) === -1) {
                this.weak_questions.push(this.question.id);
            }
            this.incorrect_answers++;
            if (this.lesson_id == -1 &&
                this.incorrect_answers > this.max_incorrect_answers) {
                this.router.navigate(['/topic/' + this.topic_id]);
            }
            else {
                this.lessonTree['questions'].push(this.question);
            }
            var dialogRef = this.dialog.open(__WEBPACK_IMPORTED_MODULE_5__bad_dialog_bad_dialog_component__["a" /* BadDialogComponent */], {
                width: '300px',
                data: { data: this.question.answers.filter(function (answer) {
                        if (answer.is_correct == 1)
                            return true;
                        return false;
                    }), explanation: this.question.explanation,
                    showAnswers: (this.lesson_id == -1) ? false : true
                }
            });
            dialogRef.afterClosed().subscribe(function (result) {
                if (result) {
                    var reportDialogRef = _this.dialog.open(__WEBPACK_IMPORTED_MODULE_6__report_dialog_report_dialog_component__["a" /* ReportDialogComponent */], {
                        //width: '300px',
                        data: { question_id: _this.question.id, answers: _this.answers }
                    });
                    reportDialogRef.afterClosed().subscribe(function (result) {
                        console.log(result);
                        _this.topicService.reportError(result.question_id, result.answers, result.option, result.text).subscribe();
                    });
                }
                if (_this.lessonTree['questions'].length) {
                    _this.nextQuestion();
                }
                else {
                    _this.question = null;
                    _this.trackingService.doneLesson(_this.topic_id, _this.lesson_id, _this.start_time, _this.weak_questions).subscribe();
                }
            });
            if (this.lesson_id == -1) {
                this.question_num--;
            }
            else {
                this.correct_answers = this.complete_percent = 0;
            }
        }
    };
    LessonComponent.prototype.isCorrect = function () {
        if (this.question.answer_mode == 'radio') {
            if (this.answers[0] === "")
                return false;
            var answer = +this.answers[0];
            if (answer < 0 || answer >= this.question.answers.length)
                return false;
            if (this.question.answers[answer].is_correct) {
                return true;
            }
        }
        else {
            if (this.answers.length < this.question.answers.length) {
                return false;
            }
            for (var i = 0; i < this.question.answers.length; i++) {
                if (this.question.answer_mode == 'checkbox') {
                    if (this.question.answers[i].is_correct && this.answers[i] === ""
                        || !this.question.answers[i].is_correct && this.answers[i] !== "") {
                        return false;
                    }
                }
                else {
                    if (this.answers[i] === "")
                        return false;
                    if (this.question.conversion) {
                        this.answers[i] = this.answers[i].replace(/[^\d.-\/]/g, '');
                        var temp = this.answers[i].split("/");
                        if (temp[1] != undefined) {
                            this.answers[i] = (Number(temp[0]) / Number(temp[1])) + "";
                        }
                        else {
                            this.answers[i] = temp[0] + "";
                        }
                    }
                    if (this.question.rounding) {
                        this.answers[i] = this.answers[i].replace(/[^\d.-]/g, '');
                        var temp = this.question.answers[i].value.split(".");
                        var roundTo = 0;
                        if (temp[1] != undefined) {
                            roundTo = temp[1].length;
                        }
                        this.answers[i] = Number(this.answers[i]).toFixed(roundTo) + "";
                    }
                    if (this.question.answers[i].is_correct &&
                        this.question.answers[i].value != this.answers[i]) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    };
    // function to shuffle answers in order
    LessonComponent.prototype.shuffle = function (array) {
        var currentIndex = array.length, temporaryValue, randomIndex;
        // While there remain elements to shuffle...
        while (0 !== currentIndex) {
            // Pick a remaining element...
            randomIndex = Math.floor(Math.random() * currentIndex);
            currentIndex -= 1;
            // And swap it with the current element.
            temporaryValue = array[currentIndex];
            array[currentIndex] = array[randomIndex];
            array[randomIndex] = temporaryValue;
        }
        return array;
    };
    LessonComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            moduleId: module.i,
            template: __webpack_require__("./src/app/_components/home/topic/lesson/lesson.component.html"),
            providers: [__WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */], __WEBPACK_IMPORTED_MODULE_2__services_index__["d" /* TrackingService */]],
            styles: [__webpack_require__("./src/app/_components/home/topic/lesson/lesson.component.scss")]
        }),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */],
            __WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */],
            __WEBPACK_IMPORTED_MODULE_2__services_index__["d" /* TrackingService */],
            __WEBPACK_IMPORTED_MODULE_1__angular_router__["a" /* ActivatedRoute */],
            __WEBPACK_IMPORTED_MODULE_3__angular_material__["e" /* MatDialog */]])
    ], LessonComponent);
    return LessonComponent;
}());



/***/ }),

/***/ "./src/app/_components/home/topic/lesson/report-dialog/report-dialog.component.html":
/***/ (function(module, exports) {

module.exports = "<h2 mat-dialog-title>Please specify reason</h2>\n<mat-dialog-content>\n    <mat-radio-group class=\"radio-group\" [(ngModel)]=\"selectedOption\">\n      <mat-radio-button class=\"radio-button\" *ngFor=\"let option of options; let optionIndex = index\" [value]=\"optionIndex\">\n        {{option}}\n      </mat-radio-button>\n    </mat-radio-group>\n</mat-dialog-content>\n<mat-form-field *ngIf=\"selectedOption == 3\">\n    <input matInput [(ngModel)]=\"custom\">\n</mat-form-field>\n<mat-dialog-actions>\n    <button mat-button [mat-dialog-close]=\"{option: options[selectedOption], text: custom, question_id: question_id, answers: answers}\" class='send-button'>Send</button>\n    <button mat-button [mat-dialog-close]=\"false\" class='cancel-button'>Cancel</button>\n</mat-dialog-actions>"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/report-dialog/report-dialog.component.scss":
/***/ (function(module, exports) {

module.exports = ".send-button {\n  background-color: #31698a; }\n\n.cancel-button {\n  background-color: #6dc066; }\n"

/***/ }),

/***/ "./src/app/_components/home/topic/lesson/report-dialog/report-dialog.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ReportDialogComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_material__ = __webpack_require__("./node_modules/@angular/material/esm5/material.es5.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
var __param = (this && this.__param) || function (paramIndex, decorator) {
    return function (target, key) { decorator(target, key, paramIndex); }
};


var ReportDialogComponent = (function () {
    function ReportDialogComponent(dialogRef, data) {
        this.dialogRef = dialogRef;
        this.data = data;
        this.options = [
            'Wording of question is confusing or unclear',
            'Answer is incorrect',
            'question does not belong in this topic',
            'other',
        ];
        this.custom = "";
        this.answers = data.answers;
        this.question_id = data.question_id;
    }
    ReportDialogComponent.prototype.onNoClick = function () {
        this.dialogRef.close();
    };
    ReportDialogComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            selector: 'report-dialog',
            template: __webpack_require__("./src/app/_components/home/topic/lesson/report-dialog/report-dialog.component.html"),
            styles: [__webpack_require__("./src/app/_components/home/topic/lesson/report-dialog/report-dialog.component.scss")]
        }),
        __param(1, Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Inject"])(__WEBPACK_IMPORTED_MODULE_1__angular_material__["a" /* MAT_DIALOG_DATA */])),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_material__["g" /* MatDialogRef */], Object])
    ], ReportDialogComponent);
    return ReportDialogComponent;
}());



/***/ }),

/***/ "./src/app/_components/home/topic/topic.component.html":
/***/ (function(module, exports) {

module.exports = "<a routerLink=\"/\" routerLinkActive=\"active\" class=\"backButton left\"><-Back</a>\n<div class=\"text-center\">\n    <h2>{{topicTree.title}}</h2>\n    <div *ngIf=\"topicTree.lessons && topicTree.lessons.length\">\n        <div *ngFor=\"let lesson of topicTree.lessons; let levelIndex = 'index'\" class=\"arrowRowContainer\">\n            <div class=\"arrowButtonContainer\" [ngClass]=\"lesson.status == 1 ? 'greenout': (lesson.status == 2) ? 'yellowout' : 'redout'\">\n                <a *ngIf=\"lesson.status == 1 || lesson.status == 2\" routerLink=\"/topic/{{topicTree.id}}/lesson/{{lesson.id}}\" routerLinkActive=\"active\">\n                    <div class=\"arrow\">\n                        <span>{{lesson.title}}</span>\n                    </div>\n                </a>\n                <div *ngIf=\"lesson.status == 0\" class=\"arrow\">\n                    <span>{{lesson.title}}</span>\n                </div>\n            </div>\n        </div>\n    </div>\n    <div *ngIf=\"(topicTree.lessons == undefined || !topicTree.lessons.length) && topicTree.next_topic_id != 0\">\n        <h3>More lessons comming soon, please continue with next topic!</h3>\n    </div>\n    <div *ngIf=\"(topicTree.lessons == undefined || !topicTree.lessons.length) && topicTree.next_topic_id == 0\">\n        <h3>More lessons comming soon!</h3>\n    </div>\n    <a\n        class=\"button-container\"\n        routerLink=\"/topic/{{topicTree.id}}/lesson/testout\"\n        routerLinkActive=\"active\"\n        *ngIf=\"topicTree.lessons && topicTree.lessons.length && !topicDone\">\n        <button mat-raised-button class=\"testout-button\">\n            <mat-icon>update</mat-icon>\n            <span>Test out to finish topic</span>\n        </button>\n    </a>\n    <a\n        class=\"button-container\"\n        routerLink=\"/topic/{{topicTree.next_topic_id}}\"\n        routerLinkActive=\"active\"\n        *ngIf=\"(topicTree.lessons == undefined || !topicTree.lessons.length || topicDone) && topicTree.next_topic_id != 0\">\n        <button mat-raised-button class=\"next-topic-button\">\n            <mat-icon>done all</mat-icon>\n            <span>Go to next topic</span>\n        </button>\n    </a>\n</div>"

/***/ }),

/***/ "./src/app/_components/home/topic/topic.component.scss":
/***/ (function(module, exports) {

module.exports = ".testout-button {\n  margin: 16px;\n  color: #000;\n  background-color: #f5f5f5; }\n\n.next-topic-button {\n  margin: 16px;\n  color: #000;\n  background-color: #f5f5f5; }\n"

/***/ }),

/***/ "./src/app/_components/home/topic/topic.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TopicComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("./src/app/_services/index.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};



var TopicComponent = (function () {
    function TopicComponent(topicService, route) {
        this.topicService = topicService;
        this.route = route;
        this.topicTree = [];
    }
    TopicComponent.prototype.ngOnInit = function () {
        var _this = this;
        this.topicDone = false;
        this.sub = this.route.params.subscribe(function (params) {
            _this.id = +params['id']; // (+) converts string 'id' to a number
            // In a real app: dispatch action to load the details here.
            // get topics tree from API
            _this.topicService.getTopic(_this.id)
                .subscribe(function (topicTree) {
                _this.topicTree = topicTree;
                var lessons = _this.topicTree.lessons;
                _this.topicDone = (_this.topicTree.status == 1);
            });
        });
    };
    TopicComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            moduleId: module.i,
            template: __webpack_require__("./src/app/_components/home/topic/topic.component.html"),
            styles: [__webpack_require__("./src/app/_components/home/topic/topic.component.scss")],
            providers: [__WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */]]
        }),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */],
            __WEBPACK_IMPORTED_MODULE_1__angular_router__["a" /* ActivatedRoute */]])
    ], TopicComponent);
    return TopicComponent;
}());



/***/ }),

/***/ "./src/app/_components/profile/profile.component.html":
/***/ (function(module, exports) {

module.exports = "<div class=\"profile\">\n  <mat-card id=\"profile-container\" class=\"grey-theme\">\n  \t<span class=\"card-title\">Change Profile Info:</span>\n    <form #changeProfile=\"ngForm\" (ngSubmit)=\"onChangeProfile()\">\n  \t\t<mat-input-container>\n        <input matInput\n          name=\"username\"\n          pattern=\"[a-zA-Z0-9]{2,255}\"\n          placeholder=\"Name\"\n          [(ngModel)]=\"user.username\" />\n        </mat-input-container>\n        <mat-input-container>\n          <input matInput\n            name=\"email\"\n            pattern=\"^\\S+@\\S+$\"\n            placeholder=\"Email\"\n            [(ngModel)]=\"user.email\" />\n        </mat-input-container> \n        <mat-input-container>\n          <label>Please set the number of consecutive correct answers  which will signify lesson completion for you. You can come back to profile to change the setting if you wish. (put 0 to answer all questions)</label>\n          <input matInput\n            name=\"question_num\"\n            pattern=\"[0-9]{1,2}\"\n            [(ngModel)]=\"user.questionNum\" />\n        </mat-input-container> \n        <div class=\"button-container\">\n          <button\n            mat-raised-button\n            type=\"submit\">\n            <mat-icon>update</mat-icon>\n            <span>Update</span>\n          </button>\n      \t</div>\n  \t</form>\n  </mat-card>\n\n  <mat-card class=\"grey-theme\">\n    <span class=\"card-title\">Change Password:</span>\n    <form #changePassword=\"ngForm\" (ngSubmit)=\"onChangePassword(newPassword.value, confirmedPassword.value)\">\n      <mat-input-container>\n        <input matInput\n          required=\"required\"\n          pattern=\".{6,30}\"\n          type=\"password\"\n          placeholder=\"New Password\"\n          #newPassword />\n      </mat-input-container>\n      <mat-input-container>\n        <input matInput\n          required=\"required\"\n          pattern=\".{6,30}\"\n          type=\"password\"\n          placeholder=\"Confirm Password\"\n          #confirmedPassword />\n      </mat-input-container>\n      <div class=\"alert alert-danger\" *ngIf=\"!passwordsMatch\">\n         <mat-icon>warning</mat-icon>\n         {{warningMessage}}\n      </div>\n      <div class=\"button-container\" >\n        <button id=\"change-password-button\"\n          mat-raised-button\n          type=\"submit\">\n          <mat-icon>update</mat-icon>\n          <span>Change Password</span>\n        </button>\n      </div>\n    </form>\n  </mat-card>\n\n</div>\n"

/***/ }),

/***/ "./src/app/_components/profile/profile.component.scss":
/***/ (function(module, exports) {

module.exports = ".profile {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  -webkit-box-orient: vertical;\n  -webkit-box-direction: normal;\n      -ms-flex-direction: column;\n          flex-direction: column;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n  -ms-flex-wrap: wrap;\n      flex-wrap: wrap;\n  padding: 0;\n  margin: 0; }\n\nmat-card {\n  margin: 10px;\n  padding: 16px;\n  min-width: 200px;\n  max-width: 600px; }\n\nspan.card-title {\n  display: table;\n  white-space: nowrap;\n  padding: 8px; }\n\nspan.card-title:before, span.card-title:after {\n  border-top: 1px solid #616161;\n  content: '';\n  display: table-cell;\n  position: relative;\n  top: 0.5em;\n  width: 45%; }\n\nspan.card-title:before {\n  right: 1.5%; }\n\nspan.card-title:after {\n  left: 1.5%; }\n\nmat-input-container {\n  width: 100%; }\n\n.button-container {\n  padding: 0;\n  margin: 0;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  -ms-flex-wrap: wrap;\n      flex-wrap: wrap; }\n\nbutton {\n  color: #fff;\n  background-color: #337AB7; }\n"

/***/ }),

/***/ "./src/app/_components/profile/profile.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ProfileComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__models_user__ = __webpack_require__("./src/app/_models/user.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_user_service__ = __webpack_require__("./src/app/_services/user.service.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__services_authentication_service__ = __webpack_require__("./src/app/_services/authentication.service.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};




var ProfileComponent = (function () {
    function ProfileComponent(userService, authenticationService) {
        this.userService = userService;
        this.authenticationService = authenticationService;
        this.user = new __WEBPACK_IMPORTED_MODULE_1__models_user__["a" /* User */]();
        this.passwordsMatch = true;
    }
    ProfileComponent.prototype.ngOnInit = function () {
        var _this = this;
        this.userService.getProfile()
            .subscribe(function (res) {
            _this.user.username = res['name'];
            _this.user.email = res['email'];
            _this.user.questionNum = res['question_num'];
            localStorage.setItem('question_num', res['question_num']);
        });
    };
    ProfileComponent.prototype.onChangeProfile = function () {
        var _this = this;
        this.userService.changeProfile(this.user)
            .subscribe(function (res) {
            localStorage.setItem('question_num', "" + _this.user.questionNum);
        });
    };
    ProfileComponent.prototype.onChangePassword = function (newPassword, confirmedPassword) {
        var _this = this;
        if (newPassword != confirmedPassword) {
            this.passwordsMatch = false;
            this.warningMessage = "Password does not match the confirm password!";
            return;
        }
        else if (newPassword == "") {
            this.passwordsMatch = false;
            this.warningMessage = "You can't use empty passwords!";
            return;
        }
        else {
            this.passwordsMatch = true;
            this.userService.changePassword(newPassword, confirmedPassword)
                .subscribe(function (res) {
                _this.authenticationService.login(_this.user.email, newPassword);
            }, function (error) { console.log(error); });
        }
    };
    ProfileComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            selector: 'app-profle',
            template: __webpack_require__("./src/app/_components/profile/profile.component.html"),
            styles: [__webpack_require__("./src/app/_components/profile/profile.component.scss")],
            providers: [__WEBPACK_IMPORTED_MODULE_2__services_user_service__["a" /* UserService */]]
        }),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_2__services_user_service__["a" /* UserService */],
            __WEBPACK_IMPORTED_MODULE_3__services_authentication_service__["a" /* AuthenticationService */]])
    ], ProfileComponent);
    return ProfileComponent;
}());



/***/ }),

/***/ "./src/app/_components/welcome/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__welcome_component__ = __webpack_require__("./src/app/_components/welcome/welcome.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__welcome_component__["a"]; });



/***/ }),

/***/ "./src/app/_components/welcome/login/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__login_component__ = __webpack_require__("./src/app/_components/welcome/login/login.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__login_component__["a"]; });



/***/ }),

/***/ "./src/app/_components/welcome/login/login.component.html":
/***/ (function(module, exports) {

module.exports = "<a routerLink=\"/welcome\" routerLinkActive=\"active\" class=\"backButton left\"><-Back Home</a>\n<div class=\"col-md-6 col-md-offset-3\">\n    <h2>Login</h2>\n    <form name=\"form\" (ngSubmit)=\"f.form.valid && login()\" #f=\"ngForm\" novalidate>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !username.valid }\">\n            <label for=\"username\">Email</label>\n            <input type=\"text\" class=\"form-control\" name=\"username\" [(ngModel)]=\"model.username\" #username=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !username.valid\" class=\"help-block\">Username is required</div>\n        </div>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !password.valid }\">\n            <label for=\"password\">Password</label>\n            <input type=\"password\" class=\"form-control\" name=\"password\" [(ngModel)]=\"model.password\" #password=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !password.valid\" class=\"help-block\">Password is required</div>\n        </div>\n        <div class=\"form-group\">\n            <button [disabled]=\"loading\" class=\"btn btn-primary\">Login</button>\n            <img *ngIf=\"loading\" src=\"data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==\" />\n            <div class=\"pull-right\">\n                <a routerLink=\"/register\" >Register account</a>\n            </div>\n        </div>\n        <div *ngIf=\"error\" class=\"alert alert-danger\">{{error}}</div>\n    </form>\n</div>\n"

/***/ }),

/***/ "./src/app/_components/welcome/login/login.component.scss":
/***/ (function(module, exports) {

module.exports = ""

/***/ }),

/***/ "./src/app/_components/welcome/login/login.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return LoginComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("./src/app/_services/index.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};



var LoginComponent = (function () {
    function LoginComponent(router, authenticationService) {
        this.router = router;
        this.authenticationService = authenticationService;
        this.model = {};
        this.loading = false;
        this.error = '';
    }
    LoginComponent.prototype.ngOnInit = function () {
        // reset login status
        this.authenticationService.logout();
    };
    LoginComponent.prototype.login = function () {
        var _this = this;
        this.loading = true;
        this.authenticationService.login(this.model.username, this.model.password)
            .subscribe(function (result) {
            if (result === true) {
                _this.router.navigate(['/']);
            }
            else {
                _this.error = 'Username or password is incorrect';
                _this.loading = false;
            }
        });
    };
    LoginComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            moduleId: module.i,
            template: __webpack_require__("./src/app/_components/welcome/login/login.component.html"),
            styles: [__webpack_require__("./src/app/_components/welcome/login/login.component.scss")]
        }),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */],
            __WEBPACK_IMPORTED_MODULE_2__services_index__["a" /* AuthenticationService */]])
    ], LoginComponent);
    return LoginComponent;
}());



/***/ }),

/***/ "./src/app/_components/welcome/register/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__register_component__ = __webpack_require__("./src/app/_components/welcome/register/register.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__register_component__["a"]; });



/***/ }),

/***/ "./src/app/_components/welcome/register/register.component.html":
/***/ (function(module, exports) {

module.exports = "<a routerLink=\"/welcome\" routerLinkActive=\"active\" class=\"backButton left\"><-Back Home</a>\n<div class=\"col-md-6 col-md-offset-3\">\n    <h2>Register</h2>\n    <form name=\"form\" (ngSubmit)=\"f.form.valid && password.value == repassword.value && register()\" #f=\"ngForm\" novalidate>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !username.valid }\">\n            <label for=\"username\">Username</label>\n            <input type=\"text\" class=\"form-control\" name=\"username\" [(ngModel)]=\"model.username\" #username=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !username.valid\" class=\"help-block\">Username is required</div>\n        </div>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !email.valid }\">\n            <label for=\"email\">Email</label>\n            <input type=\"email\" class=\"form-control\" name=\"email\" [(ngModel)]=\"model.email\" #email=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !email.valid\" class=\"help-block\">Email is required</div>\n        </div>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !password.valid }\">\n            <label for=\"password\">Password</label>\n            <input type=\"password\" class=\"form-control\" name=\"password\" [(ngModel)]=\"model.password\" #password=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !password.valid\" class=\"help-block\">Password is required</div>\n        </div>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': (f.submitted && !repassword.valid) || (f.submitted && password.value != repassword.value) }\">\n            <label for=\"repassword\">Password Confirm</label>\n            <input type=\"password\" class=\"form-control\" name=\"repassword\" [(ngModel)]=\"model.repassword\" #repassword=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !repassword.valid\" class=\"help-block\">Password Confirm is required</div>\n            <div *ngIf=\"f.submitted && repassword.valid && password.value != repassword.value\" class=\"help-block\">Password Confirm is wrong</div>\n        </div>\n        <div class=\"form-group\">\n            <button [disabled]=\"loading\" class=\"btn btn-primary\">Register</button>\n            <img *ngIf=\"loading\" src=\"data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==\" />\n        </div>\n        <div *ngIf=\"error\" class=\"alert alert-danger\">{{error}}</div>\n    </form>\n</div>\n"

/***/ }),

/***/ "./src/app/_components/welcome/register/register.component.scss":
/***/ (function(module, exports) {

module.exports = ""

/***/ }),

/***/ "./src/app/_components/welcome/register/register.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return RegisterComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("./src/app/_services/index.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};



var RegisterComponent = (function () {
    function RegisterComponent(router, authenticationService) {
        this.router = router;
        this.authenticationService = authenticationService;
        this.model = {};
        this.loading = false;
        this.error = '';
    }
    RegisterComponent.prototype.ngOnInit = function () {
        // reset login status
        this.authenticationService.logout();
    };
    RegisterComponent.prototype.register = function () {
        var _this = this;
        this.loading = true;
        this.authenticationService.register(this.model.username, this.model.email, this.model.password)
            .subscribe(function (result) {
            if (result['success'] === true) {
                _this.authenticationService.login(_this.model.email, _this.model.password).subscribe(function (result) {
                    if (result == true) {
                        _this.router.navigate(['/']);
                    }
                });
            }
            else {
                console.log(result['message']);
                if (result['message']['email']) {
                    _this.error = result['message']['email'];
                }
                else {
                    _this.error = result['message']['password'];
                }
                _this.loading = false;
            }
        });
    };
    RegisterComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            moduleId: module.i,
            template: __webpack_require__("./src/app/_components/welcome/register/register.component.html"),
            styles: [__webpack_require__("./src/app/_components/welcome/register/register.component.scss")]
        }),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */],
            __WEBPACK_IMPORTED_MODULE_2__services_index__["a" /* AuthenticationService */]])
    ], RegisterComponent);
    return RegisterComponent;
}());



/***/ }),

/***/ "./src/app/_components/welcome/try/try.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TryComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__services_index__ = __webpack_require__("./src/app/_services/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};



var TryComponent = (function () {
    function TryComponent(authenticationService, router) {
        this.authenticationService = authenticationService;
        this.router = router;
    }
    TryComponent.prototype.ngOnInit = function () {
        // reset login status
        this.authenticationService.logout();
    };
    TryComponent.prototype.onClick = function () {
        var _this = this;
        var id = this.randomString();
        var email = id + '@somemail.com';
        var password = id;
        var username = id;
        this.authenticationService.register(username, email, password)
            .subscribe(function (res) {
            if (res['success'] === true) {
                _this.authenticationService.login(email, password)
                    .subscribe(function (res) {
                    if (res == true) {
                        _this.router.navigate(['/']);
                    }
                });
            }
        });
    };
    TryComponent.prototype.randomString = function () {
        var length = 50; // max 64
        var id = "";
        var alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (var i = 0; i < length; i++) {
            id += alphabet.charAt(Math.floor(Math.random() * alphabet.length));
        }
        return id;
    };
    TryComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            selector: 'app-try',
            template: '<div>Try without registration</div>',
            host: { '(click)': 'onClick()' }
        }),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__services_index__["a" /* AuthenticationService */],
            __WEBPACK_IMPORTED_MODULE_2__angular_router__["c" /* Router */]])
    ], TryComponent);
    return TryComponent;
}());



/***/ }),

/***/ "./src/app/_components/welcome/welcome.component.html":
/***/ (function(module, exports) {

module.exports = "<div class=\"welcome\">\n  <h2 class=\"center\">Welcome!</h2>\n  <div class=\"buttons-container\">\n    <a mat-button routerLink=\"/register\" class=\"register-button\">Register account</a>\n    <a mat-button routerLink=\"/login\" class=\"login-button\">Login</a>\n    <button mat-button class=\"try-button\"> \n      <app-try></app-try>\n    </button>\n  </div>\n</div>\n"

/***/ }),

/***/ "./src/app/_components/welcome/welcome.component.scss":
/***/ (function(module, exports) {

module.exports = ".buttons-container {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  -webkit-box-align: center;\n      -ms-flex-align: center;\n          align-items: center;\n  -webkit-box-orient: horizontal;\n  -webkit-box-direction: normal;\n      -ms-flex-direction: row;\n          flex-direction: row;\n  -ms-flex-wrap: wrap;\n      flex-wrap: wrap; }\n  .buttons-container .register-button, .buttons-container .login-button, .buttons-container .try-button {\n    margin: 8px;\n    padding: 0;\n    width: 150px;\n    color: #fafafa;\n    text-decoration: none; }\n  .buttons-container .login-button {\n    background-color: #31698a; }\n  .buttons-container .register-button {\n    background-color: #6dc066; }\n  .buttons-container .try-button {\n    background-color: #daa520; }\n  .center {\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center; }\n"

/***/ }),

/***/ "./src/app/_components/welcome/welcome.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return WelcomeComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("./src/app/_services/index.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};



var WelcomeComponent = (function () {
    function WelcomeComponent(router, authenticationService) {
        this.router = router;
        this.authenticationService = authenticationService;
        this.model = {};
        this.loading = false;
        this.error = '';
    }
    WelcomeComponent.prototype.ngOnInit = function () {
        // reset login status
        this.authenticationService.logout();
    };
    WelcomeComponent.prototype.login = function () {
        var _this = this;
        this.loading = true;
        this.authenticationService.login(this.model.username, this.model.password)
            .subscribe(function (result) {
            if (result === true) {
                _this.router.navigate(['/']);
            }
            else {
                _this.error = 'Username or password is incorrect';
                _this.loading = false;
            }
        });
    };
    WelcomeComponent = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
            moduleId: module.i,
            template: __webpack_require__("./src/app/_components/welcome/welcome.component.html"),
            styles: [__webpack_require__("./src/app/_components/welcome/welcome.component.scss")]
        }),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */],
            __WEBPACK_IMPORTED_MODULE_2__services_index__["a" /* AuthenticationService */]])
    ], WelcomeComponent);
    return WelcomeComponent;
}());



/***/ }),

/***/ "./src/app/_guards/auth.guard.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AuthGuard; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};


var AuthGuard = (function () {
    function AuthGuard(router) {
        this.router = router;
    }
    AuthGuard.prototype.canActivate = function () {
        if (localStorage.getItem('currentUser')) {
            // logged in so return true
            return true;
        }
        // not logged in so redirect to login page
        this.router.navigate(['/welcome']);
        return false;
    };
    AuthGuard = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */]])
    ], AuthGuard);
    return AuthGuard;
}());



/***/ }),

/***/ "./src/app/_guards/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__auth_guard__ = __webpack_require__("./src/app/_guards/auth.guard.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__auth_guard__["a"]; });



/***/ }),

/***/ "./src/app/_models/user.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return User; });
var User = (function () {
    function User() {
    }
    return User;
}());



/***/ }),

/***/ "./src/app/_services/authentication.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AuthenticationService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_http__ = __webpack_require__("./node_modules/@angular/http/esm5/http.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_map__ = __webpack_require__("./node_modules/rxjs/_esm5/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__environments_environment__ = __webpack_require__("./src/environments/environment.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};




var AuthenticationService = (function () {
    function AuthenticationService(http) {
        this.http = http;
        this.apiUrl = __WEBPACK_IMPORTED_MODULE_3__environments_environment__["a" /* environment */].apiUrl;
        // set token if saved in local storage
        var currentUser = JSON.parse(localStorage.getItem('currentUser'));
        this.token = currentUser && currentUser.token;
    }
    AuthenticationService.prototype.login = function (username, password) {
        var _this = this;
        var request = JSON.stringify({ email: username, password: password });
        var headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Content-Type': 'application/json' }); // ... Set content type to JSON
        var options = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["e" /* RequestOptions */]({ headers: headers }); // Create a request option
        return this.http.post(this.apiUrl + '/authenticate', request, options)
            .map(function (response) {
            // login successful if there's a jwt token in the response
            var token = response.json() && response.json().message &&
                response.json().message.token;
            if (token) {
                // set token property
                _this.token = token;
                // store username and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('currentUser', JSON.stringify({ username: username, token: token }));
                var question_num = 5;
                if (response.json().message && response.json()
                    .message.question_num != undefined) {
                    question_num = response.json().message.question_num;
                }
                localStorage.setItem('question_num', question_num + "");
                // return true to indicate successful login
                return true;
            }
            else {
                // return false to indicate failed login
                return false;
            }
        });
    };
    AuthenticationService.prototype.register = function (username, email, password) {
        var request = JSON.stringify({ email: email, name: username, password: password });
        var headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Content-Type': 'application/json' }); // ... Set content type to JSON
        var options = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["e" /* RequestOptions */]({ headers: headers }); // Create a request option
        return this.http.post(this.apiUrl + '/register', request, options)
            .map(function (response) {
            // login successful if there's a jwt token in the response
            var token = response.json() && response.json()
                .message && response.json().message.token;
            return response.json();
        });
    };
    AuthenticationService.prototype.logout = function () {
        // clear token remove user from local storage to log user out
        this.token = null;
        localStorage.removeItem('currentUser');
    };
    AuthenticationService = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */]])
    ], AuthenticationService);
    return AuthenticationService;
}());



/***/ }),

/***/ "./src/app/_services/http.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return HttpService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_http__ = __webpack_require__("./node_modules/@angular/http/esm5/http.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_map__ = __webpack_require__("./node_modules/rxjs/_esm5/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_rxjs_add_operator_catch__ = __webpack_require__("./node_modules/rxjs/_esm5/add/operator/catch.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__environments_environment__ = __webpack_require__("./src/environments/environment.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__authentication_service__ = __webpack_require__("./src/app/_services/authentication.service.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};







var HttpService = (function () {
    function HttpService(http, router, authenticationService) {
        this.http = http;
        this.router = router;
        this.authenticationService = authenticationService;
        this.apiUrl = __WEBPACK_IMPORTED_MODULE_4__environments_environment__["a" /* environment */].apiUrl;
    }
    HttpService.prototype.post = function (url, body, auth) {
        var _this = this;
        if (auth === void 0) { auth = true; }
        if (auth) {
            // add authorization header with jwt token
            this.headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Authorization': 'Bearer '
                    + this.authenticationService.token, 'Content-Type': 'application/json' });
        }
        else {
            // add authorization header with jwt token
            this.headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Content-Type': 'application/json' });
        }
        var options = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["e" /* RequestOptions */]({ headers: this.headers });
        // post to api
        return this.http.post(this.apiUrl + url, body, options)
            .map(function (response) { return response.json().message; })
            .catch(function (response) {
            var json = response.json();
            if (json.status_code == 401) {
                _this.authenticationService.logout();
                _this.router.navigate(['login']);
            }
            return response.json().message;
        });
        ;
    };
    HttpService.prototype.get = function (url, auth) {
        var _this = this;
        if (auth === void 0) { auth = true; }
        if (auth) {
            // add authorization header with jwt token
            this.headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Authorization': 'Bearer '
                    + this.authenticationService.token, 'Content-Type': 'application/json' });
        }
        else {
            // add authorization header with jwt token
            this.headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Content-Type': 'application/json' });
        }
        var options = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["e" /* RequestOptions */]({ headers: this.headers });
        // get from api
        return this.http.get(this.apiUrl + url, options)
            .map(function (response) { return response.json().message; })
            .catch(function (response) {
            var json = response.json();
            if (json.status_code == 401) {
                _this.authenticationService.logout();
                _this.router.navigate(['login']);
            }
            return response.json().message;
        });
    };
    HttpService = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */],
            __WEBPACK_IMPORTED_MODULE_5__angular_router__["c" /* Router */],
            __WEBPACK_IMPORTED_MODULE_6__authentication_service__["a" /* AuthenticationService */]])
    ], HttpService);
    return HttpService;
}());



/***/ }),

/***/ "./src/app/_services/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__authentication_service__ = __webpack_require__("./src/app/_services/authentication.service.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__authentication_service__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__http_service__ = __webpack_require__("./src/app/_services/http.service.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "b", function() { return __WEBPACK_IMPORTED_MODULE_1__http_service__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__topic_service__ = __webpack_require__("./src/app/_services/topic.service.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "c", function() { return __WEBPACK_IMPORTED_MODULE_2__topic_service__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__tracking_service__ = __webpack_require__("./src/app/_services/tracking.service.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "d", function() { return __WEBPACK_IMPORTED_MODULE_3__tracking_service__["a"]; });






/***/ }),

/***/ "./src/app/_services/topic.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TopicService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map__ = __webpack_require__("./node_modules/rxjs/_esm5/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__http_service__ = __webpack_require__("./src/app/_services/http.service.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};



var TopicService = (function () {
    function TopicService(http) {
        this.http = http;
    }
    TopicService.prototype.getTopics = function () {
        // get topic from api
        return this.http.get('/topic')
            .map(function (response) { return response; });
    };
    TopicService.prototype.getTopic = function (id) {
        // get topic from api
        return this.http.get('/topic/' + id)
            .map(function (response) { return response; });
    };
    TopicService.prototype.getLesson = function (topic_id, lesson_id) {
        // get lesson from api
        if (lesson_id == -1) {
            return this.http.get('/topic/' + topic_id + '/testout')
                .map(function (response) { return response; });
        }
        else {
            return this.http.get('/topic/' + topic_id + '/lesson/' + lesson_id)
                .map(function (response) { return response; });
        }
    };
    TopicService.prototype.reportError = function (question_id, answers, option, custom) {
        // notify api about question error
        var request = JSON.stringify({ answers: answers,
            options: option, comment: custom });
        console.log(request);
        return this.http.post('/report_error/' + question_id, request)
            .map(function (response) { return response; });
    };
    TopicService = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_2__http_service__["a" /* HttpService */]])
    ], TopicService);
    return TopicService;
}());



/***/ }),

/***/ "./src/app/_services/tracking.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TrackingService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map__ = __webpack_require__("./node_modules/rxjs/_esm5/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__http_service__ = __webpack_require__("./src/app/_services/http.service.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};



var TrackingService = (function () {
    function TrackingService(http) {
        this.http = http;
    }
    TrackingService.prototype.startLesson = function (lesson_id) {
        // notify api about lesson start
        if (lesson_id == -1) {
            /** TODO: change this HARDCODED value to testoutstart! */
            return this.http.post('/', '')
                .map(function (response) { return response; });
        }
        else {
            return this.http.post('/lesson/' + lesson_id + '/start', '')
                .map(function (response) { return response; });
        }
    };
    TrackingService.prototype.doneLesson = function (topic_id, lesson_id, start_datetime, weak_questions) {
        // notify api about lesson done
        var request = JSON.stringify({ start_datetime: start_datetime,
            weak_questions: weak_questions });
        if (lesson_id == -1) {
            return this.http.post('/topic/' + topic_id + '/testoutdone', request)
                .map(function (response) { return response; });
        }
        else {
            return this.http.post('/lesson/' + lesson_id + '/done', request)
                .map(function (response) { return response; });
        }
    };
    TrackingService = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_2__http_service__["a" /* HttpService */]])
    ], TrackingService);
    return TrackingService;
}());



/***/ }),

/***/ "./src/app/_services/user.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return UserService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map__ = __webpack_require__("./node_modules/rxjs/_esm5/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__http_service__ = __webpack_require__("./src/app/_services/http.service.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};



var UserService = (function () {
    function UserService(http) {
        this.http = http;
    }
    UserService.prototype.getProfile = function () {
        return this.http.get('/profile')
            .map(function (res) { return res; })
            .catch(function (error) {
            throw Error(error);
        });
    };
    UserService.prototype.changeProfile = function (user) {
        var request = JSON.stringify({
            name: user.username,
            email: user.email,
            question_num: user.questionNum
        });
        return this.http.post('/profile', request)
            .map(function (res) { })
            .catch(function (error) {
            console.log(error);
            throw Error(error);
        });
    };
    UserService.prototype.changePassword = function (newPassword, confirmedPassword) {
        var request = JSON.stringify({
            password: newPassword,
            confirm_password: confirmedPassword
        });
        return this.http.post('/profile', request)
            .map(function (res) { })
            .catch(function (error) {
            console.log(error);
            throw Error(error);
        });
    };
    UserService = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
        __metadata("design:paramtypes", [__WEBPACK_IMPORTED_MODULE_2__http_service__["a" /* HttpService */]])
    ], UserService);
    return UserService;
}());



/***/ }),

/***/ "./src/app/app.module.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AppModule; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_platform_browser__ = __webpack_require__("./node_modules/@angular/platform-browser/esm5/platform-browser.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__angular_forms__ = __webpack_require__("./node_modules/@angular/forms/esm5/forms.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__angular_http__ = __webpack_require__("./node_modules/@angular/http/esm5/http.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__angular_platform_browser_animations__ = __webpack_require__("./node_modules/@angular/platform-browser/esm5/animations.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__angular_material__ = __webpack_require__("./node_modules/@angular/material/esm5/material.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6_angular2_fontawesome_angular2_fontawesome__ = __webpack_require__("./node_modules/angular2-fontawesome/angular2-fontawesome.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6_angular2_fontawesome_angular2_fontawesome___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_6_angular2_fontawesome_angular2_fontawesome__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7__angular_flex_layout__ = __webpack_require__("./node_modules/@angular/flex-layout/esm5/flex-layout.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8_angular_sortablejs__ = __webpack_require__("./node_modules/angular-sortablejs/dist/index.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8_angular_sortablejs___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_8_angular_sortablejs__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_9__app_routing__ = __webpack_require__("./src/app/app.routing.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_10__guards_index__ = __webpack_require__("./src/app/_guards/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_11__services_index__ = __webpack_require__("./src/app/_services/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_12__components_app_component__ = __webpack_require__("./src/app/_components/app.component.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_13__components_welcome_index__ = __webpack_require__("./src/app/_components/welcome/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_14__components_welcome_login_index__ = __webpack_require__("./src/app/_components/welcome/login/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_15__components_welcome_register_index__ = __webpack_require__("./src/app/_components/welcome/register/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_16__components_welcome_try_try_component__ = __webpack_require__("./src/app/_components/welcome/try/try.component.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_17__components_home_index__ = __webpack_require__("./src/app/_components/home/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_18__components_home_topic_index__ = __webpack_require__("./src/app/_components/home/topic/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_19__components_home_topic_lesson_index__ = __webpack_require__("./src/app/_components/home/topic/lesson/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_20__components_profile_profile_component__ = __webpack_require__("./src/app/_components/profile/profile.component.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};






















var AppModule = (function () {
    function AppModule() {
    }
    AppModule = __decorate([
        Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["NgModule"])({
            imports: [
                __WEBPACK_IMPORTED_MODULE_1__angular_platform_browser__["a" /* BrowserModule */],
                __WEBPACK_IMPORTED_MODULE_2__angular_forms__["c" /* FormsModule */],
                __WEBPACK_IMPORTED_MODULE_3__angular_http__["d" /* HttpModule */],
                __WEBPACK_IMPORTED_MODULE_9__app_routing__["a" /* routing */],
                __WEBPACK_IMPORTED_MODULE_6_angular2_fontawesome_angular2_fontawesome__["Angular2FontawesomeModule"],
                __WEBPACK_IMPORTED_MODULE_4__angular_platform_browser_animations__["a" /* BrowserAnimationsModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["i" /* MatInputModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["b" /* MatButtonModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["m" /* MatSelectModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["h" /* MatIconModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["j" /* MatMenuModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["l" /* MatRadioModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["f" /* MatDialogModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["k" /* MatProgressBarModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["n" /* MatSliderModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["o" /* MatToolbarModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["c" /* MatCardModule */],
                __WEBPACK_IMPORTED_MODULE_5__angular_material__["d" /* MatCheckboxModule */],
                __WEBPACK_IMPORTED_MODULE_7__angular_flex_layout__["a" /* FlexLayoutModule */],
                __WEBPACK_IMPORTED_MODULE_8_angular_sortablejs__["SortablejsModule"].forRoot({ animation: 150 })
            ],
            declarations: [
                __WEBPACK_IMPORTED_MODULE_12__components_app_component__["a" /* AppComponent */],
                __WEBPACK_IMPORTED_MODULE_13__components_welcome_index__["a" /* WelcomeComponent */],
                __WEBPACK_IMPORTED_MODULE_14__components_welcome_login_index__["a" /* LoginComponent */],
                __WEBPACK_IMPORTED_MODULE_15__components_welcome_register_index__["a" /* RegisterComponent */],
                __WEBPACK_IMPORTED_MODULE_17__components_home_index__["a" /* HomeComponent */],
                __WEBPACK_IMPORTED_MODULE_18__components_home_topic_index__["a" /* TopicComponent */],
                __WEBPACK_IMPORTED_MODULE_19__components_home_topic_lesson_index__["d" /* LessonComponent */],
                __WEBPACK_IMPORTED_MODULE_19__components_home_topic_lesson_index__["c" /* GoodDialogComponent */],
                __WEBPACK_IMPORTED_MODULE_19__components_home_topic_lesson_index__["a" /* BadDialogComponent */],
                __WEBPACK_IMPORTED_MODULE_19__components_home_topic_lesson_index__["e" /* ReportDialogComponent */],
                __WEBPACK_IMPORTED_MODULE_19__components_home_topic_lesson_index__["b" /* ChartComponent */],
                __WEBPACK_IMPORTED_MODULE_20__components_profile_profile_component__["a" /* ProfileComponent */],
                __WEBPACK_IMPORTED_MODULE_16__components_welcome_try_try_component__["a" /* TryComponent */]
            ],
            entryComponents: [
                __WEBPACK_IMPORTED_MODULE_19__components_home_topic_lesson_index__["c" /* GoodDialogComponent */],
                __WEBPACK_IMPORTED_MODULE_19__components_home_topic_lesson_index__["a" /* BadDialogComponent */],
                __WEBPACK_IMPORTED_MODULE_19__components_home_topic_lesson_index__["e" /* ReportDialogComponent */]
            ],
            providers: [
                __WEBPACK_IMPORTED_MODULE_10__guards_index__["a" /* AuthGuard */],
                __WEBPACK_IMPORTED_MODULE_11__services_index__["a" /* AuthenticationService */],
                __WEBPACK_IMPORTED_MODULE_11__services_index__["b" /* HttpService */],
                // providers used to create fake backend
                //fakeBackendProvider,
                //MockBackend,
                __WEBPACK_IMPORTED_MODULE_3__angular_http__["a" /* BaseRequestOptions */]
            ],
            bootstrap: [__WEBPACK_IMPORTED_MODULE_12__components_app_component__["a" /* AppComponent */]]
        })
    ], AppModule);
    return AppModule;
}());



/***/ }),

/***/ "./src/app/app.routing.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return routing; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_router__ = __webpack_require__("./node_modules/@angular/router/esm5/router.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__components_welcome_index__ = __webpack_require__("./src/app/_components/welcome/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__components_welcome_login_index__ = __webpack_require__("./src/app/_components/welcome/login/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__components_welcome_register_index__ = __webpack_require__("./src/app/_components/welcome/register/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__components_home_index__ = __webpack_require__("./src/app/_components/home/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__components_home_topic_index__ = __webpack_require__("./src/app/_components/home/topic/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__components_home_topic_lesson_index__ = __webpack_require__("./src/app/_components/home/topic/lesson/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7__guards_index__ = __webpack_require__("./src/app/_guards/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8__components_profile_profile_component__ = __webpack_require__("./src/app/_components/profile/profile.component.ts");









var appRoutes = [
    { path: 'welcome', component: __WEBPACK_IMPORTED_MODULE_1__components_welcome_index__["a" /* WelcomeComponent */] },
    { path: 'login', component: __WEBPACK_IMPORTED_MODULE_2__components_welcome_login_index__["a" /* LoginComponent */] },
    { path: 'register', component: __WEBPACK_IMPORTED_MODULE_3__components_welcome_register_index__["a" /* RegisterComponent */] },
    { path: 'profile', component: __WEBPACK_IMPORTED_MODULE_8__components_profile_profile_component__["a" /* ProfileComponent */], canActivate: [__WEBPACK_IMPORTED_MODULE_7__guards_index__["a" /* AuthGuard */]] },
    { path: '', component: __WEBPACK_IMPORTED_MODULE_4__components_home_index__["a" /* HomeComponent */], canActivate: [__WEBPACK_IMPORTED_MODULE_7__guards_index__["a" /* AuthGuard */]] },
    { path: 'topic/:id', component: __WEBPACK_IMPORTED_MODULE_5__components_home_topic_index__["a" /* TopicComponent */], canActivate: [__WEBPACK_IMPORTED_MODULE_7__guards_index__["a" /* AuthGuard */]] },
    { path: 'topic/:topic_id/lesson/:lesson_id', component: __WEBPACK_IMPORTED_MODULE_6__components_home_topic_lesson_index__["d" /* LessonComponent */],
        canActivate: [__WEBPACK_IMPORTED_MODULE_7__guards_index__["a" /* AuthGuard */]] },
    // otherwise redirect to welcome
    { path: '**', redirectTo: 'welcome' }
];
var routing = __WEBPACK_IMPORTED_MODULE_0__angular_router__["d" /* RouterModule */].forRoot(appRoutes);


/***/ }),

/***/ "./src/environments/environment.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return environment; });
var environment = {
    production: true,
    apiUrl: 'http://healthnumeracyproject.com/api' // global API URL (for production build)
    //... more of your variables
};


/***/ }),

/***/ "./src/main.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("./node_modules/@angular/core/esm5/core.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_platform_browser_dynamic__ = __webpack_require__("./node_modules/@angular/platform-browser-dynamic/esm5/platform-browser-dynamic.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__app_app_module__ = __webpack_require__("./src/app/app.module.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__environments_environment__ = __webpack_require__("./src/environments/environment.ts");




if (__WEBPACK_IMPORTED_MODULE_3__environments_environment__["a" /* environment */].production) {
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["enableProdMode"])();
}
Object(__WEBPACK_IMPORTED_MODULE_1__angular_platform_browser_dynamic__["a" /* platformBrowserDynamic */])().bootstrapModule(__WEBPACK_IMPORTED_MODULE_2__app_app_module__["a" /* AppModule */])
    .catch(function (err) { return console.log(err); });


/***/ }),

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("./src/main.ts");


/***/ })

},[0]);
//# sourceMappingURL=main.bundle.js.map