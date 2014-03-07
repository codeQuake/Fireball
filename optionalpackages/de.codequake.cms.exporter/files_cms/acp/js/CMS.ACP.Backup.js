CMS.ACP.Backup = { };
CMS.ACP.Backup.Import = Class.extend({
    _notification: null,
    _proxy: null,
    _target: null,

    init: function () {
        this._notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'), 'success');
        this._proxy = new WCF.Action.Proxy({
            success: $.proxy(this._success, this)
        });
        $('.jsRestoreRow .jsRestoreButton').click($.proxy(this._click, this));

        
    },
    _success: function () {
        this._notification.show();
        WCF.LoadingOverlayHandler.updateIcon(this._target, false);
        this._target = null;
    },
    _click: function (event) {
        this._target = $(event.currentTarget);
        event.preventDefault();

        if (this._target.data('confirmMessage')) {
            WCF.System.Confirmation.show(this._target.data('confirmMessage'), $.proxy(this._execute, this), { target: this._target });
        }
        else {
            WCF.LoadingOverlayHandler.updateIcon(this._target);
            this._sendRequest(this._target);
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