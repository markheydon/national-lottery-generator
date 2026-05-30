FROM mcr.microsoft.com/devcontainers/php:1-8.4

# The base image can include a Yarn apt source that intermittently fails
# signature verification during feature installation (for example while
# installing docker-outside-of-docker). Remove it to keep rebuilds reliable.
RUN rm -f /etc/apt/sources.list.d/yarn.list