// loader 
$(window).on('load', function(){ // makes sure the whole site is loaded
	//$('#status').fadeOut(); // will first fade out the loading animation
	$('#preloader').delay(350).fadeOut('slow'); // will fade out the white DIV that covers the website.
	//$('body').delay(350).css({'overflow':'visible'});
})

moment.locale('id');
/*
Input		Example			Description
YYYY		2014			4 or 2 digit year
YY			14				2 digit year
Y			-25				Year with any number of digits and sign
Q			1..4			Quarter of year. Sets month to first month in quarter.
M MM		1..12			Month number
MMM MMMM	Jan..December	Month name in locale set by moment.locale()
D DD		1..31			Day of month
Do			1st..31st		Day of month with ordinal
DDD DDDD	1..365			Day of year
X			1410715640.579	Unix timestamp
x			1410715640579	Unix ms timestamp
ddd dddd	Mon...Sunday	Day name in locale set by moment.locale()

H HH		0..23			Hours (24 hour time)
h hh		1..12			Hours (12 hour time used with a A.)
k kk		1..24			Hours (24 hour time from 1 to 24)
a A			am pm			Post or ante meridiem (Note the one character a p are also considered valid)
m mm		0..59			Minutes
s ss		0..59			Seconds
S SS SSS	0..999			Fractional seconds
Z ZZ		+12:00			Offset from UTC as +-HH:mm, +-HHmm, or Z


*/

var db = false;
showJam();
function showJam(){
	$('#jam').html(moment().format("HH.mm[<div>]ss[</div>]"));
	$('#tgl').html(moment().format("dddd, DD MMMM YYYY"));
	setTimeout(showJam,1000);
}














	var currentDate 	= new Date();
	var bulan 			= ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
	var items 			= {
		fajr: 'Subuh',
		//sunrise: 'Sunrise', 
		dhuhr: 'Dzuhur',
		asr: 'Ashar',
		//sunset: 'Sunset',
		maghrib: 'Maghrib',
		isha: 'Isya\''
	};
	var format 			= '24h';
	
	//setting PKPU untuk bekasi
	var lat 		= -6.14;
	var lng 		= 106.59;
	var timeZone 	= 7;
	var dst 		= 0;
	
	prayTimes.adjust({
		fajr	: 20,
		// dhuhr	: '5 min',
		// asr		: 'Standard',
		isha	: 18,
		imsak	: '10 min'
	});
		
	//+2 menit untuk waktu Ihtiyati (pengaman)
	// prayTimes.tune({
		// fajr	: 2,
		// dhuhr	: 2,
		// asr		: 2,
		// isha	: 2,
		// imsak	: 2
	// });
	
	
	//prayTimes.setMethod('Egypt');
	jadwalHariIni();
	
	function jadwalHariIni(){
		let times = prayTimes.getTimes(currentDate, [lat, lng], timeZone, dst, format);
		// times.day = currentDate.getDate();
		// console.log(times);
		var jadwal ='';
		$.each( items, function( k, v) {
			jadwal += '<div class="row"><div class="col-xs-5">'+v+'</div><div class="col-xs-7">'+times[k] +'</div></div>';
			// console.log(v+' : '+times[k]);
		});
		// jadwal += '</div>';
		$('#jadwal').html(jadwal);
	}
	
	
	
/*
$('#running-text .item').marquee({
	//duration in milliseconds of the marquee
	duration: 10000,
	//gap in pixels between the tickers
	// gap: 20,
	//time in milliseconds before the marquee will start animating
	// delayBeforeStart: 0,
	//'left' or 'right'
	// direction: 'left',
	//true or false - should the marquee be duplicated to show an effect of continues flow
	// duplicated: true
});
*/
	