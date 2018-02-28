<?php

$processAnswer = function($answer) {
	$grade = 0;
	$outcome = 'грешен';
	if($answer == '(0,1,0)') {
		$grade = 1;
		$outcome = 'верен';
	} else if($answer == '(1,0,0)' || $answer == '(0,0,1)') {
		$grade = 0.5;
		$outcome = 'почти верен';
	}
	return array(
		'grade' => $grade,
		'outcome' => $outcome.' отговор'
	);
}

?>
