'use strict';

// const webSocket = require('reconnecting-websocket');
import ReconnectingWebSocket from 'reconnecting-websocket';
const ws = new ReconnectingWebSocket('ws:' + window.location.hostname + ':' + 3001);

ws.onopen = function(e) {
  console.log("Connection established! ", window.location.pathname);
  console.log(e);
  //ws.send('aaaaa');
};

console.log(ws);
