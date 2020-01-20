/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

import React, { Component } from 'react';

class TabItemCurl extends Component {
    
    constructor(props) {
        super(props);
        this.state =  { 
            hostname: null,
            uuid: null,
            token: null,
            http_client_token_sub: null
        }   
    }

    componentDidMount() {  
        this.setState(state => ({ 
            hostname: window.location.hostname}));         
        const component = this; 
        const http_client_token_sub = http_client.token.subscribe(
            function (value) {
                component.setState(state => ({ 
                    token: value,
                    uuid: http_client.newRequestUuid()})); 
            },
            function (err) {
                // console.log('Error: ' + err);
            },
            function () {
                // console.log('Completed');
            });               
        this.setState(state => ({ 
            http_client_token_sub: http_client_token_sub}));            
        http_client.renewUuid();  
    }

    componentWillUnmount() {
        this.state.http_client_token_sub.dispose();
    }    
    
    replaceVars(children) {
        return React.Children.map(children, (childNode) => {
            if (typeof childNode === "string")
                return childNode; // cover case: <div>text<div></div></div>
            if (typeof childNode.props.children === "string") {
                let html = childNode.props.children;
                html = html.replace("${hostname}", this.state.hostname );
                html = html.replace("${uuid}", this.state.uuid );
                html = html.replace("${token}", this.state.token );
                return React.cloneElement(childNode, [], html);
            }
            return React.cloneElement(childNode, [], this.replaceVars(childNode.props.children));
        });
    }    

    render() { 
        return (
            <div>{ this.replaceVars(this.props.children) }</div>
        );
    }
}

export default TabItemCurl;
