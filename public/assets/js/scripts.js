(function (window, undefined) {
    "use strict";

    /*
  NOTE:
  ------
  PLACE HERE YOUR OWN JAVASCRIPT CODE IF NEEDED
  WE WILL RELEASE FUTURE UPDATES SO IN ORDER TO NOT OVERWRITE YOUR JAVASCRIPT CODE PLEASE CONSIDER WRITING YOUR SCRIPT HERE.  */
})(window);

/**
 * Initialize Numeral Mask with Cleave
 * ! FOR CLEAVE !
 * ! Be sure to put this script in your blade/html file !
 * <script src="{{asset('app-assets/vendors/js/forms/cleave/cleave.min.js')}}"></script>
 *
 * @param {string} selector
 */
function initNumeralMask(selector) {
    var numeralMask = $(selector);
    if (numeralMask.length) {
        numeralMask.each(function () {
            new Cleave(this, {
                numeral: true,
                numeralThousandsGroupStyle: "thousand",
                numeralDecimalMark: ",",
                delimiter: ".",
            });
        });
    }
}

/**
 * Parse a locale string of format id-ID to float value
 * @param {string} value Numeral string | e.g. 1.000,50
 * @returns {number} Float value | e.g. 1000,50
 */
function parseLocaleToNum(value) {
    return parseFloat(
        value.replace(/\./g, "_").replace(",", ".").replace(/_/g, "") || 0,
    );
    // return parseFloat(value.replace(/\,/g, "") || 0);
}

/**
 * Parse a number to locale string with id-ID format
 * @param {number} value
 * @returns {string}
 */
function parseNumToLocale(value) {
    return (
        value.toLocaleString("id-ID", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }) || 0
    );
}

/**
 * Show a delete confirmation modal with Swal
 * ! FOR JQUERY REPEATER !
 * ! Be sure to put this script in your blade/html file !
 * <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}" />
 * <script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
 *
 * @param $row
 * @param deleteElement
 * @param {string} [title]
 * @param {string} [text]
 * @param {string} [icon]
 */
function confirmDelete(
    $row,
    deleteElement,
    title = "Hapus data ini?",
    text = "Data tidak bisa dikembalikan.",
    icon = "question",
) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: "Hapus",
        customClass: {
            confirmButton: "btn btn-danger mr-1",
            cancelButton: "btn btn-secondary",
        },
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            $row.slideUp(deleteElement);
        }
    });
}

/**
 * Show general confirmation modal with Swal
 * ! Be sure to put this script in your blade/html file !
 * <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/sweetalert2.min.css')}}" />
 * <script src="{{asset('app-assets/vendors/js/extensions/sweetalert2.all.min.js')}}"></script>
 *
 */
function confirmCallback(
    { title, text, footer, icon, confirmText, confirmClass },
    cb,
) {
    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        footer: footer ? footer : "",
        showCancelButton: true,
        confirmButtonText: confirmText,
        customClass: {
            confirmButton: "btn mr-1 " + confirmClass,
            cancelButton: "btn btn-secondary",
        },
        buttonsStyling: false,
    }).then(function (result) {
        if (result.value) {
            cb();
        }
    });
}

/** Initialize jquery select2
 * ! FOR JQUERY SELECT2
 * ! Be sure to put this script in your blade/html file !
 * <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
 *
 * @param {*} $component
 * @param {string} [placeholder=Pilih]
 * @param {*} routePath
 * @param {string} [type]
 * @param {Object} [opts]
 */
function initSelect2(
    $component,
    placeholder = "Pilih",
    routePath,
    type = "",
    opts,
) {
    if (routePath) {
        $component.select2({
            ...opts,
            placeholder: placeholder,
            ajax: {
                url: routePath,
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                    };
                },
                processResults: function (data) {
                    if (type === "productWarehouse") {
                        return {
                            results: data.map((item) => ({
                                id: item.id,
                                text: item.text,
                                qty: item.qty ? item.qty : 0,
                                price: item.price ? item.price : 0,
                                uom_id: item.uom_id ? item.uom_id : null,
                                uom_name: item.uom_name ? item.uom_name : "",
                            })),
                        };
                    } else {
                        return {
                            results: data.map((item) => ({
                                id: item.id,
                                text: item.text,
                            })),
                        };
                    }
                },
                cache: true,
            },
        });
    } else {
        $component.select2({
            ...opts,
            placeholder: placeholder,
        });
    }
}
