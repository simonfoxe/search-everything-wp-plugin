(function ($) {
	var research_topic = function () {
		console.log('Yay!');

		$('#se-meta-search-button').on('click', function () {
			console.log('Button clicked');
			alert('Looking for something?');
		});
	}

	$(research_topic);
}(jQuery));