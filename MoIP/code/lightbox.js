window.addEvent('domready', function(){
  SexyLightbox = new SexyLightBox({
    find          : 'sexylightbox', // rel="sexylightbox"
    color         : 'white',
    dir           : 'code/images/',
    emergefrom    : 'bottom',
    OverlayStyles : {
      'background-color': '#000000',
      'opacity' : 0.6
    }
  });
});