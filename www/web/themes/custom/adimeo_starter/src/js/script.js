(function ($, Drupal, window, document, undefined) {

////////////////////////////////// FONCTIONS UTILITAIRES //////////////////////////////////

  //création de la fonction exists()
  jQuery.fn.exists = function(){return jQuery(this).length>0;}

  //SET CESURE
  function ct_set_cesure(selector) {
    if(!$(selector).hasClass('with-cesure')){
      $(selector).each(function () {
        var title = $(this).html().split(' ');
        var html = '';
        for (var i = 0; i < title.length; i++) {
          if (html != '')
            html += ' ';
          if (i == 0) {
            html += '<span class="first">' + title[i] + '</span>';
          }
          else if (i == title.length - 1) {
            html += '<span class="last">' + title[i] + '</span>';
          } else {
            html += '<span>' + title[i] + '</span>';
          }
        }
        $(this).html(html);
        $(this).addClass('with-cesure');
      });
    }
  }

  // EQUALIZE STACK OF ELEMENTS
  function equalizeStack(domStack) {
    var max_height = 0;
    domStack.height('auto');
    domStack.each(function () {
      if ($(this).height() > max_height)
        max_height = $(this).height();
    });
    domStack.each(function () {
      $(this).height(max_height);
    });
  }
  // EQUALIZE STACK OF ELEMENTS WITH OUTER HEIGHT
  function equalizeStackOuter(domStack) {
    var max_height = 0;
    domStack.height('auto');
    domStack.each(function () {
      if ($(this).outerHeight(true) > max_height)
        max_height = $(this).outerHeight(true);
    });
    domStack.each(function () {
      $(this).height(max_height);
    });
  }
  // EQUALIZE STACK OF ELEMENTS BY ROWS
  function equalizeStackByRows(domStack, rowCount) {
    var index = 0;
    var stackLength = domStack.length;
    do{
      var row = domStack.slice(index, index + rowCount);
      index = index + rowCount;
      equalizeStack(row);
    } while(index < stackLength - 1);
  }

  // EQUALIZE STACK OF ELEMENTS BY ROWS WITH OUTER HEIGHT
  function equalizeStackByRowsOuter(domStack, rowCount) {
    var index = 0;
    var stackLength = domStack.length;
    do{
      var row = domStack.slice(index, index + rowCount);
      index = index + rowCount;
      equalizeStackOuter(row);
    } while(index < stackLength - 1);
  }
	// RESET HEIGHT (permet d'annuler les deux fonctions précédentes, dans le cadre d'un resize notamment)
  function setHeightAuto(selector){
    $(selector).each(function () {
      $(this).css('height', 'auto');
    });
  }
  
  function setEvenAndOdd(selector){
    $(selector).removeClass('odd').removeClass('even');
    $(selector).each(function (index) {
      if ((index + 1) % 2 > 0) {
        $(this).addClass('odd');
      }
      else if ((index + 1) % 2 == 0) {
        $(this).addClass('even');
      }
    });
  }
  
  // fonction qui calcule la position des contrôles pour un diaporama
  function slideshow_control_position(wrapper_selector, title_selector, photo_selector, video_selector, control_width, control_height, gap) {
    // positionnement vertical
    var title_height = $(title_selector).outerHeight(true);
    var slide_height = $(photo_selector).height();
    //console.log(slide_height);
    if (!slide_height) {
       slide_height = $(video_selector).height();
    }

    var top = title_height + (slide_height - control_height)/2;

    // positionnement horizontal
    var wrapper_width = $(wrapper_selector).width();
    var slide_width = $(photo_selector).width();
    if (slide_width == null) {
      slide_width = $(video_selector).width();
    }
    var left = (wrapper_width - slide_width)/2 - control_width - gap;
    return {'top': top, 'left': left};
  }
  
  // fonction gère les diaporamas vides
  function slideshow_empty(wrapper_selector) {
    var slide_height = $(wrapper_selector + ' .views-field-field-visuel-image').height();
    if (!slide_height) {
       slide_height = $(wrapper_selector + ' iframe').height();
    }
    // s'il n'y pas d'image de vidéo on cache le bloc qui contient le diaporama
    if (!slide_height) {
      $(wrapper_selector).hide();
    }
  }
  //VIEWPORT TO MATCH CSS MEDIA QUERIES WIDTH
  function viewport() {
    var e = window, a = 'inner';
    if (!('innerWidth' in window )) {
      a = 'client';
      e = document.documentElement || document.body;
    }
    return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
  }


  ////////////////////////////////////////////////// DOM CONTENT LOADED //////////////////////////////////
  $(function(){
  
    var base_url = window.location.origin;
    var pathname = window.location.pathname;
    var windowWith = viewport().width;
    var windowHeight = viewport().height;
    $(window).resize(function () {
      windowWith = viewport().width;
      windowHeight = viewport().height;
    });


    ///// YOUR JS :



		//RESIZE
		$(window).resize(function () {


		});

  // fin Dom Content Loaded  
  });

  /////////////////////////////////////////// BEHAVIORS /////////////////////////////////////////////
  // To understand behaviors, see https://drupal.org/node/756722#behaviors
  Drupal.behaviors.my_custom_behavior = {
    attach: function(context, settings) {


    }//fin attach function
  };//fin behaviors

})(jQuery, Drupal, this, this.document);