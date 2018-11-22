'use strict';

const moment = require('moment');
let startTimestamp = moment().startOf('day');
import ws from 'websocket';
// const momentTimer = require('moment-timer');

$(function() {
  let timerInterval;
  let mainCounter = $('#main_counter');
  let mainAction = $('#main_button_action');
  let time = 0;

  $(document).on('click', '.timer_action', function() {
    let button = $(this);
    let counterId = button.data('timer_id');
    let oldState = button.data('state');
    let state = toggleState($(this), status);

    console.log('oldStatus=', oldState);
    if (button.attr('id') !== 'main_button_action') {
      fillMainBlock(button);
      let tr = button.parents(':eq(1)');
      time = tr.find('.row_timer').text();
      tr.find('.counter_state').text(button.data('state'));
    }

    toggleTimer(button, time);
    toggleStopButton(button.next('img.timer_stop'), state);
    toggleImageButton(button, state);

    timerEdit(button, counterId, state);
  });

  function toggleTimer(element, time) {
    if (element.data('state') === 'run') {
      startTimer(element, time)
    } else {
      timerStop()
    }
  }

  function timerStop() {
    clearInterval(timerInterval);
  }

  function startTimer(element, time) {
    if (time > 0) {
      startTimestamp = moment().startOf('day');
      startTimestamp.add(time, 'second');
    }
    timerInterval = setInterval(function() {
      startTimestamp.add(1, 'second');
      mainCounter.html(startTimestamp.format('HH:mm:ss'));
    }, 1000);
  }

  function toggleState(element, state) {
    if (element.attr('id') !== 'main_button_action') {
      let tr = element.parents(':eq(1)');
      tr.find('.counter_state').text(state || element.data('state'));
      toggleState($('#main_button_action'), state);
    }

    if (state) {
      element.data('state', state);
    } else {
      if (element.data('state') === 'stop' || element.data('state') === 'pause') {
        element.data('state', 'run');
        // element.next('img.timer.stop').data('state', 'run');
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
    $('#main_counter').val(parent.find('row_timer').text());
    $('#main_button_action').data('timer_id', element.data('timer_id'));
  }

  function timerEdit(button, counterId, state) {
    $.ajax({
      url: '/timer/edit/' + (counterId || 0),
      type: 'POST',
      dataType: 'json',
      data: {
        state: state,
        name: $('#name_counter').val(),
        project_id: $('#counter_project').val(),
        time: startTimestamp.seconds()
      },
      success: function(data) {
        if (data.id && button) {
          button.data('timer_id', data.id)
        }
        console.log(data);
      }
    });
  }

  $('td.counter_state').each(function() {
    // let mainAction = $('#main_button_action');
    if ($(this).html() === 'run' || $(this).html() === 'pause') {

    }
    if ($(this).html() === 'run') {
      let tr = $(this).parent();
      let projectId = tr.find('.row_project').data('project_id');
      time = tr.find('.row_timer').text();
      startTimestamp.add(time, 'second');
      $('#name_counter').val(tr.find('.row_name').text());
      if (projectId) {
        $('#counter_project').val(projectId);
      }

      mainAction.data('timer_id', tr.find('.timer_action').data('timer_id'));
      mainAction.click();
    } else if($(this).html() === 'pause') {
      // mainAction.data('state', 'pause');
      // let button = $(this).parent().find('.timer_action');
      // let state = toggleState(button, 'pause');
      // toggleImageButton(button, state);
    }
  });

  $('#counter_project').change(function() {
    if (!$('#main_button_action').data('timer_id')) {
      return false;
    }
    timerEdit(false, mainAction.data('timer_id'), mainAction.data('state'));

  });

console.log(ws);
}); //Autorun