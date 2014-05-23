var CMS = {};

CMS.News = {};

CMS.News.Image = {};

CMS.News.Image.Form = Class.extend({
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
			this._dialog = $('<div id="newsImages" />').appendTo(document.body);

			this._proxy.setOption('data',{
				actionName: 'getImages',
				className: 'cms\\data\\news\\image\\NewsImageAction',
				parameters: {
					imageID: this._field.val()
				}
			});
			this._proxy.sendRequest();
		} else this._dialog.wcfDialog('open');
	},

	_success: function(data, textStatus, jqXHR) {

		if (this.didInit) {
			this.dialog.find('#newsImages').html(data.returnValues.template);
			this._dialog.wcfDialog('render');
		}
		else {
			this._dialog.html(data.returnValues.template);
			this._dialog.wcfDialog({
				title: WCF.Language.get('cms.news.image.select')
			});
			upload = new CMS.News.Image.Upload();
			this._didInit = true;
		}

		//find image & add click handler
		this._dialog.find('.jsNewsImage').click($.proxy(this._imageSelect, this));
		//mark as active
		this._dialog.find('.jsNewsImage[data-object-id="'+ this._field.val() +'"]').addClass('active');
	},

	_imageSelect: function(event) {
		var $image = $(event.currentTarget);

		this._field.val($image.data('objectID'));

		$image.clone().appendTo($('.newsImage ul').html(''));

		this._dialog.wcfDialog('close');
	}
});

CMS.News.Image.Upload = WCF.Upload.extend({

	//calls parent init with params
	init: function() {
		this._super($('#imageUploadButton'), $('.imageUpload ul'), 'cms\\data\\news\\image\\NewsImageAction');
	},

	_initFile: function(file) {
		this._fileListSelector.children('li').remove();
		var $listitem = $('<li class="box32"><span class="icon icon32 icon-spinner" /><div><div><p>'+ file.name +'</p><small><progress max="100"></progress></small></div></div></li>');

		this._fileListSelector.append($listitem);
		this._fileListSelector.show();
		return $listitem;
	},

	_success: function(uploadID, data) {
		var $li = this._fileListSelector.find('li');
		//remove progressbar
		$li.find('progress').remove();
		if (data.returnValues.imageID) {
			//remove spinner icon
			$li.children('.icon-spinner').remove();
			//show img
			$li.prepend('<div class="framed"><img src="'+ data.returnValues.url +'" alt="" style="width: 32px; max-height: 32px" /></div>');

			$('<div class="framed"><img src="'+ data.returnValues.url +'" alt="" style="width: 32px; max-height: 32px" /></div>').appendTo($('.newsImage ul').html(''));
			// save upload id
			$('#imageID').val(data.returnValues.imageID);

			// show noti
			var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
			$notification.show();
		} else {
			//add fail icon
			$li.children('.icon-spinner').removeClass('icon-spinner').addClass('icon-ban-circle');

			//err msg
			$li.find('div > div').append($('<small class="innerError">'+WCF.Language.get('cms.news.image.error.' + data.returnValues.errorType)+'</small>'));
			$li.addClass('uploadFailed');
		}
		//webkit suxxx
		$li.css('display', 'block');

		WCF.DOMNodeInsertedHandler.execute();
	},

	_error: function() {
		// FAIL!!
		var $listItem = this._fileListSelector.find('li');
		$listItem.addClass('uploadFailed').children('.icon-spinner').removeClass('icon-spinner').addClass('icon-ban-circle');
		$listItem.find('div > div').append($('<small class="innerError">'+WCF.Language.get('cms.news.image.error.uploadFailed')+'</small>'));
	}
});

CMS.News.MarkAllAsRead = Class.extend({
    _proxy: null,

    init: function () {
        // initialize proxy
        this._proxy = new WCF.Action.Proxy({
            success: $.proxy(this._success, this)
        });
        //add clickhandler
        $('.markAllAsReadButton').click($.proxy(this._click, this));
    },

    _click: function () {
        this._proxy.setOption('data', {
            actionName: 'markAllAsRead',
            className: 'cms\\data\\news\\NewsAction'
        });

        this._proxy.sendRequest();
    },

    _success: function (data, textStatus, jqXHR) {
        //hide unread messages
        $('#mainMenu .active .badge').hide();
        $('.newMessageBadge').hide();
    }
});

CMS.News.Preview = WCF.Popover.extend({
    /**
	 * action proxy
	 * @var	WCF.Action.Proxy
	 */
    _proxy: null,

    /**
	 * list of links
	 * @var	object
	 */
    _newss: {},

    /**
	 * @see	WCF.Popover.init()
	 */
    init: function () {
        this._super('.newsLink');

        this._proxy = new WCF.Action.Proxy({
            showLoadingOverlay: false
        });
        WCF.DOMNodeInsertedHandler.addCallback('CMS.News.Preview', $.proxy(this._initContainers, this));
    },

    /**
	 * @see	WCF.Popover._loadContent()
	 */
    _loadContent: function () {
        var $news = $('#' + this._activeElementID);

        this._proxy.setOption('data', {
            actionName: 'getNewsPreview',
            className: 'cms\\data\\news\\NewsAction',
            objectIDs: [$news.data('newsID')]
        });

        var $elementID = this._activeElementID;
        var self = this;
        this._proxy.setOption('success', function (data, textStatus, jqXHR) {
            self._insertContent($elementID, data.returnValues.template, true);
        });
        this._proxy.sendRequest();


    }
});

CMS.News.Like = WCF.Like.extend({

    _getContainers: function () {
        return $('article.message');
    },

    _getObjectID: function (containerID) {
        return this._containers[containerID].data('newsID');
    },

    _buildWidget: function (containerID, likeButton, dislikeButton, badge, summary) {
        var $widgetContainer = this._getWidgetContainer(containerID);
        if (this._canLike) {
            var $smallButtons = this._containers[containerID].find('.smallButtons');
            likeButton.insertBefore($smallButtons.find('.toTopLink'));
            dislikeButton.insertBefore($smallButtons.find('.toTopLink'));
            dislikeButton.find('a').addClass('button');
            likeButton.find('a').addClass('button');
        }

        if (summary) {
            summary.appendTo(this._containers[containerID].find('.messageBody > .messageFooter'));
            summary.addClass('messageFooterNote');
        }
        $widgetContainer.find('.permalink').after(badge);
    },


    _getWidgetContainer: function (containerID) {
        return this._containers[containerID].find('.messageHeader');
    },

    _addWidget: function (containerID, widget) { },

    _setActiveState: function(likeButton, dislikeButton, likeStatus) {
    likeButton = likeButton.find('.button').removeClass('active');
    dislikeButton = dislikeButton.find('.button').removeClass('active');

    if (likeStatus == 1) {
        likeButton.addClass('active');
    }
    else if (likeStatus == -1) {
        dislikeButton.addClass('active');
    }
},


});
CMS.News.IPAddressHandler = Class.extend({
    _cache: {},
    _dialog: null,
    _proxy: null,

    init: function () {
        this._cache = {};
        this._dialog = null;
        this._proxy = new WCF.Action.Proxy({
            success: $.proxy(this._success, this)
        });

        this._initButtons();

        WCF.DOMNodeInsertedHandler.addCallback('CMS.News.IPAddressHandler', $.proxy(this._initButtons, this));
    },

    _initButtons: function () {
        var self = this;
        $('.jsIpAddress').each(function (index, button) {
            var $button = $(button);
            var $newsID = $button.data('newsID');

            if (self._cache[$newsID] === undefined) {
                self._cache[$newsID] = '';
                $button.click($.proxy(self._click, self));
            }
        });
    },

    _click: function (event) {
        var $newsID = $(event.currentTarget).data('newsID');

        if (this._cache[$newsID]) {
            this._showDialog($newsID);
        }
        else {
            this._proxy.setOption('data', {
                actionName: 'getIpLog',
                className: 'cms\\data\\news\\NewsAction',
                parameters: {
                    newsID: $newsID
                }
            });
            this._proxy.sendRequest();
        }
    },

    _success: function (data, textStatus, jqXHR) {
        // cache template
        this._cache[data.returnValues.newsID] = data.returnValues.template;

        // show dialog
        this._showDialog(data.returnValues.newsID);
    },


    _showDialog: function (newsID) {
        if (this._dialog === null) {
            this._dialog = $('<div id="newsIpAddressLog" />').hide().appendTo(document.body);
        }

        this._dialog.html(this._cache[newsID]);
        this._dialog.wcfDialog({
            title: WCF.Language.get('cms.news.ipAddress.title')
        });
        this._dialog.wcfDialog('render');
    }
});
