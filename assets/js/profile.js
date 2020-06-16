const Timer = new (require('easytimer'));
const channel = 'info/channel';
const WS = require('../../public/bundles/goswebsocket/js/websocket.min');

$(function() {
  // Timer.start({startValues: {seconds: 1}});
  // Timer.addEventListener('secondsUpdated', function (e) {
  //   // console.log(timer.getTotalTimeValues().seconds);
  //   $('#test_timer').html(Timer.getTimeValues().toString(['days', 'hours', 'minutes', 'seconds']));
  // });
  let websocket = WS.connect(T.wsUri);
  let mainCounter = $('#main_counter');
  let mainAction = $('#main_button_action');
  let currentTime = 0;
  let wsSend;

  function loadCounterList(isInit = false) {
    console.log('loadCounterList ', 'isInit=', isInit);
    $.ajax({
      url: '/profile',
      type: 'GET',
      success: function(html) {
        $('#counter_list').html(html);
        if (isInit) {
          pageInit();
        }
      }
    });
  }

  function toggleTimer(element, timeStart) {
    if (element.data('state') === 'run') {
      timerStart(element, timeStart);
    } else if (element.data('state') === 'pause') {
      timerPause();
    } else {
      timerStop();
    }
  }

  function timerStop() {
    Timer.stop();
    Timer.removeEventListener('secondsUpdated');
  }

  function timerPause() {
    Timer.pause();
  }

  function timerStart(element, startTime = 0) {
    let timerId = $(element).data('timer_id');
    if (Timer.isRunning() || timerId !== Timer.id) {
      timerStop();
    }

    Timer.id = timerId;
    Timer.start({startValues: {seconds: startTime}});
    Timer.addEventListener('secondsUpdated', function (e) {
      let format = ['hours', 'minutes', 'seconds'];
      if (currentTime > 86400) {
        format.unshift('days');
      }
      // if (time > 2419200) {
      //   format.unshift('days');
      // }
      $('#main_counter').html(Timer.getTimeValues().toString(format));
    });
  }

  function toggleState(element, state) {
    if (element.attr('id') !== 'main_button_action') {
      let tr = element.parents(':eq(1)');
      tr.find('.counter_state').text(state || element.data('state'));
      toggleState(mainAction, state);
    }

    if (state) {
      element.data('state', state);
    } else {
      if (element.data('state') === 'stop' || element.data('state') === 'pause') {
        element.data('state', 'run');
      } else if (element.data('state') === 'run') {
        element.data('state', 'pause');
      }
    }
    return element.data('state');
  }

  function toggleStopButton(element, state) {

    if (state !== 'pause' && state !== 'run') {

    }

    if (element.attr('id') !== 'main_button_stop') {
      toggleStopButton($('#main_button_stop'));
    }
    if (element.is(':visible')) {
      element.hide();
    } else {
      element.show();
    }
  }

  function toggleImageButton(element, state) {
    let src = '/build/images/';
    let size = '';

    if (element.attr('id') !== 'main_button_action') {
      size = '32';
      toggleImageButton($('#main_button_action'), state);
    }

    if (state === 'run') {
      src = src + 'pause_button' + size + '.png';
    } else if(state === 'pause' || state === 'stop') {
      src = src + 'play_button' + size + '.png';
    }
    element.attr('src', src);
  }

  function fillMainBlock(element) {
    let parent = element.parents(':eq(1)');
    $('#name_counter').val(parent.find('.row_name').text());
    $('#counter_project').val(parent.find('.row_project').data('project_id'));
    $('#main_counter').val(parent.find('.row_timer').text());
    $('#main_button_action').data('timer_id', element.data('timer_id'));
  }

  function timerEdit(button, counterId, state) {
    counterId = counterId || 0;
    let data = {
      state: state,
      name: $('#name_counter').val(),
      projectId: $('#counter_project').val(),
      time: currentTime
    };

    $.ajax({
      url: '/timer/edit/' + (counterId),
      type: 'POST',
      dataType: 'json',
      data: data,
      success: function(data) {
        if (data.id && button) {
          button.data('timer_id', data.id)
        }
        loadCounterList();
        if (typeof wsSend === 'function') {
          data.id = counterId;
          wsSend(data);
        }
      },
      error: function(xhr, textStatus) {
        //window.location.reload();
      }
    });
  }

  $(document).on('click', '.timer_action', function(e) {
    let button = $(this);
    let counterId = button.data('timer_id');
    let oldState = button.data('state');
    let state = toggleState($(this));

    console.log('oldStatus=', oldState);
    if (button.attr('id') !== 'main_button_action') {
      fillMainBlock(button);
      let tr = button.parents(':eq(1)');
      currentTime = parseInt(tr.find('.row_timer').text());
      tr.find('.counter_state').text(button.data('state'));
    }

    if (state === 'pause') {
      currentTime = Timer.getTotalTimeValues().seconds;
    }

    if (e.which) {
      timerEdit(button, counterId, state);
    }

    toggleTimer(button, currentTime);
    toggleStopButton(button.next('img.timer_stop'), state);
    toggleImageButton(button, state);
  });

  function pageInit() {
    console.log('mainButtonData = ', $('#main_button_action').data());
    $('td.counter_state').each(function () {
      if ($(this).html() === 'run' || $(this).html() === 'pause') {

      }
      if ($(this).html() === 'run') {
        let tr = $(this).parent();
        let projectId = tr.find('.row_project').data('project_id');
        currentTime = parseInt(tr.find('.row_timer').text());

        // startTimestamp.add(time, 'second');

        $('#name_counter').val(tr.find('.row_name').text());
        if (projectId) {
          $('#counter_project').val(projectId);
        }

        mainAction
          .data('timer_id', tr.find('.timer_action').data('timer_id'))
          //.data('start_time', time)
        ;
        mainAction.click();
        return false;
      } else if ($(this).html() === 'pause') {
        // mainAction.data('state', 'pause');
        // let button = $(this).parent().find('.timer_action');
        // let state = toggleState(button, 'pause');
        // toggleImageButton(button, state);
      }
    });
  }
  $('#counter_project').change(function() {
    if (!$('#main_button_action').data('timer_id')) {
      return false;
    }
    timerEdit(false, mainAction.data('timer_id'), mainAction.data('state'));

  });

  function refreshCounterPage(data) {
    console.log('refreshCounterPage ' , data);
    $.ajax({
      type: 'post',
      url: 'profile/counter_form',
      data: {data},
      success: function(html) {
        $('#current_counter_block').html(html);
        let isRun = data.state === 'run';
        loadCounterList(isRun);
        if (isRun === false) {
          $('#main_counter').text(data.timerDisplay);
          timerStop();
        }
      }
    });
  }

  websocket.on("socket/connect", function (session) {

    console.log('Connect');

    wsSend = function(data) {

      session.publish(channel, {
        run: ['refreshCounterPage'],
        data: data
      });

      // session.call('sample/timerEdit', data).then(
      //   function (result) {
      //     if (result.id && button) {
      //       button.data('timer_id', result.id);
      //       session.publish(channel, {
      //         run: ['refreshCounterPage'],
      //         data: data
      //       });
      //       loadCounterList();
      //     }
      //     console.log("RPC Valid!", result);
      //   },
      //   function (error, desc) {
      //     console.log("RPC Error", error, desc);
      //   }
      // );
    };

    $('#websock').off().on('click', function() {
      session.publish(channel, {msg: 'send by click'});
      console.log('click websock');
      return false;
    });

    session.subscribe(channel, function (uri, payload) {
      console.log("Received message", payload);
      if (typeof payload.run === 'object') {
        payload.run.forEach(function(exec) {
          try {
            eval(exec)(payload.data);
          } catch(e) {
            console.log(e);
          }
        });
      }
    });

    // session.call("sample/sum", {"term1": 2, "term2": 5}).then(
    //   function (result) {
    //     console.log("RPC Valid!", result);
    //   },
    //   function (error, desc) {
    //     console.log("RPC Error", error, desc);
    //   }
    // );

    // session.publish(channel, {msg: "This is a message!"});

    //session.unsubscribe(channel);

    //session.publish(channel, {msg: "I won't see this"});

  }); //websocket connect;

  websocket.on("socket/disconnect", function (error) {
    //error provides us with some insight into the disconnection: error.reason and error.code
    wsSend = null;
    console.log("Disconnected for " + error.reason + " with code " + error.code);
  });

  pageInit();
}); //Autorun