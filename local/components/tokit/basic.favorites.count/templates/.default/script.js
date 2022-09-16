document.addEventListener("DOMContentLoaded", () => {
	console.log('ENABLE JS FILE')
	document.addEventListener('favupdate', (e) => {
		if(e.detail.COUNT < 1) {
			document.querySelector('.footer_count_fav').classList.add('is-hide');
		} else {
			document.querySelector('.footer_count_fav').classList.remove('is-hide');
		}
		
		document.querySelector('.footer_count_fav').innerHTML = e.detail.COUNT;
	}, false);
});