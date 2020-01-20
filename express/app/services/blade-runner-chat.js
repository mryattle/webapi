const dialogflow = require('dialogflow');

class BladeRunnerChat {
    
    constructor(session_id, project_id) {
        this.session_id = session_id;
        this.project_id = project_id;
        // Instantiates a session client
        this.sessionClient = new dialogflow.SessionsClient({
            keyFilename: "/app/google-key.json"});        
    }

    async detectIntent(
        projectId,
        sessionId,
        query,
        contexts,
        languageCode
      ) {
        // The path to identify the agent that owns the created intent.
        const sessionPath = this.sessionClient.sessionPath(projectId, sessionId);
      
        // The text query request.
        const request = {
          session: sessionPath,
          queryInput: {
            text: {
              text: query,
              languageCode: languageCode,
            },
          },
        };
      
        if (contexts && contexts.length > 0) {
          request.queryParams = {
            contexts: contexts,
          };
        }
      
        const responses = await this.sessionClient.detectIntent(request);
        return responses[0];
      }

      async question(query, languageCode) {
        // Keeping the context across queries let's us simulate an ongoing conversation with the bot
        // let context;
        let intentResponse;
        let context;
        intentResponse = await this.detectIntent(
            this.project_id,
            this.session_id,
            query,
            context,
            languageCode
        );
        return intentResponse.queryResult.fulfillmentText;
      }      

}
module.exports = BladeRunnerChat;