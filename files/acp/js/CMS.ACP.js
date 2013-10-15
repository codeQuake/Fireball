
CMS.ACP = {};

CMS.ACP.Page = { };

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