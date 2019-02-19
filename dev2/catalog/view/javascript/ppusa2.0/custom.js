$(document).ready(function() {
    $("[rel=tipsy]").tipsy({
        fade: !0,
        gravity: "s"
    }), $(".fancybox").fancybox({
        padding: 20
    }), $(".selectpicker").selectpicker({
        width: "270px",
        style: "btn btn-xs btn-default"
    }), $(document).on("click", ".spinner .btn:first-of-type", function() {
        var a = $(this).parents(".spinner").find("input.form-control");
        a.val(parseInt(a.val(), 10) + 1), a.trigger("change")
    }), $(document).on("click", ".spinner .btn:last-of-type", function() {
        var a = $(this).parents(".spinner").find("input.form-control");
        a.val(parseInt(a.val(), 10) - 1), a.trigger("change")
    }), $(".slider-for").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: !1,
        fade: !0,
        asNavFor: ".slider-nav"
    }), $(".slider-nav").slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: ".slider-for",
        dots: !0,
        centerMode: !0,
        focusOnSelect: !0
    }), $(".homeSlider .homeSlides").slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: !1
    });
    var a = {
            scrollZoom: !0,
            easing: !0,
            lensSize: 200,
            zoomWindowWidth: 400,
            zoomWindowHeight: 400
        },
        c = ($("#gallery_01 a"), $("#img_01"));
    $(".cart-thumbs .image").click(function() {
        var a = $(this).attr("data-bigImg");
        $(".cart-items .big-image img").attr("src", a).attr("data-zoom-image", a), $(".zoomWindowContainer .zoomWindow").css("background-image", "url(" + a + ")")
    }), $(window).width() > 1023 && c.elevateZoom(a), $(".top-dropdown-icon").click(function() {
        $(this).next(".top-dropdowns").fadeToggle()
    }), $(".top-dropdown-icon").click(function() {
        $(this).next("#cart").find(".top-dropdowns").fadeToggle()
    }), $(document).mouseup(function(a) {
        var b = $(".top-dropdowns");
        b.is(a.target) || 0 !== b.has(a.target).length || $(".top-dropdown-icon").is(a.target) || b.hide()
    });
    var d = $(".catalog-steps-box").slimScroll({
        height: "185px",
        size: "10px",
        color: "#4986fe",
        railVisible: !0,
        railColor: "#f4f4f4"
    });
    $(".scroll").slimScroll({
        height: "auto",
        size: "10px",
        color: "#4986fe",
        railVisible: !0,
        railColor: "#f4f4f4",
        alwaysVisible: !0
    }), $(".scroll2").slimScroll({
        height: "auto",
        size: "10px",
        color: "#4986fe",
        railVisible: !0,
        railColor: "#f4f4f4",
        alwaysVisible: !0
    }), $(".scroll3").slimScroll({
        height: "auto",
        size: "3px",
        color: "#4986fe",
        railVisible: !0,
        railColor: "#f4f4f4",
        alwaysVisible: !0
    }), $(document).on("click", ".cataglo-list li a", function(a) {
        0 == $(this).parents(".cataglo-list").hasClass("active-anchor") && a.preventDefault(), $(this).parents(".catalog-steps-col").addClass("active")
    }), $(".catalog-steps-col:nth-child(3) .cataglo-list li").click(function() {
        $(".catalog-steps-col:nth-child(1)").hasClass("active") && $(".catalog-steps-col:nth-child(2)").hasClass("active") && ($(window).width() > 991 && ($("#grid-view").addClass("active"), $(".viewport-btns .viewport-icon:first-child").addClass("active")), $(".steps-info").fadeOut())
    }), $(".nav-tabs a").click(function(a) {
        $(this).tab("show")
    });
    var e = $("#cartRight .cart-menu").height();
    if (e = e - 125 - $("#cartRight .top-dropdowns-title").outerHeight() - $("#cartRight .top-dropdown-ftr").outerHeight(), $("#cartRight .cart-scroll2Parent").height(e), $(".cart-scroll2").slimScroll({
            height: "auto",
            size: "10px",
            color: "#4986fe",
            railVisible: !0,
            railColor: "#f4f4f4",
            alwaysVisible: !0
        }), $(window).resize(function() {
            var a = $("#cartRight .cart-menu").height();
            a = a - 125 - $("#cartRight .top-dropdowns-title").outerHeight() - $("#cartRight .top-dropdown-ftr").outerHeight(), $("#cartRight .cart-scroll2Parent").height(a), $(".cart-scroll2").slimScroll({
                height: "auto",
                size: "10px",
                color: "#4986fe",
                railVisible: !0,
                railColor: "#f4f4f4",
                alwaysVisible: !0
            })
        }), $(document).mouseup(function(a) {
            var b = $("#cartRight");
            b.is(a.target) || 0 !== b.has(a.target).length || $(b).hasClass("in") && ($(b).addClass("inOne"), b.removeClass("in"), blackOutBg())
        }), $(document).ready(function() {
            $(".top-dropdown-body").children().length > 0 && $("#cartRight").addClass("inOne"), loadCarts($(".top-dropdown-body").children().length)
        }), $(document).on("click", ".strip-cart-circle,.close-cart,.cart_icon_btn", function() {
            $(".top-dropdown-body").children().length > 0 && ($("#cartRight").removeClass("inOne"), $("#cartRight").hasClass("in") ? ($("#cartRight").removeClass("in"), $("#cartRight").addClass("inOne"), blackOutBg()) : ($("#cartRight").addClass("in"), blackOutBg()))
        }), $(".top-dropdown-body").children().length > 1 && $(".cart-scroll").slimScroll({
            height: "320px",
            size: "10px",
            color: "#4986fe",
            railVisible: !0,
            railColor: "#f4f4f4",
            alwaysVisible: !0
        }), $(document).on("click", ".product-detail .cart-close", function(a) {
            a.preventDefault(), $(this).parent().find(".removeProduct").trigger("click"), $(this).parents(".product-detail").fadeOut().remove();
            var b = $(".top-dropdown-body").children().length;
            b <= 2 == 1 && ($(".cart-scroll").slimScroll({
                destroy: !0
            }), $(".top-dropdown-body").css("height", "auto"))
        }), $(".upload-btn").click(function() {
            $("input[name='quickordercsv']").trigger("click")
        }), $(".more-phone").click(function(a) {
            a.preventDefault()
        }), $(".remove-phone").click(function(a) {
            a.preventDefault(), $(this).parent(".phone-copy").remove()
        }), $(document).on('click', ".password-click input[type=checkbox].css-checkbox", function() {
            if ($(this).prop("checked")) {
                $('.password-toggle input[type=password]').prop('required', !0)
            } else {
                $('.password-toggle input[type=password]').removeAttr('required')
            }
            1 == $(this).prop("checked") ? $(".password-toggle").fadeIn() : 0 == $(this).prop("checked") && $(".password-toggle").fadeOut()
        }), $(".addmore-track").click(function(a) {
            a.preventDefault();
            var b = $(this).next(".track-row-copy").clone(!0, !0).removeClass("track-row-copy");
            $(".track-container").append(b)
        }), $(".row-close").click(function() {
            $(this).parents(".track-row").remove()
        }), $(".info-click").click(function(a) {
            event.preventDefault(), $(window).width() > 991 ? $(".save-info").fadeIn() : $(".save-info2").fadeIn(), $(".inline-info li .input").attr("readonly", !1).addClass("active"), $(".inline-info:first-of-type .input").focus().addClass("active"), $(".save-info, .save-info2").click(function() {
                $(".inline-info li .input").attr("readonly", !0).removeClass("active"), $(window).width() > 991 ? $(".save-info").fadeOut() : $(".save-info2").fadeOut()
            })
        }), $(window).width() > 991) {
        var f = $(".header-bottom").offset().top;
        if ($(window).scroll(function() {
                $(window).scrollTop() > f ? $(".header-bottom").addClass("sticky") : $(".header-bottom").removeClass("sticky")
            }), $(document).find(".catalog-steps").length) {
            var g = $(".catalog-steps").offset().top;
            $(window).scroll(function() {
                if ($(window).scrollTop() > g) {
                    $(".catalog-steps").addClass("fixedtop");
                    $(".catalog-steps").css("height", '115px');
                    $('.catalog-steps-col .slimScrollDiv').hide()
                } else {
                    $(".catalog-steps").removeClass("fixedtop");
                    $('.catalog-steps-col .slimScrollDiv').show();
                    $(".catalog-steps.").css("height", 'auto')
                }
            })
        }
    }
    if ($(".viewport-icon").click(function(a) {
            a.preventDefault(), $(this).addClass("active").siblings().removeClass("active");
            var b = $(this).attr("href");
            $(b).fadeIn(), myString2 = $(this).siblings().attr("href"), $(myString2).fadeOut()
        }), $("#back-to-top").length) {
        var h = 100,
            i = function() {
                var a = $(window).scrollTop();
                a > h ? $("#back-to-top").addClass("show") : $("#back-to-top").removeClass("show")
            };
        i(), $(window).on("scroll", function() {
            i()
        }), $("#back-to-top").on("click", function(a) {
            a.preventDefault(), $("html,body").animate({
                scrollTop: 0
            }, 700)
        })
    }
    $("#reset-pass-btn").click(function(a) {
        a.preventDefault();
        $.ajax({
            type: 'post',
            url: 'index.php?route=account/forgotten',
            data: {
                email: $('input[name=email]').val(),
                ajaxupdate: 'yesyes'
            },
            dataType: 'json',
            beforeSend: function() {
                $('#reset-pass-btn').attr('disabled', !0)
            },
            success: function(json) {
                $('#reset-pass-btn').removeAttr('disabled');
                if (json.success) {
                    $(".password-req-note").text('A Password reset email has been sent to you. Please follow the instructions to reset your password, and sign in to your account again!')
                } else {
                    $(".password-req-note").text(json.message)
                }
                $('.password-req-note').fadeIn()
            }
        })
    }), $(document).on("change", ".filter-check li  a input[type=checkbox].css-checkbox", function() {
        var a = $(this).next("label").text(),
            b = $(this).attr("id"),
            c = $(this).closest("[data-filter-name]").data("filter-name");
        if (1 == $(this).prop("checked") && "checkAll" != $(this).attr("id") && 0 == $(".filter-buttons li a[data-btn-id=" + $(this).attr("id") + "]").length) {
            if (!$(this).parent().next().is(".subfilter")) {
                if ($(this).parent().parent().parent().find("input[type=checkbox][id!=checkAll]").length == $(this).parent().parent().parent().find("input[type=checkbox][id!=checkAll]:checked").length && ($(this).parent().parent().parent().hasClass("subfilter") || $("#checkAll").prop("checked", !0)), $(this).parent().parent().parent().hasClass("subfilter") && $(this).parent().parent().parent().find("input[type=checkbox][id!=checkAll]:checked").length > 0 && $(this).parent().parent().parent().prev("a").find("input").prop("checked", !0), $(".filter-buttons").find("li[data-filter-getter='" + c + "']").length > 0) {
                    var d = '<a href="javascript:void(0);" class="single-filter" data-btn-id="' + b + '">' + a + ' <span class="filter-close"><img src="catalog/view/theme/ppusa2.0/images/icons/cross.png" alt=""></span></a>';
                    $(d).appendTo($(".filter-buttons li[data-filter-getter='" + c + "']"))
                } else {
                    var d = '<li data-filter-getter="' + c + '"><strong>' + c + ':</strong><a href="javascript:void(0);" class="single-filter" data-btn-id="' + b + '">' + a + ' <span class="filter-close"><img src="catalog/view/theme/ppusa2.0/images/icons/cross.png" alt=""></span></a> </li>';
                    $(d).appendTo(".filter-buttons-right ul ")
                }
                $(".filter-buttons").find("li").length > 0 && $(".clear-filter").show()
            }
        } else if (0 == $(this).prop("checked")) {
            $(this).parent().next(".subfilter").slideUp(), $(this).parent().parent().parent().hasClass("subfilter") ? 0 == $(this).parent().parent().parent().find("input[type=checkbox][id!=checkAll]:checked").length : $("#checkAll").prop("checked", !0), $(".filter-buttons").find("a[data-btn-id=" + $(this).attr("id") + "]").remove();
            var e = $(".filter-buttons").find("li[data-filter-getter='" + c + "']");
            0 == e.find("a").length && e.remove(), 0 == $(".filter-buttons").find("li").length && $(".clear-filter").hide()
        }
    }), $(".filter-buttons").on("click", ".filter-close", function(a) {
        a.preventDefault(), $("#" + $(this).parent().data("btn-id")).click()
    }), $(".clear-filter").click(function(a) {
        a.preventDefault(), $(".filter-buttons-right ul li").remove(), $(".filter-check  li  a input[type=checkbox]").prop("checked", !1), $(".clear-filter").hide()
    }), $("#checkAll").change(function() {
        1 == $(this).prop("checked") ? $(this).parents("ul").find(".css-checkbox").each(function(a, b) {
            "checkAll" != $(b).attr("id") && ($(b).prop("checked", !0), $(b).trigger("change"))
        }) : $(this).parents("ul").find(".css-checkbox").prop("checked", !1).each(function(a, b) {
            "checkAll" != $(b).attr("id") && $(b).trigger("change")
        })
    });
    var j = $(window).width();
    if (RmWidth = j - $(".container").width() + 1, EdgeWidth = RmWidth / 2, $("#cart .top-dropdowns").css("right", -EdgeWidth), $(".cart-menu.top-dropdowns .caret-up").css("right", EdgeWidth + 5), $(".filter-counter").click(function() {
            $(".filter-product").addClass("slide-left");
            $('.overlay-right').show();
            $('.logo').attr('style', 'z-index: 1;')
        }), $(".filter-product .apply-filter").click(function(a) {
            a.preventDefault();
            var b = $(".filter-product input:checkbox:checked").length;
            $(".filter-product").removeClass("slide-left");
            $('.logo').attr('style', '');
            $('.overlay-right').hide();
            $(".filter-product").removeClass("slide-left"), $(".filter-counter .filter-qty").text(b)
        }), $("#close_filter_box").click(function(a) {
            a.preventDefault(), $(".filter-product").removeClass("slide-left"), $('.overlay-right').hide()
        }), $(".policy-click").click(function(a) {
            a.preventDefault(), $("#policy-detail").fadeIn()
        }), $(window).width() < 991 && (d.slimScroll({
            destroy: !0
        }), $(".catalog-steps .slimScrollBar,.catalog-steps .slimScrollRail").remove(), $(".catalog-steps .slimScrollDiv").each(function() {
            $(this).replaceWith($(this).children())
        }), $(".catalog-steps-title").click(function() {
            var a = $(this);
            1 == $(this).hasClass("slideDown") ? (d.slimScroll({
                destroy: !0
            }), $(".catalog-steps .slimScrollBar,.catalog-steps .slimScrollRail").remove(), $(".catalog-steps .slimScrollDiv").each(function() {
                $(this).replaceWith($(this).children())
            }), $(this).removeClass("slideDown").next().slideUp()) : ($(this).addClass("slideDown").next(".catalog-steps-box").slideDown(), $(this).next(".catalog-steps-box").slimScroll({
                height: "185px",
                size: "10px",
                color: "#4986fe",
                railVisible: !0,
                railColor: "#f4f4f4"
            })), $(".cataglo-list li").click(function() {
                d.slimScroll({
                    destroy: !0
                }), $(".catalog-steps .slimScrollBar,.catalog-steps .slimScrollRail").remove(), $(".catalog-steps .slimScrollDiv").each(function() {
                    $(this).replaceWith($(this).children())
                }), $(this).parents(".catalog-steps-col").is(":nth-child(3)") && $(".catalog-steps-col:nth-child(1)").hasClass("active") && $(".catalog-steps-col:nth-child(2)").hasClass("active") ? ($(this).parents(".catalog-steps-box").stop().slideDown(), a.next(".catalog-steps-box").slimScroll({
                    height: "185px",
                    size: "10px",
                    color: "#4986fe",
                    railVisible: !0,
                    railColor: "#f4f4f4"
                })) : a.removeClass("slideDown").next().slideUp()
            })
        })), $(document).on('change', ".return-list input:checkbox", function() {
            1 == $(this).prop("checked") ? $(this).parents(".return-list-box").find(".return-items-qty,.return-items-comment,.return-items-btn").slideDown() : 0 == $(this).prop("checked") && $(this).parents(".return-list-box").find(".return-items-qty,.return-items-comment,.return-items-btn").slideUp()
        }), $(document).on("change", "input.check-toggler", function() {
            1 == $(this).hasClass("check-toggler") && (1 == $(this).prop("checked") ? ($(".check-toggled").slideDown(), $("#button-guest").attr("id", "button-register")) : 0 == $(this).prop("checked") && ($(".check-toggled").slideUp(), $("#button-register").attr("id", "button-guest")))
        }), "paypal" == $(".pamentMethod-head input.css-radio2:checked").next().text() && $("#btnPaypal").hide().prev().css("margin-right", "0"), $(".pamentMethod-head input.css-radio2").change(function() {
            1 == $(this).prop("checked") && "paypal" == $(this).next().text() ? $(this).parents("#paymentMethod").find(".btn-info.light").hide().prev().css("margin-right", "0") : $(this).parents("#paymentMethod").find(".btn-info.light").show().prev().css("margin-right", "28px")
        }), $(window).width() < 992) {
        var k = $(window).height();
        $(".filter-product .filter-group").height(k - 100)
    }
    $(window).resize(function() {
        if ($(window).width() < 992) {
            var a = $(window).height();
            $(".filter-product .filter-group").height(a - 100)
        } else $(".filter-product .filter-group").css("height", "auto")
    }), $(".panel-trigger").click(function(a) {
        a.preventDefault(), $(this).toggleClass("active").parents(".panel-trigger-parent").next().next(".panel-triggered").slideToggle(), $(this).parents(".panel-trigger-parent").next(".panel-triggered").slideToggle()
    })
}), $(document).on("click", ".packageTable .home-color-picker", function() {
    loadPopularImages($(this).attr("data-product-id"), $(this).parent().parent().parent().parent().parent().parent())
}), $(document).on("change", "#home_manufacturer", function() {
    "" != $(this).val() && $.ajax({
        url: "imp/product_catalog/man_catalog.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "loadModels",
            other_id: $(this).val(),
            perform: "loadNav"
        }
    }).always(function(a) {
        var b = "";
        b += '<option value="">Model</option>';
        for (var c = 0; c < a.nav.length; c++) b += '<option value="' + a.nav[c].id + '">' + a.nav[c].name + "</option>";
        $("#home_model").html(b), $("#home_model").selectpicker("refresh")
    })
}), $(document).on("change", "#home_model", function() {
    "" != $(this).val() && $.ajax({
        url: "imp/product_catalog/man_catalog.php",
        type: "POST",
        dataType: "json",
        data: {
            action: "loadSubModels",
            other_id: $(this).val(),
            perform: "loadNav"
        }
    }).always(function(a) {
        var b = "";
        b += '<option value="">Sub-Model</option>';
        for (var c = 0; c < a.nav.length; c++) b += '<option value="' + a.nav[c].id + '">' + a.nav[c].name + "</option>";
        b += '<option value="-1">All Sub-Models</option>'
        $("#home_sub_model").html(b), $("#home_sub_model").selectpicker("refresh")
    })
}), $(document).on("click", "#sFindPart", function(a) {
    var b = $("#home_manufacturer").val(),
        c = $("#home_model").val();
    if (b && c) {
        var f = "index.php?route=catalog/repair_parts&path=" + b + "_" + c;
        window.location.replace(f)
    }
}), $(document).on("change", "#home_sub_model", function() {
    if ("" != $(this).val()) {
        var a = {};
        a.manufacturer_id = $("#home_manufacturer").val(), a.device_id = $("#home_model").val(), a.model_id = $(this).val(), a.main_class_id = "", a.attrib_id = "", a.group_id = "1633", $.ajax({
            url: "imp/product_catalog/man_catalog.php?page=1",
            type: "POST",
            dataType: "json",
            data: {
                main: "yes",
                filter: a,
                perform: "loadProducts"
            }
        }).always(function(a) {
            if (data = '<option value="">Part Type</option>', a.classes)
                for (var b = "", c = a.classes.length, d = 0; d < c; d++) a.classes[d].main_name != b && (data += '<option value="' + a.classes[d].main_name + '">' + a.classes[d].main_name + "</option>", b = a.classes[d].main_name);
            $("#home_part_type").html(data), $("#home_part_type").selectpicker("refresh")
        })
    }
}), $(document).on("click", ".discountcode #add_discount_row", function() {
    $(".discountcode #discount_codes").append('<div class="clearfix" style="padding-top:10px" ><input type="text" class="code-box" style="float:left;margin-right:3px;width:55%" value=""> <button class="btn btn-danger" style="float:left;margin-top:3px" onclick="$(this).parent().remove();">-</button> <button class="btn btn-info cart_apply_btn" style="float:right;margin-top:3px">Apply</button></div>')
}), $(document).on("click", ".discountcode .cart_apply_btn", function() {
    $(this).addClass("disabled"), $(".alert-danger").hide();
    var a = $(this),
        b = $(this).parent().children(".code-box").val();
    $.ajax({
        url: "index.php?route=checkout/cart/addVoucher",
        type: "POST",
        dataType: "json",
        data: {
            voucher: b
        }
    }).always(function(b) {
        $(a).removeClass("disabled"), b.warning && ($(".alert-danger").show(), $(".alert-danger").html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>' + b.warning), $("html,body").animate({
            scrollTop: 0
        }, 700)), b.success && ($(".alert-success").show(), $(".alert-success").html(b.success), $(".cart-product-small #cart_right_div #cart_cart_right").load("index.php?route=module/cart_right_cart"), loadxPopup(), $("#checkout_right_cart").load("index.php?route=module/checkout_right_cart"))
        if(b.ib==1)
        {
        	location.reload(true);
        }
    })
}), $('a').on('click', document, function(e) {
    if ($(this).attr('href') == '#') {
        e.preventDefault()
    }
});
$('#search_inp').keypress(function(e) {
    var key = e.which;
    if (key == 13) {
        $('#search_inp_button').click();
        return !1
    }
});

function handle(e) {
    if (e.keyCode === 13) {
        e.preventDefault();
        searcher()
    }
}

function searcher() {
    url = 'index.php?route=product/search';
    var filter_name = $('#search_inp').val();
    var brand = $('#home_manufacturer2').val();
    if (filter_name == '') {
        $('#search_inp').css('border', '1px solid red');
        return !1
    }
    if (filter_name) {
        url += '&filter_name=' + encodeURIComponent(filter_name)
    }
    if (brand) {
        url += '&brand_id=' + encodeURIComponent(brand)
    }
    location = url
}

function validEmail(email) {
    var re = /^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i;
    return email.match(re)
}

function notifyMe(product_id) {
    $('.oos_qty_error_' + product_id).text('');
    var email = $('.customer_email_' + product_id).val();
    var button = $('#notify_btn_' + product_id);
    if (!validEmail(email)) {
        $('.oos_qty_error_' + product_id).text('Enter Valid Email');
        return !1
    }
    $.ajax({
        type: 'post',
        url: 'index.php?route=product/product/notify',
        data: {
            data: email,
            product_id: product_id
        },
        dataType: 'json',
        success: function(json) {
            button.text('Notification Set');
            $('.customer_email_' + product_id).hide()
        }
    })
}
$(document).on('keyup', "#zip_cart", function(event) {
    if (event.keyCode == 13) {
        $("#apply_zip_cart").trigger('click')
    }
});
$(document).on('change', 'input[name=quickordercsv]', function() {
    $('button[name=quickOrder]').trigger('click')
})
$(document).ready(function() {
    if ($(window).width() <= 991) {
        $('#cart_right_div .panel-collapse').removeClass('in');
        $('#checkout_right_cart .panel-collapse').removeClass('in')
    }
})