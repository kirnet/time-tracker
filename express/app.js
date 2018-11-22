const createError = require('http-errors');
const express = require('express');
const path = require('path');
const cookieParser = require('cookie-parser');
const logger = require('morgan');
const mysql = require('mysql');
const unserializer = require('php-session-unserialize')


const con = mysql.createConnection({
  host: "localhost",
  database: 'mytimetracker',
  user: "root",
  password: "mypass"
});

con.connect(function(err) {
  if (err) throw err;
  console.log("mysql connected!");
});

const indexRouter = require('./routes/index');
const usersRouter = require('./routes/users');

const wss = new (require('ws')).Server({port: 3001});

const app = express();

// view engine setup
app.set('views', path.join(__dirname, 'views'));
app.set('view engine', 'pug');

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
app.use(cookieParser());
app.use(express.static(path.join(__dirname, 'public')));

app.use('/', indexRouter);
app.use('/users', usersRouter);

// catch 404 and forward to error handler
app.use(function(req, res, next) {
  next(createError(404));
});

// error handler
app.use(function(err, req, res, next) {
  // set locals, only providing error in development
  res.locals.message = err.message;
  res.locals.error = req.app.get('env') === 'development' ? err : {};

  // render the error page
  res.status(err.status || 500);
  res.render('error');
});

wss.on('connection', function (ws, req) {
  let phpSessionId;
  ws.on('close', function() {
    console.log('connection close ');
  });

  ws.on('ping', function(ts) {
    console.log("Got a ping");
  });

  ws.on('pong', function(ts) {
    console.log("Got a pong", ts);
  });

  ws.on('error', function() {
    console.log('websocket error');
  });

  ws.on('message', function incoming(message) {
    console.log(message);
  });
  console.log('WS connection');
  phpSessionId = getSessionId(req);
  console.log(phpSessionId);
  let role = getUserRole(phpSessionId);
  role.then((session) => {
    let role = session.match(/(ROLE_USER|ROLE_ADMIN)/);
    if (!role) {
      ws.terminate();
    }
    console.log('xxxx', typeof role, role);

  });
  // console.log('Role=', role.then(value => {
  //   console.log(value, 'xxxxxx')
  // }));
  // if (role.length === 0) {
  //   ws.terminate();
  // }
}); //Ws connection

function getSessionId(req) {
  let index = req.rawHeaders.findIndex(value => /PHPSESSID/.test(value));
  if (index !== -1) {
    let cookieArray = req.rawHeaders[index].split(';');
    return cookieArray[cookieArray.findIndex(value => /PHPSESSID/.test(value))].split('=')[1];
  }
}

function getUserRole(phpSessionId) {
  return new Promise(function(resolve, reject) {
    con.query("SELECT * from sessions WHERE sess_id='" + phpSessionId + "'", function(err, res) {
      if (err) throw err;
      if (res.length === 0) {
        return [];
      }
      //console.log(res[0]);
      // let role = ‌‌res[0].sess_data.toString('utf-8'); //.match('/ROLE_USER|ROLE_ADMIN/');
      //console.log('----------------------------------------');
      // resolve(unserializer(res[0].sess_data.toString('utf-8')));
      resolve(res[0].sess_data.toString('utf-8'));
      // console.log(session);
      // console.log('----------------------------------------');
    });
  });


}

module.exports = app;
