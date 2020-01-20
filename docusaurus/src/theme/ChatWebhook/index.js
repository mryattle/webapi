import React, { Component } from 'react';
import { ThemeProvider, MessageList, MessageGroup, 
  Message, MessageText,
  Row, Avatar } from '@livechat/ui-kit'
import SocketIOChatWrapper from '@theme/SocketIOChatWrapper';

class ChatWebhook extends Component {
  constructor(props) {
    super(props);  
    this.state =  {messages: []}       
    this.socket = new SocketIOChatWrapper('/webhook-chat');
  }

  componentDidMount() {
    var component = this;
    this.socket.connect({
      query: {
        apiKey:  http_client.api_key
      }
    }).on('message.sent', function(msg){
      let messages = component.state.messages;
      messages.push({bot: true, text: msg});
      component.setState(state => ({ 
        messages: messages})); 
    });     
  }

  componentWillUnmount() {
    this.socket.disconnect();
  }  

  render() {
    
    return (
      <div className="message-chat">
        <div className="header">
          <div className="title">Movie chat</div>
          <div className="subtitle">Webhook compliant app will show new messages</div>
        </div>
        <ThemeProvider theme={{
          MessageList: {
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
        </ThemeProvider>
      </div>  
    )
  
  } 

}

export default ChatWebhook;