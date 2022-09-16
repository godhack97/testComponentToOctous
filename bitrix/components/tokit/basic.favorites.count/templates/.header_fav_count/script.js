document.addEventListener("DOMContentLoaded", () => {
	console.log('ENABLE JS FILE')
	document.addEventListener('favupdate', (e) => {
		if(e.detail.COUNT < 1) {
			document.querySelector('.o-fav-link-count_custom').classList.add('is-hide');
		} else {
			document.querySelector('.o-fav-link-count_custom').classList.remove('is-hide');
		}
		
		document.querySelector('.o-fav-link-count_custom').innerHTML = e.detail.COUNT;
	}, false);
});