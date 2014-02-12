(function ($) {
	var research_topic = function () {
	
		$('#se-meta-search-button').on('click', function () {
			var meta_box_results = $('#se-meta-box-results');
			var search_term = $('#se-meta-box-text').val();
			var search_results = 'We found: <ul><li><strong>3 posts</strong> from your blog';
			search_results += '<ul class="se-results-sublevel"><li><a class="se-result-title" href="#">Post #1 title</a><span class="se-result-desc">Short description <strong>' + search_term +'</strong></span></li>';
			search_results += '<li><a class="se-result-title" href="#">Post #2 title</a><span class="se-result-desc">Short <strong>' + search_term + '</strong> description </span></li>';
			search_results += '<li><a class="se-result-title" href="#">Post #3 title</a><span class="se-result-desc">Short description 3 <strong>' + search_term + '</strong></span></li>';
			search_results += '</ul>';
			search_results += '</li><li><strong>0 pages</strong> from your blog<ul class="se-results-sublevel"></ul></li>';
			search_results += '<li><strong>4 related posts </strong>from other publishers';
			search_results += '<ul class="se-results-sublevel"><li><a class="se-result-title" href="#">Post #1 title</a><span class="se-result-desc">Short description <strong>' + search_term +'</strong></span></li>';
			search_results += '</li></ul>';

			//clear results
			meta_box_results.empty();
			meta_box_results.append(search_results);
		});
	}

	$(research_topic);
}(jQuery));