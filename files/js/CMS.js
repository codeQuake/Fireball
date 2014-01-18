var CMS = {};

CMS.News = {};

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