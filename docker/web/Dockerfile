FROM node:16-alpine as node
FROM nginx:1.20-alpine
LABEL maintainer="crea7dos3tos <crea7dos3tos@gmail.com>"
SHELL ["/bin/ash", "-oeux", "pipefail", "-c"]

ENV TZ=Asia/Tokyo

RUN apk update && \
    apk add --update --no-cache g++

# node command
COPY --from=node /usr/local/bin /usr/local/bin
# npm command
COPY --from=node /usr/local/lib /usr/local/lib
# yarn command
COPY --from=node /opt /opt

WORKDIR /app