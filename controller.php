<form action="index.php" method="GET">
	<button name="v" value="forward">Forward</button>
	<button name="v" value="neutral">Neutral</button>
	<button name="v" value="reverse">Reverse</button>
	<input type="hidden" name="c" value="direction">
</form>

<?php
	
	$config['pwm_pin'] = 1;
	$config['fwd_pin'] = 0;
	$config['rev_pin'] = 2;

	function get_pin_status( $pin_num ) {
		return exec( "/usr/local/bin/gpio readall | grep 'GPIO. " . $pin_num . "' | awk {'print $11'}" );
	}

	function set_pin( $pin_num, $value ) {
		exec( 'sudo /usr/local/bin/gpio write ' . $pin_num . ' ' . $value );
	}

	switch ( $_GET['c'] ) {
		case 'status':
			$speed = file_get_contents( '/tmp/speed' );

			$fwd_pin = get_pin_status( $config['fwd_pin'] );
			$rev_pin = get_pin_status( $config['rev_pin'] );

			$direction = $fwd_pin . $rev_pin;

			echo 'Direction: ';

			switch ( $direction ) {
				case '10':
					echo 'Forward';
				break;
				case '01':
					echo 'Reverse';
				break;
				case '00':
					echo 'Neutral';
				break;
			}

			echo '<br>';

			echo 'Speed: ' . $speed;
		break;

		case 'speed':
			exec( 'sudo /usr/local/bin/gpio pwm ' . $config['pwm_pin'] . ' ' . $_GET['v'] );
			file_put_contents( '/tmp/speed', $_GET['v'] );
		break;

		case 'direction':
			echo "Changing direction!";
			switch ( $_GET['v'] ) {
				case 'forward':
					echo "Forward!";
					set_pin( $config['fwd_pin'], 1 );
					set_pin( $config['rev_pin'], 0 );
				break;
				case 'reverse':
					echo "Reverse!";
					set_pin( $config['fwd_pin'], 0 );
					set_pin( $config['rev_pin'], 1 );
				break;
				case 'neutral':
					echo "Neutral!";
					set_pin( $config['fwd_pin'], 0 );
					set_pin( $config['rev_pin'], 0 );
				break;
			}
		break;
	}

?>