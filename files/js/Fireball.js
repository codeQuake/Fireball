/**
 * Class and function collection for fireball cms.
 *
 * @author    Jens Krumsieck
 * @copyright    2013 - 2015 codeQuake
 * @license    GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package    de.codequake.cms
 */

/**
 * jquery ui fire slider
 */
$.widget('ui.fireSlide', {
	/**
	 * button list object
	 * @var    jQuery
	 */
	_buttonList: null,

	/**
	 * number of items
	 * @var    integer
	 */
	_count: 0,

	/**
	 * item index
	 * @var    integer
	 */
	_index: 0,

	/**
	 * items
	 * @var jQuery
	 */
	_items: null,

	/**
	 * item container
	 * @var jQuery
	 */
	_itemContainer: null,

	/**
	 * timer object
	 * @var    interval
	 */
	_timer: null,

	/**
	 * list of options
	 * @var    object
	 */
	options: {
		// cycle interval in milliseconds
		speed: 2000
	},

	/**
	 * elements width
	 * @var    integer
	 */
	_width: 0,

	/**
	 * Creates a new instance of ui.FireSlide
	 */
	_create: function () {
		var items = this.element.children('div');
		this._itemContainer = $('<div />').appendTo(this.element).css('left', 0);
		items.appendTo(this._itemContainer);
		this._items = this._itemContainer.children('div');
		this._count = this._items.length;
		this._index = 0;
		var max_height = 0;

		this._width = this.element.innerWidth();
		this._items.each($.proxy(function (index, item) {
			$(item).css({
				left: (index * (40 + this._width)),
				width: this._width
			}).show();
		}, this));

		// create button list & calculate max height
		this._buttonList = $('<ul class="slideshowButtonList" />').appendTo(this.element);

		for (var $i = 0; $i < this._count; $i++) {
			if ($(this._items.get($i)).outerHeight() >= max_height) max_height = $(this._items.get($i)).outerHeight();
			var $icon = $('<li><a><span class="icon icon16 fa-circle"></span></a></li>').click($.proxy(this._click, this)).appendTo(this._buttonList);
			if ($i == 0) $icon.addClass('active');
		}
		this.element.css('height', max_height);

		// handle resize
		$(window).resize($.proxy(this._resize, this));

		// start slider
		this._restartTimer();
	},

	/**
	 * Manual slide via click
	 */
	_click: function (event) {
		event.preventDefault();

		this.slideTo($(event.currentTarget).index());
		this._restartTimer();
	},

	/**
	 * Handles window resize
	 */
	_resize: function () {
		var max_height = 0;

		// calc max-height again
		for (var $i = 0; $i < this._count; $i++) {
			if ($(this._items.get($i)).outerHeight() >= max_height) max_height = $(this._items.get($i)).outerHeight();
		}
		this.element.css('height', max_height);

		//set new width
		this._width = this.element.innerWidth();

		//resize items
		this._items.each($.proxy(function (index, item) {
			$(item).css({
				left: (index * (40 + this._width)),
				width: this._width
			});
		}, this));

		//reset slider
		this._index = 0;
		this.slideTo();
	},

	/**
	 * Restarts the timer to slide to the next slide.
	 */
	_restartTimer: function () {
		if (this._timer !== null) {
			window.clearInterval(this._timer);
		}

		this._timer = window.setInterval($.proxy(this.slideTo, this), this.options.speed);
	},

	/**
	 * Slides to a specific index or to the next slide if no index provided.
	 *
	 * @param    integer        index
	 */
	slideTo: function (index) {
		if (typeof index !== 'undefined') {
			this._index = index;
		}
		else {
			this._index = this._index + 1;
		}

		if (this._index == this._count) {
			this._index = 0;
		}

		this._buttonList.find('.active').removeClass('active');
		$(this._buttonList.children().get(this._index)).addClass('active');
		this._itemContainer.css({
			'left': (this._index * (40 + this._width) * -1)
		});
	}
});

/**
 * namespace Fireball
 */
if (!Fireball) var Fireball = {};

/**
 * namespace Fireball.Page
 */
Fireball.Page = {};

/**
 * Shows a dialog to add a new page.
 */
Fireball.Page.Add = Class.extend({
	_pageID: 0,
	_cache: {},
	_dialog: null,

	init: function (pageID) {
		this._pageID = pageID;

		// bind click event
		$('#pageAddButton').click($.proxy(this._click, this));
	},

	_click: function (event) {
		event.preventDefault();
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		this._proxy.setOption('data', {
			actionName: 'getAddDialog',
			className: 'cms\\data\\page\\PageAction',
			objectIDs: [this._pageID]
		});
		this._proxy.sendRequest();
	},

	_show: function (pageID) {
		this._dialog = $('<div id="pageAddDialog">' + this._cache[pageID] + '</div>').appendTo(document.body);

		this._dialog.wcfDialog({
			title: WCF.Language.get('cms.page.add')
		});
		//bind submit event
		this._dialog.find('input[type=submit]').click($.proxy(this._submit, this));
	},

	_submit: function (event) {
		event.preventDefault();
		parameters = {};

		//get all inputs
		$('#pageAddForm input, #pageAddForm textarea, #pageAddForm select').each(function (index) {
			var input = $(this);
			if (input.attr('type') != 'checkbox') {
				parameters[input.attr('name')] = $.trim(input.val())
			}
			else parameters[input.attr('name')] = input.prop('checked') ? 1 : 0;
		});

		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._executed, this)
		});
		this._proxy.setOption('data', {
			actionName: 'frontendCreate',
			className: 'cms\\data\\page\\PageAction',
			objectIDs: [this._pageID],
			parameters: {
				data: parameters
			}
		});
		this._proxy.sendRequest();
	},

	_executed: function (data, textStatus, jqXHR) {
		if (typeof this._cache[data.returnValues.errors] === 'undefined') {
			var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
			$notification.show(function () {
				window.location = data.returnValues.link;
			});
		}
	},

	_success: function (data, textStatus, jqXHR) {
		this._cache[data.returnValues.pageID] = data.returnValues.template;
		this._show(data.returnValues.pageID);
	}
});

/**
 * Handles the sidebar overlay with a list of all content types
 * which can be added via drag'n'drop
 */
Fireball.Page.ContentTypes = Class.extend({
	_pageID: 0,
	_proxy: null,
	_setDraggable: false,

	init: function (pageID) {
		this._pageID = pageID;
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		this._proxy.setOption('data', {
			actionName: 'getContentTypes',
			className: 'cms\\data\\page\\PageAction',
			objectIDs: [this._pageID],
			parameters: {
				position: 'both'
			}
		});
		this._proxy.sendRequest();
	},

	setSet: function () {
		this._setDraggable = true;
	},

	_success: function (data, textStatus, jqXHR) {
		$('body').append(data.returnValues.template);
	},

	_toggleSidebar: function () {
		if (!$('#contentTypeList').hasClass('open')) {
			$('#contentTypeList').addClass('open');
		}
		else {
			$('#contentTypeList').removeClass('open');
		}
	},
});

/**
 * Handles inline edits of pages and basic content edits
 */
Fireball.Page.InlineEditor = WCF.InlineEditor.extend({
	_pageID : 0,
	_environment : 'page',
	_permissions : { },
	_redirectURL : '',
	_updateHandler : null,
	_editStarted : false,
	_contentTypeOverlay : null,
	_dragging : null,

	/**
	 * @see	WCF.InlineEditor._setOptions()
	 */
	_setOptions: function() {
		this._environment = 'page';

		this._options = [
			{ label: WCF.Language.get('cms.page.edit.start'), optionName: 'start' },
			{ label: WCF.Language.get('cms.page.edit.addContent'), optionName: 'addContent' },
			{ label: WCF.Language.get('cms.page.edit.save'), optionName: 'save' },

			{ optionName: 'divider' },

			{ label: WCF.Language.get('cms.page.edit.finish'), optionName: 'finish' },
			{ label: WCF.Language.get('cms.page.edit.acp'), optionName: 'acp', isQuickOption: true }
		];
	},

	/**
	 * Returns current update handler.
	 *
	 * @return	Fireball.Page.UpdateHandler
	 */
	setUpdateHandler: function(updateHandler) {
		this._updateHandler = updateHandler;
	},

	/**
	 * @see	WCF.InlineEditor._getTriggerElement()
	 */
	_getTriggerElement: function(element) {
		return element.find('.jsPageInlineEditor');
	},

	/**
	 * @see	WCF.InlineEditor._validate()
	 */
	_validate: function(elementID, optionName) {
		switch (optionName) {
			case 'addContent':
				return this._editStarted;
				break;

			case 'start':
				return !this._editStarted;
				break;

			case 'save':
				return this._editStarted;
				break;

			case 'finish':
				return this._editStarted;
				break;

			case 'acp':
				return true;
				break;
		}

		return false;
	},

	/**
	 * @see	WCF.InlineEditor._execute()
	 */
	_execute: function(elementID, optionName) {
		// abort if option is invalid or not accessible
		if (!this._validate(elementID, optionName)) {
			return false;
		}

		switch (optionName) {
			case 'start':
				if (this._editStarted)
					return;

				var self = this;
				this._contentTypeOverlay = new Fireball.Page.ContentTypes(this._pageID);
				this._dragging = new Fireball.Content.Dragging(this._pageID);

				this._editStarted = true;
				break;

			case 'addContent':
				this._contentTypeOverlay._toggleSidebar();
				if (!this._contentTypeOverlay._setDraggable) {
					$('.draggable').draggable({
						cursor: "move",
						helper: "clone",
						revert: "invalid",
						containment: "document",
						drag: $.proxy(this._dragging._drag, this._dragging)
					});
				}

				break;

			case 'save':
				$list = this._dragging.getSortableListObject()._submit();
				break;

			case 'finish':
				var self = this;
				WCF.System.Confirmation.show(WCF.Language.get('cms.page.edit.finish.confirm'), function(action) {
					if (action === 'confirm') {
						$list = self._dragging.getSortableListObject()._submit();
						//TODO: reset contents
					}
				});
				break;

			case 'acp':
				window.location = $('#' + elementID).data('advancedUrl');
				break;
		}
	},

	/**
	 * Updates page properties.
	 *
	 * @param	string		elementID
	 * @param	string		optionName
	 * @param	object		data
	 */
	_updatePage: function(elementID, optionName, data) {
		var self = this;
		var $pageID = this._elements[elementID].data('pageID');

		this._updateData.push({
			data: data,
			elementID: elementID,
			optionName: optionName
		});

		this._proxy.setOption('data', {
			actionName: optionName,
			className: 'cms\\data\\page\\PageAction',
			objectIDs: [ this._elements[elementID].data('pageID') ],
			parameters: {
				data: data
			},
			success: function(data) {
				self._updateHandler.update($pageID, data.returnValues.pageData[$pageID]);
			}
		});
		this._proxy.sendRequest();
	},

	/**
	 * Returns a specific permission.
	 *
	 * @param	string		permission
	 * @return	integer
	 */
	_getPermission: function(permission) {
		if (this._permissions[permission]) {
			return this._permissions[permission];
		}

		return 0;
	},

	/**
	 * Sets current environment.
	 *
	 * @param	string		environment
	 * @param	integer		pageID
	 * @param	string		redirectURL
	 */
	setEnvironment: function(environment, pageID, redirectURL) {
		if (environment !== 'page') {
			environment = 'page';
		}

		this._environment = environment;
		this._pageID = pageID;
		this._redirectURL = redirectURL;
	},

	/**
	 * Sets a permission.
	 *
	 * @param	string		permission
	 * @param	integer		value
	 */
	setPermission: function(permission, value) {
		this._permissions[permission] = value;
	},

	/**
	 * Sets permissions.
	 *
	 * @param	object		permissions
	 */
	setPermissions: function(permissions) {
		for (var $permission in permissions) {
			this.setPermission($permission, permissions[$permission]);
		}
	}
});

/**
 * updatehandler for pages
 */
Fireball.Page.UpdateHandler = Class.extend({

});

/**
 * namespace Fireball.Content
 */
Fireball.Content = {};

/**
 * Handles the content specific add dialog
 */
Fireball.Content.AddForm = Class.extend({
	_pageID: 0,
	_cache: {},
	_dialog: null,

	init: function (pageID, position, type, parentID) {
		this._pageID = pageID;

		//show content settings dialog
		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		this._proxy.setOption('data', {
			actionName: 'getAddDialog',
			className: 'cms\\data\\content\\ContentAction',
			objectIDs: [],
			parameters: {
				position: position,
				type: type,
				parentID: parentID,
				pageID: this._pageID
			}
		});
		this._proxy.sendRequest();
	},

	_show: function (pageID) {
		this._dialog = $('<div id="contentAddDialog">' + this._cache[pageID] + '</div>').appendTo(document.body);

		this._dialog.wcfDialog({
			title: WCF.Language.get('cms.content.add')
		});

		//bind submit event
		this._dialog.find('input[type=submit]').click($.proxy(this._submit, this));
	},

	_executed: function (data, textStatus, jqXHR) {
		if (typeof data.returnValues.errors === 'undefined') {
			this._dialog.wcfDialog('close');
			var $notification = new WCF.System.Notification(WCF.Language.get('wcf.global.success'));
			$notification.show(function () {
				window.location = location;
			});
		}

	},

	_submit: function (event) {
		event.preventDefault();
		parameters = {};

		//get all inputs
		parameters['contentData'] = {};
		parameters['contentData']['columnData'] = [];
		$('#contentAddForm input, #contentAddForm textarea, #pageAddForm select').each(function (index) {
			var input = $(this);
			if (input.attr('type') != 'checkbox') {
				parameters[input.attr('name')] = $.trim(input.val())
			}
			else parameters[input.attr('name')] = input.prop('checked') ? 1 : 0;

			//fill contentData arra
			if (typeof input.attr('name') !== 'undefined' && input.attr('name').match('^contentData')) {
				delete parameters[input.attr('name')];
				if (input.attr('name').match('columnData')) {
					parameters['contentData']['columnData'].push($.trim(input.val()));
				}
				else parameters['contentData'][input.attr('id')] = $.trim(input.val());

				if (typeof parameters['contentData']['columnData'] !== 'undefined' && parameters['contentData']['columnData'].length == 0) {
					delete parameters['contentData']['columnData'];
				}
			}
		});

		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._executed, this)
		});
		this._proxy.setOption('data', {
			actionName: 'frontendCreate',
			className: 'cms\\data\\content\\ContentAction',
			objectIDs: [],
			parameters: {
				data: parameters
			}
		});
		this._proxy.sendRequest();
	},

	_success: function (data, textStatus, jqXHR) {
		this._cache[this._pageID] = data.returnValues.template;
		this._show(this._pageID);
	}
});

/**
 * Handles the preparation for sorting contents
 */
Fireball.Content.Dragging = Class.extend({
	_sortableList : null,
	_proxy : null,
	_pageID : 0,

	init: function (pageID) {
		this._pageID = pageID;

		$("div[id^='cmsContent']").each(function () {
			$(this).remove();
		});
		$("section[id^='cmsContent']").each(function () {
			$(this).remove();
		});

		this._proxy = new WCF.Action.Proxy({
			success: $.proxy(this._success, this)
		});
		this._proxy.setOption('data', {
			actionName: 'getSortableContentList',
			className: 'cms\\data\\page\\PageAction',
			objectIDs: [this._pageID],
			parameters: {
				position: 'body'
			}
		});
		this._proxy.sendRequest();

		this._proxy.setOption('data', {
			actionName: 'getSortableContentList',
			className: 'cms\\data\\page\\PageAction',
			objectIDs: [this._pageID],
			parameters: {
				position: 'sidebar'
			}
		});
		this._proxy.sendRequest();
	},

	_drag: function (event, ui) {
		ui.helper.css('position', 'fixed');
		ui.helper.css('margin-top', event.pageY);
		ui.helper.css('margin-left', event.pageX);
		ui.position.top = 0;
		ui.position.left = 0;
	},

	_drop: function (event, ui) {
		//check if element comes from contenttype list
		if (typeof ui.draggable.attr('id') !== 'undefined' && ui.draggable.attr('id').match('^de.codequake.cms')) {
			$(event.target).append('<div class="draggedContent ' + ui.draggable.attr('id') + '" />')
			var type = ui.draggable.attr('id');
			var position = 'body';
			if (typeof $(event.target).data('position') !== 'undefined')
				position = $(event.target).data('position');
			if (typeof $(event.target).attr('id') !== 'undefined' && $(event.target).attr('id').match('^cmsContent')) {
				var data = $(event.target).attr('id').replace('cmsContent', '');
			}
			var parentID = 0;
			if (typeof data !== 'undefined') {
				parentID = data;
			}
			//call add form
			new Fireball.Content.AddForm(this._pageID, position, type, parentID);
		}
	},

	_success: function (data, textStatus, jqXHR) {
		template = data.returnValues.template;

		if (data.returnValues.position == 'body')
			$(template).insertAfter($('.content .contentHeader'));
		else if (data.returnValues.position == 'sidebar')
			$('aside.sidebar').append(template);
		else if (data.returnValues.position == 'sidebarLeft')
			$('aside.boxesSidebarLeft').append(template);
		else if (data.returnValues.position == 'sidebarRight')
			$('aside.boxesSidebarRight').append(template);

		$container = $('#sortableContentList' + data.returnValues.position.charAt(0).toUpperCase() + data.returnValues.position.slice(1));

		this._sortableList = new Fireball.Content.Sortable.List('sortableContentList' + data.returnValues.position.charAt(0).toUpperCase() + data.returnValues.position.slice(1));

		if ($container !== undefined) {
			$container.parent().find('.ui-droppable').droppable({
				activeClass: "droppable-state-active",
				greedy: true,
				drop: $.proxy(this._drop, this)
			});
		}
	},

	getSortableListObject: function () {
		return this._sortableList;
	}
});

/**
 * namespace Fireball.Content.Sortable
 */
Fireball.Content.Sortable = {};

/**
 * Handles the sorting of contents
 */
Fireball.Content.Sortable.List = WCF.Sortable.List.extend({
	init: function (container) {
		this._super(container);
		this._className = 'cms\\data\\content\\ContentAction';
	},

	/**
	 * Saves object structure.
	 */
	_submit: function() {
		// reset structure
		this._structure = { };

		// build structure
		this._container.find('.sortableList').each($.proxy(function(index, list) {
			var $list = $(list);
			var $parentID = $list.data('objectID');

			if ($parentID !== undefined) {
				$list.children(this._options.items).each($.proxy(function(index, listItem) {
					var $objectID = $(listItem).data('objectID');

					if (!this._structure[$parentID]) {
						this._structure[$parentID] = [ ];
					}

					this._structure[$parentID].push($objectID);
				}, this));
			}
		}, this));

		// catch empty structures
		if (Object.keys(this._structure).length === 0)
			return;

		// send request
		var $parameters = $.extend(true, {
			data: {
				offset: this._offset,
				structure: this._structure
			}
		}, this._additionalParameters);

		this._proxy.setOption('data', {
			actionName: 'updatePosition',
			className: this._className,
			interfaceName: 'wcf\\data\\ISortableAction',
			parameters: $parameters
		});
		this._proxy.sendRequest();
	},
});
