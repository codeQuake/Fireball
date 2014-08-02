if (!CMS) var CMS = {};

CMS.Content = {};

CMS.Content.Type = {};

CMS.Content.Type.Slideshow = Class.extend({

	_speed: 5000,

	init: function(speed) {
		this._speed = speed;
		this._interval = '';

		//set first as active
		$('.fireballSlideContainer > div:first').css('display', 'block').addClass('active');
		this._interval = setInterval($.proxy(this.slide, this), this._speed);

		//calc max-height
		var max_height = 0;
		$('.fireballSlideContainer').append('<ul class="slideshowButtonList" />');

		$('.fireballSlideContainer').children('div').each(function() {
			if ($(this).outerHeight() >= max_height) max_height = $(this).outerHeight();
			$('.fireballSlideContainer > .slideshowButtonList').append('<li><a><span class="icon icon16 icon-circle"></span></a></li>');
		});

		$('.fireballSlideContainer > .slideshowButtonList > li:first').addClass('active');
		$('.fireballSlideContainer').css('height', max_height);

		$('.fireballSlideContainer > .slideshowButtonList > li > a').click($.proxy(this._click, this));
		$(window).resize($.proxy(this._resize, this));
	},

	slide: function() {
		$active = $('.fireballSlideContainer > div.active');
		if ($active.length == 0) $active = $('.fireballSlideContainer > div:last');

		$next = $active.next('div').length ? $active.next('div') : $('.fireballSlideContainer > div:first');

		$active.addClass('last-active').fadeOut(1500);
		$next.addClass('active').fadeIn(1500);
		$('.fireballSlideContainer > .slideshowButtonList > li').eq($active.index()).removeClass('active');
		$('.fireballSlideContainer > .slideshowButtonList > li').eq($next.index()).addClass('active');
		$active.removeClass('active last-active');
	},

	_click: function (event) {
		event.preventDefault();

		$active = $('.fireballSlideContainer > .slideshowButtonList > li.active');
		oldIndex = $active.index();
		$choosen = event.currentTarget;
		$($choosen.parentNode).addClass('active');
		$active.removeClass('active');

		$newActive = $('.fireballSlideContainer > .slideshowButtonList > li.active');
		newIndex = $newActive.index();

		$('.fireballSlideContainer > div').eq(oldIndex).fadeOut(1500).removeClass('active');
		$('.fireballSlideContainer > div').eq(newIndex).fadeIn(1500).addClass('active');

		clearInterval(this._interval);

	},

	_resize: function() {
		//calc max-height
		var max_height = 0;
		$('.fireballSlideContainer').children('div').each(function() {
			if ($(this).outerHeight() >= max_height) max_height = $(this).outerHeight();
		});
		$('.fireballSlideContainer').css('height', max_height);
	}
});
