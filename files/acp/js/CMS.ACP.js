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
	_addButton: null,

	/**
	 * count of added columns
	 * @var	integer
	 */
	_columnCount: 0,

	/**
	 * column data
	 * @var	array<integer>
	 */
	_columnData: null,

	/**
	 * container object
	 * @var	jQuery
	 */
	_container: null,

	/**
	 * max column count
	 * @var	integer
	 */
	_maxColumnCount: 5,

	/**
	 * min column count
	 * @var	integer
	 */
	_minColumnCount: 2,

	/**
	 * min column width
	 * @var	integer
	 */
	_minColumnWidth: 20,

	/**
	 * difference between mouse and left edge of resizer when dragging a
	 * column resizer
	 * @var	integer
	 */
	_mouseDifference: 0,

	/**
	 * Initializes a form for a columns content type.
	 * 
	 * @param	array		columnData
	 */
	init: function(columnData) {
		this._columnData = [ ];

		this._addButton = $('.jsAddColumn');
		this._container = $('#columnContainer');

		for (var i = 0; i < this._minColumnCount || i < columnData.length; i++) {
			this._addColumn(columnData[i] || Math.round(100 / this._minColumnCount));
		}

		// bind events
		this._container.mouseup($.proxy(this._mouseup, this));
		this._addButton.click($.proxy(function(event) {
			event.preventDefault();

			if (this._addColumn(0)) {
				var width = Math.round(100 / this._columnCount);
				this._setWidth(this._columnCount, width);
			}
		}, this));
	},

	/**
	 * Adds a new column.
	 * 
	 * @param	integer		width
	 */
	_addColumn: function(width) {
		if (this._columnCount == this._maxColumnCount) {
			return false;
		}

		this._columnCount++;

		var $grid = $('<div class="grid" data-column-number="' + this._columnCount + '"></div>').appendTo(this._container);
		var $gridInner = $('<div></div>').appendTo($grid);
		
		var $gridNumber = $('<div class="gridNumber"></div>').appendTo($gridInner);
		$('<span>' + this._columnCount + '</span>').appendTo($gridNumber);
		$('<input type="number" name="contentData[columnData][]" min="' + this._minColumnWidth + '" />').keydown($.proxy(this._preventSubmit, this)).change($.proxy(this._change, this)).appendTo($gridNumber);
		$('<button type="button">' + WCF.Language.get('wcf.global.button.delete') + '</button>').click($.proxy(this._deleteClick, this)).appendTo($gridNumber);

		$('<div class="gridResize"></div>').mousedown($.proxy(this._mousedown, this)).appendTo($grid);

		this._columnData.push(width);

		this._updateGUI();

		return true;
	},

	/**
	 * Handles changes of a column width input.
	 * 
	 * @param	object		event
	 */
	_change: function(event) {
		var $input = $(event.currentTarget);
		var $columnNumber = parseInt($input.parents('.grid').data('columnNumber'));
		var $width = parseInt($input.val());

		this._setWidth($columnNumber, $width);
	},

	_deleteClick: function(event) {
		var $button = $(event.currentTarget);
		var $columnNumber = parseInt($button.parents('.grid').data('columnNumber'));

		this._deleteColumn($columnNumber);
	},

	_deleteColumn: function(columnNumber) {
		if (this._columnCount == this._minColumnCount) {
			console.debug("[CMS.ACP.Content.Type['de.codequake.cms.content.type.columns']] Couldn't delete column '" + columnNumber + "', reached min column count.");
			return;
		}

		var secondaryColumnNumber = columnNumber + 1;
		if (secondaryColumnNumber > this._columnCount) {
			secondaryColumnNumber = columnNumber - 1;
		}

		var oldWidth = this._columnData[columnNumber - 1];
		var secondaryColumnWidth = this._columnData[secondaryColumnNumber - 1] + oldWidth;

		this._columnData.splice(columnNumber - 1, 1);
		this._container.children(':nth-child(' + columnNumber + ')').remove();
		this._columnCount--;

		// secondary column moved one to the left if it was at the
		// right of the deleted column
		if (secondaryColumnNumber > columnNumber) secondaryColumnNumber--;

		this._columnData[secondaryColumnNumber - 1] = secondaryColumnWidth;
		this._updateGUI();
	},

	/**
	 * Starts dragging a column resizer to set the width of a column.
	 * 
	 * @param	object		event
	 */
	_mousedown: function(event) {
		event.preventDefault();

		var $gridResize = $(event.currentTarget);
		var $grid = $gridResize.parent();

		this._mouseDifference = event.originalEvent.clientX - ($gridResize.offset().left + $gridResize.innerWidth()) + $(window).scrollLeft();

		this._container.mousemove($.proxy(this._mousemove, this, $grid));
	},

	/**
	 * Updates the column widths while dragging the resizer of a column.
	 * 
	 * @param	jQuery		grid
	 * @param	object		event
	 */
	_mousemove: function(grid, event) {
		var $columnNumber = grid.data('columnNumber');

		var newWidth = event.pageX - grid.offset().left - this._mouseDifference;
		var percentage = Math.round(newWidth / this._container.width() * 100);

		if (percentage != this._columnData[$columnNumber - 1]) {
			this._setWidth($columnNumber, percentage);
		}
	},

	/**
	 * Unbinds the mouse move event when the mouse button is released.
	 */
	_mouseup: function() {
		this._container.unbind('mousemove');
	},

	/**
	 * Prevents submitting the form when enter is pressed in a column width
	 * input to update the column width.
	 * 
	 * @param	object		event
	 */
	_preventSubmit: function(event) {
		if (event.keyCode == $.ui.keyCode.ENTER) {
			event.preventDefault();
			this._change(event);
		}
	},

	_updateGUI: function() {
		var $column, $width;

		for (var i = 1; i <= this._columnCount; i++) {
			$column = this._container.children(':nth-child(' + i + ')');
			$width = this._columnData[i - 1];

			$column.data('columnNumber', i);
			$column.innerWidth($width + '%');
			$column.find('span').text(i);
			$column.find('input').val($width).prop('max', 100 - this._minColumnWidth * (this._columnCount - 1));
		}

		if (this._columnCount == this._maxColumnCount) {
			this._addButton.prop('disabled', true);
		} else {
			this._addButton.prop('disabled', false);
		}

		if (this._columnCount <= this._minColumnCount) {
			this._container.find('button').prop('disabled', true);
		} else {
			this._container.find('button').prop('disabled', false);
		}
	},

	/**
	 * Sets the width of a specific column.
	 * 
	 * @param	integer		width
	 * @param	integer		width
	 */
	_setWidth: function(columnNumber, width) {
		if (columnNumber > this._columnCount) {
			console.debug("[CMS.ACP.Content.Type['de.codequake.cms.content.type.columns']] Couldn't set column width for column '" + columnNumber + "', out of boundary.");
			return;
		}

		if (width == this._columnData[columnNumber - 1]) {
			// nothing to change
			return true;
		}

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

		// search for a column on the right that can be scaled
		i = 1;
		do {
			secondaryColumnNumber = columnNumber + i;
			i++;
		} while (width > this._columnData[columnNumber - 1] && secondaryColumnNumber <= this._columnCount && this._columnData[secondaryColumnNumber - 1] <= this._minColumnWidth);

		// if no column found, search on the left for a column
		if (width > this._columnData[columnNumber - 1] && (secondaryColumnNumber > this._columnCount || this._columnData[secondaryColumnNumber - 1] <= this._minColumnWidth)) {
			i = 1;
			do {
				secondaryColumnNumber = columnNumber - i;
				i++;
			} while (secondaryColumnNumber > 1 && this._columnData[secondaryColumnNumber - 1] <= this._minColumnWidth);

			if (secondaryColumnNumber < 1 || this._columnData[secondaryColumnNumber - 1] <= this._minColumnWidth) {
				return false;
			}
		}

		var accumulatedColumnWidth = width;
		for (i = 1; i <= this._columnCount; i++) {
			if (i !== columnNumber && i !== secondaryColumnNumber) {
				accumulatedColumnWidth += this._columnData[i - 1];
			}
		}

		var secondaryColumnWidth = 100 - accumulatedColumnWidth;

		if (secondaryColumnWidth < this._minColumnWidth) {
			oldColumnWidth = width;
			secondaryColumnWidth = this._minColumnWidth;
			width = 100 - (accumulatedColumnWidth + secondaryColumnWidth - width);
		}

		this._columnData[columnNumber - 1] = width;
		this._columnData[secondaryColumnNumber - 1] = secondaryColumnWidth;

		// update dom
		this._updateGUI();

		// handle reduced column width
		if (oldColumnWidth) {
			this._setWidth(columnNumber, oldColumnWidth);
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
	 * selected files
	 * @var	object
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
		this._selected = defaultValues || { };
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
			console.debug('[CMS.ACP.File.Picker] Unable to determine form for file picker, aborting.');
			return;
		}
		$form.submit($.proxy(this._submit, this));

		// bind select event
		this._selectButton.click($.proxy(this._openPicker, this));

		this._updateSelectedFilesList();
	},

	/**
	 * Selects all given files.
	 * 
	 * @param	array		files
	 * @param	boolean		checkInput
	 */
	selectFiles: function(files, checkInput) {
		// only select first file in single selection mode
		if (!this._options.multiple) {
			var keys = Object.keys(files);
			var _files = files;
			files = { };
			files[keys[0]] = _files[keys[0]];
		}

		var file;
		for (var fileID in files) {
			if (!files.hasOwnProperty(fileID)) {
				continue;
			}

			file = files[fileID];

			if (this._selected[fileID] === undefined) {
				this._selected[fileID] = file;

				if (checkInput !== false) {
					this._dialog.find('tr[data-file-id="'+ fileID +'"]').find('input').prop('checked', true);
				}
			}
		}

		this._updateSelectedFilesList();
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
		var $tr = $input.parents('tr');
		var fileID = $input.val();

		if ($input.is(':checked')) {
			var fileData = {
				fileID: fileID,
				title: $tr.data('fileTitle'),
				formattedFilesize: $tr.data('fileFormattedSize')
			}

			this._selected[fileID] = fileData;
		} else {
			delete this._selected[fileID];
		}

		if (!this._options.multiple) {
			new CMS.ACP.File.ImageRatio($input.val());

			//delete old entries
			var self = this;
			$.each(self._selected, function(index){
				if (index != $input.val()) {
					delete self._selected[index];
				}
			});
		}

		this._updateSelectedFilesList();
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
			var _this = this;
			$.each(this._selected, function(fileID, file) {
				$('<input type="hidden" name="'+ _this._inputName +'[]" value="'+ fileID +'" />').appendTo($form);
			});
		} else {
			var keys = Object.keys(this._selected);
			var file = this._selected[keys[0]]; 
			
			if (typeof file !== 'undefined') {
				$('<input type="hidden" name="' + this._inputName + '" value="' + file.fileID + '" />').appendTo($form);
			}
			else $('<input type="hidden" name="' + this._inputName + '" value="0" />').appendTo($form);
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
			if (this._selected[$fileID] !== undefined) {
				$input.prop('checked', true);
			}

			// bind click event
			$input.click($.proxy(this._inputClick, this));
		}, this));
	},

	_updateSelectedFilesList: function() {
		var $ul = this._selectButton.parent().children('.formAttachmentList');

		// remove old files
		$ul.html('');

		// insert new files
		$.each(this._selected, function(fileID, file) {
			$('<li class="box32"><span class="icon icon32 icon-paperclip" /><div><div><p>' + file.title + '</p><small>' + file.formattedFilesize + '</small></div></div></li>').appendTo($ul);
		});
	},

	/**
	 * Handles successful uploads of new files. Reloads already loaded file
	 * lists and automatically selects uploaded files.
	 * 
	 * @param	array		files
	 */
	_uploadCallback: function(files) {
		this.selectFiles(files, false);
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
	 * list of files that where uploaded successfully
	 * @var	object
	 */
	_files: null,

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
		this._files = { };

		this._proxy = new WCF.Action.Proxy();

		$('.jsFileUploadButton').click($.proxy(this._openDialog, this));
	},

	/**
	 * Adds a file to the uploaded file list
	 * 
	 * @param	object		file
	 */
	addFile: function(file) {
		this._files[file.fileID] = file;
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

		if ($.isEmptyObject(this._files)) {
			console.log('[CMS.ACP.File.Upload] Tried to finalize upload though no files where uploaded, aborting.');
			return;
		}

		var $categoryIDs = this._categoryInput.val();
		if ($categoryIDs === null) {
			console.debug('[CMS.ACP.File.Upload] Tried to finalize upload without a selected category, aborting.');
			return;
		}

		this._submitButton.attr('disabled', 'disabled');

		// send request
		this._proxy.setOption('data', {
			actionName: 'update',
			className: 'cms\\data\\file\\FileAction',
			objectIDs: Object.keys(this._files),
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
				this._afterSubmit(this._files);
			}
		}, this));
		this._proxy.sendRequest();
	},

	/**
	 * Activates the submit button once files where uploaded and a category
	 * specified.
	 */
	_handleButtonState: function() {
		if ($.isEmptyObject(this._files)) {
			// no files uploaded
			this._submitButton.prop('disabled', true);
			return;
		}

		if (this._categoryInput.val() === null) {
			// no category specified
			this._submitButton.prop('disabled', true);
			return;
		}

		// everything fine, activate button
		this._submitButton.prop('disabled', false);
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
			CMS.ACP.File.Upload.addFile($fileData);

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

		this._previewElement = this._aliasInput.parent().find('.jsAliasPreview');
		if (!this._previewElement.length) {
			console.debug("[CMS.ACP.Page.Alias.Preview] Unable to find preview element, aborting.");
			return;
		}

		// bind events
		this._aliasInput.keyup($.proxy(this._change, this));
		if (!this._parentPageSelect.length) {
			this._parentPageSelect.change($.proxy(this._change, this));
		}

		// build alias on initialization
		this._change();
	},

	/**
	 * Builds alias preview when associated inputs were changed.
	 */
	_change: function() {
		var $aliasPrefix = '';
		var $alias = this._aliasInput.val();
		var $previewAlias = '';

		if (!this._parentPageSelect.length) {
			$aliasPrefix = this._parentPageSelect.children('option:selected').data('alias');
		}

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

CMS.ACP.Page.TypePicker = Class.extend({
	_proxy: null,
	_didInit: false,
	_objectTypeID: 0,
	_pageID: 0,
	
	init: function(objectTypeID, pageID) {
		if (this._didInit)
			return;

		this._objectTypeID = objectTypeID;
		this._pageID = pageID;
		
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		
//		if (this._objectTypeID != null && this._objectTypeID != undefined) {
//			this._click();
//		}
		
		this._buttons = $('#pageObjectTypeID');
		this._buttons.change($.proxy(this._click, this));
		
		this._didInit = true;
	},

	_click: function(event) {
		$objectTypeID = $('#pageObjectTypeID').val();
		
		this._proxy.setOption('data', {
			actionName: 'getTypeSpecificForm',
			className: 'cms\\data\\page\\PageAction',
			parameters: {
				'objectTypeID': $objectTypeID,
				'pageID' : this._pageID
			}
		});
		this._proxy.sendRequest();
	},
	
	_success: function(data, textStatus, jqXHR) {
		$('#specific').html(data.returnValues.template);
	}
});
