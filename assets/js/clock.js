/**
 * This code is just to set the initial timing, it could be done on the server side without needing any JS
 */

$(function() {
  let date = new Date();
  let seconds = date.getSeconds() * 6;
  let minutes = date.getMinutes() * 6 + seconds/60;
  let hours = (180 + date.getHours()%12 * 30 + minutes / 15);

  $('.box.seconds').css('transform', 'rotate(' + (180 + seconds) + 'deg)');
  $('.box.minutes').css('transform', 'rotate(' + (180 + minutes) + 'deg)');
  $('.box.hours').css('transform', 'rotate(' + hours + 'deg)');
});
