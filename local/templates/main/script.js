$(document).ready(function() {


  (function() {
    let $menu = $(".main-menu");
    let $search = $("#search");
    let $mobile_menu = $("<div id='mobile-menu'></div>");
    $mobile_menu.hide();
    $mobile_menu.append("<div class='container'></div>");
    $("header").append($mobile_menu);
    $("#main-menu-btn").on("click", function (e) {
      e.preventDefault();
      $mobile_menu.find(".container").append($search);
      $mobile_menu.find(".container").append($menu);
      let height = $(window).height();
      let header_height = $("header").height();
      let final_height = height - header_height;
      if(height - header_height < $mobile_menu.height()){
        final_height = $mobile_menu.height();
      }
      $mobile_menu.height(final_height);
      if ($("#mobile-menu").is(":hidden")) {
        $("#mobile-menu").slideDown("", function () {
          $(".workarea").hide();
          $("footer").hide();
        });
      }
      else {
        $(".workarea").show();
        $("footer").show();
        $("#mobile-menu").slideUp();
      }
    });

    $(window).on("resize",function(){
      if($(window).width() >= 992){
        $("#mobile-menu").hide();
        $(".workarea").show();
        $("footer").show();
        $(".search-container").html($search);
        $(".menu-container").html($menu);
      }
    });
  }());

  $('.favor').on('click', function(e) {
    var favorID = $(this).attr('data-item');
    addFavorite(favorID);
  });

  $('.like-btn').on('click', function(e) {
    var likeID = $(this).attr('data-item');
    addLike(likeID);
  });
});

function addFavorite(id)
{
  var param = 'id='+id;
  $.ajax({
    url:     '/local/ajax/favorites.php',
    type:     "GET",
    dataType: "html",
    data: param,
    success: function(response) {
      var result = $.parseJSON(response);
      if(result == 1){
        $('.favor[data-item="'+id+'"]').addClass('active');
      } else if(result == -1){
        $('.favor[data-item="'+id+'"]').removeClass('active');
      }
    },
    error: function(jqXHR, textStatus, errorThrown){
      console.log('Error: '+ errorThrown);
    }
  });
}

function addLike(id)
{
  let count = Number($('.like-btn[data-item="'+id+'"] + div').text());
  if(count<1) count = 0;

  var param = 'id='+id;
  $.ajax({
    url:     '/local/ajax/likes.php',
    type:     "GET",
    dataType: "html",
    data: param,
    success: function(response) {
      var result = $.parseJSON(response);

      if(!$('.like-btn[data-item="'+id+'"]').hasClass('liked')){
        $('.like-btn[data-item="'+id+'"]').addClass('liked');
        $('.like-btn[data-item="'+id+'"] + div').text(result);
      } else {
        $('.like-btn[data-item="'+id+'"]').removeClass('liked');
        $('.like-btn[data-item="'+id+'"] + div').text(result);
      }
    },
    error: function(jqXHR, textStatus, errorThrown){
      console.log('Error: '+ errorThrown);
    }
  });
}