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
		var $position = $(event.currentTarget).data('position');

			this._proxy.setOption('data', {
				actionName: 'getContentTypes',
				className: 'cms\\data\\page\\PageAction',
				objectIDs: [ $pageID ],
				parameters: {
					position: $position
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

    _pageID: 0,

    init: function (pageID) {
        this._pageID = pageID;

        $('#setAsHome').click($.proxy(this._click, this));
    },

    _click: function () {
        WCF.System.Confirmation.show(WCF.Language.get('cms.acp.page.setAsHome.confirmMessage'), $.proxy(function (action) {
            if (action === 'confirm') {
                this._setAsHome();
            }
        }, this));
    },

    _setAsHome: function () {
        new WCF.Action.Proxy({
            autoSend: true,
            data: {
                actionName: 'setAsHome',
                className: 'cms\\data\\page\\PageAction',
                objectIDs: [ this._pageID ]
            },
            success: $.proxy(function (data, textStatus, jqXHR) {
                var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
                $notification.show();

                // remove button
                $('#setAsHome').parent().remove();

                // insert icon
                $headline = $('.boxHeadline > h1');
                $headline.html($headline.html() + ' ');
                $('<span class="icon icon16 icon-home jsTooltip" title="' + WCF.Language.get('cms.acp.page.homePage') + '" />').appendTo($headline);


                WCF.DOMNodeInsertedHandler.execute();
            }, this)
        });
    }
});


