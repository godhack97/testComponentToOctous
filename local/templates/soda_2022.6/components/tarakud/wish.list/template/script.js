(function (window){
	'use strict';

	if (window.CWishlist)
		return;
	
	window.CWishlist = function ()
	{
		this.ajaxSend = false;
		this.init();
	}
	
	window.CWishlist.prototype = {
		init: function()
		{
			let items = document.querySelectorAll('.js-wish-delete');
	
			for (let item of items) {
				BX.bind(item, 'click', function(e) {
					e.preventDefault();
				
					let id = parseInt(BX.data(item, 'id'));
					let url = item.getAttribute('href');
					
					if (id > 0 && !this.ajaxSend) {
						this.ajaxSend = true;
						BX.ajax({
							url: url,
							method: "post",
							dataType: "json",
							data: {
								'action': 'delete',
								sessid: BX.bitrix_sessid()
							},
							onsuccess(result) {
								this.ajaxSend = false;
								if (result.status === "success") {
									let hide  = document.querySelector(".js-wishitem-" + id);
									BX.remove(hide);
									
									BX.onCustomEvent(this, 'onAfterWishlistUpdate', [{
										iblockId: result.iblockId,
										id: id,
										target: this,
										status: "del",
										count: result.count
									}]);
								}
							},
							onfailure: function() {
								this.ajaxSend = false;
								console.log("Error!");
							}
						});
					}
				});
			}
		}
	}
	
})(window);