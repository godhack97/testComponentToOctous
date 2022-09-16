BX.ready(function() {
	var popupContent = BX('DPD_pvz_popup');
		popupContent.style.display = 'block';

	var popup = new BX.PopupWindow('DPD_pvz_popup_window', null, {
			content: popupContent,
			closeIcon: {
				right: "20px",
				top: "10px"
			},
			zIndex: 0,
			offsetLeft: 0,
			offsetTop: 0,
			draggable: {
				restrict: false
			},
			width: Math.min(900, window.innerWidth - 80),
		});


	BX.bindDelegate(document, 'click', {className: 'DPD_openTerminalSelect'}, function(event) {
		var componentNode    = BX('DPD_pvz');
		var componentParams  = eval('('+ this.dataset.componentParams +')');
		var componentResult  = eval('('+ this.dataset.componentResult +')');
		var terminalCode     = BX('IPOLH_DPD_TERMINAL').value;

		popup.show();

		componentNode.DpdPickupMap.liveReload(componentParams, componentResult);
		componentNode.DpdPickupMap.highlightTerminal({code: terminalCode, highlightIcon: true});

		saveTerminalInfo(terminalCode);

		return BX.PreventDefault(event)
	});

	BX.addCustomEvent(BX('DPD_pvz'), 'dpdPickup:selectTerminal', function(terminalCode, needUpdate) {

		BX('IPOLH_DPD_TERMINAL').value = terminalCode;

		saveTerminalInfo(terminalCode);

		if (needUpdate !== false) {
			(typeof window.submitForm == "function") && setTimeout(submitForm, 500);
			(typeof window.submitFormProxy == "function") && setTimeout(submitFormProxy, 500);

			popup.close();
		}
	});

	function saveTerminalInfo(terminalCode)
	{
		var componentNode     = BX('DPD_pvz')
		var terminalFieldId   = BX('IPOLH_DPD_TERMINAL_FIELD_ID').value;
		var terminalFieldCode = BX('IPOLH_DPD_TERMINAL_FIELD_CODE').value;
		var terminalInfo      = componentNode.querySelector('.DPD_terminalSelect[data-terminal-code="'+ terminalCode +'"]');
		var terminalName      = terminalInfo ? terminalInfo.getAttribute('data-terminal-addr') +' ('+ terminalCode +')' : '';
		var terminalAddr      = terminalInfo ? terminalInfo.getAttribute('data-terminal-addr'): '';

		if (terminalAddr){
			var addr = document.getElementById('IPOLH_DPD_TERMINAL_ADDR');
			addr.innerText = terminalAddr;
		}

		var f1 = document.querySelector('*[name="ORDER_PROP_'+ terminalFieldId +'"]');
		var f2 = document.querySelector('*[name="ORDER_PROP_'+ terminalFieldCode +'"]');

		if (f1) {
			f1.value = terminalName;
			f1.setAttribute('readonly', 'readonly')
			f1.style.backgroundColor = '#eee';
		}

		if (f2) {
			f2.value = terminalName;
			f2.setAttribute('readonly', 'readonly')
			f2.style.backgroundColor = '#eee';
		}
	}
});