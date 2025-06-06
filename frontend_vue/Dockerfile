FROM node:lts-alpine

# install simple http server for serving static content
RUN npm install -g http-server

# make the 'app' folder the current working directory
WORKDIR /app

# copy both 'package.json' and 'package-lock.json' (if available)
COPY package*.json ./

#COPY src/js /app/src/js
#COPY src/assets /app/src/assets

# install project dependencies
RUN npm install

# copy project files and folders to the current working directory (i.e. 'app' folder)
COPY . .

# build app for production with minification
RUN npm run build

EXPOSE 8080
CMD [ "http-server", "dist" ]


# docker build -t revendamais_frontend_vue .

# docker run -it -d -p 8075:8080 --rm --name revendamais_frontend_vue revendamais_frontend_vue


