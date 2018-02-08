webpackJsonp(["main"],{

/***/ "../../../../../src/$$_gendir lazy recursive":
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
webpackEmptyAsyncContext.id = "../../../../../src/$$_gendir lazy recursive";

/***/ }),

/***/ "../../../../../src/app/_guards/auth.guard.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AuthGuard; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
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
    return AuthGuard;
}());
AuthGuard = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */]) === "function" && _a || Object])
], AuthGuard);

var _a;
//# sourceMappingURL=auth.guard.js.map

/***/ }),

/***/ "../../../../../src/app/_guards/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__auth_guard__ = __webpack_require__("../../../../../src/app/_guards/auth.guard.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__auth_guard__["a"]; });

//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../../../../src/app/_models/user.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return User; });
var User = (function () {
    function User() {
    }
    return User;
}());

//# sourceMappingURL=user.js.map

/***/ }),

/***/ "../../../../../src/app/_services/authentication.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AuthenticationService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_http__ = __webpack_require__("../../../http/@angular/http.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_map__ = __webpack_require__("../../../../rxjs/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_map___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_map__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_app_globals__ = __webpack_require__("../../../../../src/app/globals.ts");
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
        // set token if saved in local storage
        var currentUser = JSON.parse(localStorage.getItem('currentUser'));
        this.token = currentUser && currentUser.token;
    }
    AuthenticationService.prototype.login = function (username, password) {
        var _this = this;
        var request = JSON.stringify({ email: username, password: password });
        var headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Content-Type': 'application/json' }); // ... Set content type to JSON
        var options = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["e" /* RequestOptions */]({ headers: headers }); // Create a request option
        return this.http.post(__WEBPACK_IMPORTED_MODULE_3_app_globals__["a" /* GlobalVariable */].BASE_API_URL + '/authenticate', request, options)
            .map(function (response) {
            // login successful if there's a jwt token in the response
            var token = response.json() && response.json().message && response.json().message.token;
            if (token) {
                // set token property
                _this.token = token;
                // store username and jwt token in local storage to keep user logged in between page refreshes
                localStorage.setItem('currentUser', JSON.stringify({ username: username, token: token }));
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
        return this.http.post(__WEBPACK_IMPORTED_MODULE_3_app_globals__["a" /* GlobalVariable */].BASE_API_URL + '/register', request, options)
            .map(function (response) {
            // login successful if there's a jwt token in the response
            var token = response.json() && response.json().message && response.json().message.token;
            return response.json();
        });
    };
    AuthenticationService.prototype.logout = function () {
        // clear token remove user from local storage to log user out
        this.token = null;
        localStorage.removeItem('currentUser');
    };
    return AuthenticationService;
}());
AuthenticationService = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */]) === "function" && _a || Object])
], AuthenticationService);

var _a;
//# sourceMappingURL=authentication.service.js.map

/***/ }),

/***/ "../../../../../src/app/_services/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__authentication_service__ = __webpack_require__("../../../../../src/app/_services/authentication.service.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__authentication_service__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__server_service__ = __webpack_require__("../../../../../src/app/_services/server.service.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "b", function() { return __WEBPACK_IMPORTED_MODULE_1__server_service__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__topic_service__ = __webpack_require__("../../../../../src/app/_services/topic.service.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "c", function() { return __WEBPACK_IMPORTED_MODULE_2__topic_service__["a"]; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__tracking_service__ = __webpack_require__("../../../../../src/app/_services/tracking.service.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "d", function() { return __WEBPACK_IMPORTED_MODULE_3__tracking_service__["a"]; });




//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../../../../src/app/_services/server.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ServerService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_http__ = __webpack_require__("../../../http/@angular/http.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_map__ = __webpack_require__("../../../../rxjs/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_map___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_map__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_rxjs_add_operator_catch__ = __webpack_require__("../../../../rxjs/add/operator/catch.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3_rxjs_add_operator_catch___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_3_rxjs_add_operator_catch__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4_app_globals__ = __webpack_require__("../../../../../src/app/globals.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__authentication_service__ = __webpack_require__("../../../../../src/app/_services/authentication.service.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};







var ServerService = (function () {
    function ServerService(http, router, authenticationService) {
        this.http = http;
        this.router = router;
        this.authenticationService = authenticationService;
    }
    ServerService.prototype.post = function (url, body, auth) {
        var _this = this;
        if (auth === void 0) { auth = true; }
        if (auth) {
            // add authorization header with jwt token
            this.headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Authorization': 'Bearer ' + this.authenticationService.token, 'Content-Type': 'application/json' });
        }
        else {
            // add authorization header with jwt token
            this.headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Content-Type': 'application/json' });
        }
        var options = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["e" /* RequestOptions */]({ headers: this.headers });
        // post to api
        return this.http.post(__WEBPACK_IMPORTED_MODULE_4_app_globals__["a" /* GlobalVariable */].BASE_API_URL + url, body, options)
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
    ServerService.prototype.get = function (url, auth) {
        var _this = this;
        if (auth === void 0) { auth = true; }
        if (auth) {
            // add authorization header with jwt token
            this.headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Authorization': 'Bearer ' + this.authenticationService.token, 'Content-Type': 'application/json' });
        }
        else {
            // add authorization header with jwt token
            this.headers = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["b" /* Headers */]({ 'Content-Type': 'application/json' });
        }
        var options = new __WEBPACK_IMPORTED_MODULE_1__angular_http__["e" /* RequestOptions */]({ headers: this.headers });
        // get from api
        return this.http.get(__WEBPACK_IMPORTED_MODULE_4_app_globals__["a" /* GlobalVariable */].BASE_API_URL + url, options)
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
    return ServerService;
}());
ServerService = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_http__["c" /* Http */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_5__angular_router__["c" /* Router */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_5__angular_router__["c" /* Router */]) === "function" && _b || Object, typeof (_c = typeof __WEBPACK_IMPORTED_MODULE_6__authentication_service__["a" /* AuthenticationService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_6__authentication_service__["a" /* AuthenticationService */]) === "function" && _c || Object])
], ServerService);

var _a, _b, _c;
//# sourceMappingURL=server.service.js.map

/***/ }),

/***/ "../../../../../src/app/_services/topic.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TopicService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map__ = __webpack_require__("../../../../rxjs/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__server_service__ = __webpack_require__("../../../../../src/app/_services/server.service.ts");
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
    function TopicService(serverService) {
        this.serverService = serverService;
    }
    TopicService.prototype.getTopics = function () {
        // get topic from api
        return this.serverService.get('/topic')
            .map(function (response) { return response; });
    };
    TopicService.prototype.getTopic = function (id) {
        // get topic from api
        return this.serverService.get('/topic/' + id)
            .map(function (response) { return response; });
    };
    TopicService.prototype.getLesson = function (topic_id, lesson_id) {
        // get lesson from api
        return this.serverService.get('/topic/' + topic_id + '/lesson/' + lesson_id)
            .map(function (response) { return response; });
    };
    TopicService.prototype.reportError = function (question_id, answers, option, custom) {
        // notify api about question error
        var request = JSON.stringify({ answers: answers, options: option, comment: custom });
        console.log(request);
        return this.serverService.post('/report_error/' + question_id, request)
            .map(function (response) { return response; });
    };
    return TopicService;
}());
TopicService = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_2__server_service__["a" /* ServerService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__server_service__["a" /* ServerService */]) === "function" && _a || Object])
], TopicService);

var _a;
//# sourceMappingURL=topic.service.js.map

/***/ }),

/***/ "../../../../../src/app/_services/tracking.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TrackingService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map__ = __webpack_require__("../../../../rxjs/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__server_service__ = __webpack_require__("../../../../../src/app/_services/server.service.ts");
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
    function TrackingService(serverService) {
        this.serverService = serverService;
    }
    TrackingService.prototype.startLesson = function (lesson_id) {
        // notify api about lesson start
        return this.serverService.post('/lesson/' + lesson_id + '/start', '')
            .map(function (response) { return response; });
    };
    TrackingService.prototype.doneLesson = function (lesson_id, start_datetime, weak_questions) {
        // notify api about lesson done
        var request = JSON.stringify({ start_datetime: start_datetime, weak_questions: weak_questions });
        return this.serverService.post('/lesson/' + lesson_id + '/done', request)
            .map(function (response) { return response; });
    };
    return TrackingService;
}());
TrackingService = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_2__server_service__["a" /* ServerService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__server_service__["a" /* ServerService */]) === "function" && _a || Object])
], TrackingService);

var _a;
//# sourceMappingURL=tracking.service.js.map

/***/ }),

/***/ "../../../../../src/app/_services/user.service.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return UserService; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map__ = __webpack_require__("../../../../rxjs/add/operator/map.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_1_rxjs_add_operator_map__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__server_service__ = __webpack_require__("../../../../../src/app/_services/server.service.ts");
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
    function UserService(serverService) {
        this.serverService = serverService;
    }
    UserService.prototype.getProfile = function () {
        return this.serverService.get('/profile')
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
        return this.serverService.post('/profile', request)
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
        return this.serverService.post('/profile', request)
            .map(function (res) {
            //console.log(res); 
        })
            .catch(function (error) {
            console.log(error);
            throw Error(error);
        });
    };
    return UserService;
}());
UserService = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Injectable"])(),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_2__server_service__["a" /* ServerService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__server_service__["a" /* ServerService */]) === "function" && _a || Object])
], UserService);

var _a;
//# sourceMappingURL=user.service.js.map

/***/ }),

/***/ "../../../../../src/app/app.component.html":
/***/ (function(module, exports) {

module.exports = "<!-- main app container -->\n<div class=\"jumbotron\">\n    <div class=\"container\">\n        <div class=\"col-sm-8 col-sm-offset-2\">\n            <div *ngIf=\"showMenu\">\n                <md-toolbar color=\"primary\">\n                    <!-- This fills the remaining space of the current row -->\n                    <span class=\"fill-remaining-space\"></span>\n                    <button mat-icon-button [matMenuTriggerFor]=\"menu\">\n                        <mat-icon>menu</mat-icon>\n                    </button>\n                </md-toolbar>\n                <mat-menu #menu=\"matMenu\">\n                    <br />\n                    <a mat-menu-item routerLink=\"\">Home</a>\n                    <a mat-menu-item routerLink=\"profile\">Profile</a>\n                    <a mat-menu-item routerLink=\"login\">Logout</a>\n                </mat-menu>\n            </div>\n            <router-outlet></router-outlet>\n        </div>\n    </div>\n</div>"

/***/ }),

/***/ "../../../../../src/app/app.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AppComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_filter__ = __webpack_require__("../../../../rxjs/add/operator/filter.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_filter___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_2_rxjs_add_operator_filter__);
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
    return AppComponent;
}());
AppComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        selector: 'app-root',
        template: __webpack_require__("../../../../../src/app/app.component.html")
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_1__angular_router__["a" /* ActivatedRoute */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_router__["a" /* ActivatedRoute */]) === "function" && _b || Object])
], AppComponent);

var _a, _b;
//# sourceMappingURL=app.component.js.map

/***/ }),

/***/ "../../../../../src/app/app.module.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return AppModule; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_platform_browser__ = __webpack_require__("../../../platform-browser/@angular/platform-browser.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__angular_forms__ = __webpack_require__("../../../forms/@angular/forms.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__angular_http__ = __webpack_require__("../../../http/@angular/http.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__angular_platform_browser_animations__ = __webpack_require__("../../../platform-browser/@angular/platform-browser/animations.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__angular_material__ = __webpack_require__("../../../material/esm5/material.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__app_component__ = __webpack_require__("../../../../../src/app/app.component.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7__app_routing__ = __webpack_require__("../../../../../src/app/app.routing.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8__guards_index__ = __webpack_require__("../../../../../src/app/_guards/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_9__services_index__ = __webpack_require__("../../../../../src/app/_services/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_10__welcome_index__ = __webpack_require__("../../../../../src/app/welcome/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_11__login_index__ = __webpack_require__("../../../../../src/app/login/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_12__register_index__ = __webpack_require__("../../../../../src/app/register/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_13__home_index__ = __webpack_require__("../../../../../src/app/home/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_14__topic_index__ = __webpack_require__("../../../../../src/app/topic/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_15__lesson_index__ = __webpack_require__("../../../../../src/app/lesson/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_16_angular2_fontawesome_angular2_fontawesome__ = __webpack_require__("../../../../angular2-fontawesome/angular2-fontawesome.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_16_angular2_fontawesome_angular2_fontawesome___default = __webpack_require__.n(__WEBPACK_IMPORTED_MODULE_16_angular2_fontawesome_angular2_fontawesome__);
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_17__angular_flex_layout__ = __webpack_require__("../../../flex-layout/@angular/flex-layout.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_18__profile_profile_component__ = __webpack_require__("../../../../../src/app/profile/profile.component.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_19__try_try_component__ = __webpack_require__("../../../../../src/app/try/try.component.ts");
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};





















var AppModule = (function () {
    function AppModule() {
    }
    return AppModule;
}());
AppModule = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["NgModule"])({
        imports: [
            __WEBPACK_IMPORTED_MODULE_1__angular_platform_browser__["a" /* BrowserModule */],
            __WEBPACK_IMPORTED_MODULE_2__angular_forms__["c" /* FormsModule */],
            __WEBPACK_IMPORTED_MODULE_3__angular_http__["d" /* HttpModule */],
            __WEBPACK_IMPORTED_MODULE_7__app_routing__["a" /* routing */],
            __WEBPACK_IMPORTED_MODULE_16_angular2_fontawesome_angular2_fontawesome__["Angular2FontawesomeModule"],
            __WEBPACK_IMPORTED_MODULE_4__angular_platform_browser_animations__["a" /* BrowserAnimationsModule */],
            __WEBPACK_IMPORTED_MODULE_5__angular_material__["g" /* MatInputModule */],
            __WEBPACK_IMPORTED_MODULE_5__angular_material__["b" /* MatButtonModule */],
            __WEBPACK_IMPORTED_MODULE_5__angular_material__["j" /* MatSelectModule */],
            __WEBPACK_IMPORTED_MODULE_5__angular_material__["f" /* MatIconModule */],
            __WEBPACK_IMPORTED_MODULE_5__angular_material__["h" /* MatMenuModule */],
            __WEBPACK_IMPORTED_MODULE_5__angular_material__["i" /* MatRadioModule */],
            __WEBPACK_IMPORTED_MODULE_5__angular_material__["d" /* MatDialogModule */],
            __WEBPACK_IMPORTED_MODULE_17__angular_flex_layout__["a" /* FlexLayoutModule */]
        ],
        declarations: [
            __WEBPACK_IMPORTED_MODULE_6__app_component__["a" /* AppComponent */],
            __WEBPACK_IMPORTED_MODULE_10__welcome_index__["a" /* WelcomeComponent */],
            __WEBPACK_IMPORTED_MODULE_11__login_index__["a" /* LoginComponent */],
            __WEBPACK_IMPORTED_MODULE_12__register_index__["a" /* RegisterComponent */],
            __WEBPACK_IMPORTED_MODULE_13__home_index__["a" /* HomeComponent */],
            __WEBPACK_IMPORTED_MODULE_14__topic_index__["a" /* TopicComponent */],
            __WEBPACK_IMPORTED_MODULE_15__lesson_index__["c" /* LessonComponent */],
            __WEBPACK_IMPORTED_MODULE_15__lesson_index__["b" /* GoodDialogComponent */],
            __WEBPACK_IMPORTED_MODULE_15__lesson_index__["a" /* BadDialogComponent */],
            __WEBPACK_IMPORTED_MODULE_15__lesson_index__["d" /* ReportDialogComponent */],
            __WEBPACK_IMPORTED_MODULE_18__profile_profile_component__["a" /* ProfileComponent */],
            __WEBPACK_IMPORTED_MODULE_19__try_try_component__["a" /* TryComponent */]
        ],
        entryComponents: [
            __WEBPACK_IMPORTED_MODULE_15__lesson_index__["b" /* GoodDialogComponent */],
            __WEBPACK_IMPORTED_MODULE_15__lesson_index__["a" /* BadDialogComponent */],
            __WEBPACK_IMPORTED_MODULE_15__lesson_index__["d" /* ReportDialogComponent */]
        ],
        providers: [
            __WEBPACK_IMPORTED_MODULE_8__guards_index__["a" /* AuthGuard */],
            __WEBPACK_IMPORTED_MODULE_9__services_index__["a" /* AuthenticationService */],
            __WEBPACK_IMPORTED_MODULE_9__services_index__["b" /* ServerService */],
            // providers used to create fake backend
            //fakeBackendProvider,
            //MockBackend,
            __WEBPACK_IMPORTED_MODULE_3__angular_http__["a" /* BaseRequestOptions */]
        ],
        bootstrap: [__WEBPACK_IMPORTED_MODULE_6__app_component__["a" /* AppComponent */]]
    })
], AppModule);

//# sourceMappingURL=app.module.js.map

/***/ }),

/***/ "../../../../../src/app/app.routing.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return routing; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__welcome_index__ = __webpack_require__("../../../../../src/app/welcome/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__login_index__ = __webpack_require__("../../../../../src/app/login/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__register_index__ = __webpack_require__("../../../../../src/app/register/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_4__home_index__ = __webpack_require__("../../../../../src/app/home/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_5__topic_index__ = __webpack_require__("../../../../../src/app/topic/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_6__lesson_index__ = __webpack_require__("../../../../../src/app/lesson/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_7__guards_index__ = __webpack_require__("../../../../../src/app/_guards/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_8__profile_profile_component__ = __webpack_require__("../../../../../src/app/profile/profile.component.ts");









var appRoutes = [
    { path: 'welcome', component: __WEBPACK_IMPORTED_MODULE_1__welcome_index__["a" /* WelcomeComponent */] },
    { path: 'login', component: __WEBPACK_IMPORTED_MODULE_2__login_index__["a" /* LoginComponent */] },
    { path: 'register', component: __WEBPACK_IMPORTED_MODULE_3__register_index__["a" /* RegisterComponent */] },
    { path: 'profile', component: __WEBPACK_IMPORTED_MODULE_8__profile_profile_component__["a" /* ProfileComponent */] },
    { path: '', component: __WEBPACK_IMPORTED_MODULE_4__home_index__["a" /* HomeComponent */], canActivate: [__WEBPACK_IMPORTED_MODULE_7__guards_index__["a" /* AuthGuard */]] },
    { path: 'topic/:id', component: __WEBPACK_IMPORTED_MODULE_5__topic_index__["a" /* TopicComponent */], canActivate: [__WEBPACK_IMPORTED_MODULE_7__guards_index__["a" /* AuthGuard */]] },
    { path: 'topic/:topic_id/lesson/:lesson_id', component: __WEBPACK_IMPORTED_MODULE_6__lesson_index__["c" /* LessonComponent */], canActivate: [__WEBPACK_IMPORTED_MODULE_7__guards_index__["a" /* AuthGuard */]] },
    // otherwise redirect to welcome
    { path: '**', redirectTo: 'welcome' }
];
var routing = __WEBPACK_IMPORTED_MODULE_0__angular_router__["d" /* RouterModule */].forRoot(appRoutes);
//# sourceMappingURL=app.routing.js.map

/***/ }),

/***/ "../../../../../src/app/globals.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return GlobalVariable; });
//
// ===== File globals.ts    
//

var GlobalVariable = Object.freeze({
    //BASE_API_URL: 'http://gizmo.local/api',
    BASE_API_URL: 'http://healthnumeracyproject.com/api',
});
//# sourceMappingURL=globals.js.map

/***/ }),

/***/ "../../../../../src/app/home/home.component.html":
/***/ (function(module, exports) {

module.exports = "<div class=\"text-center\">\n    <div ng-if=\"topicsTree.length\">\n        <div *ngFor=\"let level of topicsTree; let levelIndex = index\">\n            <div *ngIf=\"level.units.length\" class=\"shadow\">\n                <h3 style=\"font-weight: bold;\">{{level.title}}</h3>\n                <div *ngFor=\"let unit of level.units; let unitIndex = index\"  class=\"shadow\">\n                    <h4>{{unit.title}}</h4>\n                    <div *ngFor=\"let topic of unit.topics; let topicIndex = index\" style=\"display: inline-block;width:33%;vertical-align:top;\">\n                        <span class=\"fa-stack fa-4x\" >\n                            <a [routerLink]=\"topic.status == 0 ? '' : '/topic/'+topic.id\" routerLinkActive=\"active\" [class.disabled]=\"topic.status == 0 ? true : null\">\n                                <div [ngClass]=\"topic.image_id ? topic.image_id : 'cb0-img'\">\n                                    <div *ngIf=\"topic.status != 2\" class=\"topicButton\"  [ngClass]=\"topic.status == 1 ? 'greenout': 'redout'\" ></div>\n                                    <div *ngIf=\"topic.status == 2\" class=\"topicButton\"  [ngClass]=\"'yellowout'\" >\n                                        <div [ngStyle]=\"{'height.%' : topic.progress.percent}\" class=\"bottom_progress\" [ngClass]=\"'greenout'\"></div>\n                                    </div>\n                                </div>\n                            </a>\n                        </span>\n                        <div> {{topic.progress.done}} of {{topic.progress.total}} done</div>\n                        <div *ngIf=\"!topic.short_name\">{{topic.title}}</div>\n                        <div *ngIf=\"topic.short_name\">{{topic.short_name}}</div>\n\n                    </div>\n                    <div style=\"width:1%\"></div>\n                    <div style=\"clear:both\"><br /></div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>"

/***/ }),

/***/ "../../../../../src/app/home/home.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return HomeComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__services_index__ = __webpack_require__("../../../../../src/app/_services/index.ts");
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
    return HomeComponent;
}());
HomeComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        template: __webpack_require__("../../../../../src/app/home/home.component.html"),
        providers: [__WEBPACK_IMPORTED_MODULE_1__services_index__["c" /* TopicService */]]
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__services_index__["c" /* TopicService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__services_index__["c" /* TopicService */]) === "function" && _a || Object])
], HomeComponent);

var _a;
//# sourceMappingURL=home.component.js.map

/***/ }),

/***/ "../../../../../src/app/home/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__home_component__ = __webpack_require__("../../../../../src/app/home/home.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__home_component__["a"]; });

//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../../../../src/app/lesson/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__lesson_component__ = __webpack_require__("../../../../../src/app/lesson/lesson.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__lesson_component__["a"]; });
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "b", function() { return __WEBPACK_IMPORTED_MODULE_0__lesson_component__["b"]; });
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "c", function() { return __WEBPACK_IMPORTED_MODULE_0__lesson_component__["c"]; });
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "d", function() { return __WEBPACK_IMPORTED_MODULE_0__lesson_component__["d"]; });

//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../../../../src/app/lesson/lesson.component.html":
/***/ (function(module, exports) {

module.exports = "<a routerLink=\"/topic/{{topic_id}}\" routerLinkActive=\"active\" class=\"backButton left\"><-Back</a>\n<div class=\"text-center\">\n    <div *ngIf=\"question !== null\">\n        <h2 [innerHtml]=\"question.question\"></h2>\n        <div *ngIf=\"question.answer_mode=='radio'\">\n            <mat-radio-group class=\"radio-group\" [(ngModel)]=\"answers[0]\">\n                <mat-radio-button class=\"radio-button\" *ngFor=\"let answer of question.answers; let answerIndex = index\" value=\"{{answerIndex}}\">\n                    {{answer.value}}\n                </mat-radio-button>\n            </mat-radio-group>\n        </div>\n        <div *ngIf=\"question.answer_mode=='TF'\">\n            <mat-radio-group class=\"radio-group\" [(ngModel)]=\"answers[0]\">\n                <mat-radio-button class=\"radio-button\" value=\"False\">\n                    false\n                </mat-radio-button>\n                <mat-radio-button class=\"radio-button\" value=\"True\">\n                    true\n                </mat-radio-button>\n            </mat-radio-group>\n        </div>\n        <div *ngIf=\"question.answer_mode=='checkbox'\">\n            <li *ngFor=\"let answer of question.answers; let answerIndex = index\">\n                <input type=\"checkbox\" [(ngModel)]=\"answers[answerIndex]\"/> {{answer.value}}\n            </li>\n        </div>\n        <div *ngIf=\"question.answer_mode=='input'\">\n            <input *ngFor=\"let answer of question.answers; let answerIndex = index\" [(ngModel)]=\"answers[answerIndex]\" name=\"'answers[{{answerIndex}}]'\"\n            (keyup.enter) = \"checkAnswer()\">\n        </div>\n        <br />\n        <button (click)=\"checkAnswer()\" >Continue</button>\n    </div>\n    <div *ngIf=\"question === null\">\n        <div *ngIf=\"initial_loading == 1\">\n            <h2>Loading....!</h2>\n        </div>\n        <div *ngIf=\"initial_loading == 0\">\n            <h2>Congratulation!</h2>\n            <h3>You have finished this lesson.</h3>\n        </div>\n    </div>\n</div>"

/***/ }),

/***/ "../../../../../src/app/lesson/lesson.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "c", function() { return LessonComponent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "b", function() { return GoodDialogComponent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return BadDialogComponent; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "d", function() { return ReportDialogComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("../../../../../src/app/_services/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__angular_material__ = __webpack_require__("../../../material/esm5/material.es5.js");
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





var LessonComponent = (function () {
    function LessonComponent(topicService, trackingService, route, dialog) {
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
    }
    LessonComponent.prototype.ngOnInit = function () {
        var _this = this;
        this.sub = this.route.params.subscribe(function (params) {
            _this.topic_id = +params['topic_id']; // (+) converts string 'id' to a number
            _this.lesson_id = +params['lesson_id']; // (+) converts string 'id' to a number
            // In a real app: dispatch action to load the details here.
            // get lesson tree from API
            _this.topicService.getLesson(_this.topic_id, _this.lesson_id)
                .subscribe(function (lessonTree) {
                _this.lessonTree = lessonTree;
                _this.initial_loading = 0;
                if (lessonTree['questions'].length) {
                    _this.nextQuestion();
                    _this.trackingService.startLesson(_this.lesson_id).subscribe(function (start_time) {
                        _this.start_time = start_time;
                    });
                }
            });
        });
    };
    LessonComponent.prototype.nextQuestion = function () {
        this.answers = [];
        this.question = this.lessonTree['questions'].shift();
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
        if (this.isCorrect()) {
            var dialogRef = this.dialog.open(GoodDialogComponent, {
                width: '300px',
                data: {}
            });
            dialogRef.afterClosed().subscribe(function (result) {
                if (_this.lessonTree['questions'].length) {
                    _this.nextQuestion();
                }
                else {
                    _this.question = null;
                    _this.trackingService.doneLesson(_this.lesson_id, _this.start_time, _this.weak_questions).subscribe();
                }
            });
        }
        else {
            if (this.weak_questions.indexOf(this.question.id) === -1) {
                this.weak_questions.push(this.question.id);
            }
            this.lessonTree['questions'].push(this.question);
            var dialogRef = this.dialog.open(BadDialogComponent, {
                width: '300px',
                data: { data: this.question.answers.filter(function (answer) {
                        if (answer.is_correct == 1)
                            return true;
                        return false;
                    }), explanation: this.question.explanation
                }
            });
            dialogRef.afterClosed().subscribe(function (result) {
                if (result) {
                    var reportDialogRef = _this.dialog.open(ReportDialogComponent, {
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
                    _this.trackingService.doneLesson(_this.lesson_id, _this.start_time, _this.weak_questions).subscribe();
                }
            });
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
                    if (this.question.answers[i].is_correct && this.question.answers[i].value != this.answers[i]) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    };
    return LessonComponent;
}());
LessonComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        template: __webpack_require__("../../../../../src/app/lesson/lesson.component.html"),
        providers: [__WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */], __WEBPACK_IMPORTED_MODULE_2__services_index__["d" /* TrackingService */]]
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_2__services_index__["d" /* TrackingService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__services_index__["d" /* TrackingService */]) === "function" && _b || Object, typeof (_c = typeof __WEBPACK_IMPORTED_MODULE_1__angular_router__["a" /* ActivatedRoute */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_router__["a" /* ActivatedRoute */]) === "function" && _c || Object, typeof (_d = typeof __WEBPACK_IMPORTED_MODULE_3__angular_material__["c" /* MatDialog */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_3__angular_material__["c" /* MatDialog */]) === "function" && _d || Object])
], LessonComponent);

var GoodDialogComponent = (function () {
    function GoodDialogComponent(dialogRef, data) {
        this.dialogRef = dialogRef;
        this.data = data;
    }
    GoodDialogComponent.prototype.onNoClick = function () {
        this.dialogRef.close();
    };
    return GoodDialogComponent;
}());
GoodDialogComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        selector: 'good-dialog',
        template: "<h2 mat-dialog-title>Correct!</h2>\n        <mat-dialog-content></mat-dialog-content>\n        <mat-dialog-actions>\n          <button mat-button [mat-dialog-close]=\"true\" style=\"background-color: yellow\">Continue</button>\n        </mat-dialog-actions>"
    }),
    __param(1, Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Inject"])(__WEBPACK_IMPORTED_MODULE_3__angular_material__["a" /* MAT_DIALOG_DATA */])),
    __metadata("design:paramtypes", [typeof (_e = typeof __WEBPACK_IMPORTED_MODULE_3__angular_material__["e" /* MatDialogRef */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_3__angular_material__["e" /* MatDialogRef */]) === "function" && _e || Object, Object])
], GoodDialogComponent);

var BadDialogComponent = (function () {
    function BadDialogComponent(dialogRef, data) {
        this.dialogRef = dialogRef;
        this.data = data;
        this.answers = data.data;
        this.explanation = data.explanation;
    }
    BadDialogComponent.prototype.onNoClick = function () {
        this.dialogRef.close();
    };
    return BadDialogComponent;
}());
BadDialogComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        selector: 'bad-dialog',
        template: "<h2 mat-dialog-title>Incorrect :(</h2>\n        <mat-dialog-content>\n            <div *ngIf=\"answers.length == 1\">\n                Correct answer is: {{answers[0].value}}\n            </div>\n            <div *ngIf=\"answers.length != 1\">\n                Correct answers are: <ul>\n                <li *ngFor=\"let answer of answers; let answerIndex = index\">{{answer.value}}</li>\n                </ul>\n            </div>\n            <div *ngIf=\"explanation!=''\">\n                {{explanation}}\n            </div>\n        </mat-dialog-content>\n        <mat-dialog-actions>\n            <button mat-button [mat-dialog-close]=\"false\" style=\"background-color: yellow\">Continue</button>\n            <button mat-button [mat-dialog-close]=\"true\" style=\"background-color: red\">Report Error!</button>\n        </mat-dialog-actions>"
    }),
    __param(1, Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Inject"])(__WEBPACK_IMPORTED_MODULE_3__angular_material__["a" /* MAT_DIALOG_DATA */])),
    __metadata("design:paramtypes", [typeof (_f = typeof __WEBPACK_IMPORTED_MODULE_3__angular_material__["e" /* MatDialogRef */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_3__angular_material__["e" /* MatDialogRef */]) === "function" && _f || Object, Object])
], BadDialogComponent);

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
    return ReportDialogComponent;
}());
ReportDialogComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        selector: 'report-dialog',
        template: "<h2 mat-dialog-title>Please specify reason</h2>\n        <mat-dialog-content>\n            <mat-radio-group class=\"radio-group\" [(ngModel)]=\"selectedOption\">\n              <mat-radio-button class=\"radio-button\" *ngFor=\"let option of options; let optionIndex = index\" [value]=\"optionIndex\">\n                {{option}}\n              </mat-radio-button>\n            </mat-radio-group>\n        </mat-dialog-content>\n        <mat-form-field *ngIf=\"selectedOption == 3\">\n            <input matInput [(ngModel)]=\"custom\">\n        </mat-form-field>\n        <mat-dialog-actions>\n            <button mat-button [mat-dialog-close]=\"{option: options[selectedOption], text: custom, question_id: question_id, answers: answers}\" style=\"background-color: blue\">Send</button>\n            <button mat-button [mat-dialog-close]=\"false\" style=\"background-color: green\">Cancel</button>\n        </mat-dialog-actions>"
    }),
    __param(1, Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Inject"])(__WEBPACK_IMPORTED_MODULE_3__angular_material__["a" /* MAT_DIALOG_DATA */])),
    __metadata("design:paramtypes", [typeof (_g = typeof __WEBPACK_IMPORTED_MODULE_3__angular_material__["e" /* MatDialogRef */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_3__angular_material__["e" /* MatDialogRef */]) === "function" && _g || Object, Object])
], ReportDialogComponent);

var _a, _b, _c, _d, _e, _f, _g;
//# sourceMappingURL=lesson.component.js.map

/***/ }),

/***/ "../../../../../src/app/login/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__login_component__ = __webpack_require__("../../../../../src/app/login/login.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__login_component__["a"]; });

//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../../../../src/app/login/login.component.html":
/***/ (function(module, exports) {

module.exports = "<a routerLink=\"/welcome\" routerLinkActive=\"active\" class=\"backButton left\"><-Back Home</a>\n<div class=\"col-md-6 col-md-offset-3\">\n    <h2>Login</h2>\n    <form name=\"form\" (ngSubmit)=\"f.form.valid && login()\" #f=\"ngForm\" novalidate>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !username.valid }\">\n            <label for=\"username\">Email</label>\n            <input type=\"text\" class=\"form-control\" name=\"username\" [(ngModel)]=\"model.username\" #username=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !username.valid\" class=\"help-block\">Username is required</div>\n        </div>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !password.valid }\">\n            <label for=\"password\">Password</label>\n            <input type=\"password\" class=\"form-control\" name=\"password\" [(ngModel)]=\"model.password\" #password=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !password.valid\" class=\"help-block\">Password is required</div>\n        </div>\n        <div class=\"form-group\">\n            <button [disabled]=\"loading\" class=\"btn btn-primary\">Login</button>\n            <img *ngIf=\"loading\" src=\"data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==\" />\n            <div class=\"pull-right\">\n                <a routerLink=\"/register\" >Register account</a>\n            </div>\n        </div>\n        <div *ngIf=\"error\" class=\"alert alert-danger\">{{error}}</div>\n    </form>\n</div>\n"

/***/ }),

/***/ "../../../../../src/app/login/login.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return LoginComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("../../../../../src/app/_services/index.ts");
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
    return LoginComponent;
}());
LoginComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        template: __webpack_require__("../../../../../src/app/login/login.component.html")
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_2__services_index__["a" /* AuthenticationService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__services_index__["a" /* AuthenticationService */]) === "function" && _b || Object])
], LoginComponent);

var _a, _b;
//# sourceMappingURL=login.component.js.map

/***/ }),

/***/ "../../../../../src/app/profile/profile.component.css":
/***/ (function(module, exports, __webpack_require__) {

exports = module.exports = __webpack_require__("../../../../css-loader/lib/css-base.js")(false);
// imports


// module
exports.push([module.i, ".profile{\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;\n  -webkit-box-pack: center;\n      -ms-flex-pack: center;\n          justify-content: center;\n  display: inline;\n}\n\nmat-card{\n  //background-color: #f9f9f9;\n  margin: 10px;\n  padding: 8px;\n}\n\nspan.card-title {\n  display: table;\n  white-space: nowrap;\n  padding: 8px;\n}\n\nspan.card-title:before, span.card-title:after {\n  border-top: 1px solid #3f51b5;\n  content: '';\n  display: table-cell;\n  position: relative;\n  top: 0.5em;\n  width: 45%;\n}\n\nspan.card-title:before {\n  right: 1.5%;\n}\n\nspan.card-title:after {\n  left: 1.5%; \n}\n\n mat-input-container {\n   width: 100%;\n }\n\n.button-container{\n  padding: 0; \n  margin: 0;\n  display: -webkit-box;\n  display: -ms-flexbox;\n  display: flex;  \n  -webkit-box-align: center;  \n      -ms-flex-align: center;  \n          align-items: center; \n  -webkit-box-pack: center; \n      -ms-flex-pack: center; \n          justify-content: center;\n  -ms-flex-wrap: wrap;\n      flex-wrap: wrap;  \n}\n\nbutton {\n\tcolor: #fff;\n\tbackground-color: #337AB7;\n}\n\n/*@media screen and (max-width: 660px) {\n  .profile {\n    display: inline;\n  }\n  mat-card{\n    max-width: 620px;\n  } \n}*/", ""]);

// exports


/*** EXPORTS FROM exports-loader ***/
module.exports = module.exports.toString();

/***/ }),

/***/ "../../../../../src/app/profile/profile.component.html":
/***/ (function(module, exports) {

module.exports = "<div class=\"profile\">\n  <mat-card id=\"profile-container\">\n  \t<span class=\"card-title\">Change Profile Info:</span>\n    <form #changeProfile=\"ngForm\" (ngSubmit)=\"onChangeProfile()\">\n  \t\t<mat-input-container>\n        <input matInput\n          name=\"username\"\n          pattern=\"[a-zA-Z0-9]{2,255}\"\n          placeholder=\"Name\"\n          [(ngModel)]=\"user.username\" />\n        </mat-input-container>\n        <mat-input-container>\n          <input matInput\n            name=\"email\"\n            pattern=\"^\\S+@\\S+$\"\n            placeholder=\"Email\"\n            [(ngModel)]=\"user.email\" />\n        </mat-input-container> \n        <mat-input-container>\n          <label>Number of correct consequence answers to finish lesson(put 0 to answer all questions)</label>\n          <input matInput\n            name=\"question_num\"\n            pattern=\"[0-9]\"\n            [(ngModel)]=\"user.questionNum\" />\n        </mat-input-container> \n        <div class=\"button-container\">\n          <button\n            mat-raised-button\n            type=\"submit\">\n            <mat-icon>update</mat-icon>\n            <span>Update</span>\n          </button>\n      \t</div>\n  \t</form>\n  </mat-card>\n\n  <mat-card>\n    <span class=\"card-title\">Change Password:</span>\n    <form #changePassword=\"ngForm\" (ngSubmit)=\"onChangePassword(newPassword.value, confirmedPassword.value)\">\n      <mat-input-container>\n        <input matInput\n          required=\"required\"\n          pattern=\".{6,30}\"\n          type=\"password\"\n          placeholder=\"New Password\"\n          #newPassword />\n      </mat-input-container>\n      <mat-input-container>\n        <input matInput\n          required=\"required\"\n          pattern=\".{6,30}\"\n          type=\"password\"\n          placeholder=\"Confirm Password\"\n          #confirmedPassword />\n      </mat-input-container>\n      <div class=\"alert alert-danger\" *ngIf=\"!passwordsMatch\">\n         <mat-icon>warning</mat-icon>\n         {{warningMessage}}\n      </div>\n      <div class=\"button-container\" >\n        <button id=\"change-password-button\"\n          mat-raised-button\n          type=\"submit\">\n          <mat-icon>update</mat-icon>\n          <span>Change Password</span>\n        </button>\n      </div>\n    </form>\n  </mat-card>\n\n</div>\n"

/***/ }),

/***/ "../../../../../src/app/profile/profile.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return ProfileComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__models_user__ = __webpack_require__("../../../../../src/app/_models/user.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_user_service__ = __webpack_require__("../../../../../src/app/_services/user.service.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__services_authentication_service__ = __webpack_require__("../../../../../src/app/_services/authentication.service.ts");
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
        var _this = this;
        this.userService = userService;
        this.authenticationService = authenticationService;
        this.user = new __WEBPACK_IMPORTED_MODULE_1__models_user__["a" /* User */]();
        this.passwordsMatch = true;
        this.userService.getProfile()
            .subscribe(function (res) {
            //console.log(JSON.stringify(res));
            _this.user.username = res['name'];
            _this.user.email = res['email'];
            _this.user.questionNum = res['question_num'];
        });
    }
    ProfileComponent.prototype.ngOnInit = function () {
    };
    ProfileComponent.prototype.onChangeProfile = function () {
        this.userService.changeProfile(this.user)
            .subscribe(function (res) {
            //console.log('Update Result: ' + res);
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
                //console.log('Change Password Result: ' + res);
                //console.log("Old Token: " + JSON.parse(localStorage.getItem('currentUser')).token);
                _this.authenticationService.login(_this.user.email, newPassword);
                //.subscribe(() => console.log("New Token: " + JSON.parse(localStorage.getItem('currentUser')).token));
            }, function (error) {
                // error
            });
        }
    };
    return ProfileComponent;
}());
ProfileComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        selector: 'app-profle',
        template: __webpack_require__("../../../../../src/app/profile/profile.component.html"),
        styles: [__webpack_require__("../../../../../src/app/profile/profile.component.css")],
        providers: [__WEBPACK_IMPORTED_MODULE_2__services_user_service__["a" /* UserService */]]
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_2__services_user_service__["a" /* UserService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__services_user_service__["a" /* UserService */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_3__services_authentication_service__["a" /* AuthenticationService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_3__services_authentication_service__["a" /* AuthenticationService */]) === "function" && _b || Object])
], ProfileComponent);

var _a, _b;
//# sourceMappingURL=profile.component.js.map

/***/ }),

/***/ "../../../../../src/app/register/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__register_component__ = __webpack_require__("../../../../../src/app/register/register.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__register_component__["a"]; });

//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../../../../src/app/register/register.component.html":
/***/ (function(module, exports) {

module.exports = "<a routerLink=\"/welcome\" routerLinkActive=\"active\" class=\"backButton left\"><-Back Home</a>\n<div class=\"col-md-6 col-md-offset-3\">\n    <h2>Register</h2>\n    <form name=\"form\" (ngSubmit)=\"f.form.valid && password.value == repassword.value && register()\" #f=\"ngForm\" novalidate>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !username.valid }\">\n            <label for=\"username\">Username</label>\n            <input type=\"text\" class=\"form-control\" name=\"username\" [(ngModel)]=\"model.username\" #username=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !username.valid\" class=\"help-block\">Username is required</div>\n        </div>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !email.valid }\">\n            <label for=\"email\">Email</label>\n            <input type=\"email\" class=\"form-control\" name=\"email\" [(ngModel)]=\"model.email\" #email=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !email.valid\" class=\"help-block\">Email is required</div>\n        </div>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': f.submitted && !password.valid }\">\n            <label for=\"password\">Password</label>\n            <input type=\"password\" class=\"form-control\" name=\"password\" [(ngModel)]=\"model.password\" #password=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !password.valid\" class=\"help-block\">Password is required</div>\n        </div>\n        <div class=\"form-group\" [ngClass]=\"{ 'has-error': (f.submitted && !repassword.valid) || (f.submitted && password.value != repassword.value) }\">\n            <label for=\"repassword\">Password Confirm</label>\n            <input type=\"password\" class=\"form-control\" name=\"repassword\" [(ngModel)]=\"model.repassword\" #repassword=\"ngModel\" required />\n            <div *ngIf=\"f.submitted && !repassword.valid\" class=\"help-block\">Password Confirm is required</div>\n            <div *ngIf=\"f.submitted && repassword.valid && password.value != repassword.value\" class=\"help-block\">Password Confirm is wrong</div>\n        </div>\n        <div class=\"form-group\">\n            <button [disabled]=\"loading\" class=\"btn btn-primary\">Register</button>\n            <img *ngIf=\"loading\" src=\"data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZfhYumCYUhDAQxRIdhHBGqRoKw0R8DYlJd8z0fMDgsGo/IpHI5TAAAIfkECQoAAAAsAAAAABAAEAAAAzIIunInK0rnZBTwGPNMgQwmdsNgXGJUlIWEuR5oWUIpz8pAEAMe6TwfwyYsGo/IpFKSAAAh+QQJCgAAACwAAAAAEAAQAAADMwi6IMKQORfjdOe82p4wGccc4CEuQradylesojEMBgsUc2G7sDX3lQGBMLAJibufbSlKAAAh+QQJCgAAACwAAAAAEAAQAAADMgi63P7wCRHZnFVdmgHu2nFwlWCI3WGc3TSWhUFGxTAUkGCbtgENBMJAEJsxgMLWzpEAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==\" />\n        </div>\n        <div *ngIf=\"error\" class=\"alert alert-danger\">{{error}}</div>\n    </form>\n</div>\n"

/***/ }),

/***/ "../../../../../src/app/register/register.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return RegisterComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("../../../../../src/app/_services/index.ts");
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
    return RegisterComponent;
}());
RegisterComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        template: __webpack_require__("../../../../../src/app/register/register.component.html")
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_2__services_index__["a" /* AuthenticationService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__services_index__["a" /* AuthenticationService */]) === "function" && _b || Object])
], RegisterComponent);

var _a, _b;
//# sourceMappingURL=register.component.js.map

/***/ }),

/***/ "../../../../../src/app/topic/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__topic_component__ = __webpack_require__("../../../../../src/app/topic/topic.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__topic_component__["a"]; });

//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../../../../src/app/topic/topic.component.html":
/***/ (function(module, exports) {

module.exports = "<a routerLink=\"/\" routerLinkActive=\"active\" class=\"backButton left\"><-Back</a>\n<div class=\"text-center\">\n    <h2>{{topicTree.title}}</h2>\n    <div ng-if=\"topicTree.lessons.length\">\n        <div *ngFor=\"let lesson of topicTree.lessons; let levelIndex = index\" class=\"arrowRowContainer\">\n            <div class=\"arrowButtonContainer\" [ngClass]=\"lesson.status == 1 ? 'greenout': (lesson.status == 2) ? 'yellowout' : 'redout'\">\n                <a *ngIf=\"lesson.status == 1 || lesson.status == 2\" routerLink=\"/topic/{{topicTree.id}}/lesson/{{lesson.id}}\" routerLinkActive=\"active\">\n                    <div class=\"arrow\">\n                        <span>{{lesson.title}}</span>\n                    </div>\n                </a>\n                <div *ngIf=\"lesson.status == 0\" class=\"arrow\">\n                    <span>{{lesson.title}}</span>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>"

/***/ }),

/***/ "../../../../../src/app/topic/topic.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TopicComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("../../../../../src/app/_services/index.ts");
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
        this.sub = this.route.params.subscribe(function (params) {
            _this.id = +params['id']; // (+) converts string 'id' to a number
            // In a real app: dispatch action to load the details here.
            // get topics tree from API
            _this.topicService.getTopic(_this.id)
                .subscribe(function (topicTree) {
                _this.topicTree = topicTree;
            });
        });
    };
    return TopicComponent;
}());
TopicComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        template: __webpack_require__("../../../../../src/app/topic/topic.component.html"),
        providers: [__WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */]]
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__services_index__["c" /* TopicService */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_1__angular_router__["a" /* ActivatedRoute */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_router__["a" /* ActivatedRoute */]) === "function" && _b || Object])
], TopicComponent);

var _a, _b;
//# sourceMappingURL=topic.component.js.map

/***/ }),

/***/ "../../../../../src/app/try/try.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return TryComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__services_index__ = __webpack_require__("../../../../../src/app/_services/index.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
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
        /* https://stackoverflow.com/questions/42538280/angular2-how-to-use-on-the-frontend-crypto-pbkdf2sync-function-from-node-js
        let crypto;
        try {
          crypto = require('crypto');
        } catch (err) {
          console.log('crypto support is disabled!');
        }
        let id = crypto.randomBytes(20, (err, buf) => {
          if (err) throw err;
        }).toString('hex');
        */
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
    return TryComponent;
}());
TryComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        selector: 'app-try',
        template: '<div>Try without registration</div>',
        host: { '(click)': 'onClick()' }
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__services_index__["a" /* AuthenticationService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__services_index__["a" /* AuthenticationService */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_2__angular_router__["c" /* Router */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__angular_router__["c" /* Router */]) === "function" && _b || Object])
], TryComponent);

var _a, _b;
//# sourceMappingURL=try.component.js.map

/***/ }),

/***/ "../../../../../src/app/welcome/index.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__welcome_component__ = __webpack_require__("../../../../../src/app/welcome/welcome.component.ts");
/* harmony namespace reexport (by used) */ __webpack_require__.d(__webpack_exports__, "a", function() { return __WEBPACK_IMPORTED_MODULE_0__welcome_component__["a"]; });

//# sourceMappingURL=index.js.map

/***/ }),

/***/ "../../../../../src/app/welcome/welcome.component.html":
/***/ (function(module, exports) {

module.exports = "<div class=\"col-md-6 col-md-offset-3\">\n    <h2 class=\"center\">Welcome!</h2>\n    <div class=\"pull-right\">\n        <a routerLink=\"/register\" class=\"btn btn-warning width150\">Register account</a>\n    </div>\n    <div>\n        <a routerLink=\"/login\" class=\"btn btn-info width150\">Login</a>\n    </div>\n    <br />\n    <div class=\"center\">\n        <app-try class=\"btn btn-success\"></app-try>\n    </div>\n</div>\n"

/***/ }),

/***/ "../../../../../src/app/welcome/welcome.component.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return WelcomeComponent; });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_router__ = __webpack_require__("../../../router/@angular/router.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__services_index__ = __webpack_require__("../../../../../src/app/_services/index.ts");
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
    return WelcomeComponent;
}());
WelcomeComponent = __decorate([
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["Component"])({
        template: __webpack_require__("../../../../../src/app/welcome/welcome.component.html")
    }),
    __metadata("design:paramtypes", [typeof (_a = typeof __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_1__angular_router__["c" /* Router */]) === "function" && _a || Object, typeof (_b = typeof __WEBPACK_IMPORTED_MODULE_2__services_index__["a" /* AuthenticationService */] !== "undefined" && __WEBPACK_IMPORTED_MODULE_2__services_index__["a" /* AuthenticationService */]) === "function" && _b || Object])
], WelcomeComponent);

var _a, _b;
//# sourceMappingURL=welcome.component.js.map

/***/ }),

/***/ "../../../../../src/environments/environment.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return environment; });
var environment = {
    production: true
};
//# sourceMappingURL=environment.js.map

/***/ }),

/***/ "../../../../../src/main.ts":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
Object.defineProperty(__webpack_exports__, "__esModule", { value: true });
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_0__angular_core__ = __webpack_require__("../../../core/@angular/core.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_1__angular_platform_browser_dynamic__ = __webpack_require__("../../../platform-browser-dynamic/@angular/platform-browser-dynamic.es5.js");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_2__app_app_module__ = __webpack_require__("../../../../../src/app/app.module.ts");
/* harmony import */ var __WEBPACK_IMPORTED_MODULE_3__environments_environment__ = __webpack_require__("../../../../../src/environments/environment.ts");




if (__WEBPACK_IMPORTED_MODULE_3__environments_environment__["a" /* environment */].production) {
    Object(__WEBPACK_IMPORTED_MODULE_0__angular_core__["enableProdMode"])();
}
Object(__WEBPACK_IMPORTED_MODULE_1__angular_platform_browser_dynamic__["a" /* platformBrowserDynamic */])().bootstrapModule(__WEBPACK_IMPORTED_MODULE_2__app_app_module__["a" /* AppModule */])
    .catch(function (err) { return console.log(err); });
//# sourceMappingURL=main.js.map

/***/ }),

/***/ 0:
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__("../../../../../src/main.ts");


/***/ })

},[0]);
//# sourceMappingURL=main.bundle.js.map