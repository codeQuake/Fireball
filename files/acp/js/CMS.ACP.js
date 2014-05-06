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
	_dialogContent: '',
	_contentTypes: [],

	init: function(){
		this._addButtons = $('.jsContentAddButton');
		this._addButtons.click($.proxy(this._click, this));
	},

	_register: function(identifier, options){
		this._contentTypes[identifier] = options;
		console.log(identifier);
	},

	_click: function(event){
		var $target = $(event.currentTarget);
		event.preventDefault();
		if(!this._dialogContent){
			this._dialogContent = $('<div id="contentAddDialog"></div>');
			$fieldset = $('<fieldset><legend>' + WCF.Language.get('cms.acp.content.contenttypes') + '</legend></fieldset>').appendTo(this._dialogContent);
			$list = $('<ul class="tripleColumned"></ul>').appendTo($fieldset);

			for(var identifier in this._contentTypes){
				var current = this._contentTypes[identifier];
				var $listItem = $('<li><span class="icon icon16 ' + current.icon + '"></span> ' + WCF.Language.get('cms.content.type.' + identifier) + '</li>').appendTo($list);
			}
		}

		$dialog = this._dialogContent.clone().appendTo(document.body);
		$dialog.wcfDialog();


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


CMS.ACP.Content = {};

CMS.ACP.Content.Preview = Class.extend({
    _objectType: '',
    _proxy: null,

    init: function (objectType) {
        this._objectType = objectType;

        $('#previewButton').click($.proxy(this._click, this));

    },
    _click: function (event) {
        $('#previewContainer').hide();
        var $preview = '';
        var $find = $('#sectionData');
        var $content = $find.val();
        switch (this._objectType) {
            case 'de.codequake.cms.section.type.headline':
                $preview = '<div class="' + $('#cssClasses').val() + '"><' + $('#hlType').val() + '>' + $content + '</' + $('#hlType').val() + '></div>';
                break;
            case 'de.codequake.cms.section.type.link':
                if ($('#type').val() == 1) {
                    $preview = '<div class="' + $('#cssClasses').val() + '"><a href="' + $('#hyperlink').val() + '" class="button">' + $content + '</a></div>';
                }
                else if ($('#type').val() == 2) {
                    $preview = '<div class="' + $('#cssClasses').val() + '"><a href="' + $('#hyperlink').val() + '" class="button small">' + $content + '</a></div>';
                }
                else {
                    $preview = '<div class="' + $('#cssClasses').val() + '"><a href="' + $('#hyperlink').val() + '">' + $content + '</a></div>';
                }
                break;
            case 'de.codequake.cms.section.type.file':
                $preview = '<div class="' + $('#cssClasses').val() + '"><div class="box32"><span class="icon icon32 icon-paper-clip"></span><div><p>' + $('#sectionData option:selected').text() + '</p><small>1.337 kB, <strong>42 Downloads</strong></small></div></div></div>';
                break;
        }
        $previewContainer = $('<div class="container containerPadding marginTop" id="previewContainer"><fieldset><legend>' + WCF.Language.get('wcf.global.preview') + '</legend><div></div></fieldset>').prependTo($('#formContainer')).wcfFadeIn();
        $previewContainer.find('div:eq(0)').html($preview);

        new WCF.Effect.Scroll().scrollTo($previewContainer);
    },

});
