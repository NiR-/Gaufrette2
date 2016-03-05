FROM node:5-slim

RUN mkdir /var/s3 && npm install -g s3rver
EXPOSE 4521


CMD [ "s3rver", "-d", "/var/s3", "-p", "4521"]
