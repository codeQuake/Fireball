/**
 * Class and function collection for fireball cms.
 * 
 * @author	Jens Krumsieck
 * @copyright	2013 - 2015 codeQuake
 * @license	GNU Lesser General Public License <http://www.gnu.org/licenses/lgpl-3.0.txt>
 * @package	de.codequake.cms
 */
if (!CMS) var CMS = {};

$.widget('ui.fireSlide', {
	/**
	 * button list object
	 * @var	jQuery
	 */
	_buttonList: null,

	/**
	 * number of items
	 * @var	integer
	 */
	_count: 0,

	/**
	 * item index
	 * @var	integer
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
	 * @var	interval
	 */
	_timer: null,

	/**
	 * list of options
	 * @var	object
	 */
	options: {
		// cycle interval in milliseconds
		speed: 2000
	},

	/**
	 * elements width
	 * @var	integer
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
			var $icon = $('<li><a><span class="icon icon16 icon-circle"></span></a></li>').click($.proxy(this._click, this)).appendTo(this._buttonList);
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
	_resize: function() {
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
	_restartTimer: function() {
		if (this._timer !== null) {
			window.clearInterval(this._timer);
		}

		this._timer = window.setInterval($.proxy(this.slideTo, this), this.options.speed);
	},

	/**
	 * Slides to a specific index or to the next slide if no index provided.
	 * 
	 * @param	integer		index
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
