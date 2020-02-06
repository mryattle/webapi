---
id: picking-the-right-paradigm
title: Picking the right paradigm
---

Picking the right API paradigm is important. An API paradigm defines the interface exposing backend data of a service to other applications. When starting out with APIs, organizations do not always consider all the factors that will make an API successful. As a result, there isn't enough room built in to add the features they want later on. This can also happen when the organization or product changes over time. Unfortunately, after there are developers using it, changing an API is difficult (if not impossible). To save time, effort, and headache and to leave room for new and exciting features it's worthwhile to give some thought to protocols, patterns, and a few best practices before you get started. This will help you design an API that allows you to make the changes you want in the future. 

Over the years, multiple API architectural styles, strategies and application level protocols have emerged. REST, RPC, GraphQL, WebHooks, and WebSockets are some of the most popular standards today.  We will describe the key aspects of the major paradigm used to develop ubiquitous computing systems, doing so we will outlines the contribution of each communication paradigm to the quality  properties that are very often sought for ubiquitous systems meaning those systems where computing is made to appear anytime and everywhere using any device, in any location, and in any format.

## Request-Reponse

Request-response APIs typically expose an interface through an HTTP-based web server. APIs define a set of endpoints. Clients make HTTP requests for data to those endpoints and the server returns responses. The response is typically sent back as JSON or XML. 

Using request response paradigm clients will send a request message to the server that will elaborate the request. Using HTTP protocol after a request message is sent **the client will wait for server response** that  response will travel back on the connection established between the two parties.

![Request-Response comunication model](/img/api/request-response.svg)

From the point of view of the system a request-response paradigm promotes the following properties:

- **Efficiency** - The detection of state changes in nearby entities (i.e., other applications, services, agents or devices) is a common operation in ubiquitous systems. Request-response paradigm can be more convenient if the notification of **changes in the state of the entities is time-constrained** or if power consumption must be controlled periodically. As a consequence request-response may help to achieve efficiency in ubiquitous systems. Request-response paradigm would be efficient if the delivery times are known a priori, and the number of recipients (as number of connections used by a single client to establish server communications) is small
- **Reliable Delivery** - Reliable delivery means that a receiver (or a set of receivers) has to  send an acknowledgement for each received message in order to confirm  their reception. In request-response, receiving a response to a request implies that the request was delivered correctly.
- **Security** - Security is an important concern in ubiquitous systems. Hence, the  information to be exchanged should be encrypted and trusting mechanisms  established for senders and receivers. Trusting mechanisms such as digital signatures or certificates are easy to establish only in request-response-based communications
- **Timeliness** - Timeliness is quality of being done or occurring at a favourable or useful time. Using request-response paradigm it is possible to establish if a notification will be received, (see *Reliable Delivery*), thus delimiting the time of a message delivery from the point of view of the sender

There are three common architectural styles used by services to expose request-response APIs: REST, RPC, and GraphQL. 

## Event-Driven

With request-response APIs, for services with constantly changing data, the response can quickly become stale. Developers who want to stay up to date with the changes in data often end up polling the API. With polling, developers constantly query API endpoints at a predetermined frequency and look for new data. 
If developers poll at a low frequency, their apps will not have data about all the events (like a resource being created, updated, or deleted) that occurred since the last poll. However, polling at a high frequency would lead to a huge waste of resources, as most API calls will not return any new data. In one case, Zapier did a study and found that only about 1.5% of their polling API calls returned new data. 

![Event-Driven comunication model](/img/api/event-driven.svg)

From the point of view of the system a event-driven paradigm promotes the following properties:

- **Efficiency** - The detection of state changes in nearby entities (i.e., other applications, services, agents or devices) is a common operation in ubiquitous systems. The request-response paradigm semantics involves sending periodical messages to retrieve the state of other entities, which is known as polling. Polling operations are usually considered very inefficient in comparison with the scheme supported by the event-driven paradigm, since such changes infrequently occur and **memory, CPU and power resources** are wasted when sending useless messages. Moreover, in request-response, to distribute information to a set of receivers, the number of messages to be sent must be equal to the number of receivers. In event-driven, publishers always distribute one message, regardless of the number of subscribers. If a piece of information has to be delivered to a wide range of receivers and it is not possible to pre-establish when it is going to be sent, nor its delivery frequency, then the event-driven paradigm should be selected in this case in order to improve efficiency
- **Mobility Support** - The event-driven paradigm promotes the decoupling between publishers and  subscribers. In particular, in event-driven-based communications, it is  totally transparent if either a publisher or a subscriber is present or not in a system.
- **Adaptability** - In ubiquitous systems, the support to context-awareness features  involves to dynamically adapt the functionality provided by services and applications to the information retrieved from the context. In event-driven communications, subscriptions may be dynamically established  and dropped depending on the context. Thus, the event-driven paradigm is more  suitable for building adaptable, ubiquitous systems.

To share data about events in real time there are common strategies like WebHooks or application level protocols as WebSockets and HTTP Streaming. 