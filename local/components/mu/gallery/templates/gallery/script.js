$(document).ready(function() {
  let setItemsSize = function(){
    let windowWidth = $(window).width();
    let gridWidth = $(".grid").width();
    let columns = 2;
    let gutter = 16;

    if(windowWidth < 768 ) columns = 2;
    else if(windowWidth < 992 ) columns = 3;
    else if(windowWidth >= 992 ) columns = 4;

    gutter = (columns-1) * gutter;
    let width = (gridWidth - gutter) / columns;
    $('.grid-item').each(function(){
      $(this).width( width );
      let ratio = $(this).attr("data-ratio");
      if(0){
        let multiplier = 1;
        if(ratio >= 1){
          multiplier = 0.715;
        }else if(ratio < 1){
          multiplier = 1.285;
        }
        $(this).height( width * multiplier );
      }else{
        $(this).height( width / ratio );
      }
    })
  };

  setItemsSize();

  $(window).resize(function(){
    setItemsSize();
  })

  $('.grid').masonry({
    itemSelector: '.grid-item',
    percentPosition: true,
    //columnWidth: 280,
    gutter: 16,
    horizontalOrder: true
  });

  Fancybox.bind("[data-fancybox]", {
    // Your custom options
  });

})
