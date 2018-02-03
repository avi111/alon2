var Vue = require('vue/dist/vue');
var $ = require('jquery');
var ajax = require('axios');
var slick = require('slick-carousel');

var homeSlider;

$(document).ready(function () {
    "use strict";
    if (document.getElementById("home-slider")) {
        homeSlider = new Vue({
            el: '#home-slider',
            data: {
                progress: 0,
                active: 0,
                maxProgress: 1000,
                step: 5,
                total: null
            },
            mounted: function () {
                "use strict";
                this.total = $("#home-slider>section").attr("total") - 1;

                setInterval(function () {
                    homeSlider.progress += homeSlider.step;
                    if (homeSlider.progress > homeSlider.maxProgress) {
                        homeSlider.active++;
                        homeSlider.inbounds();
                        homeSlider.progress = 0;
                    }
                }, 10 * this.step);
            },
            methods: {
                inbounds: function () {
                    if (homeSlider.active > homeSlider.total) {
                        homeSlider.active = 0;
                    }
                    if (homeSlider.active < 0) {
                        homeSlider.active = homeSlider.total - 1;
                    }
                },
                prev: function (e) {
                    homeSlider.active--;
                    homeSlider.inbounds();
                    homeSlider.progress = 0;
                },
                next: function (e) {
                    homeSlider.active++;
                    homeSlider.inbounds();
                    homeSlider.progress = 0;
                }
            }
        });
    }
})