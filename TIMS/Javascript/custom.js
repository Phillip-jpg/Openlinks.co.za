/**
 * Resize function without multiple trigger
 * 
 * Usage:
 * $(window).smartresize(function(){  
 *     // code here
 * });
 */


//  adminformcheck()
//  companydataformcheck()




 

(function($,sr){
    // debouncing function from John Hann
    // http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
    var debounce = function (func, threshold, execAsap) {
      var timeout;

        return function debounced () {
            var obj = this, args = arguments;
            function delayed () {
                if (!execAsap)
                    func.apply(obj, args); 
                timeout = null; 
            }

            if (timeout)
                clearTimeout(timeout);
            else if (execAsap)
                func.apply(obj, args);

            timeout = setTimeout(delayed, threshold || 100); 
        };
    };

    // smartresize 
    jQuery.fn[sr] = function(fn){  return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr); };

})(jQuery,'smartresize');
/**
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var CURRENT_URL = window.location.href.split('#')[0].split('?')[0],
    $BODY = $('body'),
    $MENU_TOGGLE = $('#menu_toggle'),
    $SIDEBAR_MENU = $('#sidebar-menu'),
    $SIDEBAR_FOOTER = $('.sidebar-footer'),
    $LEFT_COL = $('.left_col'),
    $RIGHT_COL = $('.right_col'),
    $NAV_MENU = $('.nav_menu'),
    $FOOTER = $('footer');

	
	
// Sidebar
function init_sidebar() {
	console.log("testy");
// TODO: This is some kind of easy fix, maybe we can improve this
var setContentHeight = function () {
	// reset height
	$RIGHT_COL.css('min-height', $(window).height());

	var bodyHeight = $BODY.outerHeight(),
		footerHeight = $BODY.hasClass('footer_fixed') ? -10 : $FOOTER.height(),
		leftColHeight = $LEFT_COL.eq(1).height() + $SIDEBAR_FOOTER.height(),
		contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

	// normalize content
	contentHeight -= $NAV_MENU.height() + footerHeight;

	$RIGHT_COL.css('min-height', contentHeight);
};

  $SIDEBAR_MENU.find('a').on('click', function(ev) {
	  console.log('clicked - sidebar_menu');
        var $li = $(this).parent();

        if ($li.is('.active')) {
            $li.removeClass('active active-sm');
            $('ul:first', $li).slideUp(function() {
                setContentHeight();
            });
        } else {
            // prevent closing menu if we are on child menu
            if (!$li.parent().is('.child_menu')) {
                $SIDEBAR_MENU.find('li').removeClass('active active-sm');
                $SIDEBAR_MENU.find('li ul').slideUp();
            }else
            {
				if ( $BODY.is( ".nav-sm" ) )
				{
					$SIDEBAR_MENU.find( "li" ).removeClass( "active active-sm" );
					$SIDEBAR_MENU.find( "li ul" ).slideUp();
				}
			}
            $li.addClass('active');

            $('ul:first', $li).slideDown(function() {
                setContentHeight();
            });
        }
    });

// toggle small or large menu 
$MENU_TOGGLE.on('click', function() {
		console.log('clicked - menu toggle');
		
		if ($BODY.hasClass('nav-md')) {
			$SIDEBAR_MENU.find('li.active ul').hide();
			$SIDEBAR_MENU.find('li.active').addClass('active-sm').removeClass('active');
		} else {
			$SIDEBAR_MENU.find('li.active-sm ul').show();
			$SIDEBAR_MENU.find('li.active-sm').addClass('active').removeClass('active-sm');
		}

	$BODY.toggleClass('nav-md nav-sm');

	setContentHeight();

	$('.dataTable').each ( function () { $(this).dataTable().fnDraw(); });
});

	// check active menu
	$SIDEBAR_MENU.find('a[href="' + CURRENT_URL + '"]').parent('li').addClass('current-page');

	$SIDEBAR_MENU.find('a').filter(function () {
		return this.href == CURRENT_URL;
	}).parent('li').addClass('current-page').parents('ul').slideDown(function() {
		setContentHeight();
	}).parent().addClass('active');

	// recompute content when resizing
	$(window).smartresize(function(){  
		setContentHeight();
	});

	setContentHeight();

	// fixed sidebar
	if ($.fn.mCustomScrollbar) {
		$('.menu_fixed').mCustomScrollbar({
			autoHideScrollbar: true,
			theme: 'minimal',
			mouseWheel:{ preventDefault: true }
		});
	}
};
// /Sidebar

	var randNum = function() {
	  return (Math.floor(Math.random() * (1 + 40 - 20))) + 20;
	};


// Panel toolbox
$(document).ready(function() {
    $('.collapse-link').on('click', function() {
        var $BOX_PANEL = $(this).closest('.x_panel'),
            $ICON = $(this).find('i'),
            $BOX_CONTENT = $BOX_PANEL.find('.x_content');
        
        // fix for some div with hardcoded fix class
        if ($BOX_PANEL.attr('style')) {
            $BOX_CONTENT.slideToggle(200, function(){
                $BOX_PANEL.removeAttr('style');
            });
        } else {
            $BOX_CONTENT.slideToggle(200); 
            $BOX_PANEL.css('height', 'auto');  
        }

        $ICON.toggleClass('fa-chevron-up fa-chevron-down');
    });

    $('.close-link').click(function () {
        var $BOX_PANEL = $(this).closest('.x_panel');

        $BOX_PANEL.remove();
    });
});
// /Panel toolbox

// Tooltip
// $(document).ready(function() {
//     $('[data-toggle="tooltip"]').tooltip({
//         container: 'body'
//     });
// });########
// /Tooltip

// Progressbar
// if ($(".progress .progress-bar")[0]) {
//     $('.progress .progress-bar').progressbar();
// }##########
// /Progressbar




// iCheck
$(document).ready(function() {
    if ($("input.flat")[0]) {
        $(document).ready(function () {
            $('input.flat').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });
        });
    }
});
// /iCheck

// Table
$('table input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('table input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});

var checkState = '';

$('.bulk_action input').on('ifChecked', function () {
    checkState = '';
    $(this).parent().parent().parent().addClass('selected');
    countChecked();
});
$('.bulk_action input').on('ifUnchecked', function () {
    checkState = '';
    $(this).parent().parent().parent().removeClass('selected');
    countChecked();
});
$('.bulk_action input#check-all').on('ifChecked', function () {
    checkState = 'all';
    countChecked();
});
$('.bulk_action input#check-all').on('ifUnchecked', function () {
    checkState = 'none';
    countChecked();
});

function countChecked() {
    if (checkState === 'all') {
        $(".bulk_action input[name='table_records']").iCheck('check');
    }
    if (checkState === 'none') {
        $(".bulk_action input[name='table_records']").iCheck('uncheck');
    }

    var checkCount = $(".bulk_action input[name='table_records']:checked").length;

    if (checkCount) {
        $('.column-title').hide();
        $('.bulk-actions').show();
        $('.action-cnt').html(checkCount + ' Records Selected');
    } else {
        $('.column-title').show();
        $('.bulk-actions').hide();
    }
}



// Accordion
$(document).ready(function() {
    $(".expand").on("click", function () {
        $(this).next().slideToggle(200);
        $expand = $(this).find(">:first-child");

        if ($expand.text() == "+") {
            $expand.text("-");
        } else {
            $expand.text("+");
        }
    });
});

// NProgress
if (typeof NProgress != 'undefined') {
    $(document).ready(function () {
        NProgress.start();
    });

    $(window).load(function () {
        NProgress.done();
    });
}

	
	  //hover and retain popover when on popover content
        // var originalLeave = $.fn.popover.Constructor.prototype.leave;
        // $.fn.popover.Constructor.prototype.leave = function(obj) {
        //   var self = obj instanceof this.constructor ?
        //     obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type);
        //   var container, timeout;

        //   originalLeave.call(this, obj);

        //   if (obj.currentTarget) {
        //     container = $(obj.currentTarget).siblings('.popover');
        //     timeout = self.timeout;
        //     container.one('mouseenter', function() {
        //       //We entered the actual popover – call off the dogs
        //       clearTimeout(timeout);
        //       //Let's monitor popover content instead
        //       container.one('mouseleave', function() {
        //         $.fn.popover.Constructor.prototype.leave.call(self, self);
        //       });
        //     });
        //   }
        // };

        // $('body').popover({
        //   selector: '[data-popover]',
        //   trigger: 'click hover',
        //   delay: {
        //     show: 50,
        //     hide: 400
        //   }##########
        // });


	function gd(year, month, day) {
		return new Date(year, month - 1, day).getTime();
	}
	  
	

	
		
	/* STARRR */
			
	function init_starrr() {
		
		if( typeof (starrr) === 'undefined'){ return; }
		console.log('init_starrr');
		
		$(".stars").starrr();

		$('.stars-existing').starrr({
		  rating: 4
		});

		$('.stars').on('starrr:change', function (e, value) {
		  $('.stars-count').html(value);
		});

		$('.stars-existing').on('starrr:change', function (e, value) {
		  $('.stars-count-existing').html(value);
		});
		
	  };
			
	   
	   
	function init_chart_doughnut(){
				
		if( typeof (Chart) === 'undefined'){ return; }
		
		console.log('init_chart_doughnut');
	 
		if ($('.canvasDoughnut').length){
			
		var chart_doughnut_settings = {
				type: 'doughnut',
				tooltipFillColor: "rgba(51, 51, 51, 0.55)",
				data: {
					labels: [
						"Symbian",
						"Blackberry",
						"Other",
						"Android",
						"IOS"
					],
					datasets: [{
						data: [15, 20, 30, 10, 30],
						backgroundColor: [
							"#BDC3C7",
							"#9B59B6",
							"#E74C3C",
							"#0dc0ff",
							"#3498DB"
						],
						hoverBackgroundColor: [
							"#CFD4D8",
							"#B370CF",
							"#E95E4F",
							"#36CAAB",
							"#49A9EA"
						]
					}]
				},
				options: { 
					legend: false, 
					responsive: false 
				}
			}
		
			$('.canvasDoughnut').each(function(){
				
				var chart_element = $(this);
				var chart_doughnut = new Chart( chart_element, chart_doughnut_settings);
				
			});			
		
		}  
	   
	}
	   

	   	   
	/* SPARKLINES */
			
		function init_sparklines() {
			
			if(typeof (jQuery.fn.sparkline) === 'undefined'){ return; }
			console.log('init_sparklines'); 
			
			
			$(".sparkline_one").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 4, 5, 6, 3, 5, 4, 5, 4, 5, 4, 3, 4, 5, 6, 7, 5, 4, 3, 5, 6], {
				type: 'bar',
				height: '125',
				barWidth: 13,
				colorMap: {
					'7': '#a1a1a1'
				},
				barSpacing: 2,
				barColor: '#0dc0ff'
			});
			
			
			$(".sparkline_two").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 7, 5, 4, 3, 5, 6], {
				type: 'bar',
				height: '40',
				barWidth: 9,
				colorMap: {
					'7': '#a1a1a1'	
				},
				barSpacing: 2,
				barColor: '#0dc0ff'
			});
			
			
			$(".sparkline_three").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 7, 5, 4, 3, 5, 6], {
				type: 'line',
				width: '200',
				height: '40',
				lineColor: '#0dc0ff',
				fillColor: 'rgba(223, 223, 223, 0.57)',
				lineWidth: 2,
				spotColor: '#0dc0ff',
				minSpotColor: '#0dc0ff'
			});
			
			
			$(".sparkline11").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 6, 2, 4, 3, 4, 5, 4, 5, 4, 3], {
				type: 'bar',
				height: '40',
				barWidth: 8,
				colorMap: {
					'7': '#a1a1a1'
				},
				barSpacing: 2,
				barColor: '#0dc0ff'
			});
			
			
			$(".sparkline22").sparkline([2, 4, 3, 4, 7, 5, 4, 3, 5, 6, 2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 6], {
				type: 'line',
				height: '40',
				width: '200',
				lineColor: '#0dc0ff',
				fillColor: '#ffffff',
				lineWidth: 3,
				spotColor: '#34495E',
				minSpotColor: '#34495E'
			});
	
	
			$(".sparkline_bar").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 4, 5, 6, 3, 5], {
				type: 'bar',
				colorMap: {
					'7': '#a1a1a1'
				},
				barColor: '#0dc0ff'
			});
			
			
			$(".sparkline_area").sparkline([5, 6, 7, 9, 9, 5, 3, 2, 2, 4, 6, 7], {
				type: 'line',
				lineColor: '#0dc0ff',
				fillColor: '#0dc0ff',
				spotColor: '#4578a0',
				minSpotColor: '#728fb2',
				maxSpotColor: '#6d93c4',
				highlightSpotColor: '#ef5179',
				highlightLineColor: '#8ba8bf',
				spotRadius: 2.5,
				width: 85
			});
			
			
			$(".sparkline_line").sparkline([2, 4, 3, 4, 5, 4, 5, 4, 3, 4, 5, 6, 4, 5, 6, 3, 5], {
				type: 'line',
				lineColor: '#0dc0ff',
				fillColor: '#ffffff',
				width: 85,
				spotColor: '#34495E',
				minSpotColor: '#34495E'
			});
			
			
			$(".sparkline_pie").sparkline([1, 1, 2, 1], {
				type: 'pie',
				sliceColors: ['#0dc0ff', '#ccc', '#75BCDD', '#D66DE2']
			});
			
			
			$(".sparkline_discreet").sparkline([4, 6, 7, 7, 4, 3, 2, 1, 4, 4, 2, 4, 3, 7, 8, 9, 7, 6, 4, 3], {
				type: 'discrete',
				barWidth: 3,
				lineColor: '#0dc0ff',
				width: '85',
			});

			
		};   
	   
	   
	   /* AUTOCOMPLETE */
			
		function init_autocomplete() {
			
			if( typeof (autocomplete) === 'undefined'){ return; }
			console.log('init_autocomplete');
			
			var countries = { AD:"Andorra",A2:"Andorra Test",AE:"United Arab Emirates",AF:"Afghanistan",AG:"Antigua and Barbuda",AI:"Anguilla",AL:"Albania",AM:"Armenia",AN:"Netherlands Antilles",AO:"Angola",AQ:"Antarctica",AR:"Argentina",AS:"American Samoa",AT:"Austria",AU:"Australia",AW:"Aruba",AX:"Åland Islands",AZ:"Azerbaijan",BA:"Bosnia and Herzegovina",BB:"Barbados",BD:"Bangladesh",BE:"Belgium",BF:"Burkina Faso",BG:"Bulgaria",BH:"Bahrain",BI:"Burundi",BJ:"Benin",BL:"Saint Barthélemy",BM:"Bermuda",BN:"Brunei",BO:"Bolivia",BQ:"British Antarctic Territory",BR:"Brazil",BS:"Bahamas",BT:"Bhutan",BV:"Bouvet Island",BW:"Botswana",BY:"Belarus",BZ:"Belize",CA:"Canada",CC:"Cocos [Keeling] Islands",CD:"Congo - Kinshasa",CF:"Central African Republic",CG:"Congo - Brazzaville",CH:"Switzerland",CI:"Côte d’Ivoire",CK:"Cook Islands",CL:"Chile",CM:"Cameroon",CN:"China",CO:"Colombia",CR:"Costa Rica",CS:"Serbia and Montenegro",CT:"Canton and Enderbury Islands",CU:"Cuba",CV:"Cape Verde",CX:"Christmas Island",CY:"Cyprus",CZ:"Czech Republic",DD:"East Germany",DE:"Germany",DJ:"Djibouti",DK:"Denmark",DM:"Dominica",DO:"Dominican Republic",DZ:"Algeria",EC:"Ecuador",EE:"Estonia",EG:"Egypt",EH:"Western Sahara",ER:"Eritrea",ES:"Spain",ET:"Ethiopia",FI:"Finland",FJ:"Fiji",FK:"Falkland Islands",FM:"Micronesia",FO:"Faroe Islands",FQ:"French Southern and Antarctic Territories",FR:"France",FX:"Metropolitan France",GA:"Gabon",GB:"United Kingdom",GD:"Grenada",GE:"Georgia",GF:"French Guiana",GG:"Guernsey",GH:"Ghana",GI:"Gibraltar",GL:"Greenland",GM:"Gambia",GN:"Guinea",GP:"Guadeloupe",GQ:"Equatorial Guinea",GR:"Greece",GS:"South Georgia and the South Sandwich Islands",GT:"Guatemala",GU:"Guam",GW:"Guinea-Bissau",GY:"Guyana",HK:"Hong Kong SAR China",HM:"Heard Island and McDonald Islands",HN:"Honduras",HR:"Croatia",HT:"Haiti",HU:"Hungary",ID:"Indonesia",IE:"Ireland",IL:"Israel",IM:"Isle of Man",IN:"India",IO:"British Indian Ocean Territory",IQ:"Iraq",IR:"Iran",IS:"Iceland",IT:"Italy",JE:"Jersey",JM:"Jamaica",JO:"Jordan",JP:"Japan",JT:"Johnston Island",KE:"Kenya",KG:"Kyrgyzstan",KH:"Cambodia",KI:"Kiribati",KM:"Comoros",KN:"Saint Kitts and Nevis",KP:"North Korea",KR:"South Korea",KW:"Kuwait",KY:"Cayman Islands",KZ:"Kazakhstan",LA:"Laos",LB:"Lebanon",LC:"Saint Lucia",LI:"Liechtenstein",LK:"Sri Lanka",LR:"Liberia",LS:"Lesotho",LT:"Lithuania",LU:"Luxembourg",LV:"Latvia",LY:"Libya",MA:"Morocco",MC:"Monaco",MD:"Moldova",ME:"Montenegro",MF:"Saint Martin",MG:"Madagascar",MH:"Marshall Islands",MI:"Midway Islands",MK:"Macedonia",ML:"Mali",MM:"Myanmar [Burma]",MN:"Mongolia",MO:"Macau SAR China",MP:"Northern Mariana Islands",MQ:"Martinique",MR:"Mauritania",MS:"Montserrat",MT:"Malta",MU:"Mauritius",MV:"Maldives",MW:"Malawi",MX:"Mexico",MY:"Malaysia",MZ:"Mozambique",NA:"Namibia",NC:"New Caledonia",NE:"Niger",NF:"Norfolk Island",NG:"Nigeria",NI:"Nicaragua",NL:"Netherlands",NO:"Norway",NP:"Nepal",NQ:"Dronning Maud Land",NR:"Nauru",NT:"Neutral Zone",NU:"Niue",NZ:"New Zealand",OM:"Oman",PA:"Panama",PC:"Pacific Islands Trust Territory",PE:"Peru",PF:"French Polynesia",PG:"Papua New Guinea",PH:"Philippines",PK:"Pakistan",PL:"Poland",PM:"Saint Pierre and Miquelon",PN:"Pitcairn Islands",PR:"Puerto Rico",PS:"Palestinian Territories",PT:"Portugal",PU:"U.S. Miscellaneous Pacific Islands",PW:"Palau",PY:"Paraguay",PZ:"Panama Canal Zone",QA:"Qatar",RE:"Réunion",RO:"Romania",RS:"Serbia",RU:"Russia",RW:"Rwanda",SA:"Saudi Arabia",SB:"Solomon Islands",SC:"Seychelles",SD:"Sudan",SE:"Sweden",SG:"Singapore",SH:"Saint Helena",SI:"Slovenia",SJ:"Svalbard and Jan Mayen",SK:"Slovakia",SL:"Sierra Leone",SM:"San Marino",SN:"Senegal",SO:"Somalia",SR:"Suriname",ST:"São Tomé and Príncipe",SU:"Union of Soviet Socialist Republics",SV:"El Salvador",SY:"Syria",SZ:"Swaziland",TC:"Turks and Caicos Islands",TD:"Chad",TF:"French Southern Territories",TG:"Togo",TH:"Thailand",TJ:"Tajikistan",TK:"Tokelau",TL:"Timor-Leste",TM:"Turkmenistan",TN:"Tunisia",TO:"Tonga",TR:"Turkey",TT:"Trinidad and Tobago",TV:"Tuvalu",TW:"Taiwan",TZ:"Tanzania",UA:"Ukraine",UG:"Uganda",UM:"U.S. Minor Outlying Islands",US:"United States",UY:"Uruguay",UZ:"Uzbekistan",VA:"Vatican City",VC:"Saint Vincent and the Grenadines",VD:"North Vietnam",VE:"Venezuela",VG:"British Virgin Islands",VI:"U.S. Virgin Islands",VN:"Vietnam",VU:"Vanuatu",WF:"Wallis and Futuna",WK:"Wake Island",WS:"Samoa",YD:"People's Democratic Republic of Yemen",YE:"Yemen",YT:"Mayotte",ZA:"South Africa",ZM:"Zambia",ZW:"Zimbabwe",ZZ:"Unknown or Invalid Region" };

			var countriesArray = $.map(countries, function(value, key) {
			  return {
				value: value,
				data: key
			  };
			});

			// initialize autocomplete with custom appendTo
			$('#autocomplete-custom-append').autocomplete({
			  lookup: countriesArray
			});
			
		};
	   

	   
	   /* PARSLEY */
			
		function init_parsley() {
			
			if( typeof (parsley) === 'undefined'){ return; }
			console.log('init_parsley');
			
			$/*.listen*/('parsley:field:validate', function() {
			  validateFront();
			});
			$('#demo-form .btn').on('click', function() {
			  $('#demo-form').parsley().validate();
			  validateFront();
			});
			var validateFront = function() {
			  if (true === $('#demo-form').parsley().isValid()) {
				$('.bs-callout-info').removeClass('hidden');
				$('.bs-callout-warning').addClass('hidden');
			  } else {
				$('.bs-callout-info').addClass('hidden');
				$('.bs-callout-warning').removeClass('hidden');
			  }
			};
		  
			$/*.listen*/('parsley:field:validate', function() {
			  validateFront();
			});
			$('#demo-form2 .btn').on('click', function() {
			  $('#demo-form2').parsley().validate();
			  validateFront();
			});
			var validateFront = function() {
			  if (true === $('#demo-form2').parsley().isValid()) {
				$('.bs-callout-info').removeClass('hidden');
				$('.bs-callout-warning').addClass('hidden');
			  } else {
				$('.bs-callout-info').addClass('hidden');
				$('.bs-callout-warning').removeClass('hidden');
			  }
			};
			
			  try {
				hljs.initHighlightingOnLoad();
			  } catch (err) {}
			
		};
	   
		
		  /* INPUTS */
		  
		  function onAddTag(tag) {
			alert("Added a tag: " + tag);
		}
		
		function onRemoveTag(tag) {
			alert("Removed a tag: " + tag);
		}
		
		function onChangeTag(input, tag) {
			alert("Changed a tag: " + tag);
		}
		
		//tags input
		function init_TagsInput() {
		
			if (typeof $.fn.tagsInput !== 'undefined') {

				$('#tags_1').tagsInput({
					width: 'auto'
				});
		
			}
		
		};
	   

	   


	  
	 
		/* INPUT MASK */
			
		function init_InputMask() {
			
			if( typeof ($.fn.inputmask) === 'undefined'){ return; }
			console.log('init_InputMask');
			
				$(":input").inputmask();
				
		};
	  

	   
	   

	   
	   
	   /* DATERANGEPICKER */
	   
		function init_daterangepicker() {

			if( typeof ($.fn.daterangepicker) === 'undefined'){ return; }
			console.log('init_daterangepicker');
		
			var cb = function(start, end, label) {
			  console.log(start.toISOString(), end.toISOString(), label);
			  $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
			};

			var optionSet1 = {
			  startDate: moment().subtract(29, 'days'),
			  endDate: moment(),
			  minDate: '01/01/2012',
			  maxDate: '12/31/2015',
			  dateLimit: {
				days: 60
			  },
			  showDropdowns: true,
			  showWeekNumbers: true,
			  timePicker: false,
			  timePickerIncrement: 1,
			  timePicker12Hour: true,
			  ranges: {
				'Today': [moment(), moment()],
				'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
				'Last 7 Days': [moment().subtract(6, 'days'), moment()],
				'Last 30 Days': [moment().subtract(29, 'days'), moment()],
				'This Month': [moment().startOf('month'), moment().endOf('month')],
				'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
			  },
			  opens: 'left',
			  buttonClasses: ['btn btn-default'],
			  applyClass: 'btn-small btn-primary',
			  cancelClass: 'btn-small',
			  format: 'MM/DD/YYYY',
			  separator: ' to ',
			  locale: {
				applyLabel: 'Submit',
				cancelLabel: 'Clear',
				fromLabel: 'From',
				toLabel: 'To',
				customRangeLabel: 'Custom',
				daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				firstDay: 1
			  }
			};
			
			$('#reportrange span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
			$('#reportrange').daterangepicker(optionSet1, cb);
			$('#reportrange').on('show.daterangepicker', function() {
			  console.log("show event fired");
			});
			$('#reportrange').on('hide.daterangepicker', function() {
			  console.log("hide event fired");
			});
			$('#reportrange').on('apply.daterangepicker', function(ev, picker) {
			  console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
			});
			$('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
			  console.log("cancel event fired");
			});
			$('#options1').click(function() {
			  $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
			});
			$('#options2').click(function() {
			  $('#reportrange').data('daterangepicker').setOptions(optionSet2, cb);
			});
			$('#destroy').click(function() {
			  $('#reportrange').data('daterangepicker').remove();
			});
   
		}
   	   
	   function init_daterangepicker_right() {
	      
				if( typeof ($.fn.daterangepicker) === 'undefined'){ return; }
				console.log('init_daterangepicker_right');
		  
				var cb = function(start, end, label) {
				  console.log(start.toISOString(), end.toISOString(), label);
				  $('#reportrange_right span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
				};

				var optionSet1 = {
				  startDate: moment().subtract(29, 'days'),
				  endDate: moment(),
				  minDate: '01/01/2012',
				  maxDate: '12/31/2020',
				  dateLimit: {
					days: 60
				  },
				  showDropdowns: true,
				  showWeekNumbers: true,
				  timePicker: false,
				  timePickerIncrement: 1,
				  timePicker12Hour: true,
				  ranges: {
					'Today': [moment(), moment()],
					'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
					'Last 7 Days': [moment().subtract(6, 'days'), moment()],
					'Last 30 Days': [moment().subtract(29, 'days'), moment()],
					'This Month': [moment().startOf('month'), moment().endOf('month')],
					'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
				  },
				  opens: 'right',
				  buttonClasses: ['btn btn-default'],
				  applyClass: 'btn-small btn-primary',
				  cancelClass: 'btn-small',
				  format: 'MM/DD/YYYY',
				  separator: ' to ',
				  locale: {
					applyLabel: 'Submit',
					cancelLabel: 'Clear',
					fromLabel: 'From',
					toLabel: 'To',
					customRangeLabel: 'Custom',
					daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
					monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
					firstDay: 1
				  }
				};

				$('#reportrange_right span').html(moment().subtract(29, 'days').format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));

				$('#reportrange_right').daterangepicker(optionSet1, cb);

				$('#reportrange_right').on('show.daterangepicker', function() {
				  console.log("show event fired");
				});
				$('#reportrange_right').on('hide.daterangepicker', function() {
				  console.log("hide event fired");
				});
				$('#reportrange_right').on('apply.daterangepicker', function(ev, picker) {
				  console.log("apply event fired, start/end dates are " + picker.startDate.format('MMMM D, YYYY') + " to " + picker.endDate.format('MMMM D, YYYY'));
				});
				$('#reportrange_right').on('cancel.daterangepicker', function(ev, picker) {
				  console.log("cancel event fired");
				});

				$('#options1').click(function() {
				  $('#reportrange_right').data('daterangepicker').setOptions(optionSet1, cb);
				});

				$('#options2').click(function() {
				  $('#reportrange_right').data('daterangepicker').setOptions(optionSet2, cb);
				});

				$('#destroy').click(function() {
				  $('#reportrange_right').data('daterangepicker').remove();
				});

	   }
	   
	    function init_daterangepicker_single_call() {
	      
			if( typeof ($.fn.daterangepicker) === 'undefined'){ return; }
			console.log('init_daterangepicker_single_call');
		   
			$('#single_cal1').daterangepicker({
			  singleDatePicker: true,
			  singleClasses: "picker_1"
			}, function(start, end, label) {
			  console.log(start.toISOString(), end.toISOString(), label);
			});
			$('#single_cal2').daterangepicker({
			  singleDatePicker: true,
			  singleClasses: "picker_2"
			}, function(start, end, label) {
			  console.log(start.toISOString(), end.toISOString(), label);
			});
			$('#single_cal3').daterangepicker({
			  singleDatePicker: true,
			  singleClasses: "picker_3"
			}, function(start, end, label) {
			  console.log(start.toISOString(), end.toISOString(), label);
			});
			$('#single_cal4').daterangepicker({
			  singleDatePicker: true,
			  singleClasses: "picker_4"
			}, function(start, end, label) {
			  console.log(start.toISOString(), end.toISOString(), label);
			});
  
  
		}
		
		 
		function init_daterangepicker_reservation() {
	      
			if( typeof ($.fn.daterangepicker) === 'undefined'){ return; }
			console.log('init_daterangepicker_reservation');
		 
			$('#reservation').daterangepicker(null, function(start, end, label) {
			  console.log(start.toISOString(), end.toISOString(), label);
			});

			$('#reservation-time').daterangepicker({
			  timePicker: true,
			  timePickerIncrement: 30,
			  locale: {
				format: 'MM/DD/YYYY h:mm A'
			  }
			});
	
		}
	   
	   /* SMART WIZARD */
		
		function init_SmartWizard() {
			
			if( typeof ($.fn.smartWizard) === 'undefined'){ return; }
			console.log('init_SmartWizard');
			
			$('#wizard').smartWizard();

			$('#wizard_verticle').smartWizard({
			  transitionEffect: 'slide'
			});

			$('.buttonNext').addClass('btn btn-success');
			$('.buttonPrevious').addClass('btn btn-primary');
			$('.buttonFinish').addClass('btn btn-default');
			
		};
	   
	   
	  /* VALIDATOR */

	  function init_validator () {
		 
		if( typeof (validator) === 'undefined'){ return; }
		console.log('init_validator'); 
	  
	  // initialize the validator function
      validator.message.date = 'not a real date';

      // validate a field on "blur" event, a 'select' on 'change' event & a '.reuired' classed multifield on 'keyup':
      $('form')
        .on('blur', 'input[required], input.optional, select.required', validator.checkField)
        .on('change', 'select.required', validator.checkField)
        .on('keypress', 'input[required][pattern]', validator.keypress);

      $('.multi.required').on('keyup blur', 'input', function() {
        validator.checkField.apply($(this).siblings().last()[0]);
      });

      $('form').submit(function(e) {
        e.preventDefault();
        var submit = true;

        // evaluate the form using generic validaing
        if (!validator.checkAll($(this))) {
          submit = false;
        }

        if (submit)
          this.submit();

        return false;
		});
	  
	  };
	   
	  	/* PNotify */
			
		function init_PNotify() {
			
			if( typeof (PNotify) === 'undefined'){ return; }
			console.log('init_PNotify');
			
			new PNotify({
			  title: "PNotify",
			  type: "info",
			  text: "Welcome. Try hovering over me. You can click things behind me, because I'm non-blocking.",
			  nonblock: {
				  nonblock: true
			  },
			  addclass: 'dark',
			  styling: 'bootstrap3',
			  hide: false,
			  before_close: function(PNotify) {
				PNotify.update({
				  title: PNotify.options.title + " - Enjoy your Stay",
				  before_close: null
				});

				PNotify.queueRemove();

				return false;
			  }
			});

		}; 
	   
	   
	   /* CUSTOM NOTIFICATION */
			
		function init_CustomNotification() {
			
			console.log('run_customtabs');
			
			if( typeof (CustomTabs) === 'undefined'){ return; }
			console.log('init_CustomTabs');
			
			var cnt = 10;

			TabbedNotification = function(options) {
			  var message = "<div id='ntf" + cnt + "' class='text alert-" + options.type + "' style='display:none'><h2><i class='fa fa-bell'></i> " + options.title +
				"</h2><div class='close'><a href='javascript:;' class='notification_close'><i class='fa fa-close'></i></a></div><p>" + options.text + "</p></div>";

			  if (!document.getElementById('custom_notifications')) {
				alert('doesnt exists');
			  } else {
				$('#custom_notifications ul.notifications').append("<li><a id='ntlink" + cnt + "' class='alert-" + options.type + "' href='#ntf" + cnt + "'><i class='fa fa-bell animated shake'></i></a></li>");
				$('#custom_notifications #notif-group').append(message);
				cnt++;
				CustomTabs(options);
			  }
			};

			CustomTabs = function(options) {
			  $('.tabbed_notifications > div').hide();
			  $('.tabbed_notifications > div:first-of-type').show();
			  $('#custom_notifications').removeClass('dsp_none');
			  $('.notifications a').click(function(e) {
				e.preventDefault();
				var $this = $(this),
				  tabbed_notifications = '#' + $this.parents('.notifications').data('tabbed_notifications'),
				  others = $this.closest('li').siblings().children('a'),
				  target = $this.attr('href');
				others.removeClass('active');
				$this.addClass('active');
				$(tabbed_notifications).children('div').hide();
				$(target).show();
			  });
			};

			CustomTabs();

			var tabid = idname = '';

			$(document).on('click', '.notification_close', function(e) {
			  idname = $(this).parent().parent().attr("id");
			  tabid = idname.substr(-2);
			  $('#ntf' + tabid).remove();
			  $('#ntlink' + tabid).parent().remove();
			  $('.notifications a').first().addClass('active');
			  $('#notif-group div').first().css('display', 'block');
			});
			
		};
		

	   
		
		function init_charts() {
			
				console.log('run_charts  typeof [' + typeof (Chart) + ']');
			
				if( typeof (Chart) === 'undefined'){ return; }
				
				console.log('init_charts');
			
				
				Chart.defaults.global.legend = {
					enabled: false
				};
				
				

			if ($('#canvas_line').length ){
				
				var canvas_line_00 = new Chart(document.getElementById("canvas_line"), {
				  type: 'line',
				  data: {
					labels: ["January", "February", "March", "April", "May", "June", "July"],
					datasets: [{
					  label: "My First dataset",
					  backgroundColor: "rgba(13, 192, 255, 0.31)",
					  borderColor: "rgba(13, 192, 255, 0.7)",
					  pointBorderColor: "rgba(13, 192, 255, 0.7)",
					  pointBackgroundColor: "rgba(13, 192, 255, 0.7)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(220,220,220,1)",
					  pointBorderWidth: 1,
					  data: [31, 74, 6, 39, 20, 85, 7]
					}, {
					  label: "My Second dataset",
					  backgroundColor: "rgba(3, 32, 51, 0.3)",
					  borderColor: "rgba(3, 32, 51, 0.70)",
					  pointBorderColor: "rgba(3, 32, 51, 0.70)",
					  pointBackgroundColor: "rgba(3, 32, 51, 0.70)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(151,187,205,1)",
					  pointBorderWidth: 1,
					  data: [82, 23, 66, 9, 99, 4, 2]
					}]
				  },
				});
				
			}

			
			if ($('#canvas_line1').length ){
			
				var canvas_line_01 = new Chart(document.getElementById("canvas_line1"), {
				  type: 'line',
				  data: {
					labels: ["January", "February", "March", "April", "May", "June", "July"],
					datasets: [{
					  label: "My First dataset",
					  backgroundColor: "rgba(13, 192, 255, 0.31)",
					  borderColor: "rgba(13, 192, 255, 0.7)",
					  pointBorderColor: "rgba(13, 192, 255, 0.7)",
					  pointBackgroundColor: "rgba(13, 192, 255, 0.7)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(220,220,220,1)",
					  pointBorderWidth: 1,
					  data: [31, 74, 6, 39, 20, 85, 7]
					}, {
					  label: "My Second dataset",
					  backgroundColor: "rgba(3, 32, 51, 0.3)",
					  borderColor: "rgba(3, 32, 51, 0.70)",
					  pointBorderColor: "rgba(3, 32, 51, 0.70)",
					  pointBackgroundColor: "rgba(3, 32, 51, 0.70)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(151,187,205,1)",
					  pointBorderWidth: 1,
					  data: [82, 23, 66, 9, 99, 4, 2]
					}]
				  },
				});
			
			}
				
				
			if ($('#canvas_line2').length ){		
			
				var canvas_line_02 = new Chart(document.getElementById("canvas_line2"), {
				  type: 'line',
				  data: {
					labels: ["January", "February", "March", "April", "May", "June", "July"],
					datasets: [{
					  label: "My First dataset",
					  backgroundColor: "rgba(13, 192, 255, 0.31)",
					  borderColor: "rgba(13, 192, 255, 0.7)",
					  pointBorderColor: "rgba(13, 192, 255, 0.7)",
					  pointBackgroundColor: "rgba(13, 192, 255, 0.7)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(220,220,220,1)",
					  pointBorderWidth: 1,
					  data: [31, 74, 6, 39, 20, 85, 7]
					}, {
					  label: "My Second dataset",
					  backgroundColor: "rgba(3, 32, 51, 0.3)",
					  borderColor: "rgba(3, 32, 51, 0.70)",
					  pointBorderColor: "rgba(3, 32, 51, 0.70)",
					  pointBackgroundColor: "rgba(3, 32, 51, 0.70)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(151,187,205,1)",
					  pointBorderWidth: 1,
					  data: [82, 23, 66, 9, 99, 4, 2]
					}]
				  },
				});

			}	
			
			
			if ($('#canvas_line3').length ){
			
				var canvas_line_03 = new Chart(document.getElementById("canvas_line3"), {
				  type: 'line',
				  data: {
					labels: ["January", "February", "March", "April", "May", "June", "July"],
					datasets: [{
					  label: "My First dataset",
					  backgroundColor: "rgba(13, 192, 255, 0.31)",
					  borderColor: "rgba(13, 192, 255, 0.7)",
					  pointBorderColor: "rgba(13, 192, 255, 0.7)",
					  pointBackgroundColor: "rgba(13, 192, 255, 0.7)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(220,220,220,1)",
					  pointBorderWidth: 1,
					  data: [31, 74, 6, 39, 20, 85, 7]
					}, {
					  label: "My Second dataset",
					  backgroundColor: "rgba(3, 32, 51, 0.3)",
					  borderColor: "rgba(3, 32, 51, 0.70)",
					  pointBorderColor: "rgba(3, 32, 51, 0.70)",
					  pointBackgroundColor: "rgba(3, 32, 51, 0.70)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(151,187,205,1)",
					  pointBorderWidth: 1,
					  data: [82, 23, 66, 9, 99, 4, 2]
					}]
				  },
				});

			}	
			
			
			if ($('#canvas_line4').length ){
				
				var canvas_line_04 = new Chart(document.getElementById("canvas_line4"), {
				  type: 'line',
				  data: {
					labels: ["January", "February", "March", "April", "May", "June", "July"],
					datasets: [{
					  label: "My First dataset",
					  backgroundColor: "rgba(13, 192, 255, 0.31)",
					  borderColor: "rgba(13, 192, 255, 0.7)",
					  pointBorderColor: "rgba(13, 192, 255, 0.7)",
					  pointBackgroundColor: "rgba(13, 192, 255, 0.7)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(220,220,220,1)",
					  pointBorderWidth: 1,
					  data: [31, 74, 6, 39, 20, 85, 7]
					}, {
					  label: "My Second dataset",
					  backgroundColor: "rgba(3, 32, 51, 0.3)",
					  borderColor: "rgba(3, 32, 51, 0.70)",
					  pointBorderColor: "rgba(3, 32, 51, 0.70)",
					  pointBackgroundColor: "rgba(3, 32, 51, 0.70)",
					  pointHoverBackgroundColor: "#fff",
					  pointHoverBorderColor: "rgba(151,187,205,1)",
					  pointBorderWidth: 1,
					  data: [82, 23, 66, 9, 99, 4, 2]
					}]
				  },
				});		
				
			}
			
				
			  // Line chart
			 
			if ($('#lineChart').length ){	
			
			  var ctx = document.getElementById("lineChart");
			  var lineChart = new Chart(ctx, {
				type: 'line',
				data: {
				  labels: ["January", "February", "March", "April", "May", "June"],
				  datasets: [{
					label: "My Connections",
					backgroundColor: "rgba(13, 192, 255, 0.31)",
					borderColor: "rgba(13, 192, 255, 0.7)",
					pointBorderColor: "rgba(13, 192, 255, 0.7)",
					pointBackgroundColor: "rgba(13, 192, 255, 0.7)",
					pointHoverBackgroundColor: "#fff",
					pointHoverBorderColor: "rgba(220,220,220,1)",
					pointBorderWidth: 1,
					data: [0, 4, 11, 9, 13, 12]
				  }, {
					label: "Average Connection",
					backgroundColor: "rgba(3, 32, 51, 0.3)",
					borderColor: "rgba(3, 32, 51, 0.70)",
					pointBorderColor: "rgba(3, 32, 51, 0.70)",
					pointBackgroundColor: "rgba(3, 32, 51, 0.70)",
					pointHoverBackgroundColor: "#fff",
					pointHoverBorderColor: "rgba(151,187,205,1)",
					pointBorderWidth: 1,
					data: [20.5, 17.2, 14.4, 35.1, 11, 19]
				  }]
				},
			  });
			
			}
				
			  // Bar chart
			  
			if ($('#mybarChart').length ){ 
			  
			  var ctx = document.getElementById("mybarChart");
			  var mybarChart = new Chart(ctx, {
				type: 'bar',
				data: {
				  labels: ["January", "February", "March", "April", "May", "June", "July"],
				  datasets: [{
					label: '# of Votes',
					backgroundColor: "#0dc0ff",
					data: [51, 30, 40, 28, 92, 50, 45]
				  }, {
					label: '# of Votes',
					backgroundColor: "#032033",
					data: [41, 56, 25, 48, 72, 34, 12]
				  }]
				},

				options: {
				  scales: {
					yAxes: [{
					  ticks: {
						beginAtZero: true
					  }
					}]
				  }
				}
			  });
			  
			} 
			  

			  // Doughnut chart
			  
			if ($('#canvasDoughnut').length ){ 
			  
			  var ctx = document.getElementById("canvasDoughnut");
			  var data = {
				labels: [
				  "Dark Grey",
				  "Purple Color",
				  "Gray Color",
				  "Green Color",
				  "Blue Color"
				],
				datasets: [{
				  data: [120, 50, 140, 180, 100],
				  backgroundColor: [
					"#455C73",
					"#9B59B6",
					"#BDC3C7",
					"#0dc0ff",
					"#3498DB"
				  ],
				  hoverBackgroundColor: [
					"#34495E",
					"#B370CF",
					"#CFD4D8",
					"#36CAAB",
					"#49A9EA"
				  ]

				}]
			  };

			  var canvasDoughnut = new Chart(ctx, {
				type: 'doughnut',
				tooltipFillColor: "rgba(51, 51, 51, 0.55)",
				data: data
			  });
			 
			} 

			  // Radar chart
			  
			if ($('#canvasRadar').length ){ 
			  
			  var ctx = document.getElementById("canvasRadar");
			  var data = {
				labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
				datasets: [{
				  label: "My First dataset",
				  backgroundColor: "rgba(3, 32, 51, 0.2)",
				  borderColor: "rgba(3, 32, 51, 0.80)",
				  pointBorderColor: "rgba(3, 32, 51, 0.80)",
				  pointBackgroundColor: "rgba(3, 32, 51, 0.80)",
				  pointHoverBackgroundColor: "#fff",
				  pointHoverBorderColor: "rgba(220,220,220,1)",
				  data: [65, 59, 90, 81, 56, 55, 40]
				}, {
				  label: "My Second dataset",
				  backgroundColor: "rgba(13, 192, 255, 0.2)",
				  borderColor: "rgba(13, 192, 255, 0.85)",
				  pointColor: "rgba(13, 192, 255, 0.85)",
				  pointStrokeColor: "#fff",
				  pointHighlightFill: "#fff",
				  pointHighlightStroke: "rgba(151,187,205,1)",
				  data: [28, 48, 40, 19, 96, 27, 100]
				}]
			  };

			  var canvasRadar = new Chart(ctx, {
				type: 'radar',
				data: data,
			  });
			
			}
			
			
			  // Pie chart
			  if ($('#pieChart').length ){
				  
				  var ctx = document.getElementById("pieChart");
				  var data = {
					datasets: [{
					  data: [120, 50, 140, 180, 100],
					  backgroundColor: [
						"#455C73",
						"#9B59B6",
						"#BDC3C7",
						"#0dc0ff",
						"#3498DB"
					  ],
					  label: 'My dataset' // for legend
					}],
					labels: [
					  "Dark Gray",
					  "Purple",
					  "Gray",
					  "Green",
					  "Blue"
					]
				  };

				  var pieChart = new Chart(ctx, {
					data: data,
					type: 'pie',
					options: {
					  legend: true
					}
				  });
				  
			  }
			
			  
			  // PolarArea chart

			if ($('#polarArea').length ){

				var ctx = document.getElementById("polarArea");
				var data = {
				datasets: [{
				  data: [120, 50, 140, 180, 100],
				  backgroundColor: [
					"#455C73",
					"#9B59B6",
					"#BDC3C7",
					"#0dc0ff",
					"#3498DB"
				  ],
				  label: 'My dataset'
				}],
				labels: [
				  "Dark Gray",
				  "Purple",
				  "Gray",
				  "Green",
				  "Blue"
				]
				};

				var polarArea = new Chart(ctx, {
				data: data,
				type: 'polarArea',
				options: {
				  scale: {
					ticks: {
					  beginAtZero: true
					}
				  }
				}
				});
			
			}
		}

		/* COMPOSE */
		
		function init_compose() {
		
			if( typeof ($.fn.slideToggle) === 'undefined'){ return; }
			console.log('init_compose');
		
			$('#compose, .compose-close').click(function(){
				$('.compose').slideToggle();
			});
		
		};
	   

	   
		/* DATA TABLES */
			
			function init_DataTables() {
				
				console.log('run_datatables');
				
				if( typeof ($.fn.DataTable) === 'undefined'){ return; }
				console.log('init_DataTables');
				
				var handleDataTableButtons = function() {
				  if ($("#datatable-buttons").length) {
					$("#datatable-buttons").DataTable({
					  dom: "Bfrtip",
					  buttons: [
						{
						  extend: "copy",
						  className: "btn-sm"
						},
						{
						  extend: "csv",
						  className: "btn-sm"
						},
						{
						  extend: "excel",
						  className: "btn-sm"
						},
						{
						  extend: "pdfHtml5",
						  className: "btn-sm"
						},
						{
						  extend: "print",
						  className: "btn-sm"
						},
					  ],
					  responsive: true
					});
				  }
				};

				TableManageButtons = function() {
				  "use strict";
				  return {
					init: function() {
					  handleDataTableButtons();
					}
				  };
				}();

				$('#datatable').dataTable();

				$('#datatable-keytable').DataTable({
				  keys: true
				});

				$('#datatable-responsive').DataTable();

				// $('#datatable-scroller').DataTable({
				//   ajax: "js/datatables/json/scroller-demo.json",
				//   deferRender: true,
				//   scrollY: 380,
				//   scrollCollapse: true,
				//   scroller: true
				// });
				// Commented out by us

				$('#datatable-fixed-header').DataTable({
				  fixedHeader: true
				});

				var $datatable = $('#datatable-checkbox');

				$datatable.dataTable({
				  'order': [[ 1, 'asc' ]],
				  'columnDefs': [
					{ orderable: false, targets: [0] }
				  ]
				});
				$datatable.on('draw.dt', function() {
				  $('checkbox input').iCheck({
					checkboxClass: 'icheckbox_flat-green'
				  });
				});

				TableManageButtons.init();
				
			};
	   
		
		

	   
	   
	$(document).ready(function() {
				
		init_sparklines();
		init_sidebar();
		init_InputMask();
		init_parsley();
		init_daterangepicker();
		init_TagsInput();
		init_daterangepicker_right();
		init_daterangepicker_single_call();
		init_daterangepicker_reservation();
		init_SmartWizard();
		init_charts();
		init_validator();
		init_DataTables();
		init_chart_doughnut();
		init_PNotify();
		init_starrr();
		init_compose();
		init_CustomNotification();
		init_autocomplete();
	});