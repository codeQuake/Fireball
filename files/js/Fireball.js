/**
 * Class and function collection for fireball cms.
 *
 * @author    Jens Krumsieck
 * @copyright    2013 - 2015 codeQuake
 * @license    GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package    de.codequake.cms
 */
if (!Fireball) var Fireball = {};

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

Fireball.Page.ContentTypes = Class.extend({
	_pageID: 0,
	_proxy: null,
	_initialized: 0,
	_cache: {},

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

	_success: function (data, textStatus, jqXHR) {
		this._cache[data.returnValues.pageID] = data.returnValues.template;
		$('body').append(data.returnValues.template);
		$('body').append($('<a id="contentTypeListOpen" class="button buttonPrimary"><span class="icon icon32 fa-angle-right"></span></a>'));
		$('#contentTypeListOpen').click($.proxy(this._toggleSidebar, this));
		$('#contentTypeListClose').click($.proxy(this._toggleSidebar, this));
	},

	_toggleSidebar: function (event) {
		event.preventDefault();
		if (!$('#contentTypeList').hasClass('open')) {
			if (!this._initialized) {
				new Fireball.Content.Dragging(this._pageID);
				this._initialized = 1;
			}
			$('#contentTypeList').addClass('open');
			$('#contentTypeListOpen').toggle();
		}
		else {
			$('#contentTypeList').removeClass('open');
			$('#contentTypeListOpen').toggle();
		}
	},
});

Fireball.Content = {};

Fireball.Content.Dragging = Class.extend({

	_pageID: 0,

	init: function (pageID) {
		this._pageID = pageID;
		var dropButton = '<div class="ui-droppable"></div>';

		$header = $('.content .contentHeader');
		//add sortable container
		var sortable = $('<div class="sortableListContainer sortableContentList" id="sortableContentListBody" />').insertAfter($header);

		//add root item
		var nodeList = '<ol class="sortableList" data-object-id="0">';
		var oldDepth = 0;
		//add ui-droppable class to all content, because we support infinte nesting
		$("div[id^='cmsContent']").each(function () {
			var depth = $(this).parents("div[id^='cmsContent']").length;
			var children = $(this).children("div[id^='cmsContent']").length;
			$(this).addClass('ui-droppable').attr({
				'data-depth': depth,
				'data-children': children
			}).prepend('<span class="sortableNodeLabel">' + $(this).data('contentType') + '</span>');
		});

		//convert contents to a list for use of WCF.Sortable.List
		$("div[id^='cmsContent']").each(function () {
			var depth = $(this).data('depth');
			for (var i = 0; i < (oldDepth - depth); i++) {
				nodeList += '</ol></li>';
			}

			var cache = $(this);
			cache.children("div[id^='cmsContent']").remove()
			nodeList += '<li style="margin-top: 10px; padding-bottom: 10px;" class="sortableNode jsCollapsibleCategory ui-droppable ' + $(this).attr('class') + '" id="' + $(this).attr('id') + '" data-object-id="' + $(this).attr('id').replace('cmsContent', '') + '" data-depth="' + $(this).data('depth') + '">' + cache.html()
				+ '<ol class="sortableList" data-object-id="' + $(this).attr('id').replace('cmsContent', '') + '" style="margin-left: 5px; margin-right: 5px;">';


			if ($(this).data('children') == 0) {
				//has no children
				nodeList += '</ol></li>';
			}
			oldDepth = depth;
			$(this).remove();
		});
		for (var i = 0; i < oldDepth; i++) {
			nodeList += '</ol></li>';
		}
		nodeList += '</ol>';
		$(nodeList).appendTo(sortable);

		new Fireball.Content.Sortable.List();

		//no parent drag area
		$('.userNotice').after($(dropButton));

		$('.draggable').draggable({
			cursor: "move",
			helper: "clone",
			revert: "invalid",
			containment: "document",
			drag: $.proxy(this._drag, this)
		});
		$('.ui-droppable').droppable({
			activeClass: "droppable-state-active",
			greedy: true,
			drop: $.proxy(this._drop, this)
		});
	},

	_drop: function (event, ui) {
		//check if element comes from contenttype list
		if (typeof ui.draggable.attr('id') !== 'undefined' && ui.draggable.attr('id').match('^de.codequake.cms')) {
			$(event.target).append('<div class="draggedContent ' + ui.draggable.attr('id') + '" />')
			var type = ui.draggable.attr('id');
			var position = 'body';
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
	}
}),

	Fireball.Content.Sortable = {};

Fireball.Content.Sortable.List = WCF.Sortable.List.extend({

	init: function () {
		this._super('sortableContentListBody');
		$('#contentTypeList .wideButton').children('button[data-type="submit"]').click($.proxy(this._submit, this));
		this._className = 'cms\\data\\content\\ContentAction';
	},

	_success: function (data, textStatus, jqXHR) {
		this._super(data, textStatus, jqXHR);
		window.location = location;
	}
});

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
				//watch for wysiwyg
				if ($('#contentAddForm .redactor-box').length != 0) {
					//on toggling code, bbcode is saved to textarea
					$('#' + input.attr('id')).redactor('code.toggle');
				}
				if (input.attr('name').match('columnData')) {
					parameters['contentData']['columnData'].push($.trim(input.val()));
				}
				else parameters['contentData'][input.attr('id')] = $.trim(input.val());

				if (typeof parameters['contentData']['columnData'] !== 'undefined' && parameters['contentData']['columnData'].length == 0) {
					delete parameters['contentData']['columnData'];
				}
			}
		});
		console.log(parameters);
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
