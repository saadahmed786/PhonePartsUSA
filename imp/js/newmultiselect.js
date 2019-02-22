(function ($) {
    $.fn.multiSelect = function (options) {
        $.fn.multiSelect.init($(this), options);
    };

    $.extend($.fn.multiSelect, {
        defaults: {
            actcls: 'active',
            selector: 'tbody tr',
            except: ['tbody'],
            statics: ['.static'],
            callback: false
        },
        first: null,
        last: null,
        keycode: null,
        init: function (scope, options) {
            this.scope = scope;
            this.options = $.extend({}, this.defaults, options);
            this.initEvent();
        },
        checkStatics: function (dom) {
            for (var i in this.options.statics) {
                if (dom.is(this.options.statics[i])) {
                    return true;
                }
            }
        },
        initEvent: function () {
            var self = this,
            scope = self.scope,
            options = self.options,
            callback = options.callback,
            actcls = options.actcls;

            scope.on('click.mSelect', options.selector, function (e) {
                if (!e.shiftKey && self.checkStatics($(this))) {
                    return;
                }

                if (!e.shiftKey) {
                    self.first = null;
                }

                if ($(this).hasClass(actcls)) {
                    $(this).removeClass(actcls);
                } else {
                    $(this).addClass(actcls);
                }

                if (e.shiftKey && self.last) {
                    if (!self.first) {
                        self.first = self.last;
                    }
                    var start = self.first.index();
                    var end = $(this).index();
                    if (start > end) {
                        var temp = start;
                        start = end;
                        end = temp;
                    }
                    $(options.selector, scope).removeClass(actcls).slice(start, end + 1).each(function () {
                        if (!self.checkStatics($(this))) {
                            $(this).addClass(actcls);
                        }
                    });
                    window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();
                } else if (!e.ctrlKey && !e.metaKey) {
                    $(this).siblings().removeClass(actcls);
                }
                self.last = $(this);
                $.isFunction(callback) && callback($(options.selector + '.' + actcls, scope));
            });

            $(document).on('click.mSelect', function (e) {
                for (var i in options.except) {
                    var except = options.except[i];
                    if ($(e.target).is(except) || $(e.target).parents(except).size()) {
                        return;
                    }
                }
                scope.find(options.selector).each(function () {
                    if (!self.checkStatics($(this))) {
                        $(this).removeClass(actcls);
                    }
                });
                $.isFunction(callback) && callback($(options.selector + '.' + actcls, scope));
            });

            $(document).on('keydown.mSelect', function (e) {
                if ((e.keyCode == 65) && (e.metaKey || e.ctrlKey)) {
                    $(options.selector, scope).each(function () {
                        if (!self.checkStatics($(this))) {
                            $(this).addClass(actcls);
                        }
                    });
                    $.isFunction(callback) && callback($(options.selector + '.' + actcls, scope));
                    e.preventDefault();
                    return false;
                }
            });

            $(document).on('keydown.mSelect', function (e) {
                if (e.keyCode == 17) {
                    self.first = null;
                }
            });
        }
    });
})(jQuery);

var keyCode = 0;
$(document).on('keydown.mSelect', function (e) {
    keyCode = e.keyCode;
});

function traverseCheckboxes(table, checkboxes)
{
    var Val = '';
    $(table + ' ' + checkboxes).each(function (index, element) {
        if ($(element).parent().parent().hasClass('highlightx') == true) {
            $(this).prop('checked', true);
            $(element).parent().parent().addClass('highlight');
        } else if (keyCode != 16) {
            $(element).parent().parent().removeClass('highlight');
            $(this).prop('checked', false);
        }
        if ($(element).prop("checked") === true)
        {
            if (keyCode != 0) {
                $(element).parent().parent().addClass('highlightx');
            }
        }
    });
    keyCode = 0;
}