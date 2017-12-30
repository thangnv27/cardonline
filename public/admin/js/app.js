var browseFileURL = "";
function openFileDialog(field) {
    var width = $(window).width() - $(window).width() * 0.05;
    var height = $(window).height() - $(window).height() * 0.05;
    $.colorbox({
        iframe: true,
        href: elfinder_url,
        width: width,
        height: height,
        closeButton: false,
        onClosed: function() {
            if(browseFileURL !== ""){
                document.getElementById(field).value = browseFileURL;
            }
        }
    });
}
function elFinderBrowser(field_name, url, type, win) {
    tinymce.activeEditor.windowManager.open({
        file: elfinder_url, // use an absolute path!
        title: 'Media',
        width: $(window).width(),
        height: $(window).height() - 50,
        resizable: true,
        maximizable: true
    }, {
        setUrl: function(url) {
            win.document.getElementById(field_name).value = url;
        }
    });
    return false;
}
tinymce.init({
    height: 300,
    body_id: "my_id",
    selector: ".editor",
    file_browser_callback: elFinderBrowser,
    convert_urls: 0,
    remove_script_host: 0,
    plugins: [
    "advlist autolink link unlink image lists charmap print preview hr anchor pagebreak spellchecker",
    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
    "save autosave table layer contextmenu directionality emoticons template paste textcolor inlinepopups"
    ],
    toolbar: "browse insertfile undo redo | styleselect fontsizeselect | bold italic underline strikethrough | \
            alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote charmap | \
            link unlink image media | forecolor backcolor emoticons | pagebreak preview fullscreen",
    fontsize_formats: "8px 9px 10px 11px 12px 13px 14px 15px 16px 17px 18px 19px 20px 21px 22px 23px 24px 25px 26px 27px 28px 29px 30px 32px 36px 48px",
    setup: function(ed) {
        // Add a custom button
        ed.addButton('browse', {
            title: 'Insert File',
            //image : 'img/browse.gif',
            onclick: function() {
                var width = $(window).width() - $(window).width() * 0.05;
                var height = $(window).height() - $(window).height() * 0.05;
                $.colorbox({
                    iframe: true,
                    href: elfinder_url,
                    width: width,
                    height: height,
                    closeButton: false,
                    onClosed: function() {
                        // Add you own code to execute something on click
                        ed.focus();
                        if (browseFileURL != "") {
                            var img = "";
                            if (typeof browseFileURL === 'string') {
                                img += '<img src="' + browseFileURL + '" /><br />';
                            } else {
                                for (i = 0; i < browseFileURL.length; i++) {
                                    img += '<img src="' + browseFileURL[i] + '" /><br />';
                                }
                            }
                            ed.selection.setContent(img);
                        }
                    }
                });
            }
        });
    },
    style_formats: [
    {
        title: 'Heading 1',
        block: 'h1'
    }, {
        title: 'Heading 2',
        block: 'h2'
    }, {
        title: 'Heading 3',
        block: 'h3'
    }, {
        title: 'Heading 4',
        block: 'h4'
    }, {
        title: 'Bold text',
        inline: 'b'
    }, {
        title: 'Red text',
        inline: 'span',
        styles: {
            color: '#ff0000'
        }
    }, {
        title: 'Example 1',
        inline: 'span',
        classes: 'example1'
    }, {
        title: 'Table styles'
    }, {
        title: 'Table row 1',
        selector: 'tr',
        classes: 'tablerow1'
    }
    ]
});
// add table row
tisa_table = {
    row_add: function() {
        if ($('#tr_add').length) {
            var $tr_id = $('#tbl_product_img').find("tr").length - 3;
            $('#tr_add_btn').on('click', function(e) {
                ++$tr_id;
                e.preventDefault();
                var $cloned_tr = $('#tr_clone').clone(true);
                $cloned_tr.attr({
                    id: 'prod_img_row_' + $tr_id
                }).removeAttr('style').find('input').attr({
                    id: "prod_img_" + $tr_id,
                    name: "images[" + $tr_id + "]"
                });
                $cloned_tr.find('button').attr({
                    onclick: "openFileDialog('" + "prod_img_" + $tr_id + "')"
                });
                $cloned_tr.insertBefore($('#tr_add'));
            });
        }
    },
    row_remove: function() {
        if ($('.tr_remove').length) {
            $('.tr_remove').on('click', function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
            });
        }
    }
};

function get_short_content(str, length){
    var s = "";
    if(str.length >= length){
        var txt = str.split(" ");
        for(i =0; i< txt.length - 1; i++){
            if (i == txt.length - 2) {
                s += txt[i];
            } else {
                s += txt[i] + " ";
            }
        }
        s += "...";
    } else {
        s = str;
    }
    return s;
}

function checkLoggedIn() {
    if ($("#colorbox").is(':hidden')) {
        $.ajax({
            url: dashboard_url + "/user/checkuserloggedin", 
            type: "POST", 
            dataType: "json", 
            cache: false,
            data: {
                check: 1
            },
            success: function(response, textStatus, XMLHttpRequest) {
                if (response && response.status == 'error') {
                    $.colorbox({
                        iframe: true,
                        href: login_url,
                        width: 490,
                        height: 520,
                        closeButton: false,
                        overlayClose: false,
                        escKey: false,
                        onClosed: function() {
                        }
                    });
                }
            },
            error: function(MLHttpRequest, textStatus, errorThrown) {
            //alert(errorThrown);
            }
        });
    }
}

setInterval("checkLoggedIn()", 5 * 1000);
$(function() {
    // add table row
    tisa_table.row_add();
    // remove table row
    tisa_table.row_remove();
    // tagging
    if ($('#tags').length) {
        $("#tags").select2({
            tags: post_tags
        });
    }

    $(".cbswitch").switchButton({
        width: 76,
        height: 25,
        button_width: 38,
        on_label: 'YES',
        off_label: 'NO'
    });
    $('.datepicker').datepicker();
    $("#checkall").click(function() {
        if ($(this).is(':checked')) {
            $(this).attr('checked', 'checked');
            $("input[name='item[]']").attr('checked', 'checked');
        } else {
            $(this).removeAttr('checked');
            $("input[name='item[]']").removeAttr('checked');
        }
    });
    // Hits Statistical Chart 

    if ($("#visits-log").length > 0) {
        visit_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'visits-log',
                type: 'line', // line, spline, area, areaspline, column, bar, scatter
                backgroundColor: '#FFFFFF',
                height: '300'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Hits chart in the last ' + last_day + ' days',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Tahoma',
                    fontWeight: 'bold'
                }
            },
            xAxis: {
                type: 'datetime',
                labels: {
                    rotation: -45
                },
                categories: categories_datetime
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Number of visits and visitors',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Tahoma'
                    }
                }
            },
            legend: {
                rtl: true,
                itemStyle: {
                    fontSize: '11px',
                    fontFamily: 'Tahoma'
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true,
                style: {
                    fontSize: '12px',
                    fontFamily: 'Tahoma'
                },
                useHTML: true
            },
            series: [{
                name: 'Visitor',
                data: data_visitor
            },
            {
                name: 'Visit',
                data: data_visit
            }]
        });
    }
    if ($("#search-engine-log").length > 0) {
        search_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'search-engine-log',
                type: 'line', // line, spline, area, areaspline, column, bar, scatter
                backgroundColor: '#FFFFFF',
                height: '300'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Referrer search engine chart in the last ' + last_day + ' days',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Tahoma',
                    fontWeight: 'bold'
                }
            },
            xAxis: {
                type: 'datetime',
                labels: {
                    rotation: -45
                },
                categories: categories_datetime
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Number of referrers',
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Tahoma'
                    }
                }
            },
            legend: {
                rtl: true,
                itemStyle: {
                    fontSize: '11px',
                    fontFamily: 'Tahoma'
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true,
                style: {
                    fontSize: '12px',
                    fontFamily: 'Tahoma'
                },
                useHTML: true
            },
            series: search_series
        });
    }
    if ($("#browsers-log").length > 0) {
        // Radialize the colors
        Highcharts.getOptions().colors = jQuery.map(Highcharts.getOptions().colors, function(color) {
            return {
                radialGradient: {
                    cx: 0.5, 
                    cy: 0.3, 
                    r: 0.7
                },
                stops: [
                [0, color],
                [1, Highcharts.Color(color).brighten(-0.3).get('rgb')] // darken
                ]
            };
        });
        // Build the chart
        browser_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'browsers-log',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                backgroundColor: '#FFFFFF'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Graph of Browsers',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Tahoma',
                    fontWeight: 'bold'
                }
            },
            legend: {
                rtl: true,
                itemStyle: {
                    fontSize: '11px',
                    fontFamily: 'Tahoma'
                }
            },
            tooltip: {
                formatter: function() {
                    return this.point.name + ': <b>' + Highcharts.numberFormat(this.percentage, 1) + '%</b>';
                },
                percentageDecimals: 1,
                style: {
                    fontSize: '12px',
                    fontFamily: 'Tahoma'
                },
                useHTML: true
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        style: {
                            fontSize: '11px',
                            fontFamily: 'Tahoma'
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Browser share',
                data: browser_data
            }]
        });
    }
    if ($("#platform-log").length > 0) {
        // Build the chart
        platform_chart = new Highcharts.Chart({
            chart: {
                renderTo: 'platform-log',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                backgroundColor: '#FFFFFF'
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Browsers by Platform',
                style: {
                    fontSize: '12px',
                    fontFamily: 'Tahoma',
                    fontWeight: 'bold'
                }
            },
            legend: {
                rtl: true,
                itemStyle: {
                    fontSize: '11px',
                    fontFamily: 'Tahoma'
                }
            },
            tooltip: {
                formatter: function() {
                    return this.point.name + ': <b>' + Highcharts.numberFormat(this.percentage, 1) + '%</b>';
                },
                percentageDecimals: 1,
                style: {
                    fontSize: '12px',
                    fontFamily: 'Tahoma'
                },
                useHTML: true
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        style: {
                            fontSize: '11px',
                            fontFamily: 'Tahoma'
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Platform share',
                data: platform_data
            }]
        });
    }
    if (typeof browsers_chart != "undefined") {
        for (i = 0; i < browsers_chart.length; i++) {
            browsers_chart[i].chart = new Highcharts.Chart({
                chart: {
                    renderTo: 'browser-' + browsers_chart[i].tag + '-log',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    backgroundColor: '#FFFFFF'
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: browsers_chart[i].title,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Tahoma',
                        fontWeight: 'bold'
                    }
                },
                legend: {
                    rtl: true,
                    itemStyle: {
                        fontSize: '11px',
                        fontFamily: 'Tahoma'
                    }
                },
                tooltip: {
                    formatter: function() {
                        return this.point.name + ': <b>' + Highcharts.numberFormat(this.percentage, 1) + '%</b>';
                    },
                    percentageDecimals: 1,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Tahoma'
                    },
                    useHTML: true
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#000000',
                            style: {
                                fontSize: '11px',
                                fontFamily: 'Tahoma'
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Browser version share',
                    data: browsers_data[i]
                }]
            });
        }
    }
});