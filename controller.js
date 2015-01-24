var http = require('http');
var url = require('url');
var gpio = require('pi-gpio');

var train = {
	pins: {
		fwd: 11,
		rev: 13,
		spd: 12
	}
}

http.createServer(function (req, res) {
  res.writeHead(200, {'Content-Type': 'text/plain'});
  res.end('Hello World\n');

  gpio.open(train.pins.fwd, 'output');
  gpio.open(train.pins.rev, 'output');

  var parsed = url.parse(req.url, true);

  switch (parsed.query.c) {

  	case 'status':
  		console.log('status!');
  	break;

  	case 'direction':

  		switch (parsed.query.v) {
  			case 'forward':
  				gpio.write(train.pins.fwd, 1);
  				gpio.write(train.pins.rev, 0);
  			break;
  			case 'reverse':
  				gpio.write(train.pins.fwd, 0);
  				gpio.write(train.pins.rev, 1);
  			break;
  			case 'neutral':
  				gpio.write(train.pins.fwd, 0);
  				gpio.write(train.pins.rev, 0);
  			break;
  		}

  	break;

  }

  //console.log('hello');

}).listen(1337, '0.0.0.0');

function getDirection() {
	var fwd = gpio.read(train.pins.fwd);
	var rev = gpio.read(train.pins.rev);
	return fwd - rev;
}

console.log('Server running at http://127.0.0.1:1337/');