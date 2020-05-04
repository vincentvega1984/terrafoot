jQuery.noConflict();
(function( $ ) {
$(document).ready(function(){

  /*WOW initialize*/
  new WOW().init();

  $(window).scroll(function() {
    if ($(this).scrollTop() >= 50) {        // If page is scrolled more than 50px
        $('#return-to-top').fadeIn(200);   // Fade in the arrow
    } else {
        $('#return-to-top').fadeOut(200);   // Else fade out the arrow
    }
  });
  $('#return-to-top').click(function() {      // When arrow is clicked
      $('body,html').animate({
          scrollTop : 0                       // Scroll to top of body
      }, 500);
  });

    $('.tabs-switcher .tabname').click(function(){
		var tab_id = $(this).attr('data-tab');
		$('.tabs-switcher .tabname').removeClass('current');
		$('.tab-content').removeClass('current');
		$(this).addClass('current');
		$("#"+tab_id).addClass('current');
	});

  $('.tab-content #stab_matches .table-responsive').addClass('global');

  $('.nav-mobile').click(function(){
    $(this).toggleClass('open');
    $('nav.mainmenu').toggleClass('open');
    $('.overlay-mobile').toggleClass('active');
  });

  $('.overlay-mobile').click(function(){
    $(this).toggleClass('active');
    $('nav.mainmenu').toggleClass('open');
    $('.nav-mobile').toggleClass('open');
  });

  $('.changeView .grid').click(function(){
    $(this).addClass('active')
    $('.changeView .list').removeClass('active');
    $('.itemList').addClass('grid').removeClass('list');
  });
  $('.changeView .list').click(function(){
    $(this).addClass('active')
    $('.changeView .grid').removeClass('active');
    $('.itemList').addClass('list').removeClass('grid');
  });

  if ($(window).width() < 768) {
      $('.mainmenu ul.nav.menu li.deeper.parent').click(function(){
        $(this).toggleClass('active');
        $(this).children( "#dropdown" ).slideToggle();
      });
      $("#stab_statistic .player-oneline-stat .table>tbody>tr>td:not(:first-child) .emblInline").closest('td').remove();
  }

  /*Add animation for featured block*/
  $('.featured-items .item').addClass('wow animated');
  $(".featured-items .item:nth-child(even)").addClass('fadeInLeft');
  $(".featured-items .item:nth-child(odd)").addClass('fadeInRight');

  /*Footer mobile*/
  $('.footer .footer-top .moduletable h3').click(function(){
    $(this).toggleClass('open');
    $(this).closest('.moduletable').find('ul').slideToggle();
  });

  /*Navigation became sticky on scroll use sticky.js*/
    if ($(window).width() > 768) {
        $("nav.mainmenu").sticky({topSpacing:0});
    }
  
  /*Костыль для календаря Кубка*/
  $( "<div class='jstable-row js-mdname'><div class='jsrow-matchday-name'>1/4 - Весна'19</div></div>" ).insertAfter( ".cup-cal .calendar-table .jsMatchDivMain .jstable-row:nth-child(9)" );
  $( "<div class='jstable-row js-mdname'><div class='jsrow-matchday-name'>1/2 - Весна'19</div></div>" ).insertAfter( ".cup-cal .calendar-table .jsMatchDivMain .jstable-row:nth-child(14)" );
  $( "<div class='jstable-row js-mdname'><div class='jsrow-matchday-name'>Финал - Весна'19</div></div>" ).insertAfter( ".cup-cal .calendar-table .jsMatchDivMain .jstable-row:nth-child(17)" );
  
  $('.cup-cal .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(9) .jsDivMobileMdayName').text("1/4 - Весна'19");
  $('.cup-cal .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(10) .jsDivMobileMdayName').text("1/4 - Весна'19");
  $('.cup-cal .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(11) .jsDivMobileMdayName').text("1/4 - Весна'19");
  $('.cup-cal .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(12) .jsDivMobileMdayName').text("1/4 - Весна'19");
  
  $('.cup-cal .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(13) .jsDivMobileMdayName').text("1/2 - Весна'19");
  $('.cup-cal .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(14) .jsDivMobileMdayName').text("1/2 - Весна'19");
  
  $('.cup-cal .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(15) .jsDivMobileMdayName').text("Финал - Весна'19");
  
  $( "<div class='jstable-row js-mdname'><div class='jsrow-matchday-name'>1/8 - Осень'19</div></div>" ).insertAfter( ".cup-cal-osen-19 .calendar-table .jsMatchDivMain .jstable-row:nth-child(13)" );
  $( "<div class='jstable-row js-mdname'><div class='jsrow-matchday-name'>1/4- Осень'19</div></div>" ).insertAfter( ".cup-cal-osen-19 .calendar-table .jsMatchDivMain .jstable-row:nth-child(22)" );
  $( "<div class='jstable-row js-mdname'><div class='jsrow-matchday-name'>1/2- Осень'19</div></div>" ).insertAfter( ".cup-cal-osen-19 .calendar-table .jsMatchDivMain .jstable-row:nth-child(27)" );
  
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(13) .jsDivMobileMdayName').text("1/8 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(14) .jsDivMobileMdayName').text("1/8 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(15) .jsDivMobileMdayName').text("1/8 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(16) .jsDivMobileMdayName').text("1/8 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(17) .jsDivMobileMdayName').text("1/8 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(18) .jsDivMobileMdayName').text("1/8 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(19) .jsDivMobileMdayName').text("1/8 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(20) .jsDivMobileMdayName').text("1/8 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(21) .jsDivMobileMdayName').text("1/4 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(22) .jsDivMobileMdayName').text("1/4 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(23) .jsDivMobileMdayName').text("1/4 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(24) .jsDivMobileMdayName').text("1/4 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(25) .jsDivMobileMdayName').text("1/2 - Осень'19");
  $('.cup-cal-osen-19 .calendar-table .jsMatchDivMainMobile .jsMobileMatchCont:nth-child(26) .jsDivMobileMdayName').text("1/2 - Осень'19");

  $("#joomsport-container .nav-tabs").sticky({topSpacing:70});

  /*Hide topinfo*/
  /*$('.top-info .close').click(function(){
    $('.top-info').slideToggle();
  });*/




  if ($.cookie('notice') == 'closed') {
    $('.top-info').css("display", "none");
  } else {
    $('.top-info').css("display", "block");
  }
    // Show or hide on load depending on cookie

  $('.top-info .close').click(function(e) {
    e.preventDefault();
    $.cookie('notice','closed');
    $('.top-info').slideToggle();
  });

  $("#stab_players .js_div_particName a:contains('капитан')").parent().addClass('captain');
  $("#stab_players .js_div_particName a").text(function () {
      return $(this).text().replace("капитан", "");
  });
  $(".maincontent #joomsport-container .heading > .heading h2 span:contains('капитан')").text(function () {
      return $(this).text().replace("капитан", "");
  });
  $(".maincontent #joomsport-container .heading > .heading h2 span:contains('травма')").text(function () {
      return $(this).text().replace("травма", "");
  });

  /*Shop additional image*/	
  $('.shopItemExtraFields .aliasAdditionalImage img').each(function(){
     $(this).addClass('image-additional');
     $(this).closest(".shopItemBody").find('.shopItemImageBlock a').append(this);
  }); 

  /*Shop additional body class*/
  if ($(".productView")[0]){
    $('body').addClass('product-item-view');
  }

  /*Match page*/
  $("#stab_squad .jstable-cell.name a:contains('капитан')").parent().addClass('captain');
  $("#stab_squad .jstable-cell.name a").text(function () {
      return $(this).text().replace("капитан", "");
  });

  $("#jsMatchViewID .evPlayerName:contains('капитан')").parent().addClass('captain');
  $("#jsMatchViewID .evPlayerName").text(function () {
    return $(this).text().replace("капитан", "");
  });

  $(".maincontent #joomsport-container #jsMatchViewID #stab_main table.table.table-striped tbody tr td a:contains('капитан')").parent().addClass('captain');
  $(".maincontent #joomsport-container #jsMatchViewID #stab_main table.table.table-striped tbody tr td a").text(function () {
    return $(this).text().replace("капитан", "");
  });

  $("#stab_squad .jstable-cell.name a:contains('травма')").parent().addClass('injury');
  $("#stab_squad .jstable-cell.name a").text(function () {
      return $(this).text().replace("травма", "");
  });

  $(".players-table-resp .js_div_particName a:contains('капитан')").parent().addClass('captain');
  $(".players-table-resp .js_div_particName a").text(function () {
      return $(this).text().replace("капитан", "");
  });

  $(".players-table-resp .js_div_particName a:contains('травма')").parent().addClass('injury');
  $(".players-table-resp .js_div_particName a").text(function () {
      return $(this).text().replace("травма", "");
  });

  $("#stab_players .js_div_particName a:contains('травма')").parent().addClass('injury');
  $("#stab_players .js_div_particName a").text(function () {
      return $(this).text().replace("травма", "");
  });

  $('.timeline-empty-cell').parent().addClass('hidden');

    //Транслитерация кириллицы в URL
    function urlRusLat(str) {
      str = str.toLowerCase(); // все в нижний регистр
        var cyr2latChars = new Array(
            ['а', 'a'], ['б', 'b'], ['в', 'v'], ['г', 'g'],
            ['д', 'd'],  ['е', 'e'], ['ё', 'yo'], ['ж', 'zh'], ['з', 'z'],
            ['и', 'i'], ['й', 'y'], ['к', 'k'], ['л', 'l'],
            ['м', 'm'],  ['н', 'n'], ['о', 'o'], ['п', 'p'],  ['р', 'r'],
            ['с', 's'], ['т', 't'], ['у', 'u'], ['ф', 'f'],
            ['х', 'h'],  ['ц', 'c'], ['ч', 'ch'],['ш', 'sh'], ['щ', 'shch'],
            ['ъ', ''],  ['ы', 'y'], ['ь', ''],  ['э', 'e'], ['ю', 'yu'], ['я', 'ya'],
            
            ['А', 'A'], ['Б', 'B'],  ['В', 'V'], ['Г', 'G'],
            ['Д', 'D'], ['Е', 'E'], ['Ё', 'YO'],  ['Ж', 'ZH'], ['З', 'Z'],
            ['И', 'I'], ['Й', 'Y'],  ['К', 'K'], ['Л', 'L'],
            ['М', 'M'], ['Н', 'N'], ['О', 'O'],  ['П', 'P'],  ['Р', 'R'],
            ['С', 'S'], ['Т', 'T'],  ['У', 'U'], ['Ф', 'F'],
            ['Х', 'H'], ['Ц', 'C'], ['Ч', 'CH'], ['Ш', 'SH'], ['Щ', 'SHCH'],
            ['Ъ', ''],  ['Ы', 'Y'],
            ['Ь', ''],
            ['Э', 'E'],
            ['Ю', 'YU'],
            ['Я', 'YA'],
            
            ['a', 'a'], ['b', 'b'], ['c', 'c'], ['d', 'd'], ['e', 'e'],
            ['f', 'f'], ['g', 'g'], ['h', 'h'], ['i', 'i'], ['j', 'j'],
            ['k', 'k'], ['l', 'l'], ['m', 'm'], ['n', 'n'], ['o', 'o'],
            ['p', 'p'], ['q', 'q'], ['r', 'r'], ['s', 's'], ['t', 't'],
            ['u', 'u'], ['v', 'v'], ['w', 'w'], ['x', 'x'], ['y', 'y'],
            ['z', 'z'],
            
            ['A', 'A'], ['B', 'B'], ['C', 'C'], ['D', 'D'],['E', 'E'],
            ['F', 'F'],['G', 'G'],['H', 'H'],['I', 'I'],['J', 'J'],['K', 'K'],
            ['L', 'L'], ['M', 'M'], ['N', 'N'], ['O', 'O'],['P', 'P'],
            ['Q', 'Q'],['R', 'R'],['S', 'S'],['T', 'T'],['U', 'U'],['V', 'V'],
            ['W', 'W'], ['X', 'X'], ['Y', 'Y'], ['Z', 'Z'],
            
            [' ', '_'],['0', '0'],['1', '1'],['2', '2'],['3', '3'],
            ['4', '4'],['5', '5'],['6', '6'],['7', '7'],['8', '8'],['9', '9'],
            ['-', '-']
  
        );
  
        var newStr = new String();
  
        for (var i = 0; i < str.length; i++) {
  
            ch = str.charAt(i);
            var newCh = '';
  
            for (var j = 0; j < cyr2latChars.length; j++) {
                if (ch == cyr2latChars[j][0]) {
                    newCh = cyr2latChars[j][1];
  
                }
            }
            // Если найдено совпадение, то добавляется соответствие, если нет - пустая строка
            newStr += newCh;
  
        }
        // Удаляем повторяющие знаки - Именно на них заменяются пробелы.
        // Так же удаляем символы перевода строки, но это наверное уже лишнее
        return newStr.replace(/[_]{2,}/gim, '_').replace(/\n/gim, '');
    }

  /*Achivement*/
  $('.player-main-info-wrapper .extrafldcn .jstable-row').each(function(){
    var efName = $(this).children(":first").text();
    var efNameLatin = efName.replace(':', '');
    var efNewLatin = urlRusLat(efNameLatin);
    console.log(efNewLatin);
    $(this).addClass(efNewLatin);
  })

  /*Achivement collapse*/
  $('.achivements-block .title').click(function(){
    $(this).toggleClass('open');
    $(this).next('ul').slideToggle();
  });

  if ($('.player-main-info').length > 0){
    $('.tabs').addClass('player-page');
    $('#stab_statistic, #stab_photos').wrapAll('<div class="player-container"></div>')
  }

  /** Hide player stat row if tournament name is empty. On Player page */
  $('.player-oneline-stat tbody tr').each(function () {
    var emptyCell = $(this).find('td').first();
    if(emptyCell.is(':empty')){
      emptyCell.parent().remove();
    }
  });

  /*Team page additional classes*/
  $('.team-view .extrafldcn .jstable-row').each(function(){
    var efName = $(this).children(":first").text();
    var efNameLatin = efName.replace(':', '');
    var efNewLatin = urlRusLat(efNameLatin);
    console.log(efNewLatin);
    $(this).addClass(efNewLatin);
  })

  /*Count achivements*/
  $('.achivements-block ').each(function(){
    var count = $(this).find('ul li').length;
    $(this).find('.title').prepend('<span class="achivements-count">'+ count +'</span>');
  })

  var ratingWidth = $('.jstable-row.reyting .jstable-cell:last-child').text();
  $('.jstable-row.reyting .jstable-cell:last-child').append("<span class='stars-background'><span class='stars-background-active' style='width:" + ratingWidth + '%' + "'></span></span>");

  var playerTeamImg = $('#stab_statistic .player-career-seasons .player-career-season:last-child img').attr('src');
  var playerTeamUrl = $('#stab_statistic .player-career-seasons .player-career-season:last-child a').attr('href');
  $('.player-main-info .photoPlayer .player-team img').attr('src', playerTeamImg);
  $('.player-main-info .photoPlayer .player-team').attr('href', playerTeamUrl);


  $(".polnoe_imya .jstable-cell:contains('капитан')").text(function () {
    return $(this).text().replace("капитан", "");
  });

  /*Hide captain and injury on player stats module*/
  $(".jsm_playerstat .jsblc-team-info a:contains('капитан')").text(function () {
      return $(this).text().replace("капитан", "");
  });
  $(".jsm_playerstat .jsblc-team-info a:contains('травма')").text(function () {
      return $(this).text().replace("травма", "");
  });

  $('.matches-scroller .scores-items').slick({
    infinite: true,
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    responsive: [
    {
      breakpoint: 1200,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 960,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    ]
  });
  
  $('.partners').slick({
    infinite: true,
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    responsive: [
    {
      breakpoint: 1200,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true
      }
    },
    {
      breakpoint: 960,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    ]
  });

  $('.video-items').slick({
    infinite: true,
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    responsive: [
    {
      breakpoint: 1200,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true
      }
    },
    {
      breakpoint: 960,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    ]
  }); 

  /*Main slider*/
  $('.slider-items').slick({
    infinite: true,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    speed: 1000,
    autoplaySpeed: 8500,
    fade: true,
    cssEase: 'linear'
  });

  // Move table column numbers before other
  $.moveColumn = function (table, from, to) {
      var rows = $('tr', table);
      var cols;
      rows.each(function() {
          cols = $(this).children('th, td');
          cols.eq(from).detach().insertBefore(cols.eq(to));
      });
  }

  var tbl = jQuery('#stab_players #jstable_plz');
  $.moveColumn(tbl, 7, 0);



  // Smooth scroll to anchor
  $('.itemView aside a').click(function(e){
      event.preventDefault();
      $('html, body').animate({
          scrollTop: $( $.attr(this, 'href') ).offset().top -90
      }, 700);
      return false;
  });

  // Player stat change images to strings
  $('#stab_statistic .player-oneline-stat thead th .img-responsive').each(function (index, value){
        var thtitle = $(this).attr('title');
        $(this).after("<span>" + thtitle + "</span>");
        $("#stab_statistic .player-oneline-stat thead th .img-responsive").hide();
  });

  $(".player-career-season__trigger").click(function(){
    $(this).next().slideToggle();
    $(this).toggleClass('open');
  });

  if ($(".player-career-season__trigger").find("a").length > 0){ 
      $(".player-career-season").addClass('minified');
  }
  
  //Champions league temporary play-off 
  $('body.clpo .heading h2 span').text('Лига Чемпионов 2019 плей-офф');
  $('body.clgs .heading h2 span').text('Лига Чемпионов 2019 групповой этап');

  // Add headings for Joomsport Tabs
  $("#stab_matches").prepend( "<span class='title'>Календарь</span>" );
  $("#stab_players").prepend( "<span class='title'>Состав команды</span>" );
  $("#stab_photos").prepend( "<span class='title'>Медиа</span>" );
  $("#stab_statistic").prepend( "<span class='title'>Статистика</span>" );

  $("#stab_overview").prependTo(".tabs .tab-content");
  $("#stab_main .extrafldcn").insertBefore("#stab_main .intro-block");

  $( "#joomsport-container .nav-tabs a" ).click(function( event ) {
          event.preventDefault();
          $("html, body").animate({ scrollTop: $($(this).attr("href")).offset().top - 100 }, 500);
});

/*Marking cup matches on team page*/
$("#stab_matches .table-responsive.global .jsMatchDivMain .jstable-row .jsMatchDivVenue:empty").parent().addClass('cup');

$("#rating-sortable").tablesorter();

});
})(jQuery);
