let header = document.querySelector('.header');
let hamburger = document.querySelector('.hamburger');

hamburger.addEventListener('click', function(){
header.classList.toggle('menu-open')
});



let mybutton = document.getElementById("myBtn");
window.onscroll = function() {scrollFunction()};


function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      mybutton.style.display = "block";
    } else {
      mybutton.style.display = "none";
    }
  }

  function topFunction() {
    document.body.scrollTop = 0; 
    document.documentElement.scrollTop = 0; 
  }