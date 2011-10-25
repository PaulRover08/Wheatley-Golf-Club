$(document).ready(function() {
    var pauseSlideshow = false;
    
    $('#previousSlideshow').click(function() {
        $('#backgrounds').cycle('prev');
    });
    
    $('#pauseSlideshow').click(function() {
        $('#backgrounds').cycle('toggle');
    });
    
    $('#nextSlideshow').click(function() {
        $('#backgrounds').cycle('next');
    });
    
    $('#backgrounds').cycle({
		fx: 'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc…
		timeout:  3000,
		speed: 3000,
		containerResize: 0,
		slideResize: 0
	});
});