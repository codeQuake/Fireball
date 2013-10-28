CMS = {};

CMS.News = {};

CMS.News.IPAdressHandler = Class.extend({
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
        var $postID = $(event.currentTarget).data('newsID');

        if (this._cache[$postID]) {
            this._showDialog($newsID);
        }
        else {
            this._proxy.setOption('data', {
                actionName: 'getIpLog',
                className: 'cms\\data\\news\\NewsAction',
                parameters: {
                    postID: $newsID
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