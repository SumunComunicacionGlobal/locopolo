// import * as bootstrap from 'bootstrap';

jQuery(document).ready(function($) {

  $(function () {
    $('[data-bs-toggle="tooltip"]').tooltip().click(function(e) {
      e.preventDefault();
    });
  })

  var body = $('body');
  var scrolled = false;

  jQuery(window).scroll(function(event) {
      var scroll = $(this).scrollTop();

      if (scroll >= 30) {
          body.addClass("scrolled");
          scrolled = true;
      } else {
          body.removeClass("scrolled");
          scrolled = false;
      }

    });

  $('#navbarNavOffcanvas').on('show.bs.offcanvas', function () {
    body.addClass('menu-open');
  });

  $('#navbarNavOffcanvas').on('hidden.bs.offcanvas', function () {
    body.removeClass('menu-open');
  });

  $('#search-collapse').on('shown.bs.collapse', function () {
      body.addClass('search-open');
      $('#search-collapse .search-field').focus();
  });

  $('#search-collapse').on('hidden.bs.collapse', function () {
      body.removeClass('search-open');
  });

  $('.sub-menu-toggler').click(function(e) {
    e.preventDefault();
    $(this).parent().next().toggleClass('show');
    $(this).parent().parent().toggleClass('show');
  });

  // ACTIVATE  DROPDOWN ON HOVER
  $('.dropdown, .dropup').hover(function(){ 
    $('.dropdown-toggle', this).dropdown('toggle').blur(); 
  });

  // do not hide dropdown on click
  $('.dropdown-menu').click(function(e) {
    e.stopPropagation();
  });

  $("#footer-ticker").eocjsNewsticker({
      speed: 15,
      divider: ' - ',
      timeout: 0
  });

  $(".valores-nutricionales").each(function() {
    var progressBar = $(".progress-bar");
    progressBar.each(function(indx){
        var percentage = $(this).attr("aria-valuenow")/$(this).attr("aria-valuemax")*100;
        $(this).css("width", percentage + "%");
    });
  });

  $('.slick-carousel').each(function() {

    var $slider = $(this);
    $slider.on('init', function(event, slick) {
      if (slick.slideCount <= slick.options.slidesToShow) {
        $slider.find('.slick-arrow').show();
      }
    });
  
    $(this).slick({
      dots: true,
      arrows: true,
      infinite: false,
      speed: 300,
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: false,
      centerMode: true,
      centerPadding: '25%',
      appendArrows: $(this).parent().find('.slick-navigation-container'),
      appendDots: $(this).parent().find('.slick-navigation-container'),
      responsive: [
        {
          breakpoint: 1320,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            // centerPadding: '0px',
            centerMode: false,
          }
        }
        // You can unslick at a given breakpoint now by adding:
        // settings: "unslick"
        // instead of a settings object
      ]
    });
  });

  $('.slick-gallery-slider').each(function() {

    var $slider = $(this);
    $slider.on('init', function(event, slick) {
      if (slick.slideCount <= slick.options.slidesToShow) {
        $slider.find('.slick-arrow').show();
      }
    });
  
    $(this).slick({
      dots: true,
      arrows: true,
      infinite: false,
      speed: 300,
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: false,
      appendArrows: $(this).parent().find('.slick-navigation-container'),
      appendDots: $(this).parent().find('.slick-navigation-container')
    });
  });

  $('.slick-invisible-prev').on('click', function() {
    $(this).parent().find('.slick-carousel').slick('slickPrev');
  });

  $('.slick-invisible-next').on('click', function() {
    $(this).parent().find('.slick-carousel').slick('slickNext');
  });

  $('.slick-carousel').on('edge', function(event, slick, direction) {
    console.log(direction);
    $(this).parent().find('.slick-invisible-prev').toggleClass('invisible', direction === 'left');
    $(this).parent().find('.slick-invisible-next').toggleClass('invisible', direction === 'right');
  });

});
