$('.fancy .slot').jSlots({
    number: 5,
    winnerNumber : [1,2,3,4,5,6,7,8,9,10],
    spinner: '#btn-submit',
    easing: 'easeOutSine',
    time : 3500,
    loops: 5,
    infinite: true,
    minimumSpeed: 1000,
//        endsAt : [7, 6, 5, 4, 3],
    onStart: function (jslot) {
        $('.slot').removeClass('winner');
        slts.push(jslot);
        if (jslot.\$el.hasClass('slot-main')) {
            var req = {};
            req.betPerLine = $('#betPerLine').val();
            req.linesCount = $('#linesCount').val();
            req.denomination = $('#denomination').val();
            $.post(
                    '$drawUrl',
                    req,
                    function (data) {
                        //                console.log(data);
//            console.log(slts);
//            jslot.stop(data.draw);
                $.each(slts, function () {
                    this.stop(data.draw)
                });
                stat = data;
                notify.growl = data.growl;
                        //                setTimeout(function() { jslot.stop(data.draw); }, 100);
                setTimeout(function() {
                    console.log(notify);
                    if(data.win){
                        $.notify( notify.growl, notify.config.win );
                    } else {
                        $.notify( notify.growl, notify.config.loose );
                    }
               }, 5000);
                }
            );
        }
    },
    onEnd: function(finalNumbers) {
        $('.win').text(stat.win);
                $('.account').text(Number(stat.account).formatMoney(2, ',', ' ') + ' ' + currencySymbol);
    },
    onWin: function (winCount, winners) {
        // only fires if you win
//        $.each(winners, function () {
//            if (this.hasClass('slot-main')) {
//                this.addClass('winner');
//                $('.win').text(stat.win);
//                $('.account').text(Number(stat.account).formatMoney(2, ',', ' ') + ' ' + currencySymbol);
//            }
//        });

        // react to the # of winning slots
//        if (winCount === 1) {
//            alert('You got ' + winCount + ' 7!!!');
//        } else if (winCount > 1) {
//            alert('You got ' + winCount + ' 7’s!!!');
//        }
    },
});