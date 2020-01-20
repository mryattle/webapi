FROM node:13.6.0-alpine

RUN apk --update add git
# RUN npm install -g npx
RUN npx @docusaurus/init@2.0.0-alpha.39 init /app classic
RUN npm install npm-watch
RUN npm install -g supervisor

WORKDIR /app

# Install app dependencies
# A wildcard is used to ensure both package.json AND package-lock.json are copied
# where available (npm@5+)
COPY docker/package.json ./
RUN npm install

EXPOSE 3000
CMD npm run start -- --port=3000 --host=0.0.0.0