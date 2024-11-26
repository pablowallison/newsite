/*---------------------------------------------"
// Template Name: Realtor
// Description:  Realtor Html Template
// Version: 1.0.0

--------------------------------------------*/
(function (window, document, $, undefined) {
  "use strict";
  
  var MyScroll = "";
  var Init = {
    i: function (e) {
      Init.s();
      Init.methods();
    },
    s: function (e) {
      (this._window = $(window)),
        (this._document = $(document)),
        (this._body = $("body")),
        (this._html = $("html"));
    },
    methods: function (e) {
      Init.w();
      Init.preloader();
      Init.BackToTop();
      Init.cusBtn();
      Init.uiHeader();
      Init.videoPlay();
      Init.niceSelect();
      Init.slick();
      Init.contactForm();
      Init.countdownInit(".countdown", "2024/12/01");
      Init.filterToggle();
      Init.wow();
      Init.magnifying();
    },

    w: function (e) {
      this._window.on("load", Init.l).on("scroll", Init.res);
    },
    // =================
    // Preloader
    // =================
    preloader: function () {
      setTimeout(function () {
        $("#preloader").hide("slow");
      }, 2500);
    },
    // =================
    // Bak to top
    // =================
    BackToTop: function () {
      let scrollTop = $(".scroll-top path");
      if (scrollTop.length) {
        var e = document.querySelector(".scroll-top path"),
          t = e.getTotalLength();
        (e.style.transition = e.style.WebkitTransition = "none"),
          (e.style.strokeDasharray = t + " " + t),
          (e.style.strokeDashoffset = t),
          e.getBoundingClientRect(),
          (e.style.transition = e.style.WebkitTransition =
            "stroke-dashoffset 10ms linear");
        var o = function () {
          var o = $(window).scrollTop(),
            r = $(document).height() - $(window).height(),
            i = t - (o * t) / r;
          e.style.strokeDashoffset = i;
        };
        o(), $(window).scroll(o);
        var back = $(".scroll-top"),
          body = $("body, html");
        $(window).on("scroll", function () {
          if ($(window).scrollTop() > $(window).height()) {
            back.addClass("scroll-top--active");
          } else {
            back.removeClass("scroll-top--active");
          }
        });
      }
    },
    // =======================
    //  Button Style
    // =======================
    cusBtn: function () {
      $(".cus-btn")
        .on("mouseenter", function (e) {
          var parentOffset = $(this).offset(),
            relX = e.pageX - parentOffset.left,
            relY = e.pageY - parentOffset.top;
          $(this).find("span").css({ top: relY, left: relX });
        })
        .on("mouseout", function (e) {
          var parentOffset = $(this).offset(),
            relX = e.pageX - parentOffset.left,
            relY = e.pageY - parentOffset.top;
          $(this).find("span").css({ top: relY, left: relX });
        });
    },
    // =======================
    //  UI Header
    // =======================
    uiHeader: function () {
      function dynamicCurrentMenuClass(selector) {
        let FileName = window.location.href.split("/").reverse()[0];

        selector.find("li").each(function () {
          let anchor = $(this).find("a");
          if ($(anchor).attr("href") == FileName) {
            $(this).addClass("current");
          }
        });
        selector.children("li").each(function () {
          if ($(this).find(".current").length) {
            $(this).addClass("current");
          }
        });
        if ("" == FileName) {
          selector.find("li").eq(0).addClass("current");
        }
      }

      if ($(".main-menu__list").length) {
        let mainNavUL = $(".main-menu__list");
        dynamicCurrentMenuClass(mainNavUL);
      }

      if ($(".main-menu__nav").length && $(".mobile-nav__container").length) {
        let navContent = document.querySelector(".main-menu__nav").innerHTML;
        let mobileNavContainer = document.querySelector(".mobile-nav__container");
        mobileNavContainer.innerHTML = navContent;
      }
      if ($(".sticky-header__content").length) {
        let navContent = document.querySelector(".main-menu").innerHTML;
        let mobileNavContainer = document.querySelector(".sticky-header__content");
        mobileNavContainer.innerHTML = navContent;
      }

      if ($(".mobile-nav__container .main-menu__list").length) {
        let dropdownAnchor = $(
          ".mobile-nav__container .main-menu__list .dropdown > a"
        );
        dropdownAnchor.each(function () {
          let self = $(this);
          let toggleBtn = document.createElement("BUTTON");
          toggleBtn.setAttribute("aria-label", "dropdown toggler");
          toggleBtn.innerHTML = "<i class='fa fa-angle-down'></i>";
          self.append(function () {
            return toggleBtn;
          });
          self.find("button").on("click", function (e) {
            e.preventDefault();
            let self = $(this);
            self.toggleClass("expanded");
            self.parent().toggleClass("expanded");
            self.parent().parent().children("ul").slideToggle();
          });
        });
      }

      if ($(".mobile-nav__toggler").length) {
        $(".mobile-nav__toggler").on("click", function (e) {
          e.preventDefault();
          $(".mobile-nav__wrapper").toggleClass("expanded");
          $("body").toggleClass("locked");
        });
      }

      $(window).on("scroll", function () {
        if ($(".stricked-menu").length) {
          var headerScrollPos = 130;
          var stricky = $(".stricked-menu");
          if ($(window).scrollTop() > headerScrollPos) {
            stricky.addClass("stricky-fixed");
          } else if ($(this).scrollTop() <= headerScrollPos) {
            stricky.removeClass("stricky-fixed");
          }
        }
      });
    },

    // =======================
    //  Nice Select
    // =======================
    niceSelect: function () {
      if ($(".has-nice-select").length) {
        $('.has-nice-select, .contact-form select').niceSelect();
      }
    },
    // =======================
    //  Video Play
    // =======================
    videoPlay: function () {
      $(".video .play-btn").on("click", function () {
        $(".video .img-box").hide("slow");
        $(".video .video-box").show("slow");
      });
    },

    // =======================
    //  Slick Slider
    // =======================
    slick: function () {
      if ($(".featured-properties-slider").length) {
        $('.featured-properties-slider').slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          variableWidth: true,
          infinite: true,
          autoplay: false,
          dots:false,
          arrows: true,
          lazyLoad: 'progressive',
          speed: 800,
          responsive: [
            {
              breakpoint: 575,
              settings: {
                slidesToShow: 1,
                variableWidth: false,
              },
            },
          ],
        });
      }
      if ($(".property-location-filter-slider").length) {
        $('.property-location-filter-slider').slick({
          slidesToShow: 5,
          slidesToScroll: 1,
          infinite: true,
          autoplay: true,
          dots:false,
          arrows: false,
          lazyLoad: 'progressive',
          autoplaySpeed: 2000,
          speed: 800,
          responsive: [
            {
              breakpoint: 1399,
              settings: {
                slidesToShow: 4,
              },
            },
            {
              breakpoint: 992,
              settings: {
                slidesToShow: 3,
              },
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 2,
              },
            },
            {
              breakpoint: 492,
              settings: {
                slidesToShow: 1,
              },
            },
          ],
        });
      }
      if ($(".gallery-slider").length) {
        $('.gallery-slider').slick({
          slidesToShow: 4,
          slidesToScroll: 1,
          infinite: true,
          autoplay: true,
          dots:false,
          arrows: false,
          lazyLoad: 'progressive',
          autoplaySpeed: 2000,
          speed: 800,
          responsive: [
            {
              breakpoint: 1399,
              settings: {
                slidesToShow: 4,
              },
            },
            {
              breakpoint: 992,
              settings: {
                slidesToShow: 3,
              },
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 2,
              },
            },
          ],
        });
      }
      if ($(".testimonials-slider").length) {
        $('.testimonials-slider').slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          variableWidth: true,
          infinite: true,
          autoplay: false,
          dots:false,
          arrows: true,
          lazyLoad: 'progressive',
          speed: 800,
          responsive: [
            {
              breakpoint: 575,
              settings: {
                slidesToShow: 1,
                variableWidth: false,
              },
            },
          ],
        });
      }
      if ($(".property-images-slider").length) {
        $('.property-images-slider').slick({
          slidesToShow: 1,
          slidesToScroll: 1,
          infinite: true,
          autoplay: false,
          dots:false,
          arrows: true,
          lazyLoad: 'progressive',
          speed: 800,
        });
      }
      if ($(".partners-slider").length) {
        $(".partners-slider").slick({
          slidesToShow: 5,
          arrows: false,
          dots: false,
          infinite: true,
          autoplay: true,
          cssEase: "linear",
          autoplaySpeed: 0,
          speed: 6000,
          pauseOnFocus: false,
          pauseOnHover: false,
          responsive: [
            {
              breakpoint: 1399,
              settings: {
                slidesToShow: 4,
              },
            },
            {
              breakpoint: 992,
              settings: {
                slidesToShow: 3,
              },
            },
            {
              breakpoint: 767,
              settings: {
                slidesToShow: 2,
              },
            },
            {
              breakpoint: 450,
              settings: {
                slidesToShow: 1,
              },
            },
          ],
        });
      }
      $(".prev-btn").click(function () {
        var $this = $(this).attr("data-slide");
        $('.' + $this).slick("slickPrev");
      });

      $(".next-btn").click(function () {
        var $this = $(this).attr("data-slide");
        $('.' + $this).slick("slickNext");
      });
    },
 
    // =======================
    //  Contact Form
    // =======================
    contactForm: function () {
      $(".contact-form").on("submit", function (e) {
        e.preventDefault();
        if ($(".contact-form")) {
          var _self = $(this);
          _self
            .closest("div")
            .find('button[type="submit"]')
            .attr("disabled", "disabled");
          var data = $(this).serialize();
          $.ajax({
            url: "./assets/mail/contact.php",
            type: "post",
            dataType: "json",
            data: data,
            success: function (data) {
              $(".contact-form").trigger("reset");
              _self.find('button[type="submit"]').removeAttr("disabled");
              if (data.success) {
                document.getElementById("message").innerHTML =
                  "<h5 class='color-primary'>Email Sent Successfully</h5>";
              } else {
                document.getElementById("message").innerHTML =
                  "<h5 class='text-danger'>There is an error</h5>";
              }
              $("#message").show("slow");
              $("#message").slideDown("slow");
              setTimeout(function () {
                $("#message").slideUp("hide");
                $("#message").hide("slow");
              }, 3000);
            },
          });
        } else {
          return false;
        }
      });
    },
    // =======================
    //  Coming Soon Countdown
    // =======================
    countdownInit: function (countdownSelector, countdownTime) {
      var eventCounter = $(countdownSelector);
      if (eventCounter.length) {
        eventCounter.countdown(countdownTime, function (e) {
          $(this).html(
            e.strftime(
              '<li><h1>%D</h1><h6>Days</h6></li>\
              <li><h1>%H</h1><h6>Hrs</h6></li>\
              <li><h1>%M</h1><h6>Min</h6></li>\
              <li><h1>%S</h1><h6>Sec</h6></li>'
            )
          );
        });
      }
    },
    // =======================
    //  Filter Toggle
    // =======================
    filterToggle: function () {
      if ($('.sidebar-widget').length) {
        $(".widget-title-row").on("click", function (e) {
          $(this).find('i').toggleClass('fa-horizontal-rule fa-plus', 'slow');
          // $(this).find('i').toggleClass('fa-light fa-regular', 'slow');
          $(this).parents('.sidebar-widget').find('.widget-content-block').animate({ height : 'toggle'}, 'slow');
        })
      }
    },
    // =======================
    //  Wow
    // =======================
    wow: function () {
      if ($(".wow").length) {
        var wow = new WOW({
          boxClass: "wow", // animated element css class (default is wow)
          animateClass: "animated", // animation css class (default is animated)
          mobile: true, // trigger animations on mobile devices (default is true)
          live: true, // act on asynchronously loaded content (default is true)
        });
        wow.init();
      }
    },
    // =======================
    // Magnifying Popup
    // =======================
    magnifying: function () {
      if ($('.popup-video').length) {
        $('.popup-video').magnificPopup({
          disableOn: 700,
          type: 'iframe',
          mainClass: 'mfp-fade',
          removalDelay: 160,
          preloader: false,
          fixedContentPos: false
        });
      }
    },
  };

  Init.i();
})(window, document, jQuery);