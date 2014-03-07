CMS.ACP.Backup = { };
CMS.ACP.Backup.Import = Class.extend({
    _notification: null,
    _proxy: null,

    init: function () {
        this._proxy = new WCF.Action.Proxy({
            success: function () { this._notification.show(); }
        });
        $('.jsRestoreRow .jsRestoreButton').click($.proxy(this._click, this));

        this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'), 'success');
    },

    _click: function (event) {
        var $target = $(event.currentTarget);
        event.preventDefault();

        if ($target.data('confirmMessage')) {
            WCF.System.Confirmation.show($target.data('confirmMessage'), $.proxy(this._execute, this), { target: $target });
        }
        else {
            WCF.LoadingOverlayHandler.updateIcon($target);
            this._sendRequest($target);
        }
    },

    _execute: function (action, parameters) {
        if (action === 'cancel') {
            return;
        }

        WCF.LoadingOverlayHandler.updateIcon(parameters.target);
        this._sendRequest(parameters.target);
    },

    _sendRequest: function (object) {
        this._proxy.setOption('data', {
            actionName: 'importBackup',
            className: 'cms\\data\\restore\\RestoreAction',
            objectIDs: [$(object).data('objectID')]
        });

        this._proxy.sendRequest();
    }

   
});