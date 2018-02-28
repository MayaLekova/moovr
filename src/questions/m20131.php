<?php

$processAnswer = function($answer) {
	$grade = 0;
	$outcome = 'грешен';
	if($answer == 'normal') {
		$grade = 1;
		$outcome = 'верен';
	}
	return array(
		'grade' => $grade,
		'outcome' => $outcome.' отговор'
	);
}

?>
