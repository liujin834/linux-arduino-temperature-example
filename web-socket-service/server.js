var io = require('socket.io')(8099);
var fs = require('fs');
var pg = require('pg');
var conString = "postgres://sensor:sensor@127.0.0.1:5489/sensor";

var client = new pg.Client(conString);

io.on('connection', function (socket) {
	socket.on('get data',function(data){
		var current_id = data.id;
		console.log(current_id);
		var send = setInterval(function (){
			pg.connect(conString, function(err, client, done) {
				if(err) {
					return console.error('error fetching client from pool', err);
				}

				var query_string = 'SELECT *,' +
					"date_part('year',ts_observe) AS observe_year," +
					"date_part('month',ts_observe) AS observe_month," +
					"date_part('day',ts_observe) AS observe_day," + 
					"date_part('hour',ts_observe) AS observe_hour," + 
					"date_part('minute',ts_observe) AS observe_minute" + 
					' FROM se_temperature WHERE id > $1';

				client.query(query_string, [current_id], function(err, result) {
				
				done();

			    if(err) {
			      return console.error('error running query', err);
			    }
			    console.log(result.rows);
			    socket.emit('latest data', result.rows);

				});
			});
		}, 1000 * 60);

		socket.on('disconnect', function () {
			clearInterval(send);
		});
	});//get data
});//io