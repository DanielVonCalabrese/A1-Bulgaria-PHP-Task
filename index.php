<?php

require_once "app/Connection.php";
require_once "app/NumericRanges.model.php";

use TestTaskA1\Connection as Connection;
use TestTaskA1\NumericRanges as NumericRanges;

define( "COLORS", [ 'red', 'blue', 'green' ] );


try {
    $pdo = Connection::get()->connect();
} catch(\PDOException $e) {
    echo $e->getMessage();
}

$items = [];
$tableTitle = 'No Result';
$numericRangesModel = new NumericRanges($pdo);

if(isset($_POST['submit']) === true && isset($_POST['inputNumber']) === true && $_POST['inputNumber'] !== '') {
	$inputNumber = $_POST['inputNumber'];
	$result = $numericRangesModel->getNumberColor($inputNumber);
	if (count($result) > 0) {
		$tableTitle = 'Result for <strong>' . $inputNumber . '</strong> is ' . $result['color'];
	} else {
		$tableTitle = 'No result for number <strong>' . $inputNumber . '</strong>';
	}
} else {
	$inputNumber = '';
}

$items = $numericRangesModel->getAllItems();

?>

<!DOCTYPE html>
<html>
    <head>
        <title>A1 Bulgaria PHP Task</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">
		<link rel="stylesheet" href="./assets/css/style.css">
		<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> 
		<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>
		<meta charset="utf-8" /> 
    </head>
    <body>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand" href="#">A1 Bulgaria PHP Task</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarCollapse">
				<ul class="navbar-nav mr-auto">
				</ul>
				<form class="form-inline mt-2 mt-md-0" method="post" action="" name="inputForm" id="inputForm">
					<input class="form-control mr-sm-2" type="text" placeholder="Input a number" aria-label="Input" name="inputNumber" value="<?php echo $inputNumber ?>">
					<input class="btn btn-outline-success my-2 my-sm-0" type="submit" name="submit" value="Submit">
				</form>
			</div>
		</nav>
        <div class="container my-5">
			<div class="row">
				<h1 class="mt-5"><?php echo $tableTitle; ?></h1>	
				<?php if (count($items) > 0): ?>
					<p class="ui-state-default ui-corner-all p-1 mt-5">
						<span class="ui-icon ui-icon-signal"></span>
						Color ranges
					</p>
						<?php foreach($items as $row): ?>
							<div class="col-sm-12 col-md-4 slider-holder">
								<input dbId="<?php echo $row['id'] ?>" class="rangeTo mb-3" type="text" id="amount-rangeTo-<?php echo $row['id'] ?>" name="rangeTo[<?php echo $row['id'] ?>]" value="<?php echo $row['rangeTo'] ?>">
								<div dbId="<?php echo $row['id'] ?>" class="slider-range" id="slider-range-<?php echo $row['id'] ?>" color="<?php echo $row['color'] ?>" rangeFrom="<?php echo $row['rangeFrom'] ?>" rangeTo="<?php echo $row['rangeTo'] ?>"></div>
								<input dbId="<?php echo $row['id'] ?>" class="rangeFrom my-3" type="text" id="amount-rangeFrom-<?php echo $row['id'] ?>" name="rangeFrom[<?php echo $row['id'] ?>]" value="<?php echo $row['rangeFrom'] ?>">
								<br />
								<select dbId="<?php echo $row['id'] ?>" class="color" name="color[<?php echo $row['id'] ?>]" name="color-<?php echo $row['id'] ?>" id="color-<?php echo $row['id'] ?>">
									<?php foreach(COLORS as $color): ?>
										<option value="<?php echo $color ?>" <?php echo $color === $row['color'] ? 'selected' : '' ?> ><?php echo $color ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						<?php endforeach; ?>
						<input class="btn btn-outline-success my-5 mx-auto add-new-slider-btn" type="submit" name="submit" value="Add new slider">
				<?php else: ?>
					No Items
				<?php endif; ?>
			</div>
        </div>
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script type="text/javascript">
			$( function() {
				var url = 'app/ProcessRanges.php';
				
				$( ".slider-range" ).each(function(index) {
					var values = [ parseInt($( this ).attr('rangeFrom')), parseInt($( this ).attr('rangeTo')) ];
					$( this ).empty().slider({
						orientation: "vertical",
						range: true,
						min: -128,
						max: 127,
						values:values,
						animate: true,
						change: function( event, ui ) {
							$( "#amount-rangeFrom-" + parseInt(index + 1) ).val( ui.values[ 0 ] );
							$( "#amount-rangeTo-" + parseInt(index + 1) ).val( ui.values[ 1 ] );
							
							var sendData = {
								id: $( this ).attr('dbId'),
								rangeFrom: ui.values[0],
								rangeTo: ui.values[1],
								color: $( 'select#color-' + $( this ).attr('dbId') ).val()
							}

							ajaxRequest(sendData, url);
						}
					});
				});
				
				$( '.rangeFrom, .rangeTo, .color' ).on( 'change', function(){							
					var sendData = {
						id: $( this ).attr('dbId'),
						rangeFrom: $( 'input#amount-rangeFrom-' + $( this ).attr('dbId') ).val(),
						rangeTo: $( 'input#amount-rangeTo-' + $( this ).attr('dbId') ).val(),
						color: $( 'select#color-' + $( this ).attr('dbId') ).val()
					}
					
					ajaxRequest(sendData, url);
				});
				
				$( '.add-new-slider-btn' ).on( 'click', function(e){
					e.preventDefault();
				});
				
				function ajaxRequest(sendData, url) {
					$.ajax({
						method: 'POST',
						url: url,
						data: sendData,
						success(data) {
							console.log(data);
							$( '.slider-response-alert-message, .slider-response-success-message' ).remove();
							if (data === 'true') {
								$( '<p class="ui-state-default ui-corner-all p-1 mt-5 text-center slider-response-success-message success-message">Ranges are set successfully!</p>' ).prependTo('.row');
							} else {
								$( '<p class="ui-state-default ui-corner-all p-1 mt-5 text-center slider-response-alert-message alert-message">Overlapping ranges! Ranges not set! Please try again.</p>' ).prependTo('.row');
							}
						},
						complete: function(data) {
							

						},
						error(error) {
							console.log(error);
						}
					});
				}
			} );
        </script>
    </body>
</html>