'use strict'

const moment = require('moment');

$(function() {
  let startRows = $('.row_time_start');
  startRows.each(function() {
    let start = moment($(this).text());
    let elapsedRow = $(this).next('.row_elapsed');
    let elapsed = moment(elapsedRow.text());
    let duration = moment.duration(elapsed.diff(start));
    let elapsedTime = '';

    if (duration.years() > 0) {
      elapsedTime += duration.years() + 'y. '
    }
    if (duration.months() > 0) {
      elapsedTime += duration.months() + 'm. '
    }
    if (duration.days() > 0) {
      elapsedTime += duration.days() + 'd. '
    }
    // if (elapsedTime.length) {
    //   elapsedTime += '<br>';
    // }

    elapsedTime += duration.hours() + ':' + duration.minutes() + ':' + (duration.seconds() < 9 ? ('0' + duration.seconds()) : duration.seconds())

    elapsedRow.text(elapsedTime);
    // console.log(moment.duration(start.diff(elapsed)).format());
    console.log(moment.duration(start.diff(elapsed)).humanize());

    console.log(duration.hours());
    console.log(duration.minutes());
    console.log(duration.seconds());
  });
});