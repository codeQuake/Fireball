if (!CMS) var CMS = {};
CMS.ACP = {};

CMS.ACP.Page = {};

CMS.ACP.Page.AddForm = Class.extend({
	init: function () {
		$('#alias, #parentID').change($.proxy(this._buildAliasPreview, this));
		this._buildAliasPreview();
	},

	_buildAliasPreview: function() {
		var $aliasParent = $('#parentID option:selected').data('alias');
		var $alias = $('#alias').val();
		if ($alias != '') {
			$aliasPreview = 'index.php/';
			if ($aliasParent != '' && typeof $aliasParent !== "undefined") {
				$aliasPreview += $aliasParent + '/';
			}
			$aliasPreview += $alias + '/';
			$('#aliasPreview').html(WCF.Language.get('cms.acp.page.alias.preview') + ' ' +  $aliasPreview).show();
		}
		else { $('#aliasPreview').hide(); }
	}
});

CMS.ACP.Page.Menu = Class.extend({
	init: function () {
		$('#menuItemParameters').change($.proxy(this._showNotice, this));
		$('#menuItemController').change($.proxy(this._showNotice, this));
		$('#menuItemParametersContainer > dd').append('<p id="cmsNoticeContainer" />');
		this._showNotice();
	},

	_showNotice: function () {
		if ($('#menuItemController').val() == 'cms\\page\\PagePage') {
			$('#cmsNoticeContainer').html('<small>' + WCF.Language.get('wcf.acp.pageMenu.parameters.notice') + '</small>');
		}
		else $('#cmsNoticeContainer').html("");
	}
}),

CMS.ACP.Page.AddContent = Class.extend({
	_proxy: null,
	_cache: {},
	_dialog: null,

	init: function() {
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		// bind events
		$('.jsContentAddButton').click($.proxy(this._click, this));
	},

	_click: function(event) {
		event.preventDefault();

		var $button = $(event.currentTarget);

		this._proxy.setOption('data', {
			actionName: 'getContentTypes',
			className: 'cms\\data\\page\\PageAction',
			objectIDs: [ $button.data('objectID') ],
			parameters: {
				position: $button.data('position'),
				parentID: $button.data('parentID')
			}
		});
		this._proxy.sendRequest();
	},

	_show: function(pageID) {
		this._dialog = $('<div id="contentAddDialog">' + this._cache[pageID] + '</div>').appendTo(document.body);
		this._dialog.wcfDialog({
			title: WCF.Language.get('cms.acp.content.add')
		});
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

CMS.ACP.File.Details = Class.extend({
	/**
	 * cache
	 * @var	object
	 */
	_cache: { },

	/**
	 * initialization state
	 * @var	boolean
	 */
	_didInit: false,

	/**
	 * proxy
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * Initializes file details handler.
	 */
	init: function() {
		if (!this._didInit) {
			this._didInit = true;

			this._proxy = new WCF.Action.Proxy({
				success: $.proxy(this._success, this)
			});

			WCF.DOMNodeInsertedHandler.addCallback('CMS.ACP.File.Details', $.proxy(this.init, this));
		}

		// bind events
		$('.jsFileDetails:not(.jsFileDetailsEnabled)').addClass('jsFileDetailsEnabled').click($.proxy(this._click, this));
	},

	/**
	 * Handles clicking upon a 'fileDetails' button
	 * 
	 * @param	object		event
	 */
	_click: function(event) {
		var $fileID = $(event.currentTarget).data('fileID');

		if (this._cache[$fileID] === undefined) {
			this._proxy.setOption('data', {
				actionName: 'getDetails',
				className: 'cms\\data\\file\\FileAction',
				objectIDs: [$fileID]
			});
			this._proxy.sendRequest();
		} else {
			this._cache[$fileID].wcfDialog('open');
		}
	},

	/**
	 * Handles successful AJAX responses.
	 * 
	 * @param	object		data
	 * @param	string		textStatus
	 * @param	jQuery		jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		this._cache[data.returnValues.fileID] = $(data.returnValues.template).hide().appendTo('body');
		this._cache[data.returnValues.fileID].wcfDialog({
			title: data.returnValues.title
		});
	}
});

CMS.ACP.File.Picker = Class.extend({
	/**
	 * id of the currently open category
	 * @var	integer
	 */
	_currentlyOpenCategory: 0,

	/**
	 * dialog object
	 * @var	jQuery
	 */
	_dialog: null,

	/**
	 * name of the input used to send selected files to server
	 * @var	string
	 */
	_inputName: '',

	/**
	 * Options for this file picker.
	 * 
	 * Supported options:
	 * - multiple: Indicates whether user only can select one or multiple
	 *             files.
	 * 
	 * @var	object
	 */
	_options: null,

	/**
	 * proxy
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * select button to open the picker
	 * @var	jQuery
	 */
	_selectButton: null,

	/**
	 * ids of selected files
	 * @var	array<integer>
	 */
	_selected: null,

	/**
	 * Initialises a new file picker.
	 * 
	 * @param	jQuery		selectButton
	 * @param	string		inputName
	 * @param	array		defaultValues
	 * @param	object		options
	 */
	init: function(selectButton, inputName, defaultValues, options) {
		this._selectButton = selectButton;
		this._inputName = inputName;
		this._selected = defaultValues || [ ];
		this._options = $.extend(true, {
			multiple: false,
			fileType: ''
		}, options);

		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		// bind form event to create input
		var $form = this._selectButton.parents('form');
		if (!$form.length) {
			console.log('[CMS.ACP.File.Picker] Unable to determine form for file picker, aborting.');
			return;
		}
		$form.submit($.proxy(this._submit, this));

		// bind select event
		this._selectButton.click($.proxy(this._openPicker, this));
	},

	/**
	 * Selects all given files.
	 * 
	 * @param	array<integer>	fileIDs
	 * @param	boolean		checkInput
	 */
	selectFiles: function(fileIDs, checkInput) {
		if (!this._options.multiple && fileIDs.length > 1) {
			console.log('[CMS.ACP.File.Picker] Selection of more than one file is not allowed, aborting.');
			return;
		}

		var fileID;
		for (var i = 0, length = fileIDs.length; i < length; i++) {
			fileID = fileIDs[i];

			if (this._selected.indexOf(fileID) !== -1) {
				this._selected.push(fileID);

				if (checkInput !== false) {
					this._dialog.find('tr[data-file-id="'+ fileID +'"]').find('input').prop('checked', true);
				}
			}
		}
	},

	/**
	 * Handles clicking upon a category in the category selection dropdown.
	 * 
	 * @param	object		event
	 */
	_dropdownCategoryClick: function(event) {
		var $categoryID = $(event.currentTarget).data('categoryID');
		this._openCategory($categoryID);
	},

	/**
	 * Handles clicking an input to select a file.
	 * 
	 * @param	object		event
	 */
	_inputClick: function(event) {
		var $input = $(event.currentTarget);

		if ($input.is(':checked')) {
			this._selected.push($input.val());
		} else {
			var $index = $.inArray($input.val(), this._selected);
			this._selected.splice($index, 1);
		}
	},

	/**
	 * Displays the file list of the selected category. In case the file
	 * list for that category wasn't loaded yet, the list will be fetched
	 * automatically.
	 * 
	 * @param	integer		categoryID
	 */
	_openCategory: function(categoryID) {
		var $tabularBox = this._dialog.find('.tabularBox[data-category-id="'+ categoryID +'"]');
		if ($tabularBox.length) {
			this._currentlyOpenCategory = categoryID;

			this._dialog.find('.tabularBox').hide();
			$tabularBox.show();

			// update dropdown
			$dropdownMenu = WCF.Dropdown.getDropdownMenu($('.filePickerCategoryDropdown').wcfIdentify());
			$dropdownMenu.find('li.active').removeClass('active');
			$dropdownMenu.find('li[data-category-id="'+ categoryID +'"]').addClass('active');

			// redraw dialog
			this._dialog.wcfDialog('render');
		} else {
			this._proxy.setOption('data', {
				actionName: 'getFileList',
				className: 'cms\\data\\file\\FileAction',
				parameters: {
					categoryID: categoryID,
					fileType: this._options.fileType
				}
			});
			this._proxy.sendRequest();
		}
	},

	/**
	 * Opens the picker when clicking on the select button
	 */
	_openPicker: function() {
		if (this._dialog === null) {
			this._proxy.setOption('data', {
				actionName: 'getFileList',
				className: 'cms\\data\\file\\FileAction',
				parameters: {
					fileType: this._options.fileType
				}
			});
			this._proxy.sendRequest();
		} else {
			this._dialog.wcfDialog('open');
		}
	},

	/**
	 * Removes caches of all already loaded categories and reload currently
	 * open category.
	 */
	_reload: function() {
		this._dialog.find('.tabularBox').remove();
		this._openCategory(this._currentlyOpenCategory);
	},

	/**
	 * Handles submitting the form of this file picker.
	 * 
	 * @param	object		event
	 */
	_submit: function(event) {
		var $form = $(event.currentTarget);

		if (this._options.multiple) {
			for (var i = 0, length = this._selected.length; i < length; i++) {
				var $fileID = this._selected[i];
				$('<input type="hidden" name="'+ this._inputName +'[]" value="'+ $fileID +'" />').appendTo($form);
			}
		} else {
			var $fileID = this._selected.pop();
			$('<input type="hidden" name="'+ this._inputName +'" value="'+ $fileID +'" />').appendTo($form);
		}
	},

	/**
	 * Handles successful AJAX responses.
	 * 
	 * @param	object		data
	 * @param	string		textStatus
	 * @param	jQuery		jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		if (this._dialog === null) {
			// first call, init dialog
			this._dialog = $(data.returnValues.template).hide();
			this._dialog.find('.filePickerCategoryDropdown li').click($.proxy(this._dropdownCategoryClick, this));

			this._currentlyOpenCategory = data.returnValues.categoryID;

			this._dialog.appendTo('body');
			this._dialog.wcfDialog({
				title: data.returnValues.title
			});

			CMS.ACP.File.Upload.init($.proxy(this._uploadCallback, this));
		} else {
			// loaded new category data
			$(data.returnValues.template).hide().appendTo(this._dialog);
			this._openCategory(data.returnValues.categoryID);
		}

		// handle checkbox/radiobox
		this._dialog.find('td.columnMark').each($.proxy(function(index, td) {
			var $td = $(td),
			    $fileID = $td.parent().data('fileID'),
			    $input;

			if (this._options.multiple) {
				$input = $('<input type="checkbox" name="'+ this._inputName +'Picker[]" value="'+ $fileID +'" />').appendTo($td);
			} else {
				$input = $('<input type="radio" name="'+ this._inputName +'Picker" value="'+ $fileID +'" />').appendTo($td);
			}

			// handle default values
			if (WCF.inArray($fileID, this._selected)) {
				$input.prop('checked', true);
			}

			// bind click event
			$input.click($.proxy(this._inputClick, this));
		}, this));
	},

	/**
	 * Handles successful uploads of new files. Reloads already loaded file
	 * lists and automatically selects uploaded files.
	 * 
	 * @param	array		fileIDs
	 */
	_uploadCallback: function(fileIDs) {
		if (this._options.multiple || fileIDs.length == 1) {
			this.selectFiles(fileIDs, false);
		}

		this._reload();
	}
});

CMS.ACP.File.Upload = {
	/**
	 * callback executed after submitting the upload form.
	 * @var	function
	 */
	_afterSubmit: null,

	/**
	 * category input object
	 * @var	jQuery
	 */
	_categoryInput: null,

	/**
	 * dialog overlay
	 * @var	jQuery
	 */
	_dialog: null,

	/**
	 * list of ids of files that where uploaded successfully
	 * @var	array
	 */
	_fileIDs: null,

	/**
	 * action proxy
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * submit button
	 * @var	jQuery
	 */
	_submitButton: null,

	/**
	 * Initializes the file upload system.
	 * 
	 * @param	function		afterSubmit
	 */
	init: function(afterSubmit) {
		this._afterSubmit = afterSubmit || null;
		this._fileIDs = [ ];

		this._proxy = new WCF.Action.Proxy();

		$('.jsFileUploadButton').click($.proxy(this._openDialog, this));
	},

	/**
	 * Adds a file to the uploaded file list
	 * 
	 * @param	integer		fileID
	 */
	addFile: function(fileID) {
		this._fileIDs.push(fileID);
		this._handleButtonState();
	},

	/**
	 * Redraws the dialog.
	 */
	redraw: function() {
		if (this._dialog !== null) {
			this._dialog.wcfDialog('render');
		}
	},

	/**
	 * Finalize upload of new files by assigning the uploaded files to the
	 * selected categories once the user submits the form.
	 * 
	 * @param	object		event
	 */
	_finalizeUpload: function(event) {
		event.preventDefault();

		if (!this._fileIDs.length) {
			console.log('[CMS.ACP.File.Upload] Tried to finalize upload though no files where uploaded, aborting.');
			return;
		}

		var $categoryIDs = this._categoryInput.val();
		if ($categoryIDs === null) {
			console.log('[CMS.ACP.File.Upload] Tried to finalize upload without a selected category, aborting.');
			return;
		}

		this._submitButton.attr('disabled', 'disabled');

		// send request
		this._proxy.setOption('data', {
			actionName: 'update',
			className: 'cms\\data\\file\\FileAction',
			objectIDs: this._fileIDs,
			parameters: {
				categoryIDs: $categoryIDs
			}
		});
		this._proxy.setOption('success', $.proxy(function() {
			// destroy dialog
			this._dialog.wcfDialog('close');
			new WCF.PeriodicalExecuter($.proxy(function(pe) {
				this._dialog.parents('.dialogContainer').remove();
				this._dialog = null;

				pe.stop();
			}, this), 200);

			if ($.isFunction(this._afterSubmit)) {
				this._afterSubmit(this._fileIDs);
			}
		}, this));
		this._proxy.sendRequest();
	},

	/**
	 * Activates the submit button once files where uploaded and a category
	 * specified.
	 */
	_handleButtonState: function() {
		if (!this._fileIDs.length) {
			// no files uploaded
			this._submitButton.attr('disabled', 'disabled');
			return;
		}

		if (this._categoryInput.val() === null) {
			// no category specified
			this._submitButton.attr('disabled', 'disabled');
			return;
		}

		// everything fine, activate button
		this._submitButton.removeAttr('disabled');
	},

	/**
	 * Opens the dialog to upload files.
	 */
	_openDialog: function() {
		if (this._dialog === null) {
			this._proxy.setOption('data', {
				actionName: 'getUploadDialog',
				className: 'cms\\data\\file\\FileAction'
			});
			this._proxy.setOption('success', $.proxy(this._openDialogResponse, this));
			this._proxy.sendRequest();
		} else {
			this._dialog.wcfDialog('render');
		}
	},

	/**
	 * Handles successful AJAX responses to open the dialog.
	 * 
	 * @param	object		data
	 * @param	string		textStatus
	 * @param	jQuery		jqXHR
	 */
	_openDialogResponse: function(data, textStatus, jqXHR) {
		this._dialog = $(data.returnValues.template).hide().appendTo('body');

		this._categoryInput = $('#categoryIDs');
		this._categoryInput.change($.proxy(this._handleButtonState, this));

		this._submitButton = $('#fileUploadSubmitButton');
		this._submitButton.click($.proxy(this._finalizeUpload, this));

		// init upload handler
		new CMS.ACP.File.Upload.Handler();

		this._dialog.wcfDialog({
			title: data.returnValues.title
		});
	}
};

CMS.ACP.File.Upload.Handler = WCF.Upload.Parallel.extend({
	/**
	 * @see	WCF.Upload.init()
	 */
	init: function(afterSubmit) {
		this._super($('#fileUploadButton'), $('.fileUpload ul'), 'cms\\data\\file\\FileAction');
	},

	/**
	 * @see	WCF.Upload._error()
	 */
	_error: function(jqXHR, textStatus, errorThrown) {
		// there is no really something we can do here other than
		// creating a log entry
		console.log(jqXHR, textStatus, errorThrown);
	},

	/**
	 * @see	WCF.Upload._initFile()
	 */
	_initFile: function(file) {
		$li = $('<li class="box32"><span class="icon icon32 icon-spinner" /><div><div><p>'+ file.name +'</p><small><progress max="100"></progress></small></div></div></li>').appendTo(this._fileListSelector);

		// redraw dialog
		CMS.ACP.File.Upload.redraw();

		return $li;
	},

	/**
	 * @see	WCF.Upload._success()
	 */
	_success: function(internalFileID, data) {
		var $li = this._uploadMatrix[internalFileID];

		// remove progressbar
		$li.find('progress').remove();

		if (data.returnValues.files[internalFileID]) {
			var $fileData = data.returnValues.files[internalFileID];
			CMS.ACP.File.Upload.addFile($fileData.fileID);

			// remove spinner icon
			$li.children('.icon-spinner').removeClass('icon-spinner').addClass('icon-paperclip');

			// update file size
			$li.find('small').append($fileData.formattedFilesize);
		} else {
			var $errorType = 'uploadFailed';
			if (data.returnValues.errors[internalFileID]) {
				$errorType = data.returnValues.errors[internalFileID].errorType;
			}

			// add fail icon
			$li.children('.icon-spinner').removeClass('icon-spinner').addClass('icon-ban-circle');

			// error message
			$li.find('div > div').append($('<small class="innerError">' + WCF.Language.get('cms.acp.file.error.' + $errorType) + '</small>'));
			$li.addClass('uploadFailed');
		}

		// webkit suxxx
		$li.css('display', 'block');

		// redraw dialog
		CMS.ACP.File.Upload.redraw();

		WCF.DOMNodeInsertedHandler.execute();
	}
});

CMS.ACP.Content = {};
CMS.ACP.Content.Image = {};

CMS.ACP.Content.Image.Gallery = Class.extend({
	_cache: [],
	_dialog: null,
	_didInit: false,

	_button: null,
	_proxy: null,
	_field: null,

	init: function(button, field){
		this._button = button;
		this._field = field;
		if (field.val() != 0 && field.val() != '') var length = field.val().split(',').length;
		else length = 0;
		$('#imageSelect').append('<span id="imagesBadge" class="badge green">'+length+'</span>');
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
				title: WCF.Language.get('cms.acp.content.type.de.codequake.cms.content.type.gallery.select')
			});
			this._dialog.wcfDialog('render');
			this._didInit = true;
		}

		//find image & add click handler
		this._dialog.find('.jsFileImage').click($.proxy(this._imageSelect, this));
		var dialog = this._dialog;
		var value = this._field.val();
		value = value.split(",");
		$.each(value, function(item, element){
			dialog.find('.jsFileImage[data-object-id="'+ element +'"]').addClass('active');
		});
	},

	_imageSelect: function(event) {
		var $image = $(event.currentTarget);
		if (!$image.hasClass('active')) {
			$image.addClass('active');
			var temp = this._field.val();
			if (temp != '' && temp != 0) this._field.val(temp + ',' + $image.data('objectID'));
			else this._field.val($image.data('objectID'));
		}
		else {
			$image.removeClass('active');
			var temp = this._field.val();
			temp = temp.split(",");
			$.each(temp, function(index, element){
				if (element == $image.data('objectID')) temp.splice(index,1);
			});
			temp = temp.join();
			this._field.val(temp);
		}


		$('#imagesBadge').html(this._field.val().split(',').length);
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

CMS.ACP.Page.Revisions = Class.extend({
	_proxy: null,
	_cache: {},
	_dialog: null,
	_didInit: false,

	init: function() {
		if (this._didInit)  {
			return;
		}

		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._buttons = $('.jsRevisionsButton');
		this._buttons.click($.proxy(this._click, this));

		new WCF.Action.Delete('cms\\data\\page\\revision\\PageRevisionAction', '.jsRevisionRow');

		this._didInit = true;
	},

	_click: function(event){
		event.preventDefault();
		var $pageID = $(event.currentTarget).data('objectID');

		this._proxy.setOption('data', {
			actionName: 'getRevisions',
			className: 'cms\\data\\page\\PageAction',
			objectIDs: [ $pageID ]
		});
		this._proxy.sendRequest();
	},

	_show: function(pageID){
		this._dialog = $('<div id="revisionDialog">' + this._cache[pageID] + '</div>').appendTo(document.body);
		this._dialog.wcfDialog({
			title: WCF.Language.get('cms.acp.page.revision.list')
		});
	},

	_success: function(data, textStatus, jqXHR) {
		this._cache[data.returnValues.pageID] = data.returnValues.template;
		this._show(data.returnValues.pageID);
	}
});

CMS.ACP.Page.Revisions.Restore = Class.extend({
	_proxy: null,
	_didInit:false,

	init: function () {
		if (this._didInit) {
			return;
		}
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._buttons = $('.jsRestoreRevisionButton');
		this._buttons.click($.proxy(this._click, this));

		this._didInit = true;
	},

	_click: function (event) {
		event.preventDefault();
		var $target = $(event.currentTarget);

		if ($target.data('confirmMessage')) {
			WCF.System.Confirmation.show($target.data('confirmMessage'), $.proxy(this._execute, this), { target: $target });
		}
		else {
			WCF.LoadingOverlayHandler.updateIcon($target);
			this._sendRequest($target);
		}
	},

	_sendRequest: function (object) {
		$pageID = $(object).data('pageID');
		$versionID = $(object).data('objectID');
		this._proxy.setOption('data', {
			actionName: 'restoreRevision',
			className: 'cms\\data\\page\\PageAction',
			objectIDs: [ $pageID ],
			parameters: {
				'restoreObjectID': $versionID
			}
		});
		this._proxy.sendRequest();
	},

	_execute: function (action, parameters) {
		if (action === 'cancel') {
			return;
		}

		WCF.LoadingOverlayHandler.updateIcon(parameters.target);
		this._sendRequest(parameters.target);
	},

	_success: function (data, textStatus, jqXHR) {
		var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
		$notification.show(function() {
			window.location = location;
		});
	}
});

CMS.ACP.Content.Revisions = Class.extend({
	_proxy: null,
	_cache: {},
	_dialog: null,
	_didInit: false,

	init:function(){
		if (this._didInit)  {
			return;
		}
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._buttons = $('.jsRevisionsButton');
		this._buttons.click($.proxy(this._click, this));

		this._didInit = true;
	},

	_click: function(event){
		event.preventDefault();
		var $contentID = $(event.currentTarget).data('objectID');

			this._proxy.setOption('data', {
				actionName: 'getRevisions',
				className: 'cms\\data\\content\\ContentAction',
				objectIDs: [ $contentID ]
			});
			this._proxy.sendRequest();
	},

	_show: function(contentID){
			this._dialog = $('<div id="revisionDialog">' + this._cache[contentID] + '</div>').appendTo(document.body);
			this._dialog.wcfDialog({
				title: WCF.Language.get('cms.acp.content.revision.list')
			});
	},

	_success: function(data, textStatus, jqXHR) {
		this._cache[data.returnValues.contentID] = data.returnValues.template;
		this._show(data.returnValues.contentID);
	}
});

CMS.ACP.Content.Revisions.Restore = Class.extend({
	_proxy: null,
	_didInit:false,

	init: function () {
		if (this._didInit) {
			return;
		}
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._buttons = $('.jsRestoreRevisionButton');
		this._buttons.click($.proxy(this._click, this));

		this._didInit = true;
	},

	_click: function (event) {
		event.preventDefault();
		var $target = $(event.currentTarget);

		if ($target.data('confirmMessage')) {
			WCF.System.Confirmation.show($target.data('confirmMessage'), $.proxy(this._execute, this), { target: $target });
		}
		else {
			WCF.LoadingOverlayHandler.updateIcon($target);
			this._sendRequest($target);
		}
	},

	_sendRequest: function (object) {
		$contentID = $(object).data('contentID');
		$versionID = $(object).data('objectID');
		this._proxy.setOption('data', {
			actionName: 'restoreRevision',
			className: 'cms\\data\\content\\ContentAction',
			objectIDs: [ $contentID ],
			parameters: {
				'restoreObjectID': $versionID
			}
		});
		this._proxy.sendRequest();
	},

	_execute: function (action, parameters) {
		if (action === 'cancel') {
			return;
		}

		WCF.LoadingOverlayHandler.updateIcon(parameters.target);
		this._sendRequest(parameters.target);
	},

	_success: function (data, textStatus, jqXHR) {
		var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
		$notification.show(function() {
			window.location = location;
		});
	}
});

CMS.ACP.Copy = Class.extend({
	_buttonSelector: '.jsCopyButton',
	_objectAction: 'cms\\data\\page\\PageAction',
	_proxy: null,
	_didInit: false,

	init: function (buttonSelector, objectAction) {
		if (this._didInit) {
			return;
		}
		this._buttonSelector = buttonSelector;
		this._objectAction = objectAction;
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._buttons = $(this._buttonSelector);
		this._buttons.click($.proxy(this._click, this));

		this._didInit = true;
	},

	_click: function (event) {
		event.preventDefault();
		var $objectID = $(event.currentTarget).data('objectID');

			this._proxy.setOption('data', {
				actionName: 'copy',
				className: this._objectAction,
				objectIDs: [ $objectID ]
			});
			WCF.LoadingOverlayHandler.updateIcon($(event.currentTarget));
			this._proxy.sendRequest();
	},

	_success: function (data, textStatus, jqXHR) {
		var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
		$notification.show(function() {
			location.reload();
		});
	}
});
