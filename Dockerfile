FROM ginvoicing/php:latest
ARG TIMEZONE
ENV DEBIAN_FRONTEND noninteractive
LABEL maintainer="Tarun Jangra<tarun.jangra@hotmail.com>"

# Set timezone
RUN echo ${TIMEZONE} | tee /etc/timezone

RUN apt-get update && apt-get install -y xvfb

RUN apt-get autoremove -y \
    && apt-get clean \
    && apt-get autoclean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && rm /bin/sh && ln -s /bin/bash /bin/sh


# setup wkhtmltopdf 12.6 patched with QT
ADD https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.buster_arm64.deb /tmp

RUN dpkg -i /tmp/wkhtmltox_0.12.6-1.buster_arm64.deb \
    && rm -rf /tmp/wkhtmltox_0.12.6-1.buster_arm64.deb

WORKDIR /app

ENTRYPOINT [ "/usr/bin/Xvfb", ":99"]
