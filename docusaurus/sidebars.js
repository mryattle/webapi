/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

// https://v2.docusaurus.io/docs/sidebar
// module.exports = {
//     docs: {
//       Docusaurus: ['what-is-an-api', 'doc2', 'doc3'],
//       Features: ['mdx'],
//     },
//   };

module.exports = {
  docs: {
    "Introduction": ['what-is-an-api', 'the-business-case-for-api', 'security'],
    "Choosing API": [
        'picking-the-right-paradigm',
        {
          type: 'category',
          label: 'Request-Response',
          items: ['request-response/rest', 
            'request-response/rpc',
            'request-response/graphql'],
        },              
        {
          type: 'category',
          label: 'Event-Driven',
          items: ['event-driven/webhook',
            'event-driven/websocket'],
        },
    ],
  },
};