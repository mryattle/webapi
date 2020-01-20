FROM node:13.1.0-alpine

# Create app directory
WORKDIR /app

# Install app dependencies
# A wildcard is used to ensure both package.json AND package-lock.json are copied
# where available (npm@5+)
COPY docker/package*.json ./

RUN npm install

# Develop
RUN npm install supervisor -g

COPY docker/server.js ./

CMD node server.js --port=8080