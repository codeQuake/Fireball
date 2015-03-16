/**
 * Class and function collection for cms acp.
 * 
 * @author	Jens Krumsieck, Florian Frantzen
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
if (!CMS) var CMS = { };

/**
 * Initialize CMS.ACP namespace
 */
CMS.ACP = { };

/**
 * Initialize CMS.ACP.Content namespace
 */
CMS.ACP.Content = { };

/**
 * Shows a dialog to add a new content to a page.
 */
CMS.ACP.Content.AddDialog = Class.extend({
	_proxy: null,
	_cache: { },
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

/**
 * Initialize CMS.ACP.Content.Type namespace
 */
CMS.ACP.Content.Type = { };

/**
 * Columns content type.
 * 
 * @param	array		columnData
 */
CMS.ACP.Content.Type['de.codequake.cms.content.type.columns'] = Class.extend({
	_columnCount: 0,
	_columnData: null,
	_container: null,
	_minColumnWidth: 5,

	/**
	 * @var	integer
	 */
	_mouseDifference: 0,

	init: function(columnData) {
		this._columnData = columnData || [ ];
		this._container = $('#columnContainer');

		for (var i = 0; i < 2; i++) {
			this._addColumn(this._columnData[i] || null);
		}

		this._container.mouseup($.proxy(this._mouseup, this));
		$('.jsAddColumn').click($.proxy(function(event) {
			event.preventDefault();
			this._addColumn();
		}, this));

		this._container.parents('form').submit($.proxy(this._submit, this));
	},

	/**
	 * Adds a new column.
	 */
	_addColumn: function(width) {
		this._columnCount++;

		if (!width) {
			width = Math.round(100 / this._columnCount);
			if (width < this._minColumnWidth) {
				width = this._minColumnWidth;
			}
		}

		var $grid = $('<div class="grid" data-grid-number="' + this._columnCount + '"></div>').appendTo(this._container);
		$('<div class="gridNumber">' + this._columnCount + '</div>').appendTo($grid);
		$('<div class="gridResize"></div>').mousedown($.proxy(this._mousedown, this)).appendTo($grid);

		this._columnData.push(width);
		this._setWidth(this._columnCount, width);
	},

	_mousedown: function(event) {
		event.preventDefault();

		var $gridResize = $(event.currentTarget);
		var $grid = $gridResize.parent();

		this._mouseDifference = event.originalEvent.clientX - ($gridResize.offset().left + $gridResize.innerWidth()) + $(window).scrollLeft();

		this._container.mousemove($.proxy(this._mousemove, this, $grid));
	},

	_mousemove: function(grid, event) {
		var $columnNumber = grid.data('gridNumber');

		var newWidth = event.pageX - grid.offset().left - this._mouseDifference;
		var percentage = Math.round(newWidth / this._container.width() * 100);

		if (percentage != this._columnData[$columnNumber - 1]) {
			this._setWidth($columnNumber, percentage);
		}
	},

	_mouseup: function() {
		this._container.unbind('mousemove');
	},

	/**
	 * Sets the width of a specific column.
	 * 
	 * @param	integer		width
	 * @param	integer		width
	 */
	_setWidth: function(columnNumber, width) {
		console.log('setting width', width, 'for column', columnNumber);
		var oldColumnWidth = false, secondaryColumnNumber, i;

		// ensure min column width
		if (width < this._minColumnWidth) {
			return false;
		}

		// shortpass for first column
		if (this._columnCount == 1) {
			this._columnData[0] = width;
			this._container.children().innerWidth(width + '%');
			return true;
		}

		// search a column on the right that can be scaled down
		i = 1;
		do {
			secondaryColumnNumber = columnNumber + i;
			i++;
		} while (secondaryColumnNumber <= this._columnCount && this._columnData[secondaryColumnNumber - 1] <= this._minColumnWidth);

		// if no column found, search on the left for a column
		if (secondaryColumnNumber > this._columnCount || this._columnData[secondaryColumnNumber - 1] <= this._minColumnWidth) {
			i = 1;
			do {
				secondaryColumnNumber = columnNumber - i;
				i++;
			} while (secondaryColumnNumber > 1 && this._columnData[secondaryColumnNumber - 1] <= this._minColumnWidth);

			if (this._columnData[secondaryColumnNumber - 1] <= this._minColumnWidth) {
				console.log('Could not force column width. Neither on the left nor on the right is a column with enough width.');
				return false;
			}
		}

		console.log('secondary column number is', secondaryColumnNumber);

		var accumulatedColumnWidth = width;
		for (i = 1, length = this._columnCount; i <= length; i++) {
			if (i !== columnNumber && i !== secondaryColumnNumber) {
				accumulatedColumnWidth += this._columnData[i - 1];
			}
		}
		console.log('accumulated column width is', accumulatedColumnWidth);

		var secondaryColumnWidth = 100 - accumulatedColumnWidth;
		console.log('therefore, width of secondary column is', secondaryColumnWidth);

		if (secondaryColumnWidth < this._minColumnWidth) {
			oldColumnWidth = width;
			secondaryColumnWidth = this._minColumnWidth;
			width = 100 - (accumulatedColumnWidth + secondaryColumnWidth - width);

			console.log('reduced column width to', width, 'to maintain min column width for secondary column');
		}

		this._columnData[columnNumber - 1] = width;
		this._columnData[secondaryColumnNumber - 1] = secondaryColumnWidth;

		// update dom
		this._container.children(':nth-child(' + columnNumber + ')').innerWidth(width + '%');
		this._container.children(':nth-child(' + secondaryColumnNumber + ')').innerWidth(secondaryColumnWidth + '%');

		// handle reduced column width
		if (oldColumnWidth) {
			this._setWidth(columnNumber, oldColumnWidth);
		}
	},

	/**
	 * Handles submitting the form.
	 * 
	 * @param	object		event
	 */
	_submit: function(event) {
		var $form = $(event.currentTarget);

		for (var i = 0, length = this._columnCount; i < length; i++) {
			$('<input type="hidden" name="columnData[]" value="' + this._columnData[i] + '" />').appendTo($form);
		}
	}
});

/**
 * Initialize CMS.ACP.File namespace
 */
CMS.ACP.File = { };

/**
 * Handles showing details about a specific file upon clicking on the file
 * title.
 */
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

/**
 * Allows to specify width and height of an image when displayed in the
 * frontend.
 * 
 * @param	integer		fileID
 */
CMS.ACP.File.ImageRatio = Class.extend({
	_ratio: 1,

	/**
	 * Initialises a new image radio handler.
	 * 
	 * @param	integer		fileID
	 */
	init: function(fileID) {
		new WCF.Action.Proxy({
			autoSend: true,
			data: {
				actionName: 'getSize',
				className: 'cms\\data\\file\\FileAction',
				objectIDs: [fileID]
			},
			success: $.proxy(this._success, this)
		});

		$('#width').change($.proxy(this._calculateHeight, this));
		$('#height').change($.proxy(this._calculateWidth, this));
	},

	/**
	 * Calculates the height of the image when the width was changed.
	 */
	_calculateHeight: function() {
		var $width = $('#width');
		var height = $width.val() / this._ratio;
		$('#height').val(Math.round(height));
	},

	/**
	 * Calculates the width of the image when the height was changed.
	 */
	_calculateWidth: function() {
		var $height = $('#height');
		var width = $height.val() * this._ratio;
		$('#width').val(Math.round(width));
	},

	/**
	 * Handles successful AJAX responses.
	 * 
	 * @param	object		data
	 * @param	string		textStatus
	 * @param	jQuery		jqXHR
	 */
	_success: function(data, textStatus, jqXHR) {
		this._ratio = data.returnValues.width / data.returnValues.height;
	}
});

/**
 * Provides a file picker to select one or multiple files in a form.
 * 
 * @param	jQuery		selectButton
 * @param	string		inputName
 * @param	array		defaultValues
 * @param	object		options
 */
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
	 * - fileType: Limit the listed files to the ones of a specific type.
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

		if (!this._options.multiple) {
		    new CMS.ACP.File.ImageRatio($input.val());
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

/**
 * Provides a popover preview for files.
 */
CMS.ACP.File.Preview = WCF.Popover.extend({
	/**
	 * action proxy
	 * @var	WCF.Action.Proxy
	 */
	_proxy: null,

	/**
	 * @see	WCF.Popover.init()
	 */
	init: function() {
		this._super('.cmsFileLink');

		this._proxy = new WCF.Action.Proxy({
			showLoadingOverlay: false
		});
	},

	/**
	 * @see	WCF.Popover._loadContent()
	 */
	_loadContent: function() {
		var $file = $('#' + this._activeElementID);

		this._proxy.setOption('data', {
			actionName: 'getFilePreview',
			className: 'cms\\data\\file\\FileAction',
			objectIDs: [$file.data('fileID')]
		});

		var $elementID = this._activeElementID;
		var self = this;
		this._proxy.setOption('success', function(data, textStatus, jqXHR) {
			self._insertContent($elementID, data.returnValues.template, true);
		});
		this._proxy.sendRequest();
	}
});

/**
 * Provides an upload dialog for files.
 */
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

/**
 * Handles the upload of files.
 * 
 * @see	WCF.Upload.Parallel
 */
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
		// there is not really something we can do here other than
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

/**
 * Initialize CMS.ACP.Page namespace
 */
CMS.ACP.Page = { };

/**
 * Initialize CMS.ACP.Page.Alias namespace
 */
CMS.ACP.Page.Alias = { };

/**
 * Handles building of alias preview.
 * 
 * @param	string		inputSelector
 * @param	string		parentPageSelectSelector
 * @param	string		dummyLink
 */
CMS.ACP.Page.Alias.Preview = Class.extend({
	/**
	 * alias input element
	 * @var	jQuery
	 */
	_aliasInput: null,

	/**
	 * Dummy page link to build alias preview. '123456789' will be replaced
	 * by the actual alias stack based on the given alias and the selected
	 * parent pages.
	 * @var	string
	 */
	_dummyPageLink: '',

	/**
	 * parent page select element
	 * @var	jQuery
	 */
	_parentPageSelect: null,

	/**
	 * Initializes the alias preview.
	 * 
	 * @param	string		inputSelector
	 * @param	string		parentPageSelectSelector
	 * @param	string		dummyPageLink
	 */
	init: function(inputSelector, parentPageSelectSelector, dummyPageLink) {
		this._dummyPageLink = dummyPageLink;
		this._aliasInput = $(inputSelector);
		this._parentPageSelect = $(parentPageSelectSelector);

		if (!this._aliasInput.length) {
			console.debug("[CMS.ACP.Page.Alias.Preview] Invalid alias input selector given, aborting.");
			return;
		}

		if (!this._parentPageSelect.length) {
			console.debug("[CMS.ACP.Page.Alias.Preview] Invalid parent page selector given, aborting.");
			return;
		}

		this._previewElement = this._aliasInput.parent().find('.jsAliasPreview');
		if (!this._previewElement.length) {
			console.debug("[CMS.ACP.Page.Alias.Preview] Unable to find preview element, aborting.");
			return;
		}

		// bind events
		this._aliasInput.change($.proxy(this._change, this));
		this._parentPageSelect.change($.proxy(this._change, this));

		// build alias on initialization
		this._change();
	},

	/**
	 * Builds alias preview when associated inputs were changed.
	 */
	_change: function() {
		var $aliasPrefix = this._parentPageSelect.children('option:selected').data('alias');
		var $alias = this._aliasInput.val();
		var $previewAlias = '';

		if ($alias != '') {
			if ($aliasPrefix) $previewAlias += $aliasPrefix + '/';
			$previewAlias += $alias;

			this._previewElement.html(this._dummyPageLink.replace('123456789', $previewAlias)).show();
		} else {
			this._previewElement.hide();
		}
	}
});

/**
 * Shows a notice about cms links when creating/editing a link of the page menu.
 */
CMS.ACP.Page.Menu = Class.extend({
	init: function() {
		$('#menuItemParameters').change($.proxy(this._showNotice, this));
		$('#menuItemController').change($.proxy(this._showNotice, this));
		$('#menuItemParametersContainer > dd').append('<p id="cmsNoticeContainer" />');
		this._showNotice();
	},

	_showNotice: function() {
		if ($('#menuItemController').val() == 'cms\\page\\PagePage') {
			$('#cmsNoticeContainer').html('<small>' + WCF.Language.get('wcf.acp.pageMenu.parameters.notice') + '</small>');
		}
		else $('#cmsNoticeContainer').html("");
	}
});

CMS.ACP.Page.Revisions = Class.extend({
	_proxy: null,
	_cache: { },
	_dialog: null,

	init: function() {
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});

		this._buttons = $('.jsRevisionsButton');
		this._buttons.click($.proxy(this._click, this));

		new WCF.Action.Delete('cms\\data\\page\\revision\\PageRevisionAction', '.jsRevisionRow');
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

	init: function() {
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

	_click: function(event) {
		event.preventDefault();
		var $target = $(event.currentTarget);

		if ($target.data('confirmMessage')) {
			WCF.System.Confirmation.show($target.data('confirmMessage'), $.proxy(this._execute, this), { target: $target });
		} else {
			WCF.LoadingOverlayHandler.updateIcon($target);
			this._sendRequest($target);
		}
	},

	_sendRequest: function(object) {
		$versionID = $(object).data('objectID');

		this._proxy.setOption('data', {
			actionName: 'restore',
			className: 'cms\\data\\page\\revision\\PageRevisionAction',
			objectIDs: [ $versionID ]
		});
		this._proxy.sendRequest();
	},

	_execute: function(action, parameters) {
		if (action === 'cancel') {
			return;
		}

		WCF.LoadingOverlayHandler.updateIcon(parameters.target);
		this._sendRequest(parameters.target);
	},

	_success: function(data, textStatus, jqXHR) {
		var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
		$notification.show(function() {
			window.location = location;
		});
	}
});
