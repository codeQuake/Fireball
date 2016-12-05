if (!Fireball) var Fireball = {};

Fireball.ACP = {};

Fireball.ACP.Page = {};

Fireball.ACP.Page.TypePicker = Class.extend({
	_proxy: null,
	_didInit: false,
	_objectTypeID: 0,
	_pageID: 0,
	
	init: function(objectTypeID, pageID) {
		if (this._didInit)
			return;

		this._objectTypeID = objectTypeID;
		this._pageID = pageID;
		
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		
//		if (this._objectTypeID != null && this._objectTypeID != undefined) {
//			this._click();
//		}
		
		this._buttons = $('#pageObjectTypeID');
		this._buttons.change($.proxy(this._click, this));
		
		this._didInit = true;
	},

	_click: function(event) {
		$objectTypeID = $('#pageObjectTypeID').val();
		
		this._proxy.setOption('data', {
			actionName: 'getTypeSpecificForm',
			className: 'cms\\data\\page\\PageAction',
			parameters: {
				'objectTypeID': $objectTypeID,
				'pageID' : this._pageID
			}
		});
		this._proxy.sendRequest();
	},
	
	_success: function(data, textStatus, jqXHR) {
		$('#specific').html(data.returnValues.template);
	}
});
