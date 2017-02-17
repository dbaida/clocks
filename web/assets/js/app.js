(function($) {

    var intervalId,
        $timeContainer = $('#time-container'),
        $refreshButton = $timeContainer.find('button'),
        millisecondsInSecond = 1000,
        millisecondsInMinute = 60000;


    var _setTimeInterval = function(timeInterval) {
        if (intervalId) {
            clearInterval(intervalId);
        }

        intervalId = setInterval(function() {
            _syncTimeWithServer();
        }, timeInterval);
    };


    var _initClocks = function() {
        _updateClocks($timeContainer.data('time'));
    };


    var _updateClocks = function(clocks) {
        var i,
            clock,
            hours,
            minutes,
            seconds;

        for (i in clocks) {
            clock   = clocks[i];
            hours   = clock.h;
            minutes = clock.i;
            seconds = clock.s;

            $timeContainer.find('#time-' + i).text(hours + ':' + minutes);
        }

        var checkAfterMilliseconds = (millisecondsInMinute - (parseInt(seconds) * millisecondsInSecond));

        _setTimeInterval(checkAfterMilliseconds);
    };


    var _syncTimeWithServer = function() {
        $.ajax({
            'url'     : '/actual_time',
            'dataType': 'json',
            'success' : function(response) {
                _updateClocks(response.data);
            }
        });
    };


    var _bindEvents = function() {
        $refreshButton.on('click', _syncTimeWithServer);
    };


    var _init = function() {
        _initClocks();
    };


    _init();
    _bindEvents();

})(jQuery);