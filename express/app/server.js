var express = require('express')
  , app = express()
var cors = require('cors')
var http = require('http').Server(app);
var io = require('socket.io')(http);
const bodyParser = require('body-parser');
const buildOptions = require('minimist-options');
const minimist = require('minimist');

// Enable cors
app.use(cors())

// Google dialogflow
var BladeRunnerChat = require('./services/blade-runner-chat');

// Auth UI
var jwt = require('express-jwt'); // triggers "jsonwebtoken": "^8.1.0",
var jsonwebtoken = require('jsonwebtoken');

// Auth webhook
const hmac = require('hmac-auth-express');
const { HMACAuthError } = require('hmac-auth-express/src/errors');

// User define listen
const options = buildOptions({
  port: {
      type: 'number',
      alias: 'p',
      default: 8080
  }
});
const args = minimist(process.argv.slice(2), options);
var port = process.env.PORT || ( args.port || 8080 );


// Static resources
app.use('/assets', express.static('assets'))
app.use(
  bodyParser.urlencoded({
    extended: true
  })
);
app.use(bodyParser.json());

// Include events, config and other user defined classes
var config = require('./config');

// Socket io messaging
app.get('/', function(req, res){
  res.sendFile(__dirname + '/index.html');
});


// Webhook
const wc = io.of('/webhook-chat');
wc.on('connection', function(socket){
  var query = socket.handshake.query;
  var apiKey = query.apiKey;
  socket.join(apiKey); 
});

app.get('/webhook/timestamp',
  (req, res) => {
    return res.json({
      "unix_timestamp": Date.now().toString(),
      "maxInterval": config.hmac.maxInterval});
  });
app.post('/webhook',
  hmac(config.hmac.secret, {
    algorithm: 'sha512',
    identifier: 'HMAC',
    header: 'authorization',
    maxInterval: config.hmac.maxInterval
  }),
  (req, res) => {
    wc.to(req.query.api_key).emit('message.sent', req.body.message);
    return res.json({"message":"Great !"});
  });


// Blade runner chat
const brc = io.of('/blade-runner-chat');

// Socket.io authentication middleware
// working just in polling mode
brc.use((socket, next) => { 
  socket.request.user = null;
  // https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
  if(!socket.handshake.query)
    return next(new Error('Token required')); 
  if(!socket.handshake.query.token)
    return next(new Error('Token required')); 
  try {
    jsonwebtoken.verify(socket.handshake.query.token, config.jwt.secret, function(err, decoded) {
      if(err) return next(new Error('Invalid token'));
      socket.request.user = {
        sub: decoded.sub,
        email: decoded.email};
      return next();
    });
  }
  catch(error) {
    return next(new Error(error))
  }   
});


brc.on('connection', function(socket){  

  var query = socket.handshake.query;
  var sessionId = query.sessionId;
  socket.join(sessionId); 

  // Blade runner chat
  socket.on('question', function(msg){
    const dialog = new BladeRunnerChat(
      sessionId, config.google.project);
    dialog.question(msg, 'en-US').then(
        (value) => {
          brc.to(sessionId).emit('answer', value);
        }, 
        (error) => {
          console.log(error);
        });     
  });

});

// Override default error handling to be JSON compliant 
app.use(function (err, req, res, next) {
  
  // check by error instance
  if (err instanceof HMACAuthError) {
    res.status(401).json({
      error: 'Invalid request',
      info: err.message
    })
  }
 
  // alternative: check by error code
  if (err.code === 'ERR_HMAC_AUTH_INVALID') {
    res.status(401).json({
      error: 'Invalid request',
      info: err.message
    })
  }

  if (err.name === 'UnauthorizedError') {
    return res.status(401).json({"message":"invalid token"});
  }
  return res.status(500).json({
    'message': err.message});  
});

http.listen(port, function(){
  console.log('listening on *:' + port);
});


