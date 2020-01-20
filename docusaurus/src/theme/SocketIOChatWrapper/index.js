import React, { Component } from 'react';
// import { Widget, toggleWidget, dropMessages } from 'react-chat-widget';

// class Chat extends Component {

//   constructor(props) {
//     super(props);
//     this.title = (props.title ? props.title : 'Sample chat');
//     this.subtitle = (props.subtitle ? props.subtitle : 'Test you API using this sample chat');
//     this.senderPlaceHolder = (props.senderPlaceHolder ? props.senderPlaceHolder : 'Type a message ....');
//   }

//   componentDidMount() {
//     dropMessages();
//     toggleWidget();
//   }

//   componentWillUnmount() {
//     dropMessages();
//     toggleWidget();
//   }

//   render() { 
//     return (
//       <div className="chat">
//         <Widget title={this.title}
//           showCloseButton={this.socket}
//           senderPlaceHolder={this.senderPlaceHolder}
//           showChat={true}
//           showAvatar={true}
//           profileAvatar="/img/api/robotic.svg"
//           subtitle={this.subtitle}
//           handleNewUserMessage={this.handleNewUserMessage} />
//       </div>
//     );
//   }
// }

// Wrapping Socket.IO to reconnect and passing
// late observerd values like token
class SocketIOChatWrapper {
  constructor(channel) {
    this.channel = channel;
    this.socket = null;
    return this;
  }
  connect(options) {
    /***
     * extraHeaders cannot be used to pass token
     * first connection in polling is not carrying extrHeaders
      options: {
        query: {
          ${roomName}:  ${roomId},
          token: `${token}`
        }          
      }
    */
    this.disconnect();  
    // var c = this;       
    this.socket = io(this.channel, options)
      .on('error', function (error) { 
        // Authentication fail raises errors
        console.log(`${error}`);
      }).on('connect_error', function(error){
        console.log(`${error}`);
      }).on('connect_error', function(error){
        console.log(`${error}`);
      });
      // .on('connect', function(){
      //   console.log(`${c.channel} connected`);
      // }).on('reconnecting', function(number){
      //   console.log(`reconnecting: ${number}`);
      // }).on('reconnect_error', function(error){
      //   console.log(`reconnect_error: ${error}`);
      // }).on('reconnect_failed', function(){
      //   console.log(`reconnect_failed`);
      // });    
    return this; 
  }
  on(eventName, callback) {
    this.socket.on(eventName, callback);
    return this;
  }
  emit(eventName, message) {
    if(this.socket === null)
      return;
    this.socket.emit(eventName, message);
  }
  disconnect() {
    if(this.socket !== null) {
      this.socket.close();     
    }
  }
}

export default SocketIOChatWrapper;