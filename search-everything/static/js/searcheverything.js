var SearchEverything = (function ($) {
	var r = {
			months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
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
			displayResults: function (holder, data) {
				$.each(data, function (i, result) {
					var listItem = $('<li><a title="Insert a link to this post"><h6></h6><p></p></a></li>');

					listItem.find('h6').text(result.post_title || 'Title missing');
					listItem.find('p').text($('<div>' + result.post_content.replace(/\[.*?\]\s?/g, '') + '</div>').text().substring(0, 150) || 'No excerpt');
					listItem.data(result);

					holder.append(listItem);
				});
			},
			performSearch: function () {
				var input = $('#se-metabox-text'),
					results = $('<div id="se-metabox-results"><div id="se-metabox-own-results"><h4>Results from your blog</h4><ul></ul></div><div class="se-spinner"></div></div>');

				$('#se-metabox-results').remove();
				input.closest('div').after(results);

				$.ajax({
					url: input.data('ajaxurl'),
					method: 'get',
					dataType: "json",
					data: {
						action: 'search_everything',
						s: input.prop('value') || ''
					},
					success: function (data) {
						var results = $('#se-metabox-own-results'),
							resultsList = results.find('ul');
						$('.se-spinner, .se-no-results').remove();
						if (data.length === 0) {
							$('#se-metabox-results').append('<p class="se-no-results">It seems we haven\'t found any results for search term <strong>' + input.prop('value') + '</strong>.</p>');
						} else {
							results.show();
							r.displayResults(resultsList, data);
						}
					}
				})
			},
			urlDomain: function (url) { // http://stackoverflow.com/a/8498668
				var a = document.createElement('a');
				a.href = url;
				return a.hostname;
			},
			handleSearch: function () {
				var input = $('#se-metabox-text');
				input.on('keypress', function (ev) {
					if (13 === ev.which) {
						ev.preventDefault(); // Don't actually post... that would be silly
						if (!input.data('apiKey')) {
							r.performSearch();
						}
					}
				});

				$('#se-metabox-search').on('click', function (ev) {
					ev.preventDefault(); // Don't actually go to another page... that would be destructive
					if (!input.data('apiKey')) {
						r.performSearch();
					}
				});
			},
			initResultBehaviour: function () {
				$('#se-metabox').on('click', '#se-metabox-results li', function (ev) {
					var html = $('<div id="se-just-a-wrapper">' +
							'<p>' +
								'<a target="_blank" class="se-box">' +
									'<span class="se-box-heading">' +
										'<span class="se-box-heading-title"></span>' +
										'<span class="se-box-heading-domain"></span>' +
									'</span>' +
									'<span class="se-box-text"></span>' +
									'<span class="se-box-date"></span>' +
								'</a>' +
							'</p>' +
						'</div>'),
						listItem = $(this),
						date = (function () {
							var datePart = listItem.data('post_date').split(' ')[0].split('-'),
								actualDate = new Date(datePart[0], datePart[1]-1, datePart[2]);

							return r.months[actualDate.getMonth()] + ' ' + actualDate.getDate() + ' ' + actualDate.getFullYear();
						}());

					html.find('.se-box-heading-title').text(listItem.data('post_title') || 'Title missing');
					html.find('.se-box-heading-domain').text('(' + r.urlDomain(listItem.data('guid')) + ')');
					html.find('.se-box-text').text($('<div>' + listItem.data('post_content').replace(/\[.*?\]\s?/g, '') + '</div>').text().substring(0, 150) || 'No excerpt');
					html.find('.se-box-date').text(date);
					html.find('.se-box').attr('href', listItem.data('guid'));

					console.log(listItem.data());

					if (send_to_editor) {
						send_to_editor(html.html());
					} else {
						// Dunno yet
					}
				});
			}
		},
		u = {
			initialize: function () {
				r.resizeSearchInput();
				r.handleWindowResize();
				r.handleMetaboxMove();
				r.handleSearch();
				r.initResultBehaviour();

				return this;
			}
		};
	return u;
}(jQuery));

jQuery(function () {
	SearchEverything.initialize();
});
