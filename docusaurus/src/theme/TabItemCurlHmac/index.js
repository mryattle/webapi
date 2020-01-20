/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

import React, { Component } from 'react';
import * as jsSHA from 'jssha';
import * as md5 from 'md5';

class TabItemCurlHmac extends Component {

    constructor(props) {
        super(props);
        this.state =  { 
            hostname: null,
            http_client_unix_timestamp_sub: null,
            message: (props.message ? props.message : ''),
            api_key: null,
            unix_timestamp: null
        }  
        // this.http_client_unix_timestamp_sub = null;  
        // if(props.http_client) {
        //     this.http_client = props.http_client;
        // }   
    }

    componentDidMount() {
        this.setState(state => ({ 
            hostname: window.location.hostname,
            api_key: http_client.api_key,}));
        const component = this;
        const http_client_unix_timestamp_sub = http_client.unix_timestamp.subscribe(
            function (value) {
                component.setState(state => ({
                    unix_timestamp: value})); 
            },
            function (err) {
                // console.log('Error: ' + err);
            },
            function () {
                // console.log('Completed');
            });                         
        http_client.renewUuid();  
        this.setState(state => ({ 
            http_client_unix_timestamp_sub: http_client_unix_timestamp_sub}));
    }

    componentWillUnmount() {
        this.state.http_client_unix_timestamp_sub.dispose();
    }    
    
    hmacSignature(timestamp, method, url, payload) {
        try {
            let hmac = new jsSHA('SHA-512', 'TEXT');
            hmac.setHMACKey('dCJ6rP9Ztvd3a5fakJujPBaXRBZ9cT2N','TEXT');
            hmac.update(timestamp);
            hmac.update(method);
            hmac.update(url);
            hmac.update(md5(payload));
            return hmac.getHMAC('HEX')
        } catch(e) {
            return e.message
        }    
    }  

    replaceVars(children) {
        return React.Children.map(children, (childNode) => {
            if (typeof childNode === "string")
                return childNode; // cover case: <div>text<div></div></div>
            if (typeof childNode.props.children === "string") {
                let html = childNode.props.children;
                html = html.replace("${hostname}", this.state.hostname );
                html = html.replace("${api_key}", this.state.api_key );
                html = html.replace("${message}", this.state.message );
                const hmac = this.hmacSignature(this.state.unix_timestamp, 
                    'POST', `/webhook?api_key=${this.state.api_key}`, 
                    this.state.message);
                html = html.replace("${hmac_signature}", `${this.state.unix_timestamp}:${hmac}` );
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

export default TabItemCurlHmac;
