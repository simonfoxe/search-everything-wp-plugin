var SearchEverything = (function ($) {
	var r = {
			resizeSearchInput: function () {
				var input = $('#se-metabox-text');

				input.css({
					'width': input.closest('div').outerWidth(true) - input.next('a').outerWidth(true)
				});
			},
			handleWindowResize: function () {
				$(window).resize(r.resizeSearchInput);
			},
			handleMetaboxMove: function () {
				$('.meta-box-sortables').on('sortstop', function (event, ui) {
					if (ui.item && ui.item.length && ui.item[0].id === 'se-metabox') {
						r.resizeSearchInput();
					}
				});
			},
			performLocalSearch: function () {
				var input = $('#se-metabox-text');

				$.ajax({
					url: input.data('ajaxurl'),
					method: 'get',
					dataType: "json",
					data: {
						'action': 'wp_ajax_search_everything',
						's': input.prop('value') || ''
					},
					success: function (data) {
						console.log(data);
					}
				})
			},
			handleSearch: function () {
				$('#se-metabox-text').on('keypress', function (ev) {
					if (13 === ev.which) {
						ev.preventDefault(); // Don't actually post... that'd be silly
						r.performLocalSearch();
					}
				});

				$('#se-metabox-search').on('click', function (ev) {
					ev.preventDefault(); // Don't actually go to another page, that would really screw things up
					r.performLocalSearch();
				});
			}
		},
		u = {
			initialize: function () {
				r.resizeSearchInput();
				r.handleWindowResize();
				r.handleMetaboxMove();
				r.handleSearch();

				return this;
			}
		};
	return u;
}(jQuery));
jQuery(function () {
	SearchEverything.initialize();
});