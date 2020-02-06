---
id: security
title: API Security 
---

Security is a critical element of any web application, particularly so for APIs. New security issues and vulnerabilities are always being discovered, and it's important to protect your APIs from attacks. A security breach can be disastrous, poor security implementations can lead to loss of critical data as well as revenue. 
To ensure an application is secure, there are many things engineers tend to do. This includes input validation, using the Secure Sockets Layer (SSL) protocol everywhere, validating content types, maintaining audit logs, and protecting against cross-site request forgery (CSRF) and cross-site scripting (XSS). All of these are important for any web application, and you should be doing them. Beyond these typical web application security practices, there are additional techniques that apply specifically to web APIs that you expose to developers outside your company. We look closely at those best practices and how companies are securing APIs in practice. Authentication and authorization are two foundation elements of security: 

- **Authentication**  - The process of **verifying who you are**. Web applications usually accomplish this by asking you to log in with a username and password. This combination is checked against an existing valid username/password record to ensure the request is authentic. 
- **Authorization**  - The process of verifying that **you are permitted to do what you are trying to do**. For instance, a web application might allow you to view a page; however, it might not allow you to edit that page unless you are an administrator. That's authorization. 

As you design an API, you need to think about how app developers will perform both authentication and authorization with your API. 

## HTTP basic authentication

Early on, API providers started supporting Basic Authentication. It's the simplest technique used to enforce access control on the web.  HTTP basic authentication is a **simple challenge and response mechanism** with which a server can request authentication information (a user ID and password) from a client. The client passes the authentication information to the server in an Authorization header. The authentication information is in base-64 encoding.

If a client makes a request for which the server expects authentication information, the server sends an HTTP response with a 401 status code, a reason phrase indicating an authentication error, and a WWW-Authenticate header. Most web clients handle this response by requesting a user ID and password from the user.

The format of a WWW-Authenticate header for HTTP basic authentication is:

```http
WWW-Authenticate: Basic realm="Our Site"
```

The WWW-Authenticate header contains a **realm attribute, which identifies the set of resources** to which the user ID and password will apply. Web clients display this string to the user. **Each realm might require different authentication information**, this means that you should need to authenticate with different credentials to different realms. Web clients can store the authentication information for each realm so that users do not need to retype the information for every request.

When the web client has obtained a user ID and password, it resends the original request with an Authorization header. Alternatively, the client can send the Authorization header when it makes its original request, and this header might be accepted by the server, avoiding the challenge and response process. 

The format of the Authorization header is a base64 encoded string containing two elements (user and password):

```http
Authorization: Basic dXN1cjIDIANXNzd29yZA==
```

![Basic auth authentication](/img/api/security/basic-auth.svg)

### Weakness

Although HTTP basic authentication is simple, it offers the least amount of security. It has several disadvantages, including the following: 

- Applications are required to store these credentials in clear text  or in a way that they can decrypt them (HTTP authentication digest can solve this problem)
- Users cannot revoke access to a single application without revoking access to all the applications by changing the password. 
- **Users cannot limit access to selected resources**. This means that **authorization process is oversimplified** giving users full access to all resources.

Standards like OAuth and SAML are born to overcome this disadvantages. 

## JSON Web Token

JSON Web Token (JWT) is an Internet standard for creating JSON-based access tokens that assert some number of claims. JWT defines a compact and self-contained mechanism for transmitting data  between parties in a way that can be verified and trusted because it is  digitally signed. Additionally, the encoding rules of a JWT also make  these tokens very easy to use within the context of HTTP. The tokens are signed by one party's private key (usually the server's), so that both parties (the other already being, by some suitable and trustworthy means, in possession of the corresponding public key) are able to verify that the token is legitimate. The tokens are designed to be compact, URL-safe, and usable especially in a web-browser single-sign-on (SSO) context. JWT claims can be typically used to pass identity of authenticated users between an identity provider and a service provider, or any other type of claims as required by business processes.

### When should you use JSON Web Tokens?

Here are some scenarios where JSON Web Tokens are useful:

- **Distributed authentication**: This is the most common scenario for using JWT. **Once the user is logged in**, each subsequent request will include the JWT, allowing the user to access routes, services, and resources that needs to verify that user identity. Single Sign On is a feature that widely uses JWT nowadays, because of its small overhead and its ability to be easily used across different domains.
- Information Exchange: JSON Web Tokens are a good way of securely transmitting information between parties. Because JWTs can be signed - for example, using public/private key pairs - you can be sure the senders are who they say they are. Additionally, as the signature is calculated using the header and the payload, you can also verify that the content hasn't been tampered with.

![JWT token authentication](/img/api/security/jwt-token.svg)

### Use

In authentication, **when the user successfully logs in using their credentials, a JSON Web Token will be returned** and must be saved locally (typically in local or session storage, but cookies can also be used), instead of the traditional approach of creating a session in the server and returning a cookie.

Whenever the user wants to access a protected route or resource, the user agent should send the JWT, typically in the Authorization header using the Bearer schema. The content of the header might look like the following:

```http
Authorization: Bearer eyJhbGci...<snip>...yu5CSpyHI
```

This is a stateless authentication mechanism as the user state is never saved in server memory. The server's protected routes will check for a valid JWT in the Authorization header, and if it is present, the user will be allowed to access protected resources. As JWTs are self-contained, all the necessary information is there, reducing the need to query the database multiple times.  

### Vulnerabilities and criticism

JSON web tokens may contain session state. But if project requirements allow **session invalidation before JWT expiration**, services can no longer trust token assertions by the token alone. To validate the session stored in the token is not revoked, token assertions must be checked against a data store. This renders the tokens no longer stateless, undermining the primary advantage of JWTs.

Software security architect Kurt Rodarmer points out additional **JWT design vulnerabilities around cryptographic signing keys** and a significant vulnerability that exposes a library’s JSON parser to open attack. This is a direct result of choosing JSON to express the token header, and is more difficult to mitigate. 

JWT it's a straightforward mechanism, it's simplicity allows developer to easily achieve authentication when performing system integration (especially when we want to perform legacy systems integration).

## OAuth

OAuth is an open standard that allows users to **grant access to applications without sharing passwords with them**. The latest version of the standard, OAuth 2.0, is the industry-standard protocol for authentication and authorization. It has been adopted by several companies, including Amazon, Google, Facebook, GitHub, Stripe, and Slack.

The biggest benefit of OAuth is that users do not need to share passwords with applications. For example, say TripAdvisor wants to build an application that will use a user's Facebook identity, profile, friend list, and other Facebook data. With OAuth, TripAdvisor can redirect that user to Facebook, where they can authorize TripAdvisor to access their information. After the user authorizes the sharing of data, TripAdvisor can then call the Facebook API to fetch this information. 

The second benefit of OAuth is that it allows API providers' users to **grant selective permission**. Each application has different requirements of what data it needs from an API provider. The OAuth framework allows API providers to grant access to one or more resources. 

Token Generation With OAuth, applications use an **access token to call APIs** on behalf of a user. The generation of this token happens in a multistep flow.  JWT format can be used during OAuth token generation. 

A token refresh and token revocation mechanisms are part of the OAuth standard. Using this features it is possible for the application to achieve session invalidation. 

### Use

Typical web APIs scenario will involve:

- an OAuth server that will provides Authentication & Authorization mechanisms
- a User Interface that the user will interact with
- a resource server (OAuth term for API server) that will provide resource for the UI 

![OAuth 2.0 authentication and authorization](/img/api/security/oauth2.svg)

Every request to the resource server(s) has to be performed using an authorization token. When securing the transmitting channel using SSL we ensure that tokens cannot be stolen, in this way the User Interface will take care of users sessions and will perform stateless requests on resources.