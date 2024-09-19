/*! DataTables Tailwind CSS integration
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'datatables.net'], function ($) {
            return factory($, window, document);
        });
    }
    else if (typeof exports === 'object') {
        // CommonJS
        var jq = require('jquery');
        var cjsRequires = function (root, $) {
            if (!$.fn.dataTable) {
                require('datatables.net')(root, $);
            }
        };

        if (typeof window === 'undefined') {
            module.exports = function (root, $) {
                if (!root) {
                    // CommonJS environments without a window global must pass a
                    // root. This will give an error otherwise
                    root = window;
                }

                if (!$) {
                    $ = jq(root);
                }

                cjsRequires(root, $);
                return factory($, root, root.document);
            };
        }
        else {
            cjsRequires(window, jq);
            module.exports = factory(jq, window, window.document);
        }
    }
    else {
        // Browser
        factory(jQuery, window, document);
    }
}(function ($, window, document) {
    'use strict';
    var DataTable = $.fn.dataTable;



    /*
     * This is a tech preview of Tailwind CSS integration with DataTables.
     */

    // Set the defaults for DataTables initialisation
    $.extend(true, DataTable.defaults, {
        renderer: 'tailwindcss'
    });


    // Default class modification
    $.extend(true, DataTable.ext.classes, {
        container: "dt-container dt-tailwindcss",
        search: {
            input: "border placeholder-neutral-300 ml-2 rounded-lg border-base-200 bg-base-100 shadow"
        },
        length: {
            select: "border px-3 py-2 rounded-lg border-base-200 bg-base-100"
        },
        processing: {
            container: "dt-processing"
        },
        paging: {
            active: 'font-semibold bg-base-100',
            notActive: 'bg-base-300',
            button: 'shadow relative inline-flex justify-center items-center space-x-2 px-4 py-2 -mr-px leading-6 hover:z-10 focus:z-10 active:z-10 active:shadow-none',
            first: 'rounded-l-lg',
            last: 'rounded-r-lg',
            enabled: 'text-base-content hover:border-base-200 hover:shadow-sm',
            notEnabled: 'text-base-200'
        },
        table: 'dataTable min-w-full text-sm align-middle whitespace-nowrap',
        thead: {
            row: 'border-b border-base-100 ',
            cell: 'px-3 py-4 bg-base-200 font-semibold text-left border border-base-300'
        },
        tbody: {
            row: 'even:bg-base-50 shadow-sm',
            cell: 'p-3 border border-base-300'
        },
        tfoot: {
            row: 'even:bg-base-50',
            cell: 'p-3 text-left border border-base-300'
        },
    });

    DataTable.ext.renderer.pagingButton.tailwindcss = function (settings, buttonType, content, active, disabled) {
        var classes = settings.oClasses.paging;
        var btnClasses = [classes.button];

        btnClasses.push(active ? classes.active : classes.notActive);
        btnClasses.push(disabled ? classes.notEnabled : classes.enabled);

        var a = $('<a>', {
            'href': disabled ? null : '#',
            'class': btnClasses.join(' ')
        })
            .html(content);

        return {
            display: a,
            clicker: a
        };
    };

    DataTable.ext.renderer.pagingContainer.tailwindcss = function (settings, buttonEls) {
        var classes = settings.oClasses.paging;

        buttonEls[0].addClass(classes.first);
        buttonEls[buttonEls.length - 1].addClass(classes.last);

        return $('<ul/>').addClass('pagination').append(buttonEls);
    };

    DataTable.ext.renderer.layout.tailwindcss = function (settings, container, items) {
        var row = $('<div/>', {
            "class": items.full ?
                'grid grid-cols-1 gap-4 mb-4' :
                'grid grid-cols-2 gap-4 mb-4'
        })
            .appendTo(container);

        $.each(items, function (key, val) {
            var klass;

            // Apply start / end (left / right when ltr) margins
            if (val.table) {
                klass = 'col-span-2';
            }
            else if (key === 'start') {
                klass = 'justify-self-start';
            }
            else if (key === 'end') {
                klass = 'col-start-2 justify-self-end';
            }
            else {
                klass = 'col-span-2 justify-self-center';
            }

            $('<div/>', {
                id: val.id || null,
                "class": klass + ' ' + (val.className || '')
            })
                .append(val.contents)
                .appendTo(row);
        });
    };


    return DataTable;
}));