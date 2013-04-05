<html>
<head>
	<title>News Recommender</title>
	<script type="text/javascript" src="assets/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="assets/jqueryplugins.js"></script>

	<link rel="stylesheet" type="text/css" href="assets/recstyle.css" />

	<script>
	StorySet = null;
	START_TIME = new Date();
	
	function getTime(){
		var currentTime = new Date();
		return (currentTime - START_TIME);
	}
	
	// Create a table for categories
	function generateTable(divSelector, leftSideKey, rightSideKey){
		var $tableHTML = $('<table>').attr('class', 'news-cat');
		
		// print category headers;
		$tableHTML.append(
			$('<tr>').append(
				$('<th>').attr('class', 'cat-header').text(StorySet[leftSideKey][1]['cat']).after(
				$('<th>').attr('class', 'save-label').text("save article")).after(
				$('<th>').attr('class', 'cat-header').text(StorySet[rightSideKey][1]['cat']).after(
				$('<th>').attr('class', 'save-label').text("save article"))
				))
		);

		// Each row contains the story on the left side's category and story on the right side's category
		for(var i=1; i<=5; i++){
			var $storyRow = $('<tr>');
			$storyRow.append(
				$('<td>').attr('class', 'story-cell-left').append(
					$('<label>').html(StorySet[leftSideKey][i]['title']).append(
						$('<p>').html(StorySet[leftSideKey][i]['desc']).attr('class', 'desc')
					)
				).after(
				$('<td>').append(
					$('<input>').attr({
						class: 'save-box left-checkbox',
						type: 'checkbox',
						catorder: StorySet[leftSideKey][i]['catorder'],
						storyorder: StorySet[leftSideKey][i]['storyorder']
					})
				).after(
				$('<td>').attr('class', 'story-cell-right').append(
					$('<label>').html(StorySet[rightSideKey][i]['title']).append(
						$('<p>').html(StorySet[rightSideKey][i]['desc']).attr('class', 'desc')
					)
				).after(
				$('<td>').append(
					$('<input>').attr({
						class: 'save-box right-checkbox',
						type: 'checkbox',
						catorder: StorySet[rightSideKey][i]['catorder'],
						storyorder: StorySet[rightSideKey][i]['storyorder']
					})
				)
			))));	
			$tableHTML.append($storyRow);
		}
		$(divSelector).html($tableHTML);

		// when user selects article
		$('table input.save-box').click(function() {
			// highlights checked articles
			$(this).parent().toggleClass('saved-art');
			if($(this).hasClass('left-checkbox')){
				$(this).closest('tr').find('.story-cell-left').toggleClass('saved-art');
			}
			else if(($(this).hasClass('right-checkbox'))){
				$(this).closest('tr').find('.story-cell-right').toggleClass('saved-art');
			}
			
			// record article is selected
			var catorder = $(this).attr('catorder'),
				storyorder = $(this).attr('storyorder');

			if($(this).is(':checked')){
				StorySet[catorder][storyorder].selecttime = getTime();
			}
			// Reset to sentinel value when they unselect
			else{
				StorySet[catorder][storyorder].selecttime = -1;
			}
		});

	}

	function populateDOM(firstSet){
		var leftSideKey, rightSideKey;
		
		// In the StorySet json object
		// StorySet[1] and StorySet[2] are the left and right side articles of first page respectively
 		// StorySet[3] and StorySet[4] are the left and right side articles of second page respectively
		if(firstSet === "FIRST_SET"){
			leftSideKey = 1;
			rightSideKey = 2;	
		}
		else{
			leftSideKey = 3;
			rightSideKey = 4;
		}

		generateTable("#story-table-div", leftSideKey, rightSideKey);
	}
	
	function finishExperiment(){
		$.ajax({
			type : "POST",
			async : false,
			url : "insertrecord.php",
			data: { json : StorySet, guid : $.QueryString['guid']},
			dataType : 'json',
			success : function(data){
			debugger;
			window.location.href(
				"https://stanforduniversity.qualtrics.com/SE/?SID=SV_9oCfYU57kAdJKV6?guid=" 
				+ $.QueryString['guid'] + "rversion=" + $.QueryString['rversion']);
			}
		});
	}
	
	function generateHistoryInterface(divSelector){
		var $tableHTML = $('<table>');
				
		// print category headers;
		$tableHTML.append(
			$('<tr>').append(
				$('<th>').text("saved stories").attr('class', 'hide-header-left').after(
				$('<th>').attr('class', 'hide-header-right').text("hide article"))
				)
		);

		// loop through categories
		for(var i=1; i<=4; i++){
			// loop through stories within categories
			for(var j=1; j<=5; j++){
				if(StorySet[i][j].selecttime > -1){
					var $storyRow = $('<tr>');
					$storyRow.append(
						$('<td>').attr('class', 'hide-cell-left').append(
							$('<label>').html(StorySet[i][j]['title']).append(
								$('<p>').html(StorySet[i][j]['desc']).attr('class', 'desc')
							)
						).after(
						$('<td>').attr('class', 'hide-cell-right').append(
							$('<button>').attr({
								class: 'hide-btn-active',
								catorder: StorySet[i][j]['catorder'],
								storyorder: StorySet[i][j]['storyorder']
							}).html("Hide")
						)));
					$tableHTML.append($storyRow);
				}
			}
		}
		$(divSelector).html($tableHTML);
		
		// when user hides an article
		$('.hide-btn-active').click(function() {
			var input = this,
				$parenttd = $(this).parent(),
				$parenttr = $(this).closest('tr').find('.hide-cell-left'),
				catorder = $(this).attr('catorder'),
				storyorder = $(this).attr('storyorder');
			$parenttd.toggleClass('hidden-art');
			$parenttr.toggleClass('hidden-art');
			
			$(this).html("Hidden");
			$(this).removeClass("hide-btn-active");
			$(this).toggleClass("hidden-btn");
			this.disabled = true;
			
			StorySet[catorder][storyorder].hidetime = getTime();
	   	});
		$('#button-container').html('<button id="finish-hide">Finished Hiding Articles</button>');
		
		$("#finish-hide").click(function() {
			$.ajax({
				type : "POST",
				async : false,
				url : "insertrecord.php",
				data: { json : StorySet},
				dataType : 'json',
				success : function(data){
					finishExperiment();
				}
			})
		});
	}
	
	$(document).ready(function(){
		$.ajax({
			type : "GET",
			async : false,
			url : "getarticles.php",
			dataType : 'json',
			success : function(data){
				StorySet = data;
				populateDOM("FIRST_SET");
			}
		})
		
		$("#next-set").click(function() {
			populateDOM("SECOND_SET");
			$('body').remove("#next-set");
			$('#button-container').html('<button id="finish-select">Finished Selecting Articles</button>');
	    
			$("#finish-select").click(function() {				
				if($.QueryString['rversion'] === "3"){
					generateHistoryInterface("#story-table-div");
				}
				else{		
					finishExperiment();
				}
			});
	    });
	});
	</script>	
</head>

<body>
	<div id="story-table-div"></div>
	<div id="button-container"><button id="next-set">Load Next Set of Stories</button></div>
</body>
</html>