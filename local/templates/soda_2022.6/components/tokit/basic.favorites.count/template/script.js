document.addEventListener("DOMContentLoaded", () => {

  document.addEventListener('favupdate', (e) => {
    if(e.detail.COUNT < 1) {
      //document.querySelector('.favorites-head').classList.remove('active');
      document.querySelector('.favorites-head .favorites-count').classList.add('is-hide');
    } else {
      //document.querySelector('.favorites-head').classList.add('active');
      document.querySelector('.favorites-head .favorites-count').classList.remove('is-hide');
    }

    document.querySelector('.favorites-head .favorites-count').innerHTML = e.detail.COUNT;
  }, false);

}); 