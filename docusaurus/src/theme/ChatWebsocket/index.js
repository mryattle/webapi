import React, { Component } from 'react';
import { ThemeProvider, MessageList, MessageGroup, 
  Message, MessageText,
  Row, Avatar, TextComposer, TextInput, SendButton } from '@livechat/ui-kit'
import SocketIOChatWrapper from '@theme/SocketIOChatWrapper';

class ChatWebsocket extends Component {

  constructor(props) {
    super(props);
    this.state =  {
      messages: [],
      http_client_subscribe: null
    }
    this.socket = new SocketIOChatWrapper('/blade-runner-chat');
  }

  componentDidMount() {
    // JWT Token
    var component = this;
    const http_client_subscribe =  http_client.token.subscribe(
      function (value) {
        const options = {
          query: {
            sessionId:  http_client.session_id,
            token: value
          }
        };
        /*** Autentication
        	Socket.IO https://socket.io/docs/server-api/#new-Server-httpServer-options is using distinct connections to:
        	+ initialize websocket connection params via HTTP
        	+ create a Websocket to exchange messages with clients
        	RFC 6455 says "The WebSocket
   server can use any client authentication mechanism available to a
   generic HTTP server" https://tools.ietf.org/html/rfc6455#page-53, we will use Socket.IO HTTP connection to authenticate clients
        */        
        component.socket.connect(options).on('answer', function(msg){
          let messages = component.state.messages;
          messages.push({bot: true, text: msg});
          component.setState(state => ({ 
            messages: messages})); 
        });      
      },
      function (error) {
        console.log(`${error}`);
      },
      function () {
        console.log('Completed');
      });    
      this.setState(state => ({ 
        http_client_subscribe: http_client_subscribe}));        
  }    

  componentWillUnmount() {
    if(this.state.http_client_subscribe === null)
      return;
      this.state.http_client_subscribe.dispose();
    this.socket.disconnect();       
  }   

  askQuestion(msg) {
    let messages = this.state.messages;
    messages.push({bot: false, text: msg});
    this.setState(state => ({ 
      messages: messages})); 
    this.socket.emit('question', msg);
  }

  onSend(msg) {
    this.chat.askQuestion(msg)
  }  

  render() {
    return (
      <div className="message-chat">
        <div className="header">
          <div className="title">Blade Runner Chat</div>
          <div className="subtitle">Websocket compliant chat will answer to your questions</div>
        </div>
        <ThemeProvider theme={{
            TextComposer: {
              css: {
                  borderRadius: '0px 0px 8px 8px',
              },
            },
          }}>    
          <div className="message-list" style={{ height: 300 }}>
            <MessageList active>
              <MessageGroup onlyFirstWithMeta>
                {this.state.messages.map(function(message, index){
                    if(message.bot) {
                      return <Row key={ index }>
                        <Avatar imgUrl='/img/api/robotic.svg' />
                        <Message className='bot'>
                          <MessageText>{ message.text }</MessageText>
                        </Message>
                      </Row>;
                    }
                    return <Row key={ index } reverse>
                      <Message className='me'>
                        <MessageText>{ message.text }</MessageText>
                      </Message>
                    </Row>;
                })}               
              </MessageGroup>
            </MessageList>
          </div>
          <TextComposer chat={this} onSend={this.onSend}>
            <Row align="center">
              <TextInput placeholder="Ask a question about Blade Runner ..." fill="true" />
              <SendButton fit />
            </Row>
          </TextComposer>      
        </ThemeProvider>
      </div>  
    )
  
  }   

}

export default ChatWebsocket;