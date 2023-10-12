"use strict";

var jsPDF = window.jspdf.jsPDF;

(function ($) {
    $(document).ready(function () {
        $('body').on('click', '.stm_preview_certificate', function (e) {
            var courseId = '';
            var id = false;
            var certificate_dc = false;

            if (typeof $(this).attr('data-id') !== 'undefined') {
                id = $(this).attr('data-id');
            }

            if( typeof $(this).attr('data-certificate-dc') !== 'undefined') {
                certificate_dc = $(this).attr('data-certificate-dc');
            }

            if (typeof $(this).attr('data-course-id') !== 'undefined') {
                courseId = $(this).attr('data-course-id');
            }

            e.preventDefault();

            if(certificate_dc == '1') {
                $('.stm_lms_confirm_certificate').addClass('active');
            } else if (id || courseId) {
                getCertificate(id, courseId, certificate_dc);
            }
        });
    });

    $('.certificado_button').on('click', function () {
        var $el = $(this);
        var course_id = $el.data('courseId');

        var url = stm_lms_ajaxurl + '?action=stm_get_area_del_curso_child&course_id=' + course_id;


        $.ajax({
            url: url,
            method: 'GET',
            success: function(data) {
                jQuery('#area_del_curso').val(data);
            }		 
        });

        //console.log($el.data('coursetitle'));
    });

    $('#confirm_certificate_form_btn').on('click', function (e) {
        e.preventDefault();
        $(this).addClass('loading');
        var courseId = '';
        var id = false;
        var certificate_dc = false;

        if (typeof $('.certificado_button').attr('data-id') !== 'undefined') {
            id = $('.certificado_button').attr('data-id');
        }

        if( typeof $('.certificado_button').attr('data-certificate-dc') !== 'undefined') {
            certificate_dc = $('.certificado_button').attr('data-certificate-dc');
        }

        if (typeof $('.certificado_button').attr('data-course-id') !== 'undefined') {
            courseId = $('.certificado_button').attr('data-course-id');
        }
        var nombre = $('#nombre').val()
        var apellidos = $('#apellidos').val()
        var curp = $('#curp').val()
        var puesta = $('#puesta').val()
        var rfc = $('#rfc').val()
        var ocupacion = $('#ocupacion').val()
        var nombre_razon = $('#nombre_razon').val()

        var url = stm_lms_ajaxurl + '?action=stm_get_certificate_child&nonce=' + stm_lms_nonces['stm_get_certificate'] + '&post_id=' + id + '&course_id=' + courseId + '&certificate_dc=' + certificate_dc + '&curp=' + curp + '&puesta=' + puesta + '&rfc=' + rfc + '&ocupacion=' + ocupacion + '&nombre_razon=' +nombre_razon + '&nombre=' +nombre + '&apellidos=' +apellidos;
        $.ajax({
            url: url,
            method: 'get',
            success: function success(data) {
                if (typeof data.data !== 'undefined') {
                    $('#confirm_certificate_form_btn').removeClass('loading');
                    generateCertificate(data.data);
                }
            }
        });
    });

    function getCertificate(id) {
        var courseId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';
        var certificate_dc = arguments.length > 1 && arguments[2] !== undefined ? arguments[2] : '';
        var url = stm_lms_ajaxurl + '?action=stm_get_certificate_child&nonce=' + stm_lms_nonces['stm_get_certificate'] + '&post_id=' + id + '&course_id=' + courseId + '&certificate_dc=' + certificate_dc;
        $.ajax({
            url: url,
            method: 'get',
            success: function success(data) {
                if (typeof data.data !== 'undefined') {
                    generateCertificate(data.data);
                }
            }
        });
    }

    function generateCertificate(data) {
        var orientation = data.orientation;
        var doc = new jsPDF({
            orientation: orientation,
            unit: 'px',
            format: [600, 900]
        });
        doc.addFileToVFS('OpenSans-Regular-normal.ttf', openSansRegular);
        doc.addFont('OpenSans-Regular-normal.ttf', 'OpenSans', 'normal');
        doc.addFileToVFS('OpenSans-Bold-normal.ttf', openSansBold);
        doc.addFont('OpenSans-Bold-normal.ttf', 'OpenSans', 'bold');
        doc.addFileToVFS('OpenSans-BoldItalic-normal.ttf', openSansBoldItalic);
        doc.addFont('OpenSans-BoldItalic-normal.ttf', 'OpenSans', 'bolditalic');
        doc.addFileToVFS('OpenSans-Italic-italic.ttf', openSansItalic);
        doc.addFont('OpenSans-Italic-italic.ttf', 'OpenSans', 'italic');
        doc.addFileToVFS('Montserrat-normal.ttf', montserratRegular);
        doc.addFont('Montserrat-normal.ttf', 'Montserrat', 'normal');
        doc.addFileToVFS('Montserrat-bold.ttf', montserratBold);
        doc.addFont('Montserrat-bold.ttf', 'Montserrat', 'bold');
        doc.addFileToVFS('Montserrat-italic.ttf', montserratItalic);
        doc.addFont('Montserrat-italic.ttf', 'Montserrat', 'italic');
        doc.addFileToVFS('Montserrat-bolditalic.ttf', montserratBoldItalic);
        doc.addFont('Montserrat-bolditalic.ttf', 'Montserrat', 'bolditalic');
        doc.addFileToVFS('Merriweather-normal.ttf', merriweatherRegular);
        doc.addFont('Merriweather-normal.ttf', 'Merriweather', 'normal');
        doc.addFileToVFS('Merriweather-bold.ttf', merriweatherBold);
        doc.addFont('Merriweather-bold.ttf', 'Merriweather', 'bold');
        doc.addFileToVFS('Merriweather-italic.ttf', merriweatherItalic);
        doc.addFont('Merriweather-italic.ttf', 'Merriweather', 'italic');
        doc.addFileToVFS('Merriweather-bolditalic.ttf', merriweatherBoldItalic);
        doc.addFont('Merriweather-bolditalic.ttf', 'Merriweather', 'bolditalic');
        doc.addFileToVFS('Katibeh-normal.ttf', katibeh);
        doc.addFont('Katibeh-normal.ttf', 'Katibeh', 'normal');
        doc.addFont('Katibeh-normal.ttf', 'Katibeh', 'bold');
        doc.addFont('Katibeh-normal.ttf', 'Katibeh', 'italic');
        doc.addFont('Katibeh-normal.ttf', 'Katibeh', 'bolditalic');
        doc.addFileToVFS('Amiri-normal.ttf', Amiri);
        doc.addFont('Amiri-normal.ttf', 'Amiri', 'normal');
        doc.addFont('Amiri-normal.ttf', 'Amiri', 'bold');
        doc.addFont('Amiri-normal.ttf', 'Amiri', 'italic');
        doc.addFont('Amiri-normal.ttf', 'Amiri', 'bolditalic');
        doc.addFileToVFS('Oswald-normal.ttf', oswald);
        doc.addFont('Oswald-normal.ttf', 'Oswald', 'normal');
        doc.addFont('Oswald-normal.ttf', 'Oswald', 'italic');
        doc.addFileToVFS('Oswald-bold.ttf', oswaldBold);
        doc.addFont('Oswald-bold.ttf', 'Oswald', 'bold');
        doc.addFont('Oswald-bold.ttf', 'Oswald', 'bolditalic');
        var background = data.image;

        if (background) {
            var imageSize = data.image_size;
            var bgWidth = imageSize[0];
            var bgHeight = imageSize[1];
            doc.addImage(background, "JPEG", 0, 0, bgWidth, bgHeight);
        }

        data.fields.forEach(function (field) {
            if (field.content) {
                if (field.type === 'image') {
                    if (typeof field.image_data !== 'undefined' && field.image_data) {
                        doc.addImage(field.image_data, "JPEG", parseInt(field.x), parseInt(field.y), parseInt(field.w), parseInt(field.h));
                    }
                } else {
                    var textColor = hexToRGB(field.styles.color.hex);
                    var r = textColor.r;
                    var g = textColor.g;
                    var b = textColor.b;
                    var fontSize = parseInt(field.styles.fontSize.replace('px', ''));
                    var fieldWidth = parseInt(field.w) - 12;
                    var x = parseInt(field.x);
                    var y = parseInt(field.y) + fontSize * 0.8;

                    if (field.styles.textAlign === 'right') {
                        x = x + fieldWidth;
                    } else if (field.styles.textAlign === 'center') {
                        x = x + 6 + fieldWidth / 2;
                    } else {
                        x = x + 6;
                    }

                    var options = {
                        maxWidth: fieldWidth,
                        align: field.styles.textAlign,
                        lineHeightFactor: 1.25
                    };
                    var fontStyle = 'normal';

                    if (field.styles.fontWeight && field.styles.fontWeight !== "false") {
                        fontStyle = 'bold';

                        if (field.styles.fontStyle && field.styles.fontStyle !== "false") {
                            fontStyle = 'bolditalic';
                        }
                    } else if (field.styles.fontStyle && field.styles.fontStyle !== "false") {
                        fontStyle = 'italic';
                    }

                    doc.setTextColor(field.styles.color.hex);
                    doc.setFontSize(fontSize);
                    doc.setFont(field.styles.fontFamily, fontStyle);
                    doc.text(field.content, x, y, options);
                }
            }
        });
        $('.stm_lms_confirm_certificate').removeClass('active');
        window.open(doc.output('bloburl')); // doc.autoPrint();
        // doc.output('save', 'Certificate.pdf');
    }

    function hexToRGB(h) {
        var r = 0,
            g = 0,
            b = 0; // 3 digits

        if (h.length == 4) {
            r = "0x" + h[1] + h[1];
            g = "0x" + h[2] + h[2];
            b = "0x" + h[3] + h[3]; // 6 digits
        } else if (h.length == 7) {
            r = "0x" + h[1] + h[2];
            g = "0x" + h[3] + h[4];
            b = "0x" + h[5] + h[6];
        }

        return {
            r: r,
            g: g,
            b: b
        };
    }
})(jQuery);