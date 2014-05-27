if (!CMS) var CMS = {};
CMS.ACP = {};

CMS.ACP.Page = {};

CMS.ACP.Page.AddForm = Class.extend({
    init: function () {
        $('#alias, #parentID').change($.proxy(this._buildAliasPreview, this));
        $('#title').change($.proxy(this._buildAlias, this));
        this._buildAliasPreview();
    },

    _buildAliasPreview: function() {
        var $aliasParent = $('#parentID option:selected').data('alias');
        var $alias = $('#alias').val();
        if ($alias != '') {
            $aliasPreview = window.location.origin + '/index.php/';
            if ($aliasParent != '') {
                $aliasPreview += $aliasParent + '/';
            }
            $aliasPreview += $alias + '/';
            $('#aliasPreview').html(WCF.Language.get('cms.acp.page.general.alias.preview') + ' ' +  $aliasPreview).show();
        }
        else { $('#aliasPreview').hide(); }
    },

    _buildAlias: function(){
        var $alias = $('#alias').val();
        //prevent alias from beeing overwritten
        if($alias == ''){
        	var $title = $('#title').val();
        	var $minus = [" ", "\\", "/", ":", ";", ".", "_", ","];
        	$minus.forEach(function(entry){
        		$title = $title.replace(entry, "-");
        	});

        	var $empty = ["{", "}", "[", "]", "&", "%", "$", "§", "\"", "!", "*", "'", "+", "#", "@", "<", ">", "|", "µ", "?", ")", "("];
        	$empty.forEach(function(entry){
        		$title = $title.replace(entry, "");
        	});

        	$title = $title.toLowerCase();

        	$('#alias').val($title);
        	this._buildAliasPreview();
        }
    }
});

CMS.ACP.Page.AddContent = Class.extend({

	_buttonSelector: '.jsContentAddButton',
	_proxy: null,
	_cache: {},
	_dialog: null,
	_didInit: false,

	init: function(){
		if (this._didInit) {
			return;
		}
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._addButtons = $('.jsContentAddButton');
		this._addButtons.click($.proxy(this._click, this));

		this._didInit = true;
	},


	_click: function(event){
		event.preventDefault();
		var $pageID = $(event.currentTarget).data('objectID');
		var $parentID = $(event.currentTarget).data('parentID');
		var $position = $(event.currentTarget).data('position');

			this._proxy.setOption('data', {
				actionName: 'getContentTypes',
				className: 'cms\\data\\page\\PageAction',
				objectIDs: [ $pageID ],
				parameters: {
					position: $position,
					parentID: $parentID
				}
			});
			this._proxy.sendRequest();
	},

	_show: function(pageID){
			this._dialog = $('<div id="contentAddDialog">' + this._cache[pageID] + '</div>').appendTo(document.body);
			this._dialog.wcfDialog();
	},

	_success: function(data, textStatus, jqXHR) {
		this._cache[data.returnValues.pageID] = data.returnValues.template;
		this._show(data.returnValues.pageID);
	}
});

CMS.ACP.Page.SetAsHome = Class.extend({

	_buttonSelector: '.jsSetAsHome',
	_proxy: null,
	_didInit: false,

    init: function () {
    	if (this._didInit) {
			return;
		}
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._buttons = $('.jsSetAsHome');
		this._buttons.click($.proxy(this._click, this));

		this._didInit = true;
    },

    _click: function (event) {
    	event.preventDefault();
		var $pageID = $(event.currentTarget).data('objectID');

			this._proxy.setOption('data', {
				actionName: 'setAsHome',
                className: 'cms\\data\\page\\PageAction',
                objectIDs: [ $pageID ]
			});
			WCF.LoadingOverlayHandler.updateIcon($(event.currentTarget));
			this._proxy.sendRequest();
    },

    _success: function (data, textStatus, jqXHR) {
    	var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
		var self = this;
		$notification.show(function() {
			window.location = location;
		});
    }

});

CMS.ACP.File = {};

CMS.ACP.File.Upload = WCF.Upload.extend({

	_folderID: 0,

	//calls parent init with params
	init: function(folderID, multiple) {
		var options = {
			action: 'upload',
			multiple: multiple,
			url: 'index.php/AJAXUpload/?t=' + SECURITY_TOKEN + SID_ARG_2ND
		};
		this._folderID = folderID;
		this._super($('#fileUploadButton'), $('.fileUpload ul'), 'cms\\data\\file\\FileAction', options);
	},

	_initFile: function(file) {
		return $('<li class="box32"><span class="icon icon32 icon-spinner" /><div><div><p>'+ file.name +'</p><small><progress max="100"></progress></small></div></div></li>').appendTo(this._fileListSelector);
	},

	_getParameters: function() {
		return {'folderID': this._folderID};
	},
	_success: function(uploadID, data) {
		var $li = this._fileListSelector.find('li');
		//remove progressbar
		$li.find('progress').remove();
		$.each(data.returnValues, function (key, value){
			if (value.fileID) {
				//remove spinner icon
				$li.children('.icon-spinner').removeClass('icon-spinner').addClass('icon-paperclip');

				// show noti
				var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
				$notification.show();
			} else {
				//add fail icon
				$li.children('.icon-spinner').removeClass('icon-spinner').addClass('icon-ban-circle');

				//err msg
				$li.find('div > div').append($('<small class="innerError">'+WCF.Language.get('cms.acp.file.error.' + data.returnValues.errorType)+'</small>'));
				$li.addClass('uploadFailed');
			}
			//webkit suxxx
			$li.css('display', 'block');
		});

		WCF.DOMNodeInsertedHandler.execute();
	},

	_error: function() {
		// FAIL!!
		var $listItem = this._fileListSelector.find('li');
		$listItem.addClass('uploadFailed').children('.icon-spinner').removeClass('icon-spinner').addClass('icon-ban-circle');
		$listItem.find('div > div').append($('<small class="innerError">'+WCF.Language.get('cms.acp.file.error.uploadFailed')+'</small>'));
	}

});

CMS.ACP.Content = {};

CMS.ACP.Content.Image = Class.extend({
	_cache: [],
	_dialog: null,
	_didInit: false,

	_button: null,
	_proxy: null,
	_field: null,

	init: function(button, field){
		this._button = button;
		this._field = field;
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success,this)
		});

		//add click event
		this._button.click($.proxy(this._click, this));
	},

	_click: function(event) {
		event.preventDefault();
		var $target = $(event.currentTarget);
		this.button = $target;

		if (this._dialog == null) {
			this._dialog = $('<div id="images" />').appendTo(document.body);

			this._proxy.setOption('data',{
				actionName: 'getImages',
				className: 'cms\\data\\file\\FileAction',
				parameters: {
					imageID: this._field.val()
				}
			});
			this._proxy.sendRequest();
		} else this._dialog.wcfDialog('open');
	},

	_success: function(data, textStatus, jqXHR) {

		if (this.didInit) {
			this.dialog.find('#images').html(data.returnValues.template);
			this._dialog.wcfDialog('render');
		}
		else {
			this._dialog.html(data.returnValues.template);
			this._dialog.wcfDialog({
				title: WCF.Language.get('cms.acp.content.type.de.codequake.cms.content.type.image.select')
			});
			this._didInit = true;
		}

		//find image & add click handler
		this._dialog.find('.jsFileImage').click($.proxy(this._imageSelect, this));
		//mark as active
		this._dialog.find('.jsFileImage[data-object-id="'+ this._field.val() +'"]').addClass('active');
	},

	_imageSelect: function(event) {
		var $image = $(event.currentTarget);

		this._field.val($image.data('objectID'));
		$('#width').val($image.data('width'));
		$('#height').val($image.data('height'));
		ratio = new CMS.ACP.Image.Ratio($image.data('width'), $image.data('height'));

		$image.clone().appendTo($('.image ul').html(''));

		this._dialog.wcfDialog('close');
	}
});


CMS.ACP.Image = {};

CMS.ACP.Image.Ratio = Class.extend({

	_ratio: 1,

	init: function(width, height) {
		this._ratio = width/height;
		 $('#width').change($.proxy(this._calculateHeight, this));
		 $('#height').change($.proxy(this._calculateWidth, this));
	},

	_calculateHeight: function() {
		var $width = $('#width');
		var height = $width.val() / this._ratio;
		$('#height').val(Math.round(height));
    },

    _calculateWidth: function() {
		var $height = $('#height');
		var width = $height.val() * this._ratio;
		$('#width').val(Math.round(width));
    }
});
